<?php

namespace Lucaszz\DoctrineDatabaseBackup\tests\Backup\Executor;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use Lucaszz\DoctrineDatabaseBackup\Backup\DoctrineDatabaseBackup;
use Lucaszz\DoctrineDatabaseBackup\tests\Integration\Entity\TestProduct;

class ExampleTest extends \PHPUnit_Framework_TestCase
{
    /** @var EntityManager */
    private $entityManager;

    public function testThatDatabaseContainsProduct()
    {
        $this->entityManager->persist(new TestProduct('Teapot', 25));
        $this->entityManager->flush();

        $this->assertCount(1, $this->entityManager->getRepository('\Lucaszz\DoctrineDatabaseBackup\tests\Integration\Entity\TestProduct')->findAll());
    }

    public function testThatDatabaseIsClearBeforeNextTest()
    {
        $this->entityManager->persist(new TestProduct('Iron', 99));
        $this->entityManager->flush();

        $this->assertCount(1, $this->entityManager->getRepository('\Lucaszz\DoctrineDatabaseBackup\tests\Integration\Entity\TestProduct')->findAll());
    }

    /**
     * Before first test of PHP process database should be created.
     */
    public static function setUpBeforeClass()
    {
        $entityManager = self::createEntityManager();
        self::setupDatabase($entityManager);
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->entityManager = $this->createEntityManager();

        $backup = new DoctrineDatabaseBackup($this->entityManager);

        if (!$backup->isCreated()) {
            $backup->clearDatabase();
            $backup->create();
        }

        $backup->restore();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        $this->entityManager = null;
    }

    /**
     * Example of creating EntityManager.
     */
    private static function createEntityManager()
    {
        $entityPath = array(__DIR__.'/Entity');

        $config = Setup::createAnnotationMetadataConfiguration($entityPath, false);
        $driver = new AnnotationDriver(new AnnotationReader(), $entityPath);
        AnnotationRegistry::registerLoader('class_exists');
        $config->setMetadataDriverImpl($driver);

        return EntityManager::create(self::getParams(), $config);
    }

    /**
     * Example of setup database before test.
     */
    private function setupDatabase(EntityManager $entityManager)
    {
        $params = self::getParams();

        $tmpConnection = DriverManager::getConnection($params);
        $tmpConnection->getSchemaManager()->createDatabase($params['path']);

        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->dropDatabase();

        $class = 'Lucaszz\DoctrineDatabaseBackup\tests\Integration\Entity\TestProduct';

        $schemaTool->createSchema(array($entityManager->getClassMetadata($class)));
    }

    /**
     * Example of Database connection parameters.
     */
    private static function getParams()
    {
        return array(
            'driver' => 'pdo_sqlite',
            'user' => 'root',
            'password' => '',
            'path' => __DIR__.'/database/sqlite.db',
        );
    }
}