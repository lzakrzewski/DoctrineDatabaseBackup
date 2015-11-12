<?php

namespace Lucaszz\DoctrineDatabaseBackup\Backup;

use Doctrine\DBAL\Connection;
use Lucaszz\DoctrineDatabaseBackup\Command;
use Lucaszz\DoctrineDatabaseBackup\Purger;

class MySqlBackup implements Backup
{
    /** @var string */
    private static $dataSql;
    /** @var bool */
    private static $isCreated = false;
    /** @var Connection */
    private $connection;
    /** @var Purger */
    private $purger;
    /** @var Command */
    private $command;

    /**
     * @param Connection $connection
     * @param Purger     $purger
     * @param Command    $command
     */
    public function __construct(Connection $connection, Purger $purger, Command $command)
    {
        $this->connection = $connection;
        $this->purger     = $purger;
        $this->command    = $command;
    }

    /** {@inheritdoc} */
    public function create()
    {
        static::$dataSql   = $this->dataSql();
        static::$isCreated = true;
    }

    /** {@inheritdoc} */
    public function restore()
    {
        if (!$this->isBackupCreated()) {
            throw new \RuntimeException('Backup should be created before restore database.');
        }

        $this->purger->purge();

        if (null !== static::$dataSql) {
            $this->execute(static::$dataSql);
        }
    }

    /** {@inheritdoc} */
    public function isBackupCreated()
    {
        return static::$isCreated;
    }

    /** {@inheritdoc} */
    public static function clearMemory()
    {
        static::$dataSql = null;
        /** @var bool */
        static::$isCreated = false;
    }

    private function execute($sql)
    {
        $this->connection->beginTransaction();
        $this->connection->exec($sql);

        $this->connection->commit();
    }

    private function dataSql()
    {
        $params  = $this->connection->getParams();
        $command = sprintf('mysqldump %s --no-create-info ', escapeshellarg($params['dbname']));

        if (isset($params['host']) && strlen($params['host'])) {
            $command .= sprintf(' --host=%s', escapeshellarg($params['host']));
        }
        if (isset($params['user']) && strlen($params['user'])) {
            $command .= sprintf(' --user=%s', escapeshellarg($params['user']));
        }
        if (isset($params['password']) && strlen($params['password'])) {
            $command .= sprintf(' --password=%s', escapeshellarg($params['password']));
        }

        $output = $this->command->run($command);

        if (false === stripos($output, 'INSERT')) {
            return;
        }

        return $output;
    }
}
