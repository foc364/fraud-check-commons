<?php

declare(strict_types=1);

namespace PicPay\FraudCheckCommons\EventSourcing\Domain;

interface EventStoreSnapshotRepository
{
    public function insert(array $payload): void;

    public function findByAggregateRoot(string $aggregateRootId): ?array;
}
