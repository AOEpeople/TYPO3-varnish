/*
 * vcl_recv
 * subroutine is called at the beginning of a request, after the complete request has been received and parsed.
 * Its purpose is to decide whether or not to serve the request, how to do it, and, if applicable, which backend to use.
 * In vcl_recv you can also alter the request. Typically you can alter the cookies and add and unset request headers.
 * Note that in vcl_recv only the request object, req is available.
 */
sub vcl_recv {
    return(hash);
    # Catch BAN Command (flush cache)
    if (req.method == "BAN" && client.ip ~ ban) {

        # feature: X-Ban-Regex -> BAN all caches matching the url regex
        if (req.http.X-Ban-Regex) {
            ban("obj.http.url ~ " + req.http.X-Ban-Regex);
            return (synth(200,"Banned " + req.http.X-Ban-Regex));
        }

        # BAN all pages on BAN_ALL
        if(req.http.X-Ban-All) {
            ban("req.url ~ /");
            return (synth(200,"Banned all"));
        }

        # BAN pages with X-Tags
        if(req.http.X-Ban-Tags) {
            ban("obj.http.X-Tags ~ " + req.http.X-Ban-Tags);
            return (synth(200,"Banned tag: " + req.http.X-Ban-Tags));
        }

        return (synth(405,"Unknown BAN header. Use either 'X-Ban-All' or 'X-Ban-Tags'."));
    } elseif(req.method == "BAN" && client.ip !~ ban) {
        return (synth(403,"Not allowed to BAN from IP: " + client.ip));
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
    # @TODO: make VCL 4 comptabible
    #if (req.backend.healthy) {
    #    set req.grace = 30s;
    #} else {
    #    set req.grace = 600s;
    #}

    # Pipe unknown Methods
    if (req.method != "GET" &&
        req.method != "HEAD" &&
        req.method != "PUT" &&
        req.method != "POST" &&
        req.method != "TRACE" &&
        req.method != "OPTIONS" &&
        req.method != "DELETE") {
        return (pipe);
    }

    # Cache only GET or HEAD Requests
    if (req.method != "GET" && req.method != "HEAD") {
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

    # unset cookie for known-static file extensions
    if (req.url ~ "^[^?]*\.(css|js|htc|xml|txt|swf|flv|pdf|gif|jpe?g|png|ico)$") {
        unset req.http.Cookie;
    }
    # Lookup everything else in the Cache
    return (hash);
}