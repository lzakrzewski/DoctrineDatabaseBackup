<?php

namespace Lucaszz\DoctrineDatabaseBackup;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Lucaszz\DoctrineDatabaseBackup\Backup\Backup;
use Lucaszz\DoctrineDatabaseBackup\Backup\MySqlBackup;
use Lucaszz\DoctrineDatabaseBackup\Backup\SqliteBackup;
use Lucaszz\DoctrineDatabaseBackup\Command\MysqldumpCommand;
use Lucaszz\DoctrineDatabaseBackup\Storage\InMemoryStorage;
use Lucaszz\DoctrineDatabaseBackup\Storage\LocalStorage;

/**
 * @deprecated
 */
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

        return new SqliteBackup($params['path'], InMemoryStorage::instance(), new LocalStorage());
    }

    private function mySqlBackup()
    {
        $params = $this->connection->getParams();

        if (false === isset($params['dbname'])) {
            throw new \RuntimeException('Database name should be provided');
        }

        $host     = (isset($params['host'])) ? $params['host'] : null;
        $user     = (isset($params['user'])) ? $params['user'] : null;
        $password = (isset($params['password'])) ? $params['password'] : null;

        $command = new MysqldumpCommand($params['dbname'], $host, $user, $password);

        return new MySqlBackup($this->connection, InMemoryStorage::instance(), $this->purger, $command);
    }
}
