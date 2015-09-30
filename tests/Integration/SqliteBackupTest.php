<?php

namespace Lucaszz\DoctrineDatabaseBackup\tests\Integration;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use Lucaszz\DoctrineDatabaseBackup\Backup\Backup;

class SqliteBackupTest extends IntegrationTestCase
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

        $this->backup = new Backup($this->entityManager->getConnection());
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
            'driver' => 'pdo_sqlite',
            'user' => 'root',
            'password' => '',
            'path' => __DIR__.'/database/sqlite.db',
        );
    }

    protected function setupDatabase()
    {
        $params = $this->getParams();

        $tmpConnection = DriverManager::getConnection($params);
        $tmpConnection->getSchemaManager()->createDatabase($params['path']);

        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->dropDatabase();

        $class = $this->productClass();
        $schemaTool->createSchema(array($this->entityManager->getClassMetadata($class)));
    }

    private function givenDatabaseIsClear()
    {
    }

    private function addProduct()
    {
        $this->entityManager->persist($this->productInstance());
        $this->entityManager->flush();
    }

    private function assertThatDatabaseIsClear()
    {
        $this->assertEmpty($this->entityManager->getRepository(get_class($this->productInstance()))->findAll());
    }
}
