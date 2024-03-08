<?php

declare(strict_types=1);

namespace PicPay\FraudCheckCommons\Bus\Domain;

interface CommandBusInterface
{
    public function dispatch(CommandInterface $command);

    public function addHandler(string $command, string $handler): void;

    public function setMiddleware(array $middleware): self;
}
