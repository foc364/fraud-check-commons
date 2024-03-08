<?php

declare(strict_types=1);

namespace Test\Unit\Repository;

use Hyperf\GoTask\MongoClient\Collection;
use Hyperf\GoTask\MongoClient\Database;
use Hyperf\GoTask\MongoClient\MongoClient;
use PHPUnit\Framework\TestCase;
use PicPay\FraudCheckCommons\Repository\Infrastructure\Repository;

class RepositoryTest extends TestCase
{
    private MongoClient $client;
    private Database $database;
    private Collection $collection;

    public function setUp(): void
    {
        parent::setUp();

        $this->client = $this->createMock(MongoClient::class);
        $this->database = $this->createMock(Database::class);
        $this->collection = $this->createMock(Collection::class);

        $this->client->expects($this->once())
            ->method('database')
            ->with(env('DB_DATABASE'))
            ->willReturn($this->database);

        $this->database->expects($this->once())
            ->method('collection')
            ->with('collection_test')
            ->willReturn($this->collection);
    }

    public function testShouldCheckConfigDatabase(): void
    {
        $class = new class ($this->client) extends Repository {
            protected string $collection = 'collection_test';

            public function __construct(MongoClient $mongoClient)
            {
                parent::__construct($mongoClient);
            }
        };

        $class->getConnection();
    }
}
