<?php
namespace Aoe\Varnish\System;

class Cookie
{
    /**
     * @param $name
     * @param null $value
     * @param null $expire
     * @param null $path
     * @param null $domain
     * @param null $secure
     * @param null $httponly
     */
    public function set(
        $name,
        $value = null,
        $expire = null,
        $path = null,
        $domain = null,
        $secure = null,
        $httponly = null
    ) {
        setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
    }
}
