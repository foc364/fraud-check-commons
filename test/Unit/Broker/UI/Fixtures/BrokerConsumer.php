<?php

declare(strict_types=1);

namespace Test\Unit\Broker\UI\Fixtures;

use PicPay\FraudCheckCommons\Broker\Domain\BrokerConsumerInterface;

class BrokerConsumer implements BrokerConsumerInterface
{
    public function handle(array $payload): void
    {
    }
}
