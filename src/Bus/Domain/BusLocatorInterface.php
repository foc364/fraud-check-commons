<?php

declare(strict_types=1);

namespace PicPay\FraudCheckCommons\Bus\Domain;

interface BusLocatorInterface
{
    public function addHandler(string $handler, string $commandClassName): void;

    public function addHandlers(array $commandClassToHandlerMap): void;
}
