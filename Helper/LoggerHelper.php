<?php

use Ipaas\Gapp\Logger\Client as LoggerClient;

if (!function_exists('ilog')) {
    /**
     * @param null $key
     * @return LoggerClient
     */
    function ilog($key = null)
    {
        /** @var LoggerClient $logger */
        $logger = app('logger-context');

        if (is_null($key)) {
            return $logger;
        }

        return $logger->setData($key);
    }
}