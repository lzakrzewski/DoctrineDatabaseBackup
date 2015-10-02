<?php

namespace Lucaszz\DoctrineDatabaseBackup\Backup\Executor;

class BackupFile
{
    const BACKUP_DIR = 'db-backup';

    /** @var string */
    private $tmpDir;

    /**
     * @param string $tmpDir
     */
    public function __construct($tmpDir)
    {
        $this->tmpDir = $tmpDir;
    }

    public function prepareDir()
    {
        @mkdir($this->dir());
        $this->cleanUp();
    }

    /**
     * @param string $source
     * @param string $destination
     */
    public function copy($source, $destination)
    {
        if (false === file_exists($source)) {
            throw new \RuntimeException(sprintf("Source file '%s' does not exist.", $source));
        }

        if (!copy($source, $destination)) {
            throw new \RuntimeException(sprintf("Unable to copy '%s' to '%s'", $source, $destination));
        }
    }

    /**
     * @param $string
     *
     * @return bool
     */
    public function exists($string)
    {
        return file_exists($string);
    }

    /**
     * @return string
     */
    public function path()
    {
        return $this->dir().'/'.md5(getmypid());
    }

    private function cleanUp()
    {
        $backupDir = $this->dir();

        if (false === file_exists($this->dir())) {
            return false;
        }

        foreach (scandir($this->dir()) as $file) {
            $filePath = $backupDir.'/'.$file;

            if (false === is_file($filePath) || $filePath == $this->path()) {
                continue;
            }

            unlink($filePath);
        }
    }

    private function dir()
    {
        return $this->tmpDir.'/'.self::BACKUP_DIR;
    }
}
