<?php

declare(strict_types=1);

namespace Test\Unit\EventSourcing\Infrastructure\Message;

use PicPay\FraudCheckCommons\EventSourcing\Domain\EventStoreSnapshotRepository;
use PicPay\FraudCheckCommons\EventSourcing\Infrastructure\Message\SnapshotRepository;
use EventSauce\EventSourcing\AggregateRootId;
use EventSauce\EventSourcing\Snapshotting\Snapshot;
use PHPUnit\Framework\TestCase;

class SnapshotRepositoryTest extends TestCase
{
    public function testPersist(): void
    {
        $eventStoreSnapshotRepository = $this->createMock(EventStoreSnapshotRepository::class);
        $aggregateRootId = $this->createMock(AggregateRootId::class);

        $eventStoreSnapshotRepository
            ->expects($this->once())
            ->method('insert');

        $aggregateRootId
            ->expects($this->once())
            ->method('toString')
            ->willReturn('doc');

        $snapshot = new Snapshot($aggregateRootId, 1, []);

        (new class ($eventStoreSnapshotRepository) extends SnapshotRepository{

            public function retrieve(AggregateRootId $id): ?Snapshot
            {
                return null;
            }
        })->persist($snapshot);
    }
}
