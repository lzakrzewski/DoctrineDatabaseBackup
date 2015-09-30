<?php

namespace Lucaszz\DoctrineDatabaseBackup\tests\Integration;

use Lucaszz\DoctrineDatabaseBackup\Backup\Backup;

class SqliteBackupTest extends IntegrationTestCase
{
    /** @var Backup */
    private $backup;

    /** @test */
    public function it_can_restore_database()
    {
        $this->givenDatabaseIsClear();

        $this->backup->create();
        $this->addEntity();

        $this->backup->restore();

        $this->assertThatDatabaseIsClear();
    }

    protected function setUp()
    {
        parent::setUp();

        $this->backup = $this->prophesize('Lucaszz\DoctrineDatabaseBackup\Backup\Backup');
    }

    private function givenDatabaseIsClear()
    {
    }

    private function addEntity()
    {
    }

    private function assertThatDatabaseIsClear()
    {
    }
}
