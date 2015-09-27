<?php

namespace Lucaszz\DoctrineDatabaseBackup\Backup;

interface BackupInterface
{
    public function create();

    public function restore();
}
