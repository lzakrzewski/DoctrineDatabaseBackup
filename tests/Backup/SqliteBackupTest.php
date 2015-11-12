<?php

namespace Lucaszz\DoctrineDatabaseBackup\tests\Backup;

use Lucaszz\DoctrineDatabaseBackup\Backup\SqliteBackup;
use Lucaszz\DoctrineDatabaseBackup\Storage\LocalStorage;
use Prophecy\Prophecy\ObjectProphecy;

class SqliteBackupTest extends \PHPUnit_Framework_TestCase
{
    /** @var SqliteBackup */
    private $backup;
    /** @var ObjectProphecy|LocalStorage */
    private $storage;

    /**
     * @test
     *
     * @expectedException \RuntimeException
     */
    public function it_fails_when_source_database_file_does_not_exists()
    {
        $this->givenMemoryIsClear();
        $this->storage->has('/var/www/project/database/sqlite.db')->willReturn(false);

        $this->backup->create();
    }

    /** @test */
    public function it_creates_database_backup()
    {
        $this->givenMemoryIsClear();
        $this->storage->has('/var/www/project/database/sqlite.db')->willReturn(true);
        $this->storage->read('/var/www/project/database/sqlite.db')->willReturn('contents');

        $this->backup->create();
    }

    /** @test */
    public function it_restores_database_from_backup()
    {
        $this->givenMemoryIsClear();
        $this->storage->has('/var/www/project/database/sqlite.db')->willReturn(true);
        $this->storage->read('/var/www/project/database/sqlite.db')->willReturn('contents');

        $this->backup->create();

        $this->storage->put('/var/www/project/database/sqlite.db', 'contents')->shouldBeCalled();

        $this->backup->restore();
    }

    /** @test */
    public function it_confirms_that_backup_was_created()
    {
        $this->givenMemoryIsClear();
        $this->storage->has('/var/www/project/database/sqlite.db')->willReturn(true);
        $this->storage->read('/var/www/project/database/sqlite.db')->willReturn('contents');

        $this->backup->create();

        $this->assertTrue($this->backup->isBackupCreated());
    }

    /** @test */
    public function it_confirms_that_backup_was_not_created()
    {
        $this->givenMemoryIsClear();
        $this->assertFalse($this->backup->isBackupCreated());
    }

    /** @test */
    public function it_clears_memory()
    {
        $this->givenMemoryIsNotClear();

        SqliteBackup::clearMemory();

        $this->assertFalse($this->backup->isBackupCreated());
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->storage = $this->prophesize('Lucaszz\DoctrineDatabaseBackup\Storage\LocalStorage');

        $this->backup = new SqliteBackup('/var/www/project/database/sqlite.db', $this->storage->reveal());
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        $this->storage = null;

        $this->backup = null;
    }

    private function givenMemoryIsClear()
    {
        SqliteBackup::clearMemory();
    }

    private function givenMemoryIsNotClear()
    {
        $reflection = new \ReflectionClass($this->backup);
        $property   = $reflection->getProperty('contents');
        $property->setAccessible(true);

        $property->setValue($this->backup, 'xyz');
        $property->setAccessible(false);
    }
}
