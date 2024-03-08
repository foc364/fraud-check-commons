<?php

declare(strict_types=1);

namespace PicPay\FraudCheckCommons\EventSourcing\Infrastructure\Message;

use EventSauce\EventSourcing\AggregateRootId;
use EventSauce\EventSourcing\Snapshotting\Snapshot;
use EventSauce\EventSourcing\Snapshotting\SnapshotRepository as EventSauceSnapshotRepository;
use PicPay\FraudCheckCommons\EventSourcing\Domain\EventStoreSnapshotRepository;

abstract class SnapshotRepository implements EventSauceSnapshotRepository
{
    public function __construct(protected EventStoreSnapshotRepository $repository)
    {
    }

    public function persist(Snapshot $snapshot): void
    {
        $this->repository->insert([
            'aggregate_root_id' => $snapshot->aggregateRootId()->toString(),
            'version' => $snapshot->aggregateRootVersion(),
            'state' => $snapshot->state(),
        ]);
    }

    abstract public function retrieve(AggregateRootId $id): ?Snapshot;
}
