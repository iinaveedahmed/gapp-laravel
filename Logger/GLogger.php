<?php
namespace Ipaas\Logger;

use Google\Cloud\Logging\LoggingClient;
use Monolog\Logger;

/**
 * Class GLogger
 * @package Ipaas\Logger
 */
class GLogger
{
    public function __invoke(array $config)
    {
        $logName = isset($config['logName']) ? $config['logName'] : 'app';
        $psrLogger = LoggingClient::psrBatchLogger($logName);
        $handler = new PsrContext($psrLogger);
        $logger = new Logger($logName, [$handler]);
        return $logger;
    }
}
