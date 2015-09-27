<?php

namespace tests\Lucaszz\DoctrineDatabaseBackup\Backup\Executor;

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
    public function it_fails_when_source_database_does_not_exists()
    {
        $this->executor->create();
    }

    /** @test */
    public function it_creates_backup()
    {
        $this->givenSourceDatabaseExists();

        $this->executor->create();

        $this->assertThatBackupFileWasCreated();
    }

    /** @test */
    public function it_creates_backup_only_if_it_does_not_exists()
    {
        $this->markTestIncomplete();
    }

    /** @test */
    public function it_does_not_create_more_than_one_backup_file()
    {
        $this->markTestIncomplete();
    }

    /** @test */
    public function it_restores_database()
    {
        $this->markTestIncomplete();
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        vfsStream::setup('project');

        $this->executor = new SqliteExecutor('vfs://project/sqlite.db');
    }

    private function givenSourceDatabaseExists()
    {
        file_put_contents('vfs://project/sqlite.db', 'database-contents');
    }

    private function assertThatBackupFileWasCreated()
    {
        $this->assertTrue(is_file('vfs://project/backup_1.db'));
    }
}
