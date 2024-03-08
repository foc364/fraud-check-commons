<?php

declare(strict_types=1);

namespace PicPay\FraudCheckCommons\EventSourcing\Domain;

interface AggregateRootInterface
{
    public function handle(array $payload): void;
}
