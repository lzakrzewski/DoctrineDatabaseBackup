<?php

namespace Lucaszz\DoctrineDatabaseBackup\tests;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Lucaszz\DoctrineDatabaseBackup\BackupFactory;
use Prophecy\Prophecy\ObjectProphecy;

class BackupFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var EntityManager|ObjectProphecy */
    private $entityManager;
    /** @var Connection|ObjectProphecy */
    private $connection;

    /** @test */
    public function it_can_create_instance_of_sqlite_backup()
    {
        $this->givenSqliteDatabasePlatformWasEnabled();

        $backup = BackupFactory::instance($this->entityManager->reveal());

        $this->assertInstanceOf('\Lucaszz\DoctrineDatabaseBackup\Backup\SqliteBackup', $backup);
    }

    /** @test */
    public function it_can_create_instance_of_mysql_backup()
    {
        $this->givenMySqlDatabasePlatformWasEnabled();

        $backup = BackupFactory::instance($this->entityManager->reveal());

        $this->assertInstanceOf('\Lucaszz\DoctrineDatabaseBackup\Backup\MySqlBackup', $backup);
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function it_can_not_create_instance_of_backup_with_unsupported_platform()
    {
        $this->givenUnsupportedDatabasePlatformWasEnabled();

        BackupFactory::instance($this->entityManager->reveal());
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function it_can_not_instance_of_sqlite_in_memory_backup()
    {
        $this->givenSqliteInMemoryDatabasePlatformWasEnabled();

        BackupFactory::instance($this->entityManager->reveal());
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function it_can_not_create_backup_with_mysql_platform_without_dbname_provided()
    {
        $this->givenMySqlWithoutDbnameDatabasePlatformWasEnabled();

        BackupFactory::instance($this->entityManager->reveal());
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
