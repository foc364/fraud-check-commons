<?php

declare(strict_types=1);

use LeoCarmo\CircuitBreaker\Adapters\RedisClusterAdapter;

return [
    'feedzai' => [
        'adapter' => RedisClusterAdapter::class,
        'config' => [
            'timeWindow' => 60,
            'failureRateThreshold' => 100,
            'intervalToHalfOpen' => 30,
        ],
    ],
];
