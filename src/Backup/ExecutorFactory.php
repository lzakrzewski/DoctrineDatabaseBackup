<?php

namespace Lucaszz\DoctrineDatabaseBackup\Backup;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Lucaszz\DoctrineDatabaseBackup\Backup\Executor\MySqlExecutor;
use Lucaszz\DoctrineDatabaseBackup\Backup\Executor\SqliteExecutor;

class ExecutorFactory
{
    /** @var Connection */
    private $connection;
    /** @var Purger */
    private $purger;

    /**
     * @param Connection $connection
     * @param Purger     $purger
     */
    public function __construct(Connection $connection, Purger $purger)
    {
        $this->connection = $connection;
        $this->purger = $purger;
    }

    public function create()
    {
        if ($this->connection->getDatabasePlatform() instanceof SqlitePlatform) {
            return $this->sqliteExecutor();
        }

        if ($this->connection->getDatabasePlatform() instanceof MySqlPlatform) {
            return $this->mySqlExecutor();
        }

        throw new \RuntimeException('Unsupported database platform. Currently "SqlitePlatform" is supported.');
    }

    private function sqliteExecutor()
    {
        $params = $this->connection->getParams();

        if (false === isset($params['path']) || $params['path'] == ':memory:') {
            throw new \RuntimeException('Backup for Sqlite "in_memory" is not supported.');
        }

        return new SqliteExecutor($params['path'], new Filesystem());
    }

    private function mySqlExecutor()
    {
        return new MySqlExecutor($this->connection, $this->purger, new Command());
    }
}
