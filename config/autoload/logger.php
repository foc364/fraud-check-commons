<?php

declare(strict_types=1);

use PicPay\FraudCheckCommons\Context\Infraestructure\ContextManager;
use PicPay\FraudCheckCommons\Http\Infrastructure\CorrelationIdService;
use PicPay\FraudCheckCommons\Logger\Infrastructure\CorrelationIdLogProcessor;

return [
    'default' => [
        'handler' => [
            'class' => Monolog\Handler\StreamHandler::class,
            'constructor' => [
                'stream' => 'php://stdout',
                'level' => Monolog\Logger::INFO,
            ],
        ],
        'formatter' => [
            'class' => NewRelic\Monolog\Enricher\Formatter::class,
        ],
        'processors' => [
            [
                'class' => CorrelationIdLogProcessor::class,
                'constructor' => [
                    'correlationIdService' => new CorrelationIdService(new ContextManager())
                ],
            ],
        ]
    ],
];
