<?php

declare(strict_types=1);

namespace PicPay\FraudCheckCommons\EventSourcing\Infrastructure;

use PicPay\FraudCheckCommons\EventSourcing\Domain\EventStoreSnapshotRepository;
use PicPay\FraudCheckCommons\Repository\Infrastructure\PostgreSqlRepository;
use PicPay\FraudCheckCommons\Repository\Infrastructure\RepositoryException;

final class EventStorePostgreSqlSnapshotRepository extends PostgreSqlRepository implements EventStoreSnapshotRepository
{
    protected string $table = 'event_store_snapshot';

    /**
     * @throws RepositoryException
     */
    public function insert(array $payload): void
    {
        $where = ['aggregate_root_id' => $payload['aggregate_root_id']];

        $this->getConnection()
            ->table($this->fullTableConnection())
            ->updateOrInsert($where, $payload);
    }

    /**
     * @throws RepositoryException
     */
    public function findByAggregateRoot(string $aggregateRootId): ?array
    {
        return $this->getConnection()
            ->table($this->fullTableConnection())
            ->where('aggregate_root_id', '=', $aggregateRootId)
            ->first();
    }
}
