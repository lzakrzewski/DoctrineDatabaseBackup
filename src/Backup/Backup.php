<?php

namespace Lucaszz\DoctrineDatabaseBackup\Backup;

interface Backup
{
    /**
     * This method checks if backup was created during current PHP process.
     *
     * @return bool
     */
    public function isCreated();

    /**
     * This method creates backup per PHP process.
     */
    public function create();

    /**
     * This method restores DB state from backup created during current PHP process.
     */
    public function restore();
}
