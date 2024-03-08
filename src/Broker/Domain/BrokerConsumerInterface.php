<?php

declare(strict_types=1);

namespace PicPay\FraudCheckCommons\Broker\Domain;

interface BrokerConsumerInterface
{
    public function handle(array $payload): void;
}
