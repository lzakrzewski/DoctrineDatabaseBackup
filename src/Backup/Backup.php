<?php

namespace Lucaszz\DoctrineDatabaseBackup\Backup;

interface Backup
{
    public function create();

    public function restore();
}
