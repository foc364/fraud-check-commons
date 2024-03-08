<?php

declare(strict_types=1);

namespace PicPay\FraudCheckCommons\Bus\Infrastructure;

use PicPay\FraudCheckCommons\Bus\Domain\CommandInterface;
use PicPay\FraudCheckCommons\Bus\Domain\CommandBusInterface;
use PicPay\FraudCheckCommons\Bus\Domain\BusLocatorInterface;
use League\Tactician\CommandBus as TacticianCommandBus;
use League\Tactician\Middleware;

class CommandBus implements CommandBusInterface
{
    private array $middleware;

    public function __construct(private BusLocatorInterface $locator)
    {
    }

    public function dispatch(CommandInterface $command)
    {
        return (new TacticianCommandBus($this->middleware))->handle($command);
    }

    public function addHandler(string $command, string $handler): void
    {
        $this->locator->addHandler($handler, $command);
    }

    /**
     * @param Middleware[] $middleware
     */
    public function setMiddleware(array $middleware): CommandBusInterface
    {
        $this->middleware = $middleware;
        return $this;
    }
}
