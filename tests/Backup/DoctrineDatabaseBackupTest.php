<?php

namespace Lucaszz\DoctrineDatabaseBackup\tests\Backup;

use Doctrine\ORM\EntityManager;
use Lucaszz\DoctrineDatabaseBackup\Backup\Backup;
use Lucaszz\DoctrineDatabaseBackup\Backup\DoctrineDatabaseBackup;
use Lucaszz\DoctrineDatabaseBackup\Backup\Purger;
use Prophecy\Prophecy\ObjectProphecy;

class DoctrineDatabaseBackupTest extends \PHPUnit_Framework_TestCase
{
    /** @var ObjectProphecy|EntityManager */
    private $entityManager;
    /** @var ObjectProphecy|Backup */
    private $executor;
    /** @var ObjectProphecy|Purger */
    private $purger;
    /** @var DoctrineDatabaseBackup */
    private $backup;

    /** @test */
    public function it_creates_backup()
    {
        $this->executor->create()->shouldBeCalled();

        $this->backup->create();
    }

    /** @test */
    public function it_restores_database_from_backup()
    {
        $this->executor->restore()->shouldBeCalled();

        $this->backup->restore();
    }

    /** @test */
    public function it_clears_database()
    {
        $this->purger->purge()->shouldBeCalled();

        $this->backup->clearDatabase();
    }

    /** @test */
    public function it_checks_if_backup_was_created()
    {
        $isCreated = (bool) rand(0, 1);

        $this->executor->isCreated()->willReturn($isCreated);

        $this->assertEquals($isCreated, $this->backup->isCreated());
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

        $this->executor = $this->prophesize('Lucaszz\DoctrineDatabaseBackup\Backup\Backup');
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
