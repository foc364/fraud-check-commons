<?php

namespace Test\Unit\EventSourcing\Infrastructure\Message\PostgreSql;

use EventSauce\EventSourcing\Snapshotting\Snapshot;
use PHPUnit\Framework\TestCase;
use PicPay\FraudCheckCommons\EventSourcing\Domain\EventStoreSnapshotRepository;
use PicPay\FraudCheckCommons\EventSourcing\Infrastructure\Aggregate\AggregateRootId;
use PicPay\FraudCheckCommons\EventSourcing\Infrastructure\Message\MongoDb\MongoDbSnapshotRepository;
use PicPay\FraudCheckCommons\EventSourcing\Infrastructure\Message\PostgreSql\PostgreSqlSnapshotRepository;

class PostgreSqlSnapshotRepositoryTest extends TestCase
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

        $snapshot = (new PostgreSqlSnapshotRepository($eventStoreSnapshotRepository))->retrieve(new AggregateRootId('teste'));

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

        $snapshot = (new PostgreSqlSnapshotRepository($eventStoreSnapshotRepository))->retrieve(new AggregateRootId('teste'));

        $this->assertNull($snapshot);
    }
}
