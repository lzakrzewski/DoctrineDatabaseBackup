<?php

namespace Lucaszz\DoctrineDatabaseBackup\tests;

use Lucaszz\DoctrineDatabaseBackup\Backup\Backup;
use Lucaszz\DoctrineDatabaseBackup\DoctrineDatabaseBackup;
use Lucaszz\DoctrineDatabaseBackup\Purger;
use Prophecy\Prophecy\ObjectProphecy;

class DoctrineDatabaseBackupTest extends \PHPUnit_Framework_TestCase
{
    /** @var ObjectProphecy|Backup */
    private $backup;
    /** @var ObjectProphecy|Purger */
    private $purger;

    /** @var DoctrineDatabaseBackup */
    private $doctrineDatabaseBackup;

    /** @test */
    public function it_can_restore_database_when_backup_was_not_created()
    {
        $this->givenBackupWasNotCreated();

        $this->purger->purge()->shouldBeCalled();
        $this->backup->create()->shouldBeCalled();
        $this->backup->restore()->shouldBeCalled();

        $this->doctrineDatabaseBackup->restoreClearDatabase();
    }

    /** @test */
    public function it_can_restore_database_when_backup_was_created()
    {
        $this->givenBackupWasCreated();

        $this->purger->purge()->shouldNotBeCalled();
        $this->backup->create()->shouldNotBeCalled();
        $this->backup->restore()->shouldBeCalled();

        $this->doctrineDatabaseBackup->restoreClearDatabase();
    }

    /** {@inheritdoc} */
    protected function setUp()
    {
        $this->backup = $this->prophesize('\Lucaszz\DoctrineDatabaseBackup\Backup\Backup');
        $this->purger = $this->prophesize('\Lucaszz\DoctrineDatabaseBackup\Purger');

        $this->doctrineDatabaseBackup = new DoctrineDatabaseBackup($this->backup->reveal(), $this->purger->reveal());
    }

    /** {@inheritdoc} */
    protected function tearDown()
    {
        $this->backup = null;
        $this->purger = null;

        $this->doctrineDatabaseBackup = null;
    }

    private function givenBackupWasNotCreated()
    {
        $this->backup->isBackupCreated()->willReturn(false);
    }

    private function givenBackupWasCreated()
    {
        $this->backup->isBackupCreated()->willReturn(true);
    }
}
