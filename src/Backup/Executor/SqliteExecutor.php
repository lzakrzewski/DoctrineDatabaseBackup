<?php

namespace Lucaszz\DoctrineDatabaseBackup\Backup\Executor;

/**
 * @todo probably method "create" could be private
 */
class SqliteExecutor implements Executor
{
    /** @var string */
    private $sourcePath;

    /**
     * @param string $sourcePath
     * @param string $backupDir
     */
    public function __construct($sourcePath, $backupDir = 'db-backup')
    {
        $this->sourcePath = $sourcePath;
        $this->backupDir = $backupDir;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        if (false === file_exists($this->sourcePath)) {
            throw new \RuntimeException(sprintf('Database %s should exists.', $this->sourcePath));
        }

        if ($this->isBackupCreated()) {
            return;
        }

        $this->copy($this->sourcePath, 'vfs://project/backup_1.db');
    }

    /**
     * {@inheritdoc}
     */
    public function restore()
    {
    }

    private function isBackupCreated()
    {
        return false;
    }

    private function copy($sourcePath, $backupPath)
    {
        if (!copy($sourcePath, $backupPath)) {
            throw new \RuntimeException("Unable to copy '$sourcePath' to '$backupPath'");
        }
    }
}
