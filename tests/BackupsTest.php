<?php

namespace Lucaszz\DoctrineDatabaseBackup\tests;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Lucaszz\DoctrineDatabaseBackup\Backups;
use Prophecy\Prophecy\ObjectProphecy;

class BackupsTest extends \PHPUnit_Framework_TestCase
{
    /** @var EntityManager|ObjectProphecy */
    private $entityManager;
    /** @var Connection|ObjectProphecy */
    private $connection;

    /** @test */
    public function it_can_create_doctrine_database_backup_with_sqlite_platform()
    {
        $this->givenSqliteDatabasePlatformWasEnabled();

        $this->assertInstanceOf(
            '\Lucaszz\DoctrineDatabaseBackup\DoctrineDatabaseBackup',
            Backups::newInstance($this->entityManager->reveal())
        );
    }

    /** @test */
    public function it_can_create_doctrine_database_backup_with_mysql_platform()
    {
        $this->givenMySqlDatabasePlatformWasEnabled();

        $this->assertInstanceOf(
            '\Lucaszz\DoctrineDatabaseBackup\DoctrineDatabaseBackup',
            Backups::newInstance($this->entityManager->reveal())
        );
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function it_can_not_create_doctrine_database_backup_with_unsupported_platform()
    {
        $this->givenUnsupportedDatabasePlatformWasEnabled();

        Backups::newInstance($this->entityManager->reveal());
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function it_can_not_create_doctrine_database_backup_with_sqlite_in_memory_platform()
    {
        $this->givenSqliteInMemoryDatabasePlatformWasEnabled();

        Backups::newInstance($this->entityManager->reveal());
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function it_can_not_create_doctrine_database_backup_with_mysql_platform_without_dbname_provided()
    {
        $this->givenMySqlWithoutDbnameDatabasePlatformWasEnabled();

        Backups::newInstance($this->entityManager->reveal());
    }

    /** {@inheritdoc} */
    protected function setUp()
    {
        $this->entityManager = $this->prophesize('\Doctrine\ORM\EntityManager');
        $this->connection    = $this->prophesize('\Doctrine\DBAL\Connection');

        $this->entityManager->getConnection()->willReturn($this->connection->reveal());
    }

    /** {@inheritdoc} */
    protected function tearDown()
    {
        $this->entityManager = null;
        $this->connection    = null;
    }

    private function givenSqliteDatabasePlatformWasEnabled()
    {
        $sqlitePlatform = $this->prophesize('\Doctrine\DBAL\Platforms\SqlitePlatform');

        $this->connection->getDatabasePlatform()->willReturn($sqlitePlatform->reveal());
        $this->connection->getParams()->willReturn(['path' => '/path/to/database.db']);
    }

    private function givenMySqlDatabasePlatformWasEnabled()
    {
        $mySqlPlatform = $this->prophesize('\Doctrine\DBAL\Platforms\MySqlPlatform');

        $this->connection->getDatabasePlatform()->willReturn($mySqlPlatform->reveal());
        $this->connection->getParams()->willReturn(['dbname' => 'test', 'host' => 'localhost', 'user' => 'johndoe', 'password' => 'testing1']);
    }

    private function givenUnsupportedDatabasePlatformWasEnabled()
    {
        $unsupportedPlatform = $this->prophesize('\Doctrine\DBAL\Platforms\OraclePlatform');

        $this->connection->getDatabasePlatform()->willReturn($unsupportedPlatform->reveal());
    }

    private function givenSqliteInMemoryDatabasePlatformWasEnabled()
    {
        $this->givenSqliteDatabasePlatformWasEnabled();

        $this->connection->getParams()->willReturn(['path' => ':memory:']);
    }

    private function givenMySqlWithoutDbnameDatabasePlatformWasEnabled()
    {
        $this->givenMySqlDatabasePlatformWasEnabled();

        $this->connection->getParams()->willReturn(['host' => 'localhost']);
    }
}
