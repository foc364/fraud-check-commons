<?php

declare(strict_types=1);

namespace PicPay\FraudCheckCommons\Repository\Infrastructure;

use Hyperf\GoTask\MongoClient\Collection;
use Hyperf\GoTask\MongoClient\MongoClient;

abstract class Repository
{
    protected string $collection = '';

    public function __construct(private MongoClient $mongoClient)
    {
    }

    public function getConnection(): Collection
    {
        if (empty($this->collection) === true) {
            throw new RepositoryException('You must define collection before the repository is initialized.');
        }

        $database = config('mongodb.database.database');

        if (empty($database) === true) {
            throw new RepositoryException('You must define database before the repository is initialized.');
        }

        return $this->mongoClient
            ->database($database)
            ->collection($this->collection);
    }
}
