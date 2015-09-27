<?php

namespace tests\Lucaszz\DoctrineDatabaseBackup\Backup;

use Lucaszz\DoctrineDatabaseBackup\Backup\SqliteBackup;

class SqliteBackupTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_do_amazing_stuff()
    {
        $backup = new SqliteBackup();

        $this->assertInstanceOf('Lucaszz\DoctrineDatabaseBackup\Backup\SqliteBackup', $backup);
    }
}
