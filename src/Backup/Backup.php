<?php

namespace Lucaszz\DoctrineDatabaseBackup\Backup;

/**
 * @todo: Add isCreated method
 */
interface Backup
{
    public function create();

    public function restore();
}
