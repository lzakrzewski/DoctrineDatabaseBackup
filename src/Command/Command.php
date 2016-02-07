<?php

namespace Lzakrzewski\DoctrineDatabaseBackup\Command;

interface Command
{
    /**
     * @throws \RuntimeException
     *
     * @return string
     */
    public function run();
}
