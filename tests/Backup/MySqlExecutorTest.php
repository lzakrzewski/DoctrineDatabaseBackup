<?php

namespace Lucaszz\DoctrineDatabaseBackup\tests\Backup;

use Doctrine\DBAL\Connection;
use Lucaszz\DoctrineDatabaseBackup\Backup\MySqlExecutor;
use Lucaszz\DoctrineDatabaseBackup\Purger;
use Lucaszz\DoctrineDatabaseBackup\tests\FakeCommand;
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
        $this->givenMemoryIsClear();
        $this->executor->create();

        $this->assertThatCommandWasCalled("mysqldump 'doctrine-database-test' --no-create-info  --user='root' --password='pass'");
    }

    /** @test */
    public function it_restores_database()
    {
        $this->givenMemoryIsClear();
        $this->command->setExpectedOutput('EXAMPLE SQL');
        $this->executor->create();

        $this->purger->purge()->shouldBeCalled();

        $this->connection->exec('EXAMPLE SQL')->shouldNotBeCalled();

        $this->executor->restore();
    }

    /** @test */
    public function it_restores_database_with_data()
    {
        $this->givenMemoryIsClear();
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
        $this->givenMemoryIsClear();
        $this->executor->create();

        $this->assertTrue($this->executor->isBackupCreated());
    }

    /** @test */
    public function it_confirms_that_backup_was_not_created()
    {
        $this->givenMemoryIsClear();
        $this->assertFalse($this->executor->isBackupCreated());
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function it_fails_during_restoring_database_without_backup()
    {
        $this->executor->restore();
    }

    /** @test */
    public function it_clears_memory()
    {
        $this->givenMemoryIsNotClear();

        MySqlExecutor::clearMemory();

        $this->assertFalse($this->executor->isBackupCreated());
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->connection = $this->prophesize('\Doctrine\DBAL\Connection');
        $this->purger = $this->prophesize('\Lucaszz\DoctrineDatabaseBackup\Purger');

        $params = array(
            'driver' => 'pdo_mysql',
            'user' => 'root',
            'password' => 'pass',
            'dbname' => 'doctrine-database-test',
        );

        $this->connection->getParams()->willReturn($params);
        $this->command = new FakeCommand();

        $this->executor = new MySqlExecutor($this->connection->reveal(), $this->purger->reveal(), $this->command);
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

    private function givenMemoryIsClear()
    {
        MySqlExecutor::clearMemory();
    }

    private function givenMemoryIsNotClear()
    {
        $reflection = new \ReflectionClass($this->executor);
        $property = $reflection->getProperty('isCreated');
        $property->setAccessible(true);

        $property->setValue($this->executor, true);
        $property->setAccessible(false);
    }
}
