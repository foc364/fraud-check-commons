<?php

namespace Test\Unit\EventSourcing\Infrastructure\Message\MongoDb;

use EventSauce\EventSourcing\Snapshotting\Snapshot;
use PHPUnit\Framework\TestCase;
use PicPay\FraudCheckCommons\EventSourcing\Domain\EventStoreSnapshotRepository;
use PicPay\FraudCheckCommons\EventSourcing\Infrastructure\Aggregate\AggregateRootId;
use PicPay\FraudCheckCommons\EventSourcing\Infrastructure\Message\MongoDb\MongoDbSnapshotRepository;

class MongoDbSnapshotRepositoryTest extends TestCase
{
    public function testShouldRetrieve(): void
    {
        $eventStoreSnapshotRepository = $this->createMock(EventStoreSnapshotRepository::class);

        $eventStoreSnapshotRepository
            ->expects($this->once())
            ->method('findByAggregateRoot')
            ->willReturn([
                'version' => 1,
                'state' => []
            ]);

        $snapshot = (new MongoDbSnapshotRepository ($eventStoreSnapshotRepository))->retrieve(new AggregateRootId('teste'));

        $this->assertInstanceOf(Snapshot::class, $snapshot);
        $this->assertEquals($snapshot->aggregateRootId()->toString(), 'teste');
    }

    public function testShouldRetrieveNull(): void
    {
        $eventStoreSnapshotRepository = $this->createMock(EventStoreSnapshotRepository::class);

        $eventStoreSnapshotRepository
            ->expects($this->once())
            ->method('findByAggregateRoot')
            ->willReturn(null);

        $snapshot = (new MongoDbSnapshotRepository ($eventStoreSnapshotRepository))->retrieve(new AggregateRootId('teste'));

        $this->assertNull($snapshot);
    }
}