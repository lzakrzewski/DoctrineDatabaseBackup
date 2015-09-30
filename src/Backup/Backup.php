<?php

namespace Lucaszz\DoctrineDatabaseBackup\Backup;

use Doctrine\DBAL\Connection;
use Lucaszz\DoctrineDatabaseBackup\Backup\Executor\Executor;

class Backup
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
            $this->executor = (new ExecutorFactory($connection))->create();

            return;
        }

        $this->executor = $factory->create();
    }

    public function create()
    {
        $this->executor->create();
    }

    public function restore()
    {
        $this->executor->restore();
    }
}
