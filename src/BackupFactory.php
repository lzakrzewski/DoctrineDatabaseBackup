<?php

namespace Lucaszz\DoctrineDatabaseBackup;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Lucaszz\DoctrineDatabaseBackup\Backup\Backup;
use Lucaszz\DoctrineDatabaseBackup\Backup\MySqlBackup;
use Lucaszz\DoctrineDatabaseBackup\Backup\SqliteBackup;
use Lucaszz\DoctrineDatabaseBackup\Storage\LocalStorage;

class BackupFactory
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
        $this->purger     = $purger;
    }

    /**
     * @return Backup
     */
    public function create()
    {
        if ($this->connection->getDatabasePlatform() instanceof SqlitePlatform) {
            return $this->sqliteBackup();
        }

        if ($this->connection->getDatabasePlatform() instanceof MySqlPlatform) {
            return $this->mySqlBackup();
        }

        throw new \RuntimeException('Unsupported database platform. Currently "SqlitePlatform" is supported.');
    }

    private function sqliteBackup()
    {
        $params = $this->connection->getParams();

        if (false === isset($params['path']) || $params['path'] == ':memory:') {
            throw new \RuntimeException('Backup for Sqlite "in_memory" is not supported.');
        }

        return new SqliteBackup($params['path'], new LocalStorage());
    }

    private function mySqlBackup()
    {
        return new MySqlBackup($this->connection, $this->purger, new Command());
    }
}
