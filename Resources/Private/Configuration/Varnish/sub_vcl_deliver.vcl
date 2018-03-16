/*
 * vcl_deliver
 */
sub vcl_deliver {

    # feature: X-Ban-Regex
    # unset resp.http.url set in backend_response to be able to ban cache objects by url regex
    # this action is a cleanup
    unset resp.http.url;

    # Expires Header set by TYPO3 are used to define Varnish caching only
    # therefore do not send them to the Client
    if (resp.http.Pragma == "public") {
        unset resp.http.expires;
        unset resp.http.pragma;
        unset resp.http.cache-control;
    }

    if(resp.http.X-Debug) {
        if (resp.http.X-Varnish ~ "[0-9]+ +[0-9]+") {
            set resp.http.X-Cache = "HIT";
        } else {
            set resp.http.X-Cache = "MISS";
        }
    }else {
        # unset internal headers
        unset resp.http.Via;
        unset resp.http.Server;
        unset resp.http.X-Varnish;
        unset resp.http.X-Tags;
        unset resp.http.X-Debug;
        unset resp.http.X-Cacheable;
        unset resp.http.X-Cache;
        unset resp.http.X-Powered-By;
    }

    return (deliver);
}