<?php

namespace Lzakrzewski\DoctrineDatabaseBackup\tests\Backup;

use Doctrine\DBAL\Connection;
use Lzakrzewski\DoctrineDatabaseBackup\Backup\MySqlBackup;
use Lzakrzewski\DoctrineDatabaseBackup\Purger;
use Lzakrzewski\DoctrineDatabaseBackup\Storage\InMemoryStorage;
use Lzakrzewski\DoctrineDatabaseBackup\tests\FakeLegacyCommand;
use Prophecy\Prophecy\ObjectProphecy;

class MySqlBackupTest extends \PHPUnit_Framework_TestCase
{
    /** @var ObjectProphecy|Connection */
    private $connection;
    /** @var ObjectProphecy|InMemoryStorage */
    private $memoryStorage;
    /** @var ObjectProphecy|Purger */
    private $purger;
    /** @var FakeLegacyCommand */
    private $command;
    /** @var MySqlBackup */
    private $backup;

    /** @test */
    public function it_creates_backup()
    {
        $this->command->run()->willReturn(null);

        $this->backup->create();

        $this->memoryStorage->put(MySqlBackup::BACKUP_KEY, null)->shouldBeCalled();
    }

    /** @test */
    public function it_creates_backup_with_data()
    {
        $this->command->run()->willReturn('INSERT INTO table VALUES (1, 2, 3, 4)');

        $this->backup->create();

        $this->memoryStorage->put(MySqlBackup::BACKUP_KEY, 'INSERT INTO table VALUES (1, 2, 3, 4)')->shouldBeCalled();
    }

    /** @test */
    public function it_restores_database()
    {
        $this->givenMemoryBackupExists();

        $this->backup->restore();

        $this->purger->purge()->shouldBeCalled();
        $this->memoryStorage->read(MySqlBackup::BACKUP_KEY)->shouldBeCalled();
    }

    /** @test */
    public function it_restores_database_with_data()
    {
        $this->givenMemoryBackupWithDataExists();

        $this->backup->restore();

        $this->purger->purge()->shouldBeCalled();
        $this->connection->beginTransaction()->shouldBeCalled();
        $this->connection->exec('INSERT INTO table VALUES (1, 2, 3, 4)')->shouldBeCalled();
        $this->connection->commit()->shouldBeCalled();
    }

    /** @test */
    public function it_confirms_that_backup_was_created()
    {
        $this->givenMemoryBackupExists();

        $this->assertTrue($this->backup->isBackupCreated());
    }

    /** @test */
    public function it_confirms_that_backup_was_not_created()
    {
        $this->givenMemoryBackupDoesNotExists();

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

    /** {@inheritdoc} */
    protected function setUp()
    {
        $this->connection    = $this->prophesize('\Doctrine\DBAL\Connection');
        $this->memoryStorage = $this->prophesize('\Lzakrzewski\DoctrineDatabaseBackup\Storage\InMemoryStorage');
        $this->purger        = $this->prophesize('\Lzakrzewski\DoctrineDatabaseBackup\Purger');
        $this->command       = $this->prophesize('\Lzakrzewski\DoctrineDatabaseBackup\Command\Command');

        $this->backup = new MySqlBackup($this->connection->reveal(), $this->memoryStorage->reveal(), $this->purger->reveal(), $this->command->reveal());
    }

    /** {@inheritdoc} */
    protected function tearDown()
    {
        $this->connection    = null;
        $this->memoryStorage = null;
        $this->purger        = null;
        $this->command       = null;

        $this->backup = null;
    }

    private function givenMemoryBackupExists()
    {
        $this->memoryStorage->has(MySqlBackup::BACKUP_KEY)->willReturn(true);
        $this->memoryStorage->read(MySqlBackup::BACKUP_KEY)->willReturn('contents');
    }

    private function givenMemoryBackupDoesNotExists()
    {
        $this->memoryStorage->has(MySqlBackup::BACKUP_KEY)->willReturn(false);
    }

    private function givenMemoryBackupWithDataExists()
    {
        $this->memoryStorage->has(MySqlBackup::BACKUP_KEY)->willReturn(true);
        $this->memoryStorage->read(MySqlBackup::BACKUP_KEY)->willReturn('INSERT INTO table VALUES (1, 2, 3, 4)');
    }
}
