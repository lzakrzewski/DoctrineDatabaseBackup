<?php

namespace Lucaszz\DoctrineDatabaseBackup\Backup;

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

    /**
     * @return string
     */
    public function path()
    {
        return $this->dir().'/'.md5(getmypid());
    }

    /**
     * @return string
     */
    public function dir()
    {
        return $this->tmpDir.'/'.self::BACKUP_DIR;
    }
}
