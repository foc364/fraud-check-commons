<?php

declare(strict_types=1);

namespace PicPay\FraudCheckCommons\EventSourcing\Infrastructure\Aggregate;

use EventSauce\EventSourcing\AggregateRootId as EventSauceAggregateRootId;

final class AggregateRootId implements EventSauceAggregateRootId
{
    public function __construct(private string $aggregateRootId)
    {
    }

    public function toString(): string
    {
        return $this->aggregateRootId;
    }

    public static function fromString(string $aggregateRootId): static
    {
        return new static($aggregateRootId);
    }
}
