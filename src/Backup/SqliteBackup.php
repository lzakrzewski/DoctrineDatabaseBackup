<?php

namespace Lucaszz\DoctrineDatabaseBackup\Backup;

use Lucaszz\DoctrineDatabaseBackup\Storage\InMemoryStorage;
use Lucaszz\DoctrineDatabaseBackup\Storage\LocalStorage;

class SqliteBackup implements Backup
{
    const BACKUP_KEY = 'sqlite';

    /** @var string */
    private $sourcePath;
    /** @var LocalStorage */
    private $localStorage;
    /** @var InMemoryStorage */
    private $memoryStorage;

    /**
     * @param $sourcePath
     * @param InMemoryStorage $memoryStorage
     * @param LocalStorage    $localStorage
     */
    public function __construct($sourcePath, InMemoryStorage $memoryStorage, LocalStorage $localStorage)
    {
        $this->sourcePath    = $sourcePath;
        $this->memoryStorage = $memoryStorage;
        $this->localStorage  = $localStorage;
    }

    /** {@inheritdoc} */
    public function create()
    {
        $sourcePath = $this->sourcePath;

        if (!$this->localStorage->has($sourcePath)) {
            throw new \RuntimeException(sprintf("Source database '%s' should exists.", $sourcePath));
        }

        $this->memoryStorage->put(self::BACKUP_KEY, $this->localStorage->read($sourcePath));
    }

    /** {@inheritdoc} */
    public function restore()
    {
        if (!$this->isBackupCreated()) {
            throw new \RuntimeException('Backup file should be created before restore database.');
        }

        $this->localStorage->put($this->sourcePath, $this->memoryStorage->read(self::BACKUP_KEY));
    }

    /** {@inheritdoc} */
    public function isBackupCreated()
    {
        return $this->memoryStorage->has(self::BACKUP_KEY);
    }
}
