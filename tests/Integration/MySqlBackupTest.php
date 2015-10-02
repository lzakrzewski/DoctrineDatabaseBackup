<?php

namespace Lucaszz\DoctrineDatabaseBackup\tests\Integration;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Tools\SchemaTool;
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

        $this->backup = new DoctrineDatabaseBackup($this->entityManager->getConnection());
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
            'dbname' => 'doctrine-database-test',
        );
    }

    /**
     * @todo extract drop && create db methods
     */
    protected function setupDatabase()
    {
        $params = $this->getParams();
        $name = $params['dbname'];

        unset($params['dbname']);

        $tmpConnection = DriverManager::getConnection($params);
        $nameEscaped = $tmpConnection->getDatabasePlatform()->quoteSingleIdentifier($name);

        if (in_array($name, $tmpConnection->getSchemaManager()->listDatabases())) {
            $tmpConnection->getSchemaManager()->dropDatabase($nameEscaped);
        }

        $tmpConnection->getSchemaManager()->createDatabase($nameEscaped);

        $class = $this->productClass();
        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->createSchema(array($this->entityManager->getClassMetadata($class)));
    }

    private function givenDatabaseIsClear()
    {
    }
}
