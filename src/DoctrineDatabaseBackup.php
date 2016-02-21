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

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;

        $this->backup = BackupFactory::instance($entityManager);
        $this->purger = PurgerFactory::instance($entityManager);
    }

    public function restore(callable $setupDatabaseCallback = null)
    {
        if (!$this->backup->isBackupCreated()) {
            $this->purger->purge();

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
