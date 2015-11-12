<?php

namespace Lucaszz\DoctrineDatabaseBackup\tests\Backup;

use Doctrine\DBAL\Connection;
use Lucaszz\DoctrineDatabaseBackup\Backup\MySqlBackup;
use Lucaszz\DoctrineDatabaseBackup\Purger;
use Lucaszz\DoctrineDatabaseBackup\tests\FakeCommand;
use Prophecy\Prophecy\ObjectProphecy;

class MySqlBackupTest extends \PHPUnit_Framework_TestCase
{
    /** @var ObjectProphecy|Connection */
    private $connection;
    /** @var ObjectProphecy|Purger */
    private $purger;
    /** @var FakeCommand */
    private $command;
    /** @var MySqlBackup */
    private $backup;

    /** @test */
    public function it_creates_backup()
    {
        $this->givenMemoryIsClear();
        $this->backup->create();

        $this->assertThatCommandWasCalled("mysqldump 'doctrine-database-test' --no-create-info  --user='root' --password='pass'");
    }

    /** @test */
    public function it_restores_database()
    {
        $this->givenMemoryIsClear();
        $this->command->setExpectedOutput('EXAMPLE SQL');
        $this->backup->create();

        $this->purger->purge()->shouldBeCalled();

        $this->connection->exec('EXAMPLE SQL')->shouldNotBeCalled();

        $this->backup->restore();
    }

    /** @test */
    public function it_restores_database_with_data()
    {
        $this->givenMemoryIsClear();
        $this->command->setExpectedOutput('INSERT INTO table VALUES (1, 2, 3, 4)');
        $this->backup->create();

        $this->purger->purge()->shouldBeCalled();

        $this->connection->beginTransaction()->shouldBeCalled();
        $this->connection->exec('INSERT INTO table VALUES (1, 2, 3, 4)')->shouldBeCalled();
        $this->connection->commit()->shouldBeCalled();

        $this->backup->restore();
    }

    /** @test */
    public function it_confirms_that_backup_was_created()
    {
        $this->givenMemoryIsClear();
        $this->backup->create();

        $this->assertTrue($this->backup->isBackupCreated());
    }

    /** @test */
    public function it_confirms_that_backup_was_not_created()
    {
        $this->givenMemoryIsClear();
        $this->assertFalse($this->backup->isBackupCreated());
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function it_fails_during_restoring_database_without_backup()
    {
        $this->backup->restore();
    }

    /** @test */
    public function it_clears_memory()
    {
        $this->givenMemoryIsNotClear();

        MySqlBackup::clearMemory();

        $this->assertFalse($this->backup->isBackupCreated());
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->connection = $this->prophesize('\Doctrine\DBAL\Connection');
        $this->purger     = $this->prophesize('\Lucaszz\DoctrineDatabaseBackup\Purger');

        $params = [
            'driver'   => 'pdo_mysql',
            'user'     => 'root',
            'password' => 'pass',
            'dbname'   => 'doctrine-database-test',
        ];

        $this->connection->getParams()->willReturn($params);
        $this->command = new FakeCommand();

        $this->backup = new MySqlBackup($this->connection->reveal(), $this->purger->reveal(), $this->command);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        $this->connection = null;
        $this->purger     = null;
        $this->command    = null;

        $this->backup = null;
    }

    private function assertThatCommandWasCalled($expectedCommand)
    {
        $this->assertContains($expectedCommand, $this->command->getCommands());
    }

    private function givenMemoryIsClear()
    {
        MySqlBackup::clearMemory();
    }

    private function givenMemoryIsNotClear()
    {
        $reflection = new \ReflectionClass($this->backup);
        $property   = $reflection->getProperty('isCreated');
        $property->setAccessible(true);

        $property->setValue($this->backup, true);
        $property->setAccessible(false);
    }
}
