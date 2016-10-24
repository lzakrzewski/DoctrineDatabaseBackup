<?php

namespace Lzakrzewski\DoctrineDatabaseBackup;

use Doctrine\ORM\EntityManager;
use Lzakrzewski\DoctrineDatabaseBackup\Backup\Backup;

class DoctrineDatabaseBackup
{
    /** @var Backup */
    private $backup;
    /** @var Purger */
    private $purger;
    /** @var EntityManager */
    private $entityManager;
    /** @var bool */
    private $purgeDB;

    /**
     * @param EntityManager $entityManager
     * @param bool $purgeDB
     */
    public function __construct(EntityManager $entityManager, $purgeDB = true)
    {
        $this->entityManager = $entityManager;

        $this->backup = BackupFactory::instance($entityManager);
        $this->purger = PurgerFactory::instance($entityManager);

        $this->purgeDB = $purgeDB;
    }

    public function restore(callable $setupDatabaseCallback = null)
    {
        if (!$this->backup->isBackupCreated()) {

            if ($this->purgeDB) {
                $this->purger->purge();
            }

            if (null !== $setupDatabaseCallback) {
                $setupDatabaseCallback($this->entityManager);
            }

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
