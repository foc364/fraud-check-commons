<?php

declare(strict_types=1);

namespace PicPay\FraudCheckCommons\Broker\Domain;

interface BrokerProducerInterface
{
    public function connect(string $queue): self;
    public function produce(array $payload): void;
}
