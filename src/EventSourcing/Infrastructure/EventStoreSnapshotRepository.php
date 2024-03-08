<?php

declare(strict_types=1);

namespace PicPay\FraudCheckCommons\EventSourcing\Infrastructure;

use PicPay\FraudCheckCommons\EventSourcing\Domain\EventStoreSnapshotRepository as EventStoreSnapshotRepositoryContract;
use PicPay\FraudCheckCommons\Repository\Infrastructure\Repository;
use PicPay\FraudCheckCommons\Repository\Infrastructure\RepositoryException;

final class EventStoreSnapshotRepository extends Repository implements EventStoreSnapshotRepositoryContract
{
    protected string $collection = 'event_store_snapshot';

    /**
     * @throws RepositoryException
     */
    public function insert(array $payload): void
    {
        $this->getConnection()->updateOne(
            [
                'aggregate_root_id' => $payload['aggregate_root_id']
            ],
            [
                '$set' => $payload
            ],
            [
                'upsert' => true
            ]
        );
    }

    /**
     * @throws RepositoryException
     */
    public function findByAggregateRoot(string $aggregateRootId): ?array
    {
        $event = $this->getConnection()
            ->find([
                'aggregate_root_id' => $aggregateRootId,
            ]);

        return $event[0] ?? null;
    }
}
