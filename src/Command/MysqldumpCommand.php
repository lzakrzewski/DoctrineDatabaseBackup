<?php

namespace Lucaszz\DoctrineDatabaseBackup\Command;

use Symfony\Component\Process\Process;

class MysqldumpCommand implements Command
{
    /** @var string */
    private $dbname;
    /** @var string */
    private $host;
    /** @var string */
    private $user;
    /** @var string */
    private $password;

    /**
     * @param $dbname
     * @param $host
     * @param $user
     * @param $password
     */
    public function __construct($dbname, $host, $user, $password)
    {
        $this->dbname   = $dbname;
        $this->host     = $host;
        $this->user     = $user;
        $this->password = $password;
    }

    /** {@inheritdoc} */
    public function run()
    {
        $command = sprintf('mysqldump %s --no-create-info ', escapeshellarg($this->dbname));

        if (null !== $this->host && strlen($this->host)) {
            $command .= sprintf(' --host=%s', escapeshellarg($this->host));
        }
        if (null !== $this->user && strlen($this->user)) {
            $command .= sprintf(' --user=%s', escapeshellarg($this->user));
        }
        if (null !== $this->password && strlen($this->password)) {
            $command .= sprintf(' --password=%s', escapeshellarg($this->password));
        }

        return $this->execute($command);
    }

    protected function execute($command)
    {
        $process = new Process($command);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        return $process->getOutput();
    }
}
