<?php

declare(strict_types=1);

namespace PicPay\FraudCheckCommons\EventSourcing\Infrastructure\Message\PostgreSql;

use EventSauce\EventSourcing\AggregateRootId;
use EventSauce\EventSourcing\Snapshotting\Snapshot;
use EventSauce\EventSourcing\Snapshotting\SnapshotRepository as EventSauceSnapshotRepository;
use PicPay\FraudCheckCommons\EventSourcing\Infrastructure\Message\SnapshotRepository;

final class PostgreSqlSnapshotRepository extends SnapshotRepository implements EventSauceSnapshotRepository
{
    public function retrieve(AggregateRootId $id): ?Snapshot
    {
        $snapshot = $this->repository->findByAggregateRoot($id->toString());

        if ($snapshot === null) {
            return null;
        }

        $state = $snapshot['state'];
        if (is_string($snapshot['state'])) {
            $state = json_decode($snapshot['state'], true, 512, JSON_THROW_ON_ERROR);
        }

        return new Snapshot(
            $id,
            $snapshot['version'],
            $state
        );
    }
}
