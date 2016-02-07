<?php

namespace Lzakrzewski\DoctrineDatabaseBackup\tests\Backup;

use Lzakrzewski\DoctrineDatabaseBackup\Backup\SqliteBackup;
use Lzakrzewski\DoctrineDatabaseBackup\Storage\InMemoryStorage;
use Lzakrzewski\DoctrineDatabaseBackup\Storage\LocalStorage;
use Prophecy\Prophecy\ObjectProphecy;

class SqliteBackupTest extends \PHPUnit_Framework_TestCase
{
    /** @var SqliteBackup */
    private $backup;
    /** @var ObjectProphecy|InMemoryStorage */
    private $memoryStorage;
    /** @var ObjectProphecy|LocalStorage */
    private $localStorage;

    /**
     * @test
     *
     * @expectedException \RuntimeException
     */
    public function it_fails_when_source_database_file_does_not_exists()
    {
        $this->givenDatabaseFileDoesNotExists();

        $this->backup->create();
    }

    /** @test */
    public function it_creates_database_backup()
    {
        $this->givenDatabaseFileExists();

        $this->backup->create();

        $this->memoryStorage->put(SqliteBackup::BACKUP_KEY, 'contents')->shouldBeCalled();
    }

    /** @test */
    public function it_restores_database_from_backup()
    {
        $this->givenMemoryBackupExists();

        $this->backup->restore();

        $this->localStorage->put('/var/www/project/database/sqlite.db', 'contents')->shouldBeCalled();
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

    /** {@inheritdoc} */
    protected function setUp()
    {
        $this->memoryStorage = $this->prophesize('Lzakrzewski\DoctrineDatabaseBackup\Storage\InMemoryStorage');
        $this->localStorage  = $this->prophesize('Lzakrzewski\DoctrineDatabaseBackup\Storage\LocalStorage');

        $this->backup = new SqliteBackup(
            '/var/www/project/database/sqlite.db',
            $this->memoryStorage->reveal(),
            $this->localStorage->reveal()
        );
    }

    /** {@inheritdoc} */
    protected function tearDown()
    {
        $this->memoryStorage = $this->prophesize('Lzakrzewski\DoctrineDatabaseBackup\Storage\InMemoryStorage');
        $this->localStorage  = $this->prophesize('Lzakrzewski\DoctrineDatabaseBackup\Storage\LocalStorage');

        $this->backup = null;
    }

    private function givenDatabaseFileDoesNotExists()
    {
        $this->localStorage->has('/var/www/project/database/sqlite.db')->willReturn(false);
    }

    private function givenDatabaseFileExists()
    {
        $this->localStorage->has('/var/www/project/database/sqlite.db')->willReturn(true);
        $this->localStorage->read('/var/www/project/database/sqlite.db')->willReturn('contents');
    }

    private function givenMemoryBackupExists()
    {
        $this->memoryStorage->has(SqliteBackup::BACKUP_KEY)->willReturn(true);
        $this->memoryStorage->read(SqliteBackup::BACKUP_KEY)->willReturn('contents');
    }

    private function givenMemoryBackupDoesNotExists()
    {
        $this->memoryStorage->has(SqliteBackup::BACKUP_KEY)->willReturn(false);
    }
}
