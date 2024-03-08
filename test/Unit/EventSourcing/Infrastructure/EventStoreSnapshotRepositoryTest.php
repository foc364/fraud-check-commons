<?php

namespace Test\Unit\EventSourcing\Infrastructure;

use PicPay\FraudCheckCommons\EventSourcing\Infrastructure\EventStoreSnapshotRepository;
use Hyperf\GoTask\MongoClient\Collection;
use Hyperf\GoTask\MongoClient\Database;
use Hyperf\GoTask\MongoClient\MongoClient;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class EventStoreSnapshotRepositoryTest extends TestCase
{
    /**
     * @var MongoClient|MockObject
     */
    private MongoClient $mongoClient;

    /**
     * @var Collection|MockObject
     */
    private Collection $collection;

    /**
     * @var Database|MockObject
     */
    private Database $database;

    public function setUp(): void
    {
        parent::setUp();

        $this->mongoClient = $this->createMock(MongoClient::class);
        $this->database = $this->createMock(Database::class);
        $this->collection = $this->createMock(Collection::class);
    }

    public function testMustInsertNewSnapshot(): void
    {
        $this->collection
            ->expects($this->once())
            ->method('updateOne');

        $this->database
            ->expects($this->once())
            ->method('collection')
            ->willReturn($this->collection);

        $this->mongoClient
            ->expects($this->once())
            ->method('database')
            ->willReturn($this->database);

        (new EventStoreSnapshotRepository($this->mongoClient))->insert([
            'aggregate_root_id' => 'fe7c6593-b427-4899-9752-c592ea9261e0',
            'version' => 1,
            'state' => []
        ]);
    }

    /**
     * @dataProvider findDataProvider
     */
    public function testMustFindSnapshotByAggregateRoot(?array $payload, ?array $assert): void
    {
        $this->collection
            ->expects($this->once())
            ->method('find')
            ->willReturn($payload);

        $this->database
            ->expects($this->once())
            ->method('collection')
            ->willReturn($this->collection);

        $this->mongoClient
            ->expects($this->once())
            ->method('database')
            ->willReturn($this->database);

        $events = (new EventStoreSnapshotRepository($this->mongoClient))
            ->findByAggregateRoot('fe7c6593-b427-4899-9752-c592ea9261e0');

        $this->assertEquals(
            $assert,
            $events
        );
    }

    public function findDataProvider(): array
    {
        return [
            [
                [
                    [
                        'event_id' => 'bffd58cd-f6b0-4e32-9b66-768387e00c29',
                        'event_type' => 'Test',
                        'aggregate_root_id' => 'fe7c6593-b427-4899-9752-c592ea9261e0',
                        'recorded_at' => '2021-10-28 19:06:08.378353+0000',
                        'version' => 1,
                        'payload' => [],
                    ]
                ],
                [
                    'event_id' => 'bffd58cd-f6b0-4e32-9b66-768387e00c29',
                    'event_type' => 'Test',
                    'aggregate_root_id' => 'fe7c6593-b427-4899-9752-c592ea9261e0',
                    'recorded_at' => '2021-10-28 19:06:08.378353+0000',
                    'version' => 1,
                    'payload' => [],
                ]
            ],
            [
                null,
                null
            ]
        ];
    }
}
