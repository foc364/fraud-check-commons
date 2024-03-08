<?php

declare(strict_types=1);

return [
    'httpbin' => [
        'client' => [
            'base_uri' => 'https://httpbin.org',
            'timeout' => 5,
            'connect_timeout' => 5,
        ],
        'pool' => [
            'max_connections' => 10,
        ],
        'retry' => [
            'count' => 1,
            'delay_ms' => 100,
        ],
        'circuit-breaker' => false,
        'health-check' => false
    ],
    'feedzai' => [
        'client' => [
            'base_uri' => env('FEEDZAI_PULSE_URL'),
            'timeout' => 5,
            'connect_timeout' => 5,
        ],
        'pool' => [
            'max_connections' => 10,
        ],
        'retry' => [
            'count' => 1,
            'delay_ms' => 100,
        ],
        'circuit-breaker' => true,
        'health-check' => false
    ],
];
