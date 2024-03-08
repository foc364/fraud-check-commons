<?php

declare(strict_types=1);

namespace PicPay\FraudCheckCommons\Logger\Infrastructure;

use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Logger\LoggerFactory;
use Psr\Container\ContainerInterface;

class StdoutLoggerFactory
{
    public function __invoke(ContainerInterface $container): StdoutLoggerInterface
    {
        $loggerFactory = $container->get(LoggerFactory::class);

        return $loggerFactory->get('stdout');
    }
}
