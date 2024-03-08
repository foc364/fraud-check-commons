<?php

namespace Test\Unit\EventSourcing\Infrastructure;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Database\ConnectionInterface;
use Hyperf\Database\Query\Builder;
use Hyperf\DbConnection\Db;
use Mockery;
use PHPUnit\Framework\TestCase;
use PicPay\FraudCheckCommons\EventSourcing\Infrastructure\EventStorePostgreSqlSnapshotRepository;

class EventStorePostgreSqlSnapshotRepositoryTest extends TestCase
{
    protected function setUp(): void
    {
        $this->dbMock = Mockery::mock(Db::class);
        $this->connectionMock = Mockery::mock(ConnectionInterface::class);
        $this->configMock = Mockery::mock(ConfigInterface::class);
        $this->builderMock = Mockery::mock(Builder::class);

        $this->dbMock
            ->shouldReceive('connection')
            ->andReturn($this->connectionMock);

        $this->configMock->shouldReceive('get')
            ->with('databases.default.database')
            ->andReturn('database_name');

        $this->configMock->shouldReceive('get')
            ->with('databases.default.schema')
            ->andReturn('schema_name');

        $this->connectionMock
            ->shouldReceive('table')
            ->with('database_name.schema_name.event_store_snapshot')
            ->andReturn($this->builderMock);

        parent::setUp();
    }

    /**
    * @doesNotPerformAssertions
    */
    public function testShouldInsert(): void
    {
        $payload = [
            'aggregate_root_id' => 'abc',
            'state' => '{"teste":"teste"}',
            'varsion' => 1,
        ];

        $this->builderMock->shouldReceive('updateOrInsert')
            ->withArgs([
                ['aggregate_root_id' => $payload['aggregate_root_id']],
                $payload
            ])
            ->andReturnTrue();

        (new EventStorePostgreSqlSnapshotRepository($this->dbMock, $this->configMock))->insert($payload);
    }

    /**
     * @dataProvider findDataProvider
     */
    public function testShouldFindByAggregateRoot(array $payload)
    {
        $this->builderMock->shouldReceive('where')
            ->withArgs(['aggregate_root_id', '=', $payload['payload']['aggregate_root_id']])
            ->andReturnSelf();

        $this->builderMock->shouldReceive('first')
            ->andReturn($payload['expected_result']);

        $events = (new EventStorePostgreSqlSnapshotRepository($this->dbMock, $this->configMock))
            ->findByAggregateRoot(
                $payload['payload']['aggregate_root_id'],
            );

        $this->assertEquals($payload['expected_result'], $events);
    }

    public function findDataProvider(): iterable
    {
        yield 'with_result' => [[
            'payload' => [
                'aggregate_root_id' => 'fe7c6593-b427-4899-9752-c592ea9261e0',
                'version' => 1,
                'state' => [],
            ],
            'expected_result' => [
                'aggregate_root_id' => 'fe7c6593-b427-4899-9752-c592ea9261e0',
                'version' => 1,
                'state' => [],
            ]
        ]];
        yield 'with_null_result' => [[
            'payload' => [
                'aggregate_root_id' => 'fe7c6593-b427-4899-9752-c592ea9261e0',
                'version' => 1,
                'state' => [],
            ],
            'expected_result' => [
                null
            ]
        ]];
    }
}
