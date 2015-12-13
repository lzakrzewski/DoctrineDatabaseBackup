<?php

namespace Lucaszz\DoctrineDatabaseBackup;

use Lucaszz\DoctrineDatabaseBackup\Backup\Backup;

class DoctrineDatabaseBackup
{
    /** @var Backup */
    private $backup;
    /** @var Purger */
    private $purger;

    /**
     * @param Backup $backup
     * @param Purger $purger
     */
    public function __construct(Backup $backup, Purger $purger)
    {
        $this->backup = $backup;
        $this->purger = $purger;
    }

    public function restoreClearDatabase()
    {
        if (!$this->backup->isBackupCreated()) {
            $this->purger->purge();
            $this->backup->create();
        }

        $this->backup->restore();
    }

    /**
     * @return Backup
     */
    public function getBackup()
    {
        return $this->backup;
    }

    /**
     * @return Purger
     */
    public function getPurger()
    {
        return $this->purger;
    }
}
