<?php

declare(strict_types=1);

namespace Test\Unit\Repository;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Database\ConnectionInterface;
use Hyperf\DbConnection\Db;
use PHPUnit\Framework\TestCase;
use PicPay\FraudCheckCommons\Repository\Infrastructure\PostgreSqlRepository;
use Mockery;

class PostgreSqlRepositoryTest extends TestCase
{
    public function testShouldGetConnection(): void
    {
        $dbMock = Mockery::mock(Db::class);
        $connectionMock = Mockery::mock(ConnectionInterface::class);
        $configMock = Mockery::mock(ConfigInterface::class);

        $dbMock
            ->shouldReceive('connection')
            ->andReturn($connectionMock);

        $configMock->shouldReceive('get')
            ->with('databases.default.database')
            ->andReturn('teste');

        $configMock->shouldReceive('get')
            ->with('databases.default.schema')
            ->andReturn('schema_name');

        $class = new class ($dbMock, $configMock) extends PostgreSqlRepository {
            public function __construct(private Db $connection, protected ConfigInterface $config)
            {
                parent::__construct($connection, $config);
            }
        };

        $this->assertInstanceOf(ConnectionInterface::class, $class->getConnection());
    }
}
