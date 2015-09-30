<?php

namespace Lucaszz\DoctrineDatabaseBackup\Backup;

use Doctrine\DBAL\Connection;

class DoctrineDatabaseBackup implements Backup
{
    /** @var Backup */
    private $executor;

    /**
     * @param Connection           $connection
     * @param ExecutorFactory|null $factory
     */
    public function __construct(Connection $connection, ExecutorFactory $factory = null)
    {
        if (null === $factory) {
            $this->executor = (new ExecutorFactory($connection))->create();

            return;
        }

        $this->executor = $factory->create();
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
}
