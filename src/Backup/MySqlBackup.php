<?php

namespace Lucaszz\DoctrineDatabaseBackup\Backup;

use Doctrine\DBAL\Connection;
use Lucaszz\DoctrineDatabaseBackup\Command\Command;
use Lucaszz\DoctrineDatabaseBackup\Purger;
use Lucaszz\DoctrineDatabaseBackup\Storage\InMemoryStorage;

class MySqlBackup implements Backup
{
    const BACKUP_KEY = 'mysql';

    /** @var Connection */
    private $connection;
    /** @var InMemoryStorage */
    private $memoryStorage;
    /** @var Purger */
    private $purger;
    /** @var Command */
    private $command;

    /**
     * @param Connection      $connection
     * @param InMemoryStorage $memoryStorage
     * @param Purger          $purger
     * @param Command         $command
     */
    public function __construct(Connection $connection, InMemoryStorage $memoryStorage, Purger $purger, Command $command)
    {
        $this->connection    = $connection;
        $this->memoryStorage = $memoryStorage;
        $this->purger        = $purger;
        $this->command       = $command;
    }

    /** {@inheritdoc} */
    public function create()
    {
        $this->memoryStorage->put(self::BACKUP_KEY, $this->dataSql());
    }

    /** {@inheritdoc} */
    public function restore()
    {
        if (!$this->isBackupCreated()) {
            throw new \RuntimeException('Backup should be created before restore database.');
        }

        $this->purger->purge();

        if (null !== $dataSql = $this->memoryStorage->read(self::BACKUP_KEY)) {
            $this->execute($dataSql);
        }
    }

    /** {@inheritdoc} */
    public function isBackupCreated()
    {
        return $this->memoryStorage->has(self::BACKUP_KEY);
    }

    private function execute($sql)
    {
        $this->connection->beginTransaction();
        $this->connection->exec($sql);

        $this->connection->commit();
    }

    private function dataSql()
    {
        $output = $this->command->run();

        if (false === stripos($output, 'INSERT')) {
            return;
        }

        return $output;
    }
}
