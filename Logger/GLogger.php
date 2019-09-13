<?php

namespace Ipaas\Gapp\Logger;

use Closure;
use Exception;
use Google\Cloud\ErrorReporting\Bootstrap;
use Google\Cloud\Logging\PsrLogger;
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

        $logger = new Logger(
            $logName,
            [$handler],
            $this->getProcessor($psrLogger)
        );
        return $logger;
    }

    /**
     * @param  PsrLogger  $psrLogger
     * @return Closure[]
     */
    public function getProcessor(PsrLogger $psrLogger)
    {
        return [
            function ($record) use ($psrLogger) {

                try { // try context from helper in-case helper is not loaded
                    $context = ilog()->toArray();
                } catch (Exception $exception) {
                    $context = "Unable to get context";
                }

                return $record['context'] += [
                    'serviceContext' => [
                        'service' => $psrLogger->getMetadataProvider()->serviceId(),
                        'version' => $psrLogger->getMetadataProvider()->versionId(),
                    ],
                    'context' => $context
                ];
            }
        ];
    }
}
