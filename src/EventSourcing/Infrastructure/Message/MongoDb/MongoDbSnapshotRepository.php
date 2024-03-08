<?php

declare(strict_types=1);

namespace PicPay\FraudCheckCommons\EventSourcing\Infrastructure\Message\MongoDb;

use EventSauce\EventSourcing\AggregateRootId;
use EventSauce\EventSourcing\Snapshotting\Snapshot;
use EventSauce\EventSourcing\Snapshotting\SnapshotRepository as EventSauceSnapshotRepository;
use PicPay\FraudCheckCommons\EventSourcing\Infrastructure\Message\SnapshotRepository;

final class MongoDbSnapshotRepository extends SnapshotRepository implements EventSauceSnapshotRepository
{
    public function retrieve(AggregateRootId $id): ?Snapshot
    {
        $snapshot = $this->repository->findByAggregateRoot($id->toString());

        if ($snapshot === null) {
            return null;
        }

        return new Snapshot(
            $id,
            $snapshot['version'],
            $snapshot['state']
        );
    }
}
