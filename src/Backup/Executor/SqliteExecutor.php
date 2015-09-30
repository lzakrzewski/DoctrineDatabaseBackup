<?php

namespace Lucaszz\DoctrineDatabaseBackup\Backup\Executor;

class SqliteExecutor implements Executor
{
    const BACKUP_DIR = 'db-backup';

    /** @var string */
    private $sourcePath;

    /**
     * @param string $sourcePath
     */
    public function __construct($sourcePath)
    {
        $this->sourcePath = $sourcePath;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        $this->cleanUp();

        if (false === file_exists($this->sourcePath)) {
            throw new \RuntimeException(sprintf("Source database '%s' should exists.", $this->sourcePath));
        }

        if ($this->isBackupCreated()) {
            return;
        }

        $this->createBackup();
    }

    /**
     * {@inheritdoc}
     */
    public function restore()
    {
        if (false === $this->isBackupCreated()) {
            throw new \RuntimeException('Backup file should be created before restore database.');
        }

        $backupPath = $this->backupPath();

        $this->copy($backupPath, $this->sourcePath);
    }

    private function isBackupCreated()
    {
        if (false === file_exists($this->backupDir())) {
            return false;
        }

        return file_exists($this->backupPath());
    }

    private function createBackup()
    {
        @mkdir($this->backupDir());

        $backupPath = $this->backupPath();

        $this->copy($this->sourcePath, $backupPath);
    }

    private function backupDir()
    {
        $pathinfo = pathinfo($this->sourcePath);

        return $pathinfo['dirname'].'/'.self::BACKUP_DIR;
    }

    private function backupPath()
    {
        return $this->backupDir().'/'.md5(getmypid()).'.db';
    }

    private function copy($source, $destination)
    {
        if (!copy($source, $destination)) {
            throw new \RuntimeException(sprintf("Unable to copy '%s' to '%s'", $source, $destination));
        }
    }

    private function cleanUp()
    {
        $backupDir = $this->backupDir();

        if (false === file_exists($this->backupDir())) {
            return false;
        }

        foreach (scandir($this->backupDir()) as $file) {
            $filePath = $backupDir.'/'.$file;

            if (false === is_file($filePath) || $filePath == $this->backupPath()) {
                continue;
            }

            unlink($filePath);
        }
    }
}
