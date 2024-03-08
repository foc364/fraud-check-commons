<?php

declare(strict_types=1);

namespace PicPay\FraudCheckCommons\Repository\Infrastructure;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Database\ConnectionInterface;
use Hyperf\DbConnection\Db;

use function sprintf;

abstract class PostgreSqlRepository
{
    private const DEFAULT_SCHEMA = 'public';
    protected string $connectionPool = 'default';
    protected string $database = '';
    protected string $schema;
    protected string $table = '';

    public function __construct(private Db $connection, protected ConfigInterface $config)
    {
        $this->database = $this->config->get(sprintf('databases.%s.database', $this->connectionPool));
        $this->schema = $this->config->get(sprintf('databases.%s.schema', $this->connectionPool)) ?? self::DEFAULT_SCHEMA;
    }

    public function getConnection(): ConnectionInterface
    {
        if (empty($this->database) === true) {
            throw new RepositoryException('You must define database before the repository is initialized.');
        }

        return $this->connection
            ->connection($this->connectionPool);
    }

    protected function fullTableConnection(): string
    {
        return sprintf('%s.%s.%s', $this->database, $this->schema, $this->table);
    }
}
