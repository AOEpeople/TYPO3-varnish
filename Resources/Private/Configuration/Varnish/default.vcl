vcl 4.0;

import std;

include "backend.vcl";
include "acl.vcl";

include "sub_vcl_recv.vcl";
include "sub_vcl_backend_response.vcl";
include "sub_vcl_deliver.vcl";
include "sub_vcl_pipe.vcl";