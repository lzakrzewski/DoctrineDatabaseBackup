<?php

namespace Lucaszz\DoctrineDatabaseBackup\Backup\Executor;

use Lucaszz\DoctrineDatabaseBackup\Backup\Backup;

class MySqlExecutor implements Backup
{
    /** @var string */
    private $databaseName;

    /**
     * @param string $databaseName
     */
    public function __construct($databaseName)
    {
        $this->databaseName = $databaseName;
    }

    public function create()
    {
    }

    public function restore()
    {
    }
}
