<?php

namespace Lucaszz\DoctrineDatabaseBackup\tests;

use Doctrine\ORM\EntityManager;
use Lucaszz\DoctrineDatabaseBackup\Backup\Backup;
use Lucaszz\DoctrineDatabaseBackup\DoctrineDatabaseBackup;
use Lucaszz\DoctrineDatabaseBackup\Purger;
use Prophecy\Prophecy\ObjectProphecy;

class DoctrineDatabaseBackupTest extends \PHPUnit_Framework_TestCase
{
    /** @var ObjectProphecy|EntityManager */
    private $entityManager;
    /** @var ObjectProphecy|Backup */
    private $backup;
    /** @var ObjectProphecy|Purger */
    private $purger;
    /** @var DoctrineDatabaseBackup */
    private $doctrineDatabaseBackup;

    /** @test */
    public function it_creates_backup_from_clear_database_and_restore_backup()
    {
        $this->backup->isBackupCreated()->willReturn(false);
        $this->purger->purge()->shouldBeCalled();
        $this->backup->create()->shouldBeCalled();
        $this->backup->restore()->shouldBeCalled();

        $this->doctrineDatabaseBackup->restoreClearDatabase();
    }

    /** @test */
    public function it_restores_clear_database()
    {
        $this->backup->isBackupCreated()->willReturn(true);
        $this->purger->purge()->shouldNotBeCalled();
        $this->backup->create()->shouldNotBeCalled();
        $this->backup->restore()->shouldBeCalled();

        $this->doctrineDatabaseBackup->restoreClearDatabase();
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $connection = $this->prophesize('\Doctrine\DBAL\Connection');
        $platform   = $this->prophesize('\Doctrine\DBAL\Platforms\MySqlPlatform');

        $connection->getDatabasePlatform()->willReturn($platform);
        $connection->getParams()->willReturn(['dbname' => 'test']);

        $this->entityManager = $this->prophesize('\Doctrine\ORM\EntityManager');
        $this->entityManager->getConnection()->willReturn($connection->reveal());

        $this->backup = $this->prophesize('Lucaszz\DoctrineDatabaseBackup\Backup\Backup');
        $this->purger = $this->prophesize('Lucaszz\DoctrineDatabaseBackup\Purger');

        $this->doctrineDatabaseBackup = new DoctrineDatabaseBackup($this->entityManager->reveal());

        $this->doctrineDatabaseBackup->setBackup($this->backup->reveal());
        $this->doctrineDatabaseBackup->setPurger($this->purger->reveal());
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        $this->entityManager          = null;
        $this->backup                 = null;
        $this->purger                 = null;
        $this->doctrineDatabaseBackup = null;
    }
}
