<?php

declare(strict_types=1);

namespace Test\Unit\EventSourcing\Infrastructure;

use Carbon\Carbon;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Database\ConnectionInterface;
use Hyperf\Database\Query\Builder;
use Hyperf\DbConnection\Db;
use Hyperf\Utils\Collection;
use Mockery;
use PHPUnit\Framework\TestCase;
use PicPay\FraudCheckCommons\EventSourcing\Infrastructure\EventStorePostgreSqlRepository;

class EventStorePostgresSqlRepositoryTest extends TestCase
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
            ->with('database_name.schema_name.event_store')
            ->andReturn($this->builderMock);

        parent::setUp();
    }

    /**
     * @dataProvider findDataProvider
     * @doesNotPerformAssertions
     */
    public function testShouldInsert(array $payload): void
    {
        $this->builderMock->shouldReceive('insert')->with($payload['payload'])->andReturnTrue();

        (new EventStorePostgreSqlRepository($this->dbMock, $this->configMock))->insert($payload['payload']);
    }

    /**
     * @dataProvider findDataProvider
     */
    public function testMustFindEventsByAggregateRoot(array $payload): void
    {
        $this->builderMock->shouldReceive('where')
            ->withArgs(['aggregate_root_id', '=', $payload['payload']['aggregate_root_id']])
            ->andReturnSelf();

        $this->builderMock->shouldReceive('get')
            ->andReturn(new Collection($payload['expected_result']));

        $events = (new EventStorePostgreSqlRepository($this->dbMock, $this->configMock))
            ->findByAggregateRoot(
                $payload['payload']['aggregate_root_id'],
            );

        $this->assertEquals($payload['expected_result'], $events);
    }

    /**
     * @dataProvider findDataProvider
     */
    public function testMustFindByAggregateRootAndVersionGreaterThan(array $payload): void
    {
        $this->builderMock->shouldReceive('where')
            ->withArgs(['aggregate_root_id', '=', $payload['payload']['aggregate_root_id']])
            ->andReturnSelf();
        $this->builderMock->shouldReceive('where')
            ->withArgs(['version', '>=', $payload['payload']['version']])
            ->andReturnSelf();

        $this->builderMock->shouldReceive('get')
            ->andReturn(new Collection($payload['expected_result']));

        $events = (new EventStorePostgreSqlRepository($this->dbMock, $this->configMock))
            ->findByAggregateRootAndVersionGreaterThan(
                $payload['payload']['aggregate_root_id'],
                1
            );

        $this->assertEquals($payload['expected_result'], $events);
    }

    /**
     * @dataProvider findDataProvider
     */
    public function testShouldFindTransactionsByEventTypePerDays(array $payload): void
    {
        $this->builderMock->shouldReceive('where')
            ->withArgs(['aggregate_root_id', '=', $payload['payload']['aggregate_root_id']])
            ->andReturnSelf();
        $this->builderMock->shouldReceive('where')
            ->withArgs(['event_type', '=', $payload['payload']['event_type']])
            ->andReturnSelf();
        $this->builderMock->shouldReceive('where')
            ->withArgs(['timestamp_recorded_at', '>=', Carbon::now()->subDays(1)->startOfDay()->getTimestamp()])
            ->andReturnSelf();
        $this->builderMock->shouldReceive('where')
            ->withArgs(['timestamp_recorded_at', '<=', Carbon::now()->endOfDay()->getTimestamp()])
            ->andReturnSelf();
        $this->builderMock->shouldReceive('get')
            ->andReturn(new Collection($payload['expected_result']));

        $events = (new EventStorePostgreSqlRepository($this->dbMock, $this->configMock))
            ->findTransactionsByEventTypePerDays(
                $payload['payload']['aggregate_root_id'],
                $payload['payload']['event_type'],
                1
            );

        $this->assertEquals($payload['expected_result'], $events);
    }

    public function findDataProvider(): iterable
    {
        yield 'with_result' => [[
            'payload' => [
                'event_id' => 'bffd58cd-f6b0-4e32-9b66-768387e00c29',
                'event_type' => 'Test',
                'aggregate_root_id' => 'fe7c6593-b427-4899-9752-c592ea9261e0',
                'recorded_at' => Carbon::now()->toDateTimeString(),
                'timestamp_recorded_at' => Carbon::now()->timestamp,
                'version' => 1,
                'payload' => [],
            ],
            'expected_result' => [
                [
                    'event_id' => 'bffd58cd-f6b0-4e32-9b66-768387e00c29',
                    'event_type' => 'Test',
                    'aggregate_root_id' => 'fe7c6593-b427-4899-9752-c592ea9261e0',
                    'recorded_at' => Carbon::now()->toDateTimeString(),
                    'timestamp_recorded_at' => Carbon::now()->timestamp,
                    'version' => 1,
                    'payload' => [],
                ]
            ]
        ]];
        yield 'with_null_result' => [[
            'payload' => [
                'event_type' => 'Test',
                'aggregate_root_id' => 'fe7c6593-b427-4899-9752-c592ea9261e0',
                'recorded_at' => Carbon::now()->toDateTimeString(),
                'timestamp_recorded_at' => Carbon::now()->timestamp,
                'version' => 1,
            ],
            'expected_result' => [
                null
            ]
        ]];
    }
}
