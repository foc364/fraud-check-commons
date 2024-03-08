<?php

declare(strict_types=1);

namespace PicPay\FraudCheckCommons\EventSourcing\Infrastructure\Aggregate;

use PicPay\FraudCheckCommons\EventSourcing\Infrastructure\DomainEvent;
use EventSauce\EventSourcing\AggregateRootBehaviour;
use EventSauce\EventSourcing\AggregateRootId;
use EventSauce\EventSourcing\Snapshotting\AggregateRootWithSnapshotting as EventSauceAggregateRootWithSnapshotting;
use EventSauce\EventSourcing\Snapshotting\SnapshottingBehaviour;

abstract class AggregateRoot implements EventSauceAggregateRootWithSnapshotting
{
    use AggregateRootBehaviour;
    use SnapshottingBehaviour;

    private mixed $state;
    private int $aggregateRootVersion = 0;

    public function apply(object $event): void
    {
        $parts = explode('\\', get_class($event));
        $methodName = 'apply' . end($parts);

        if (method_exists($this, $methodName)) {
            $this->{$methodName}($event);
        }

        ++$this->aggregateRootVersion;
    }

    protected function createSnapshotState(): mixed
    {
        return $this->state;
    }

    protected static function reconstituteFromSnapshotState(
        AggregateRootId $id,
        mixed $state
    ): static {
        $aggregateRoot = new static($id);
        $aggregateRoot->state = $state;

        return $aggregateRoot;
    }

    public static function initiate(AggregateRootId $aggregateRootId): static
    {
        return new static($aggregateRootId);
    }

    final protected function record(DomainEvent $event): void
    {
        $this->state = $event->toPayload();

        $this->recordThat($event);
    }
}
