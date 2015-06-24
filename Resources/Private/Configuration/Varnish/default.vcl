include "backend.vcl";
include "acl.vcl";

/*
 * vcl_recv
 */
sub vcl_recv {

	# Catch BAN Command
	if (req.request == "BAN" && client.ip ~ ban) {

        # BAN all pages on BAN_ALL
		if(req.http.X-Ban-All) {
			ban("req.url ~ /");
			error 200 "Banned all";
		}

        # BAN pages with X-Tags
		if(req.http.X-Ban-Tags) {
			ban("obj.http.X-Tags ~ " + req.http.X-Ban-Tags);
			error 200 "Banned tag: " + req.http.X-Ban-Tags;
		}

		error 405 "Unknown BAN header. Use either 'X-Ban-All' or 'X-Ban-Tags'.";
	}elseif(req.request == "BAN" && client.ip !~ ban) {
	    error 403 "Not allowed to BAN from IP: " + client.ip;
	}

	# Set X-Forwarded-For Header
	if (req.restarts == 0) {
		if (req.http.x-forwarded-for) {
			set req.http.X-Forwarded-For =
			req.http.X-Forwarded-For + ", " + client.ip;
		} else {
			set req.http.X-Forwarded-For = client.ip;
		}
	}

    # asynchronous cache invalidation https://www.varnish-cache.org/trac/wiki/VCLExampleGrace
	if (req.backend.healthy) {
        set req.grace = 30s;
    } else {
        set req.grace = 600s;
    }

	# Pipe unknown Methods
	if (req.request != "GET" &&
		req.request != "HEAD" &&
		req.request != "PUT" &&
		req.request != "POST" &&
		req.request != "TRACE" &&
		req.request != "OPTIONS" &&
		req.request != "DELETE") {
		return (pipe);
	}

	# Cache only GET or HEAD Requests
	if (req.request != "GET" && req.request != "HEAD") {
		return (pass);
	}

	# do not cache TYPO3 BE User requests
	if (req.http.Cookie ~ "be_typo_user" || req.url ~ "^/typo3/") {
		return (pass);
	}

	# do not cache Authorized content
	if (req.http.Authorization) {
		return (pass);
	}

    # Remove cookie for known-static file extensions
    if (req.url ~ "^[^?]*\.(css|js|htc|xml|txt|swf|flv|pdf|gif|jpe?g|png|ico)$") {
        remove req.http.Cookie;
    }

	# Lookup everything else in the Cache
	return (lookup);
}


/*
 * vcl_fetch
 */
sub vcl_fetch {

    # set minimum timeouts to auto-discard stored objects
    set beresp.grace = 600s;

    # Varnish determined the object was not cacheable
    if (beresp.ttl <= 0s && beresp.http.X-Debug) {
        set beresp.http.X-Cacheable = "NO:TTL zero";
    }

    # You are respecting the Cache-Control=private header from the backend
    if (beresp.http.Cache-Control ~ "private") {
        if(beresp.http.X-Debug) {
            set beresp.http.X-Cacheable = "NO:Cache-Control=" + beresp.http.Cache-Control;
        }
        return(hit_for_pass);
    }

	# Cache only GET or HEAD Requests
	if (req.request != "GET" && req.request != "HEAD") {
        if(beresp.http.X-Debug) {
            set beresp.http.X-Cacheable = "NO:No GET or HEAD request";
        }
		return (hit_for_pass);
	}

	# do not cache TYPO3 BE User requests
    if (req.http.Cookie ~ "be_typo_user" || req.url ~ "^/typo3/") {
        if(beresp.http.X-Debug) {
            set beresp.http.X-Cacheable = "NO:be_typo_user cookie found or within path '^/typo3/'";
        }
        return (hit_for_pass);
    }

	# Cache everything else
	set beresp.http.X-Cacheable = "YES";
    return (deliver);
}


/*
 * vcl_deliver
 */
sub vcl_deliver {

	# Expires Header set by TYPO3 are used to define Varnish caching only
	# therefore do not send them to the Client
	if (resp.http.Pragma == "public") {
		unset resp.http.expires;
		unset resp.http.pragma;
		unset resp.http.cache-control;
	}

	if(resp.http.X-Debug) {
	    if (obj.hits > 0) {
            set resp.http.X-Cache = "HIT";
        } else {
            set resp.http.X-Cache = "MISS";
        }
	}else {
        # Remove internal headers
        remove resp.http.Via;
        remove resp.http.Server;
        remove resp.http.X-Varnish;
        remove resp.http.X-Tags;
        remove resp.http.X-Debug;
        remove resp.http.X-Cacheable;
        remove resp.http.X-Cache;
	}

	return (deliver);
}


/*
 * vcl_pipe
 */
sub vcl_pipe {
    # http://www.varnish-cache.org/ticket/451
    # This forces every pipe request to be the first one.
    set bereq.http.connection = "close";
}