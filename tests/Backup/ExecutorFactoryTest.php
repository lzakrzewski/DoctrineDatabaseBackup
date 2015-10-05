<?php

namespace Lucaszz\DoctrineDatabaseBackup\tests\Backup;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Platforms\OraclePlatform;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Lucaszz\DoctrineDatabaseBackup\Backup\ExecutorFactory;
use Lucaszz\DoctrineDatabaseBackup\Backup\Purger;
use Prophecy\Prophecy\ObjectProphecy;

class ExecutorFactoryTest extends \PHPUnit_Framework_TestCase
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
    /** @var ObjectProphecy|ExecutorFactory */
    private $factory;

    /** @test */
    public function it_can_create_sqlite_executor()
    {
        $this->connection->getDatabasePlatform()->willReturn($this->sqlitePlatform->reveal());
        $this->connection->getParams()->willReturn(array('path' => '/some/dir/sqlite.db'));

        $executor = $this->factory->create();

        $this->assertInstanceOf('Lucaszz\DoctrineDatabaseBackup\Backup\Executor\SqliteExecutor', $executor);
    }

    /** @test */
    public function it_can_create_mysql_executor()
    {
        $this->connection->getDatabasePlatform()->willReturn($this->mySqlPlatform->reveal());
        $this->connection->getParams()->willReturn(array('dbname' => 'test-database'));

        $executor = $this->factory->create();

        $this->assertInstanceOf('Lucaszz\DoctrineDatabaseBackup\Backup\Executor\MySqlExecutor', $executor);
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function it_does_not_supports_sqlite_in_memory()
    {
        $this->connection->getDatabasePlatform()->willReturn($this->sqlitePlatform->reveal());
        $this->connection->getParams()->willReturn(array('path' => ':memory:', 'memory' => true));

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
        $this->sqlitePlatform = $this->prophesize('Doctrine\DBAL\Platforms\SqlitePlatform');
        $this->mySqlPlatform = $this->prophesize('Doctrine\DBAL\Platforms\MySqlPlatform');
        $this->unknownPlatform = $this->prophesize('Doctrine\DBAL\Platforms\OraclePlatform');
        $this->connection = $this->prophesize('Doctrine\DBAL\Connection');
        $this->purger = $this->prophesize('Lucaszz\DoctrineDatabaseBackup\Backup\Purger');

        $this->factory = new ExecutorFactory($this->connection->reveal(), $this->purger->reveal());
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        $this->sqlitePlatform = null;
        $this->mySqlPlatform = null;
        $this->unknownPlatform = null;
        $this->connection = null;
        $this->purger = null;
        $this->factory = null;
    }
}
