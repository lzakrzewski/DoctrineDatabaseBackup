<?php

namespace Lucaszz\DoctrineDatabaseBackup\Backup;

use Doctrine\ORM\EntityManager;
use Lucaszz\DoctrineDatabaseBackup\Backup\Executor\Executor;

class DoctrineDatabaseBackup
{
    /** @var Executor */
    private $executor;
    /** @var Purger */
    private $purger;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->purger = new Purger($entityManager);
        $this->executor = (new ExecutorFactory($entityManager->getConnection(), $this->purger))->create();
    }

    public function restoreClearDatabase()
    {
        if (!$this->executor->isBackupCreated()) {
            $this->purger->purge();
            $this->executor->create();
        }

        $this->executor->restore();
    }

    /**
     * @param Executor $executor
     */
    public function setExecutor(Executor $executor)
    {
        $this->executor = $executor;
    }

    /**
     * @param Purger $purger
     */
    public function setPurger(Purger $purger)
    {
        $this->purger = $purger;
    }

    /**
     * @return Executor
     */
    public function getExecutor()
    {
        return $this->executor;
    }

    /**
     * @return Purger
     */
    public function getPurger()
    {
        return $this->purger;
    }
}
