<?php

namespace Lucaszz\DoctrineDatabaseBackup;

use Doctrine\ORM\EntityManager;
use Lucaszz\DoctrineDatabaseBackup\Backup\Backup;

class DoctrineDatabaseBackup
{
    /** @var Backup */
    private $backup;
    /** @var Purger */
    private $purger;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->backup = BackupFactory::instance($entityManager);
        $this->purger = PurgerFactory::instance($entityManager);
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
