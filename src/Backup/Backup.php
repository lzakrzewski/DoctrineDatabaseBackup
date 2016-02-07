<?php

namespace Lzakrzewski\DoctrineDatabaseBackup\Backup;

interface Backup
{
    /**
     * This method checks if backup was created during current PHP process.
     *
     * @return bool
     */
    public function isBackupCreated();

    /**
     * This method creates backup per PHP process.
     *
     * @throws \RuntimeException
     */
    public function create();

    /**
     * This method restores DB state from backup created during current PHP process.
     *
     * @throws \RuntimeException
     */
    public function restore();
}
