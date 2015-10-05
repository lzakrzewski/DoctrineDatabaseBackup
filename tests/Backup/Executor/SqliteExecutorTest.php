<?php

namespace Lucaszz\DoctrineDatabaseBackup\tests\Backup\Executor;

use Lucaszz\DoctrineDatabaseBackup\Backup\BackupFile;
use Lucaszz\DoctrineDatabaseBackup\Backup\Executor\SqliteExecutor;
use Lucaszz\DoctrineDatabaseBackup\Backup\Filesystem;
use Prophecy\Prophecy\ObjectProphecy;

class SqliteExecutorTest extends \PHPUnit_Framework_TestCase
{
    /** @var SqliteExecutor */
    private $executor;
    /** @var ObjectProphecy|Filesystem */
    private $filesystem;
    /** @var ObjectProphecy|BackupFile */
    private $backupFile;

    /**
     * @test
     *
     * @expectedException \RuntimeException
     */
    public function it_fails_when_source_database_file_does_not_exists()
    {
        $this->filesystem->exists('/var/www/project/database/sqlite.db')->willReturn(false);

        $this->executor->create();
    }

    /** @test */
    public function it_creates_database_backup_file()
    {
        $this->filesystem->exists('/var/www/project/database/sqlite.db')->willReturn(true);
        $this->filesystem->prepareDir('/var/www/project/backup')->shouldBeCalled();
        $this->filesystem->copy('/var/www/project/database/sqlite.db', '/var/www/project/backup/123456')->shouldBeCalled();

        $this->executor->create();
    }

    /** @test */
    public function it_restores_database_from_backup_file()
    {
        $this->filesystem->exists('/var/www/project/backup')->willReturn(true);
        $this->filesystem->exists('/var/www/project/backup/123456')->willReturn(true);
        $this->filesystem->copy('/var/www/project/backup/123456', '/var/www/project/database/sqlite.db')->shouldBeCalled();

        $this->executor->restore();
    }

    /**
     * @test
     *
     * @expectedException \RuntimeException
     */
    public function it_fails_when_backup_database_file_does_not_exists()
    {
        $this->filesystem->exists('/var/www/project/backup')->willReturn(true);
        $this->filesystem->exists('/var/www/project/backup/123456')->willReturn(false);
        $this->filesystem->copy('/var/www/project/backup/123456', '/var/www/project/database/sqlite.db')->shouldNotBeCalled();

        $this->executor->restore();
    }

    /** @test */
    public function it_confirms_that_backup_was_created()
    {
        $this->filesystem->exists('/var/www/project/backup')->willReturn(true);
        $this->filesystem->exists('/var/www/project/database/sqlite.db')->willReturn(true);
        $this->filesystem->exists('/var/www/project/backup/123456')->willReturn(true);
        $this->filesystem->prepareDir('/var/www/project/backup')->shouldBeCalled();
        $this->filesystem->copy('/var/www/project/database/sqlite.db', '/var/www/project/backup/123456')->shouldBeCalled();
        $this->executor->create();

        $this->assertTrue($this->executor->isCreated());
    }

    /** @test */
    public function it_confirms_that_backup_was_not_created()
    {
        $this->assertFalse($this->executor->isCreated());
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->filesystem = $this->prophesize('Lucaszz\DoctrineDatabaseBackup\Backup\Filesystem');
        $this->backupFile = $this->prophesize('Lucaszz\DoctrineDatabaseBackup\Backup\BackupFile');

        $this->backupFile->dir()->willReturn('/var/www/project/backup');
        $this->backupFile->path()->willReturn('/var/www/project/backup/123456');

        $this->executor = new SqliteExecutor(
            '/var/www/project/database/sqlite.db',
            $this->filesystem->reveal(),
            $this->backupFile->reveal()
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        $this->filesystem = null;
        $this->backupFile = null;

        $this->executor = null;
    }
}
