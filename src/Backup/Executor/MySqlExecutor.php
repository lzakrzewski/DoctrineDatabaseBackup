<?php

namespace Lucaszz\DoctrineDatabaseBackup\Backup\Executor;

use Lucaszz\DoctrineDatabaseBackup\Backup\Backup;
use Lucaszz\DoctrineDatabaseBackup\Backup\BackupFile;
use Lucaszz\DoctrineDatabaseBackup\Backup\Filesystem;
use Symfony\Component\Process\Process;

class MySqlExecutor implements Backup
{
    private $mysqldumpBin = 'mysqldump';
    private $mysqlBin = 'mysql';

    /** @var string */
    private $databaseName;
    /**
     * @var BackupFile
     */
    private $backupFile;
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @param string     $databaseName
     * @param Filesystem $filesystem
     * @param BackupFile $backupFile
     */
    public function __construct($databaseName, Filesystem $filesystem, BackupFile $backupFile)
    {
        $this->databaseName = $databaseName;
        $this->filesystem = $filesystem;
        $this->backupFile = $backupFile;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        $params = $this->params();

        $command = sprintf('%s %s > %s', $this->mysqldumpBin, escapeshellarg($this->databaseName), escapeshellarg($this->backupFile->path()));
        if (isset($params['host']) && strlen($params['host'])) {
            $command .= sprintf(' --host=%s', escapeshellarg($params['host']));
        }
        if (isset($params['user']) && strlen($params['user'])) {
            $command .= sprintf(' --user=%s', escapeshellarg($params['user']));
        }
        if (isset($params['password']) && strlen($params['password'])) {
            $command .= sprintf(' --password=%s', escapeshellarg($params['password']));
        }
        $this->runCommand($command);
    }
    /**
     * {@inheritdoc}
     */
    public function restore()
    {
        $params = $this->params();

        $command = sprintf('%s %s < %s', $this->mysqlBin, escapeshellarg($this->databaseName), escapeshellarg($this->backupFile->path()));
        if (isset($params['host']) && strlen($params['host'])) {
            $command .= sprintf(' --host=%s', escapeshellarg($params['host']));
        }
        if (isset($params['user']) && strlen($params['user'])) {
            $command .= sprintf(' --user=%s', escapeshellarg($params['user']));
        }
        if (isset($params['password']) && strlen($params['password'])) {
            $command .= sprintf(' --password=%s', escapeshellarg($params['password']));
        }
        $this->runCommand($command);
    }

    /**
     * @param string $command
     *
     * @return int
     *
     * @throws \RuntimeException
     */
    private function runCommand($command)
    {
        $process = new Process($command);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        return $process->getExitCode();
    }

    private function params()
    {
        return array(
            'host' => 'localhost',
            'user' => 'root',
        );
    }
}
