<?php

namespace Lucaszz\DoctrineDatabaseBackup\tests;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Platforms\OraclePlatform;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Lucaszz\DoctrineDatabaseBackup\BackupFactory;
use Lucaszz\DoctrineDatabaseBackup\Purger;
use Prophecy\Prophecy\ObjectProphecy;

class BackupFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var ObjectProphecy|SqlitePlatform */
    private $sqlitePlatform;
    /** @var ObjectProphecy|MySqlPlatform */
    private $mySqlPlatform;
    /** @var ObjectProphecy|OraclePlatform */
    private $unknownPlatform;
    /** @var ObjectProphecy|Connection */
    private $connection;
    /** @var ObjectProphecy|Purger */
    private $purger;
    /** @var ObjectProphecy|BackupFactory */
    private $factory;

    /** @test */
    public function it_can_create_sqlite_backup()
    {
        $this->connection->getDatabasePlatform()->willReturn($this->sqlitePlatform->reveal());
        $this->connection->getParams()->willReturn(['path' => '/some/dir/sqlite.db']);

        $backup = $this->factory->create();

        $this->assertInstanceOf('Lucaszz\DoctrineDatabaseBackup\Backup\SqliteBackup', $backup);
    }

    /** @test */
    public function it_can_create_mysql_backup()
    {
        $this->connection->getDatabasePlatform()->willReturn($this->mySqlPlatform->reveal());
        $this->connection->getParams()->willReturn(['dbname' => 'test-database']);

        $backup = $this->factory->create();

        $this->assertInstanceOf('Lucaszz\DoctrineDatabaseBackup\Backup\MySqlBackup', $backup);
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function it_does_not_supports_sqlite_in_memory()
    {
        $this->connection->getDatabasePlatform()->willReturn($this->sqlitePlatform->reveal());
        $this->connection->getParams()->willReturn(['path' => ':memory:', 'memory' => true]);

        $this->factory->create();
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function it_does_not_supports_another_database_platforms()
    {
        $this->connection->getDatabasePlatform()->willReturn($this->unknownPlatform->reveal());

        $this->factory->create();
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->sqlitePlatform  = $this->prophesize('Doctrine\DBAL\Platforms\SqlitePlatform');
        $this->mySqlPlatform   = $this->prophesize('Doctrine\DBAL\Platforms\MySqlPlatform');
        $this->unknownPlatform = $this->prophesize('Doctrine\DBAL\Platforms\OraclePlatform');
        $this->connection      = $this->prophesize('Doctrine\DBAL\Connection');
        $this->purger          = $this->prophesize('Lucaszz\DoctrineDatabaseBackup\Purger');

        $this->factory = new BackupFactory($this->connection->reveal(), $this->purger->reveal());
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        $this->sqlitePlatform  = null;
        $this->mySqlPlatform   = null;
        $this->unknownPlatform = null;
        $this->connection      = null;
        $this->purger          = null;
        $this->factory         = null;
    }
}
