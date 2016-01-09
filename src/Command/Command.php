<?php

namespace Lucaszz\DoctrineDatabaseBackup\Command;

interface Command
{
    /**
     * @throws \RuntimeException
     *
     * @return string
     */
    public function run();
}
