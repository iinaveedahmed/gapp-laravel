<?php
return [
    'stack-driver' => [
        'driver' => 'custom',
        'via' => Ipaas\Gapp\Logger\GLogger::class,
    ]
];
