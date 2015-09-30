<?php

namespace Lucaszz\DoctrineDatabaseBackup\tests\Backup;

use Doctrine\DBAL\Connection;
use Lucaszz\DoctrineDatabaseBackup\Backup\Backup;
use Lucaszz\DoctrineDatabaseBackup\Backup\DoctrineDatabaseBackup;
use Lucaszz\DoctrineDatabaseBackup\Backup\ExecutorFactory;
use Prophecy\Prophecy\ObjectProphecy;

class DoctrineDatabaseBackupTest extends \PHPUnit_Framework_TestCase
{
    /** @var ObjectProphecy|Connection */
    private $connection;
    /** @var ObjectProphecy|ExecutorFactory */
    private $factory;
    /** @var ObjectProphecy|Backup */
    private $executor;

    /** @test */
    public function it_creates_backup()
    {
        $this->executor->create()->shouldBeCalled();
        $this->factory->create()->willReturn($this->executor->reveal());

        $backup = new DoctrineDatabaseBackup($this->connection->reveal(), $this->factory->reveal());

        $backup->create();
    }

    /** @test */
    public function it_restores_database_from_backup()
    {
        $this->executor->restore()->shouldBeCalled();
        $this->factory->create()->willReturn($this->executor->reveal());

        $backup = new DoctrineDatabaseBackup($this->connection->reveal(), $this->factory->reveal());

        $backup->restore();
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->connection = $this->prophesize('\Doctrine\DBAL\Connection');
        $this->factory = $this->prophesize('Lucaszz\DoctrineDatabaseBackup\Backup\ExecutorFactory');
        $this->executor = $this->prophesize('Lucaszz\DoctrineDatabaseBackup\Backup\Backup');
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        $this->connection = null;
        $this->factory = null;
        $this->executor = null;
    }
}
