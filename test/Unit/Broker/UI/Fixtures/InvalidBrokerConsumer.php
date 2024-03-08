<?php

declare(strict_types=1);

namespace Test\Unit\Broker\UI\Fixtures;

class InvalidBrokerConsumer
{
    public function handle(array $payload): void
    {
    }
}
