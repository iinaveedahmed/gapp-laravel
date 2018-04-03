<?php
if (!function_exists('ilog')) {

    function ilog($key = null, $default = null)
    {
        if (is_null($key)) {
            return app('ipaas-info');
        }

        if (is_array($key)) {
            return app('ipaas-info')->put($key);
        }

        return app('ipaas-info')->get($key, $default);
    }
}

if (!function_exists('iresponse')) {

    function iresponse($key = null, $default = null)
    {
        if (is_null($key)) {
            return app('ipaas-response');
        }

        if (is_array($key)) {
            return app('ipaas-response')->put($key);
        }

        return app('ipaas-response')->get($key, $default);
    }
}

if (!function_exists('irequest')) {

    function irequest()
    {
        return new \App\Ipaas\Request(request());
    }
}
