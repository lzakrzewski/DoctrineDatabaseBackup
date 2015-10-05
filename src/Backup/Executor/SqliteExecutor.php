<?php

namespace Lucaszz\DoctrineDatabaseBackup\Backup\Executor;

use Lucaszz\DoctrineDatabaseBackup\Backup\Backup;
use Lucaszz\DoctrineDatabaseBackup\Backup\BackupFile;
use Lucaszz\DoctrineDatabaseBackup\Backup\Filesystem;

class SqliteExecutor implements Backup
{
    /** @var string */
    private $sourcePath;
    /** @var Filesystem */
    private $filesystem;
    /** @var BackupFile */
    private $backupFile;

    /**
     * @param string     $sourcePath
     * @param Filesystem $filesystem
     * @param BackupFile $backupFile
     */
    public function __construct($sourcePath, Filesystem $filesystem, BackupFile $backupFile)
    {
        $this->sourcePath = $sourcePath;
        $this->filesystem = $filesystem;
        $this->backupFile = $backupFile;
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

        $this->filesystem->prepareDir($this->backupFile->dir());
        $this->filesystem->copy($sourcePath, $this->backupFile->path());
    }

    /**
     * {@inheritdoc}
     */
    public function restore()
    {
        if (!$this->isCreated()) {
            throw new \RuntimeException('Backup file should be created before restore database.');
        }

        $this->filesystem->copy($this->backupFile->path(), $this->sourcePath);
    }

    /**
     * {@inheritdoc}
     */
    public function isCreated()
    {
        if (!$this->filesystem->exists($this->backupFile->dir())) {
            return false;
        }

        return $this->filesystem->exists($this->backupFile->path());
    }
}
