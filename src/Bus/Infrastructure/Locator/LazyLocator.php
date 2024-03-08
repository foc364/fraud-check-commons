<?php

declare(strict_types=1);

namespace PicPay\FraudCheckCommons\Bus\Infrastructure\Locator;

use PicPay\FraudCheckCommons\Bus\Domain\CommandHandlerInterface;
use League\Tactician\Exception\MissingHandlerException;

class LazyLocator extends HandlerLocator
{
    public function addHandler(string $handler, string $commandClassName): void
    {
        $this->handlers[$commandClassName] = function () use ($handler) {
            return make($handler);
        };
    }

    public function getHandlerForCommand($commandName): CommandHandlerInterface
    {
        if (!is_callable($this->handlers[$commandName])) {
            throw MissingHandlerException::forCommand($commandName);
        }

        $handler = $this->handlers[$commandName];

        return $handler();
    }
}
