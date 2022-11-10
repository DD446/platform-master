<?php declare(strict_types=1);

return [
    'hosts' => [
        [
            'host' => env('ELASTIC_HOST', 'localhost'),
            'port' => env('ELASTIC_PORT', '9200'),
            'scheme' => env('ELASTIC_SCHEME', 'https'),
            'user' => env('ELASTIC_USER', 'stats'),
            'pass' => env('ELASTIC_PASS', 'ZRL4tUdh'),
        ]
    ]
];
