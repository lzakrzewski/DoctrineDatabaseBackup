<?php

namespace Lucaszz\DoctrineDatabaseBackup\Backup;

use Doctrine\DBAL\Connection;
use Lucaszz\DoctrineDatabaseBackup\Backup\Executor\Executor;

class Backup implements BackupInterface
{
    /** @var Executor */
    private $executor;

    /**
     * @param Connection           $connection
     * @param ExecutorFactory|null $factory
     */
    public function __construct(Connection $connection, ExecutorFactory $factory = null)
    {
        if (null === $factory) {
            $factory = (new ExecutorFactory($connection))->create();
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
