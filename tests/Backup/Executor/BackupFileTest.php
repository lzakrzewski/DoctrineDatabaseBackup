<?php

namespace Lucaszz\DoctrineDatabaseBackup\tests\Backup\Executor;

use Lucaszz\DoctrineDatabaseBackup\Backup\Executor\BackupFile;
use org\bovigo\vfs\vfsStream;

class BackupFileTest extends \PHPUnit_Framework_TestCase
{
    /** @var BackupFile */
    private $backupFile;

    /** @test */
    public function it_can_create_backup_directory()
    {
        $this->backupFile->prepareDir();

        $this->assertThatBackupDirectoryExists();
    }

    /** @test */
    public function it_can_clean_up_backup_directory()
    {
        $this->givenGarbageInBackupDirectoryExists();

        $this->backupFile->prepareDir();

        $this->assertThatBackupDirectoryIsEmpty();
    }

    /** @test */
    public function it_copies()
    {
        $this->givenFileExists('vfs://project/source.db');

        $this->backupFile->copy('vfs://project/source.db', 'vfs://project/destination.db');

        $this->assertThatFileExists('vfs://project/destination.db');
    }

    /** @test */
    public function it_can_copy_twice()
    {
        $this->givenFileExists('vfs://project/source.db');

        $this->backupFile->copy('vfs://project/source.db', 'vfs://project/destination.db');
        $this->backupFile->copy('vfs://project/source.db', 'vfs://project/destination.db');

        $this->assertThatFileExists('vfs://project/destination.db');
    }

    /** @test */
    public function it_checks_if_file_exists()
    {
        $this->givenFileExists('vfs://project/source.db');

        $this->assertTrue($this->backupFile->exists('vfs://project/source.db'));
    }

    /** @test */
    public function it_checks_if_file_does_not_exists()
    {
        $this->assertFalse($this->backupFile->exists('vfs://project/source.db'));
    }

    /** @test */
    public function it_can_get_backup_file_path()
    {
        $expectedPath = 'vfs://project/'.BackupFile::BACKUP_DIR.'/'.md5(getmypid());

        $this->assertEquals($expectedPath, $this->backupFile->path());
    }

    /**
     * @test
     *
     * @expectedException \RuntimeException
     */
    public function it_fails_during_creating_copy_when_source_file_does_not_exist()
    {
        $this->backupFile->copy('vfs://project/non-existing.db', 'vfs://project/sqlite.db');
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        vfsStream::setup('project');

        $this->backupFile = new BackupFile('vfs://project', 'db');
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        $this->backupFile = null;
    }

    private function givenGarbageInBackupDirectoryExists()
    {
        mkdir('vfs://project/'.BackupFile::BACKUP_DIR);

        file_put_contents('vfs://project/'.BackupFile::BACKUP_DIR.'/garbage', 'garbage-garbage');
    }

    private function givenFileExists($fileName)
    {
        file_put_contents($fileName, 'contents');
    }

    private function assertThatBackupDirectoryExists()
    {
        $this->assertTrue(file_exists('vfs://project/'.BackupFile::BACKUP_DIR));
    }

    private function assertThatFileExists($fileName)
    {
        $this->assertTrue(file_exists($fileName));
    }

    private function assertThatBackupDirectoryIsEmpty()
    {
        $this->assertCount(0, array_diff(scandir('vfs://project/'.BackupFile::BACKUP_DIR), array('.', '..')));
    }
}
