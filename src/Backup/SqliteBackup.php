<?php

namespace Lucaszz\DoctrineDatabaseBackup\Backup;

use Lucaszz\DoctrineDatabaseBackup\Storage\LocalStorage;

class SqliteBackup implements Backup
{
    /** @var string */
    private $sourcePath;
    /** @var LocalStorage */
    private $storage;
    /** @var string */
    private static $contents;

    /**
     * @param string       $sourcePath
     * @param LocalStorage $storage
     */
    public function __construct($sourcePath, LocalStorage $storage)
    {
        $this->sourcePath = $sourcePath;
        $this->storage    = $storage;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        $sourcePath = $this->sourcePath;

        if (!$this->storage->has($sourcePath)) {
            throw new \RuntimeException(sprintf("Source database '%s' should exists.", $sourcePath));
        }

        static::$contents = $this->storage->read($sourcePath);
    }

    /**
     * {@inheritdoc}
     */
    public function restore()
    {
        if (!$this->isBackupCreated()) {
            throw new \RuntimeException('Backup file should be created before restore database.');
        }

        $this->storage->put($this->sourcePath, static::$contents);
    }

    /**
     * {@inheritdoc}
     */
    public function isBackupCreated()
    {
        return null !== static::$contents;
    }

    /** {@inheritdoc} */
    public static function clearMemory()
    {
        static::$contents = null;
    }
}
