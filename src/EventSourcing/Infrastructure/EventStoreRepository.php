<?php

declare(strict_types=1);

namespace PicPay\FraudCheckCommons\EventSourcing\Infrastructure;

use Carbon\Carbon;
use PicPay\FraudCheckCommons\EventSourcing\Domain\EventStoreRepository as EventStoreRepositoryContract;
use PicPay\FraudCheckCommons\Repository\Infrastructure\Repository;
use PicPay\FraudCheckCommons\Repository\Infrastructure\RepositoryException;

final class EventStoreRepository extends Repository implements EventStoreRepositoryContract
{
    protected string $collection = 'event_store';

    /**
     * @throws RepositoryException
     */
    public function insert(array $payload): void
    {
        $this->getConnection()->insertOne($payload);
    }

    /**
     * @throws RepositoryException
     */
    public function findByAggregateRoot(string $aggregateRootId): ?array
    {
        $events = $this->getConnection()
            ->find([
                'aggregate_root_id' => $aggregateRootId,
            ]);

        return $events ?? null;
    }

    /**
     * @throws RepositoryException
     */
    public function findByAggregateRootAndVersionGreaterThan(string $aggregateRootId, int $version): ?array
    {
        $events = $this->getConnection()
            ->find([
                'aggregate_root_id' => $aggregateRootId,
                'version' => [
                    '$gte' => $version
                ]
            ]);

        return $events ?? null;
    }

    public function findTransactionsByEventTypePerDays(string $aggregateRootId, string $eventType, int $days): ?array
    {
        $events = $this->getConnection()
            ->find([
                'aggregate_root_id' => $aggregateRootId,
                'event_type' => $eventType,
                'timestamp_recorded_at' => [
                    '$gte' => Carbon::now()->subDays($days)->startOfDay()->getTimestamp(),
                    '$lte' => Carbon::now()->endOfDay()->getTimestamp()
                ]
            ]);

        return $events ?? null;
    }
}
