<?php

declare(strict_types=1);

namespace PicPay\FraudCheckCommons\EventSourcing\Domain;

interface EventStoreRepository
{
    public function insert(array $payload): void;

    public function findByAggregateRoot(string $aggregateRootId): ?array;

    public function findByAggregateRootAndVersionGreaterThan(string $aggregateRootId, int $version): ?array;

    public function findTransactionsByEventTypePerDays(string $aggregateRootId, string $eventType, int $days): ?array;
}
