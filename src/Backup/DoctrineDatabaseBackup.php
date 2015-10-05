<?php

namespace Lucaszz\DoctrineDatabaseBackup\Backup;

use Doctrine\ORM\EntityManager;

class DoctrineDatabaseBackup implements Backup
{
    /** @var Backup */
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

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        $this->executor->create();
    }

    /**
     * {@inheritdoc}
     */
    public function restore()
    {
        $this->executor->restore();
    }

    /**
     * {@inheritdoc}
     */
    public function isCreated()
    {
        return $this->executor->isCreated();
    }

    public function clearDatabase()
    {
        $this->purger->purge();
    }

    /**
     * @param Backup $executor
     */
    public function setExecutor(Backup $executor)
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
}
