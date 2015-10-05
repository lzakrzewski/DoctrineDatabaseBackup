<?php

namespace Lucaszz\DoctrineDatabaseBackup\tests\Backup;

use Lucaszz\DoctrineDatabaseBackup\Backup\Command;

class FakeCommand extends Command
{
    /** @var array */
    private $commands = array();
    /** @var string */
    private $expectedOutput;

    /**
     * {@inheritdoc}
     */
    public function run($command)
    {
        $this->commands[] = $command;

        if ($this->expectedOutput) {
            return $this->expectedOutput;
        }
    }

    /**
     * @return array
     */
    public function getCommands()
    {
        return $this->commands;
    }

    /**
     * @param string $expectedOutput
     */
    public function setExpectedOutput($expectedOutput)
    {
        $this->expectedOutput = $expectedOutput;
    }
}
