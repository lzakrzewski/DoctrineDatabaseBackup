<?php

namespace Lucaszz\DoctrineDatabaseBackup\tests\Backup\Executor;

use Doctrine\DBAL\Connection;
use Lucaszz\DoctrineDatabaseBackup\Backup\Executor\MySqlExecutor;
use Lucaszz\DoctrineDatabaseBackup\Backup\Purger;
use Lucaszz\DoctrineDatabaseBackup\tests\Backup\FakeCommand;
use Prophecy\Prophecy\ObjectProphecy;

class MySqlExecutorTest extends \PHPUnit_Framework_TestCase
{
    /** @var ObjectProphecy|Connection */
    private $connection;
    /** @var ObjectProphecy|Purger */
    private $purger;
    /** @var FakeCommand */
    private $command;
    /** @var MySqlExecutor */
    private $executor;

    /** @test */
    public function it_creates_backup()
    {
        $this->executor->create();

        $this->assertThatCommandWasCalled("mysqldump 'doctrine-database-test' --no-create-info  --user='root' --password='pass'");
    }

    /** @test */
    public function it_restores_database()
    {
        $this->command->setExpectedOutput('EXAMPLE SQL');
        $this->executor->create();

        $this->purger->purge()->shouldBeCalled();

        $this->connection->exec('EXAMPLE SQL')->shouldNotBeCalled();

        $this->executor->restore();
    }

    /** @test */
    public function it_restores_database_with_data()
    {
        $this->command->setExpectedOutput('INSERT INTO table VALUES (1, 2, 3, 4)');
        $this->executor->create();

        $this->purger->purge()->shouldBeCalled();

        $this->connection->beginTransaction()->shouldBeCalled();
        $this->connection->exec('INSERT INTO table VALUES (1, 2, 3, 4)')->shouldBeCalled();
        $this->connection->commit()->shouldBeCalled();

        $this->executor->restore();
    }

    /** @test */
    public function it_confirms_that_backup_was_created()
    {
        $this->executor->create();

        $this->assertTrue($this->executor->isCreated());
    }

    /** @test */
    public function it_confirms_that_backup_was_not_created()
    {
        $this->assertFalse($this->executor->isCreated());
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function it_fails_during_restoring_database_without_backup()
    {
        $this->executor->restore();
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->connection = $this->prophesize('\Doctrine\DBAL\Connection');
        $this->purger = $this->prophesize('\Lucaszz\DoctrineDatabaseBackup\Backup\Purger');

        $params = array(
            'driver' => 'pdo_mysql',
            'user' => 'root',
            'password' => 'pass',
            'dbname' => 'doctrine-database-test',
        );

        $this->connection->getParams()->willReturn($params);
        $this->command = new FakeCommand();

        $this->executor = new MySqlExecutor($this->connection->reveal(), $this->purger->reveal(), $this->command);

        $this->refreshMySqlExecutor();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        $this->connection = null;
        $this->purger = null;
        $this->command = null;

        $this->executor = null;
    }

    private function assertThatCommandWasCalled($expectedCommand)
    {
        $this->assertContains($expectedCommand, $this->command->getCommands());
    }

    private function refreshMySqlExecutor()
    {
        $reflection = new \ReflectionClass($this->executor);
        $property = $reflection->getProperty('isCreated');
        $property->setAccessible(true);

        $property->setValue($this->executor, false);
        $property->setAccessible(false);
    }
}
