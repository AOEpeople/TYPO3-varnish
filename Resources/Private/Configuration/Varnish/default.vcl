vcl 4.0;

import std;

include "backend.vcl";
include "acl.vcl";

include "sub_vcl_recv.vcl";
include "sub_vcl_backend_response.vcl";
include "sub_vcl_deliver.vcl";

/*
 * vcl_pipe
 */
sub vcl_pipe {
    # http://www.varnish-cache.org/ticket/451
    # This forces every pipe request to be the first one.
    set bereq.http.connection = "close";
}