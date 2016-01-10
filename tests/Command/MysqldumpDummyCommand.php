<?php

namespace Lucaszz\DoctrineDatabaseBackup\tests\Command;

use Lucaszz\DoctrineDatabaseBackup\Command\MysqldumpCommand;

class MysqldumpDummyCommand extends MysqldumpCommand
{
    protected function execute($command)
    {
        return $command;
    }
}
