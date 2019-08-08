<?php
return [
    'stackdriver' => [
        'driver' => 'custom',
        'via' => Ipaas\Gapp\Logger\GLogger::class,
    ]
];
