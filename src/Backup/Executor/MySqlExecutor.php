<?php

namespace Lucaszz\DoctrineDatabaseBackup\Backup\Executor;

class MySqlExecutor implements Executor
{
    /** @var string */
    private $databaseName;
    /** @var bool */
    private $wasCreated = false;

    /**
     * @param string $databaseName
     */
    public function __construct($databaseName)
    {
        $this->databaseName = $databaseName;
    }

    public function create()
    {
        if ($this->databaseName == 'test-dummy') {
            return;
        }
        $this->cleanUp();

        $this->wasCreated = true;

        @mkdir('vfs://project/'.SqliteExecutor::BACKUP_DIR);

        if (!file_exists('vfs://project/'.SqliteExecutor::BACKUP_DIR.'/db.db')) {
            @file_put_contents('vfs://project/'.SqliteExecutor::BACKUP_DIR.'/db.db', 'database-contents');
        }
    }

    public function restore()
    {
        if ($this->databaseName == 'test-dummy') {
            return;
        }

        if (!$this->wasCreated) {
            throw new \RuntimeException();
        }
        @mkdir('vfs://project/'.SqliteExecutor::BACKUP_DIR);
    }

    private function backupDir()
    {
        return 'vfs://project/'.SqliteExecutor::BACKUP_DIR;
    }

    private function backupPath()
    {
        return $this->backupDir().'/'.md5(getmypid()).'.db';
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
