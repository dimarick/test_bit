<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\DBAL\Connection;

class RepositoryRegistry
{
    /**
     * @var array|RepositoryInterface[]
     */
    private $repositories = [];

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return Connection
     */
    public function getConnection(): Connection
    {
        return $this->connection;
    }

    /**
     * @param string $className
     * @return RepositoryInterface repository
     */
    public function getRepository(string $className): RepositoryInterface
    {
        return $this->repositories[$className] ?? ($this->repositories[$className] = new $className($this));
    }
}