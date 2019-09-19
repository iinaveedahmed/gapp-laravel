<?php

namespace Ipaas\Gapp\Logger;

use Google\Cloud\Logging\LoggingClient;
use Monolog\Handler\PsrHandler;
use Monolog\Logger;

/**
 * Class GLogger
 * @package Ipaas\Gapp\Logger
 */
class GLogger
{
    public function __invoke(array $config)
    {
        $logName = isset($config['logName']) ? $config['logName'] : 'app';
        $psrLogger = LoggingClient::psrBatchLogger($logName);
        $handler = new PsrHandler($psrLogger);
        $logger = new Logger($logName, [$handler]);
        return $logger;
    }
}
