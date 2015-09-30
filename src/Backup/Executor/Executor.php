<?php

namespace Lucaszz\DoctrineDatabaseBackup\Backup\Executor;

interface Executor
{
    public function create();

    public function restore();
}
