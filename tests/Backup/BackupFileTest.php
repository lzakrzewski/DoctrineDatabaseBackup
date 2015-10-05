<?php

namespace Lucaszz\DoctrineDatabaseBackup\tests\Backup;

use Lucaszz\DoctrineDatabaseBackup\Backup\BackupFile;

class BackupFileTest extends \PHPUnit_Framework_TestCase
{
    /** @var BackupFile */
    private $backupFile;

    /** @test */
    public function it_have_file_path()
    {
        $expectedPath = '/tmp/dir/'.BackupFile::BACKUP_DIR.'/'.md5(getmypid());
        $this->assertEquals($expectedPath, $this->backupFile->path());
    }

    /** @test */
    public function it_has_file_dir()
    {
        $expectedPath = '/tmp/dir/'.BackupFile::BACKUP_DIR;
        $this->assertEquals($expectedPath, $this->backupFile->dir());
    }

    /** {@inheritdoc} */
    protected function setUp()
    {
        $this->backupFile = new BackupFile('/tmp/dir');
    }

    /** {@inheritdoc} */
    protected function tearDown()
    {
        $this->backupFile = null;
    }
}
