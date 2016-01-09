<?php

namespace Lucaszz\DoctrineDatabaseBackup;

use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\ORM\EntityManager;
use Lucaszz\DoctrineDatabaseBackup\Backup\MySqlBackup;
use Lucaszz\DoctrineDatabaseBackup\Backup\SqliteBackup;
use Lucaszz\DoctrineDatabaseBackup\Command\MysqldumpCommand;
use Lucaszz\DoctrineDatabaseBackup\Storage\InMemoryStorage;
use Lucaszz\DoctrineDatabaseBackup\Storage\LocalStorage;

final class Backups
{
    /**
     * @param EntityManager $entityManager
     *
     * @return DoctrineDatabaseBackup
     */
    public static function newInstance(EntityManager $entityManager)
    {
        $backup = self::backup($entityManager);
        $purger = self::purger($entityManager);

        return new DoctrineDatabaseBackup($backup, $purger);
    }

    private function __construct()
    {
    }

    private static function backup(EntityManager $entityManager)
    {
        $connection = $entityManager->getConnection();

        if ($connection->getDatabasePlatform() instanceof SqlitePlatform) {
            return self::sqliteBackup($entityManager);
        }

        if ($connection->getDatabasePlatform() instanceof MySqlPlatform) {
            return self::mySqlBackup($entityManager);
        }

        throw new \RuntimeException('Unsupported database platform. Currently "SqlitePlatform" is supported.');
    }

    private static function sqliteBackup(EntityManager $entityManager)
    {
        $params = $entityManager->getConnection()->getParams();

        if (false === isset($params['path']) || $params['path'] == ':memory:') {
            throw new \RuntimeException('Backup for Sqlite "in_memory" is not supported.');
        }

        return new SqliteBackup($params['path'], InMemoryStorage::instance(), new LocalStorage());
    }

    private static function mySqlBackup(EntityManager $entityManager)
    {
        $params = $entityManager->getConnection()->getParams();

        if (false === isset($params['dbname'])) {
            throw new \RuntimeException('Database name should be provided');
        }

        $host     = (isset($params['host'])) ? $params['host'] : null;
        $user     = (isset($params['user'])) ? $params['user'] : null;
        $password = (isset($params['password'])) ? $params['password'] : null;

        $purger  = self::purger($entityManager);
        $command = new MysqldumpCommand($params['dbname'], $host, $user, $password);

        return new MySqlBackup($entityManager->getConnection(), InMemoryStorage::instance(), $purger, $command);
    }

    private static function purger(EntityManager $entityManager)
    {
        return new Purger($entityManager, InMemoryStorage::instance());
    }
}
