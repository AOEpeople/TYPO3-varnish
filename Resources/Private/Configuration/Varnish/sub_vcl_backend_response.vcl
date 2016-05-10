/*
 * vcl_backend_response
 * Called after the response headers have been successfully retrieved from the backend.
 */
sub vcl_backend_response {

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
        set beresp.uncacheable = true;
        return(deliver);
    }

    # Cache everything elsecd
    set beresp.http.X-Cacheable = "YES";
    return (deliver);
}