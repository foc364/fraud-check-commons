<?php

declare(strict_types=1);

namespace PicPay\FraudCheckCommons\Bus\Infrastructure\Locator;

use PicPay\FraudCheckCommons\Bus\Domain\BusLocatorInterface;
use PicPay\FraudCheckCommons\Bus\Domain\CommandHandlerInterface;
use League\Tactician\Exception\MissingHandlerException;
use League\Tactician\Handler\Locator\HandlerLocator as HandlerLocatorInterface;

class HandlerLocator implements BusLocatorInterface, HandlerLocatorInterface
{
    protected array $handlers = [];

    public function addHandler(string $handler, string $commandClassName): void
    {
        $handlerInstance = make($handler);
        $this->handlers[$commandClassName] = $handlerInstance;
    }

    public function addHandlers(array $commandClassToHandlerMap): void
    {
        foreach ($commandClassToHandlerMap as $commandClass => $handler) {
            $this->addHandler($handler, $commandClass);
        }
    }

    public function getHandlerForCommand($commandName): CommandHandlerInterface
    {
        if (!isset($this->handlers[$commandName])) {
            throw MissingHandlerException::forCommand($commandName);
        }

        return $this->handlers[$commandName];
    }
}
