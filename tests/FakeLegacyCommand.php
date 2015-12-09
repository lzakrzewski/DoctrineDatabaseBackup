<?php

namespace Lucaszz\DoctrineDatabaseBackup\tests;

use Lucaszz\DoctrineDatabaseBackup\LegacyCommand;

class FakeLegacyCommand extends LegacyCommand
{
    /** @var array */
    private $commands = [];
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
