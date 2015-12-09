<?php

namespace Lucaszz\DoctrineDatabaseBackup;

use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\ORM\EntityManager;
use Lucaszz\DoctrineDatabaseBackup\Backup\MySqlBackup;
use Lucaszz\DoctrineDatabaseBackup\Backup\SqliteBackup;
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
        $purger = new Purger($entityManager);

        return new DoctrineDatabaseBackup($entityManager, $backup, $purger);
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

        return new SqliteBackup($params['path'], new LocalStorage());
    }

    private static function mySqlBackup(EntityManager $entityManager)
    {
        $purger = new Purger($entityManager);

        return new MySqlBackup($entityManager->getConnection(), $purger, new LegacyCommand());
    }
}
