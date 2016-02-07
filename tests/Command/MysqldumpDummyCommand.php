<?php

namespace Lzakrzewski\DoctrineDatabaseBackup\tests\Command;

use Lzakrzewski\DoctrineDatabaseBackup\Command\MysqldumpCommand;

class MysqldumpDummyCommand extends MysqldumpCommand
{
    protected function execute($command)
    {
        return $command;
    }
}
