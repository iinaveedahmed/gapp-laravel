<?php
return [
    'stackdriver' => [
        'driver' => 'custom',
        'via' => Ipaas\Logger\GLogger::class,
    ]
];