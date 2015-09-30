<?php

namespace Lucaszz\DoctrineDatabaseBackup\tests\Integration;

use Lucaszz\DoctrineDatabaseBackup\Backup\Backup;
use Lucaszz\DoctrineDatabaseBackup\Backup\DoctrineDatabaseBackup;

class MySqlBackupTest extends IntegrationTestCase
{
    /** @var Backup */
    private $backup;

    /** @test */
    public function it_can_restore_database()
    {
        $this->givenDatabaseIsClear();

        $this->backup->create();
        $this->addProduct();

        $this->backup->restore();

        $this->assertThatDatabaseIsClear();
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->setupDatabase();

        $connection = $this->prophesize('\Doctrine\DBAL\Connection');
        $mysqlPlatform = $this->prophesize('\Doctrine\DBAL\Platforms\MySqlPlatform');
        $connection->getParams()->willReturn(array('dbname' => 'test-dummy'));
        $connection->getDatabasePlatform()->willReturn($mysqlPlatform->reveal());

        $this->backup = new DoctrineDatabaseBackup($connection->reveal());
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        $this->backup = null;

        parent::tearDown();
    }

    /**
     * {@inheritdoc}
     */
    protected function getParams()
    {
        return array(
            'driver' => 'pdo_mysql',
            'user' => 'root',
            'password' => '',
            'dbname' => 'test',
        );
    }

    protected function setupDatabase()
    {
        //Todo not implemented yet
    }

    private function givenDatabaseIsClear()
    {
    }

    protected function addProduct()
    {
        //Todo not implemented yet
    }

    protected function assertThatDatabaseIsClear()
    {
        //Todo not implemented yet
    }
}
