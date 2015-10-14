<?php

namespace Lucaszz\DoctrineDatabaseBackup\Backup\Executor;

use Lucaszz\DoctrineDatabaseBackup\Backup\Filesystem;

class SqliteExecutor implements Executor
{
    /** @var string */
    private $sourcePath;
    /** @var Filesystem */
    private $filesystem;
    /** @var string */
    private static $contents;

    /**
     * @param string     $sourcePath
     * @param Filesystem $filesystem
     */
    public function __construct($sourcePath, Filesystem $filesystem)
    {
        $this->sourcePath = $sourcePath;
        $this->filesystem = $filesystem;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        $sourcePath = $this->sourcePath;

        if (!$this->filesystem->exists($sourcePath)) {
            throw new \RuntimeException(sprintf("Source database '%s' should exists.", $sourcePath));
        }

        static::$contents = $this->filesystem->read($sourcePath);
    }

    /**
     * {@inheritdoc}
     */
    public function restore()
    {
        if (!$this->isBackupCreated()) {
            throw new \RuntimeException('Backup file should be created before restore database.');
        }

        $this->filesystem->write($this->sourcePath, static::$contents);
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
