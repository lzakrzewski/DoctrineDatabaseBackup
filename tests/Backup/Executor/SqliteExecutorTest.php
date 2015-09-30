<?php

namespace Lucaszz\DoctrineDatabaseBackup\Tests\Backup\Executor;

use Lucaszz\DoctrineDatabaseBackup\Backup\Executor\SqliteExecutor;
use org\bovigo\vfs\vfsStream;

class SqliteExecutorTest extends \PHPUnit_Framework_TestCase
{
    /** @var SqliteExecutor */
    private $executor;

    /**
     * @test
     *
     * @expectedException \RuntimeException
     */
    public function it_fails_when_source_database_file_does_not_exists()
    {
        $this->executor->create();
    }

    /** @test */
    public function it_creates_backup_file()
    {
        $this->givenSourceDatabaseExists();

        $this->executor->create();

        $this->assertThatOneBackupDatabaseFileExists();
    }

    /** @test */
    public function if_cleanups_after_test_fails_from_past()
    {
        $this->givenSourceDatabaseExists();
        $this->givenGarbageInBackupDirectoryExists();

        $this->executor->create();

        $this->assertThatOneBackupDatabaseFileExists();
    }

    /** @test */
    public function it_does_not_create_more_than_one_backup_file()
    {
        $this->givenSourceDatabaseExists();

        $this->executor->create();
        $this->executor->create();

        $this->assertThatOneBackupDatabaseFileExists();
    }

    /** @test */
    public function it_restores_database_from_backup_file()
    {
        $this->givenSourceDatabaseExists();

        $this->executor->create();
        $this->removeSourceDatabaseFile();

        $this->executor->restore();

        $this->assertThatOneBackupDatabaseFileExists();
        $this->assertThatSourceDatabaseFileExists();
    }

    /**
     * @test
     *
     * @expectedException \RuntimeException
     */
    public function it_fails_when_backup_database_file_does_not_exists()
    {
        $this->executor->restore();

        $this->assertThatOneBackupDatabaseFileExists();
        $this->assertThatSourceDatabaseFileExists();
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        vfsStream::setup('project');

        $this->executor = new SqliteExecutor('vfs://project/sqlite.db');
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        $this->executor = null;
    }

    private function givenSourceDatabaseExists()
    {
        file_put_contents('vfs://project/sqlite.db', 'database-contents');
    }

    private function assertThatOneBackupDatabaseFileExists()
    {
        $this->assertCount(1, array_diff(scandir('vfs://project/'.SqliteExecutor::BACKUP_DIR), array('.', '..')));
    }

    private function assertThatSourceDatabaseFileExists()
    {
        $this->assertTrue(file_exists('vfs://project/sqlite.db'));
    }

    private function removeSourceDatabaseFile()
    {
        unlink('vfs://project/sqlite.db');
    }

    private function givenGarbageInBackupDirectoryExists()
    {
        mkdir('vfs://project/'.SqliteExecutor::BACKUP_DIR);

        file_put_contents('vfs://project/'.SqliteExecutor::BACKUP_DIR.'/garbage.db', 'garbage-garbage');
    }
}
