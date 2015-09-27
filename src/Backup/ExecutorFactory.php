<?php

namespace Lucaszz\DoctrineDatabaseBackup\Backup;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Lucaszz\DoctrineDatabaseBackup\Backup\Executor\SqliteExecutor;

class ExecutorFactory
{
    /** @var Connection */
    private $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function create()
    {
        $params = $this->connection->getParams();

        if ($this->connection->getDatabasePlatform() instanceof SqlitePlatform) {
            return new SqliteExecutor($params['path']);
        }

        throw new \RuntimeException('Unsupported database platform. Currently "SqlitePlatform" is supported.');
    }
}
