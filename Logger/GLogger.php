<?php
namespace Ipaas\Logger;

use Google\Cloud\Logging\LoggingClient;
use Monolog\Handler\PsrHandler;
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
        $handler = new PsrHandler($psrLogger);
        $logger = new Logger(
            $logName,
            [$handler],
            [
                function ($record) {
                    $record['context'] += ilog()->toArray();
                    return $record;
                }
            ]
        );
        return $logger;
    }
}
