<?php

namespace Lucaszz\DoctrineDatabaseBackup\tests\Backup\Executor;

use Lucaszz\DoctrineDatabaseBackup\Backup\Executor\MySqlExecutor;
use Lucaszz\DoctrineDatabaseBackup\Backup\Executor\SqliteExecutor;
use org\bovigo\vfs\vfsStream;

class MySqlExecutorTest extends \PHPUnit_Framework_TestCase
{
    /** @var MySqlExecutor */
    private $executor;

    /** @test */
    public function it_creates_backup_file()
    {
        $this->executor->create();

        $this->assertThatOneBackupDatabaseFileExists();
    }

    /** @test */
    public function if_cleanups_after_test_fails_from_past()
    {
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

        $this->executor->restore();

        $this->assertThatOneBackupDatabaseFileExists();
    }

    /**
     * @test
     *
     * @expectedException \RuntimeException
     */
    public function it_fails_when_backup_database_file_does_not_exists()
    {
        $this->executor->restore();
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        vfsStream::setup('project');

        $this->executor = new MySqlExecutor('test-database-name');
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

    private function givenGarbageInBackupDirectoryExists()
    {
        mkdir('vfs://project/'.SqliteExecutor::BACKUP_DIR);

        file_put_contents('vfs://project/'.SqliteExecutor::BACKUP_DIR.'/garbage.db', 'garbage-garbage');
    }
}
