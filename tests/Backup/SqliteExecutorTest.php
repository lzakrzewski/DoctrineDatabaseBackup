<?php

namespace Lucaszz\DoctrineDatabaseBackup\tests\Backup;

use Lucaszz\DoctrineDatabaseBackup\Backup\SqliteExecutor;
use Lucaszz\DoctrineDatabaseBackup\Filesystem;
use Prophecy\Prophecy\ObjectProphecy;

class SqliteExecutorTest extends \PHPUnit_Framework_TestCase
{
    /** @var SqliteExecutor */
    private $executor;
    /** @var ObjectProphecy|Filesystem */
    private $filesystem;

    /**
     * @test
     *
     * @expectedException \RuntimeException
     */
    public function it_fails_when_source_database_file_does_not_exists()
    {
        $this->givenMemoryIsClear();
        $this->filesystem->exists('/var/www/project/database/sqlite.db')->willReturn(false);

        $this->executor->create();
    }

    /** @test */
    public function it_creates_database_backup()
    {
        $this->givenMemoryIsClear();
        $this->filesystem->exists('/var/www/project/database/sqlite.db')->willReturn(true);
        $this->filesystem->read('/var/www/project/database/sqlite.db')->willReturn('contents');

        $this->executor->create();
    }

    /** @test */
    public function it_restores_database_from_backup()
    {
        $this->givenMemoryIsClear();
        $this->filesystem->exists('/var/www/project/database/sqlite.db')->willReturn(true);
        $this->filesystem->read('/var/www/project/database/sqlite.db')->willReturn('contents');

        $this->executor->create();

        $this->filesystem->write('/var/www/project/database/sqlite.db', 'contents')->shouldBeCalled();

        $this->executor->restore();
    }

    /** @test */
    public function it_confirms_that_backup_was_created()
    {
        $this->givenMemoryIsClear();
        $this->filesystem->exists('/var/www/project/database/sqlite.db')->willReturn(true);
        $this->filesystem->read('/var/www/project/database/sqlite.db')->willReturn('contents');

        $this->executor->create();

        $this->assertTrue($this->executor->isBackupCreated());
    }

    /** @test */
    public function it_confirms_that_backup_was_not_created()
    {
        $this->givenMemoryIsClear();
        $this->assertFalse($this->executor->isBackupCreated());
    }

    /** @test */
    public function it_clears_memory()
    {
        $this->givenMemoryIsNotClear();

        SqliteExecutor::clearMemory();

        $this->assertFalse($this->executor->isBackupCreated());
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->filesystem = $this->prophesize('Lucaszz\DoctrineDatabaseBackup\Filesystem');

        $this->executor = new SqliteExecutor('/var/www/project/database/sqlite.db', $this->filesystem->reveal());
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        $this->filesystem = null;

        $this->executor = null;
    }

    private function givenMemoryIsClear()
    {
        SqliteExecutor::clearMemory();
    }

    private function givenMemoryIsNotClear()
    {
        $reflection = new \ReflectionClass($this->executor);
        $property = $reflection->getProperty('contents');
        $property->setAccessible(true);

        $property->setValue($this->executor, 'xyz');
        $property->setAccessible(false);
    }
}
