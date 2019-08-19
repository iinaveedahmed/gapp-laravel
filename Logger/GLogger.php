<?php

namespace Ipaas\Gapp\Logger;

use Google\Cloud\ErrorReporting\Bootstrap;
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
        $logName = $config['logName'] ?? 'app';
        $psrLogger = Bootstrap::$psrLogger;
        $handler = new PsrHandler($psrLogger);

        $service = $psrLogger->getMetadataProvider()->serviceId();
        $version = $psrLogger->getMetadataProvider()->versionId();

        $logger = new Logger(
            $logName,
            [$handler],
            [
                function ($record) use ($service, $version) {
                    $record['context'] += [
                        'serviceContext' => [
                            'service' => $service,
                            'version' => $version,
                        ],
                        'context' => ilog()->toArray()
                    ];
                    return $record;
                }
            ]
        );
        return $logger;
    }
}
