<?php

namespace Lucaszz\DoctrineDatabaseBackup;

use Doctrine\ORM\EntityManager;
use Lucaszz\DoctrineDatabaseBackup\Backup\Backup;
use Lucaszz\DoctrineDatabaseBackup\Storage\InMemoryStorage;

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
        $this->purger = new Purger($entityManager, InMemoryStorage::instance());
        $this->backup = (new BackupFactory($entityManager->getConnection(), $this->purger))->create();
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
     * @param Backup $backup
     */
    public function setBackup(Backup $backup)
    {
        $this->backup = $backup;
    }

    /**
     * @param Purger $purger
     */
    public function setPurger(Purger $purger)
    {
        $this->purger = $purger;
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
