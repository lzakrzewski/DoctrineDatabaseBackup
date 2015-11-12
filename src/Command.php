<?php

namespace Lucaszz\DoctrineDatabaseBackup;

use Symfony\Component\Process\Process;

class Command
{
    /**
     * @param $command
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    public function run($command)
    {
        $process = new Process($command);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        return $process->getOutput();
    }
}
