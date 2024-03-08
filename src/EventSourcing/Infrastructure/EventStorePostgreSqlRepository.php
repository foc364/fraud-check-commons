<?php

declare(strict_types=1);

namespace PicPay\FraudCheckCommons\EventSourcing\Infrastructure;

use Carbon\Carbon;
use PicPay\FraudCheckCommons\EventSourcing\Domain\EventStoreRepository as EventStoreRepositoryContract;
use PicPay\FraudCheckCommons\Repository\Infrastructure\PostgreSqlRepository;
use PicPay\FraudCheckCommons\Repository\Infrastructure\RepositoryException;

final class EventStorePostgreSqlRepository extends PostgreSqlRepository implements EventStoreRepositoryContract
{
    protected string $table = 'event_store';

    /**
     * @throws RepositoryException
     */
    public function insert(array $payload): void
    {
        $this->getConnection()
            ->table($this->fullTableConnection())
            ->insert($payload);
    }

    /**
     * @throws RepositoryException
     */
    public function findByAggregateRoot(string $aggregateRootId): ?array
    {
        $events = $this->getConnection()
            ->table($this->fullTableConnection())
            ->where(
                'aggregate_root_id', '=', $aggregateRootId
            )->get();

        $data = $events->toArray();

        return $data ?? null;
    }

    /**
     * @throws RepositoryException
     */
    public function findByAggregateRootAndVersionGreaterThan(string $aggregateRootId, int $version): ?array
    {
        $events = $this->getConnection()
            ->table($this->fullTableConnection())
            ->where('aggregate_root_id', '=', $aggregateRootId)
            ->where('version', '>=', $version)
            ->get();

        $data = $events->toArray();

        return $data ?? null;
    }

    public function findTransactionsByEventTypePerDays(string $aggregateRootId, string $eventType, int $days): ?array
    {
        $events = $this->getConnection()
            ->table($this->fullTableConnection())
            ->where('aggregate_root_id', '=', $aggregateRootId)
            ->where('event_type', '=', $eventType)
            ->where('timestamp_recorded_at', '>=', Carbon::now()->subDays($days)->startOfDay()->getTimestamp())
            ->where('timestamp_recorded_at', '<=', Carbon::now()->endOfDay()->getTimestamp())
            ->get();

        $data = $events->toArray();

        return $data ?? null;
    }
}
