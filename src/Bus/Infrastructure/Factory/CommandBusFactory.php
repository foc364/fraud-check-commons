<?php

declare(strict_types=1);

namespace PicPay\FraudCheckCommons\Bus\Infrastructure\Factory;

use PicPay\FraudCheckCommons\Bus\Domain\CommandBusInterface;
use PicPay\FraudCheckCommons\Bus\Infrastructure\Locator\LazyLocator;
use PicPay\FraudCheckCommons\Bus\Infrastructure\CommandBus;
use Hyperf\Contract\ConfigInterface;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\MethodNameInflector\HandleInflector;
use Psr\Container\ContainerInterface;

class CommandBusFactory
{
    public function __invoke(ContainerInterface $container): CommandBusInterface
    {
        $config = $container->get(ConfigInterface::class);

        $locator = new LazyLocator();
        $locator->addHandlers($config->get('command-bus'));

        $commandHandlerMiddleware = new CommandHandlerMiddleware(
            new ClassNameExtractor(),
            $locator,
            new HandleInflector()
        );

        $middlewares = [
            $commandHandlerMiddleware,
        ];

        return (new CommandBus($locator))->setMiddleware($middlewares);
    }
}
