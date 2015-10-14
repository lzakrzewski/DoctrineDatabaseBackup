<?php

namespace Lucaszz\DoctrineDatabaseBackup\tests\Backup;

use Doctrine\ORM\EntityManager;
use Lucaszz\DoctrineDatabaseBackup\Backup\DoctrineDatabaseBackup;
use Lucaszz\DoctrineDatabaseBackup\Backup\Executor\Executor;
use Lucaszz\DoctrineDatabaseBackup\Backup\Purger;
use Prophecy\Prophecy\ObjectProphecy;

class DoctrineDatabaseBackupTest extends \PHPUnit_Framework_TestCase
{
    /** @var ObjectProphecy|EntityManager */
    private $entityManager;
    /** @var ObjectProphecy|Executor */
    private $executor;
    /** @var ObjectProphecy|Purger */
    private $purger;
    /** @var DoctrineDatabaseBackup */
    private $backup;

    /** @test */
    public function it_creates_backup_from_clear_database_and_restore_backup()
    {
        $this->executor->isBackupCreated()->willReturn(false);
        $this->purger->purge()->shouldBeCalled();
        $this->executor->create()->shouldBeCalled();
        $this->executor->restore()->shouldBeCalled();

        $this->backup->restoreClearDatabase();
    }

    /** @test */
    public function it_restores_clear_database()
    {
        $this->executor->isBackupCreated()->willReturn(true);
        $this->purger->purge()->shouldNotBeCalled();
        $this->executor->create()->shouldNotBeCalled();
        $this->executor->restore()->shouldBeCalled();

        $this->backup->restoreClearDatabase();
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $connection = $this->prophesize('\Doctrine\DBAL\Connection');
        $platform = $this->prophesize('\Doctrine\DBAL\Platforms\MySqlPlatform');

        $connection->getDatabasePlatform()->willReturn($platform);

        $this->entityManager = $this->prophesize('\Doctrine\ORM\EntityManager');
        $this->entityManager->getConnection()->willReturn($connection->reveal());

        $this->executor = $this->prophesize('Lucaszz\DoctrineDatabaseBackup\Backup\Executor\Executor');
        $this->purger = $this->prophesize('Lucaszz\DoctrineDatabaseBackup\Backup\Purger');

        $this->backup = new DoctrineDatabaseBackup($this->entityManager->reveal());

        $this->backup->setExecutor($this->executor->reveal());
        $this->backup->setPurger($this->purger->reveal());
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        $this->entityManager = null;
        $this->executor = null;
        $this->purger = null;
        $this->backup = null;
    }
}
