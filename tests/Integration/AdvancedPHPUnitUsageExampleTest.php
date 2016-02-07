<?php

namespace Lzakrzewski\DoctrineDatabaseBackup\tests\Integration;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use Lzakrzewski\DoctrineDatabaseBackup\DoctrineDatabaseBackup;
use Lzakrzewski\DoctrineDatabaseBackup\Storage\InMemoryStorage;
use Lzakrzewski\DoctrineDatabaseBackup\tests\Integration\Entity\Product\TestProduct;

class AdvancedPHPUnitUsageExampleTest extends \PHPUnit_Framework_TestCase
{
    /** @var EntityManager */
    private $entityManager;

    public function testThatItAddsProduct()
    {
        $this->entityManager->persist(new TestProduct('Teapot', 25));
        $this->entityManager->flush();

        $this->assertCount(2, $this->entityManager->getRepository('\Lzakrzewski\DoctrineDatabaseBackup\tests\Integration\Entity\Product\TestProduct')->findAll());
    }

    public function testThatDatabaseContainsFixtures()
    {
        $this->assertCount(1, $this->entityManager->getRepository('\Lzakrzewski\DoctrineDatabaseBackup\tests\Integration\Entity\Product\TestProduct')->findAll());
    }

    /**
     * Before first test of PHP process database should be created.
     */
    public static function setUpBeforeClass()
    {
        $entityManager = self::createEntityManager();
        self::setupDatabase($entityManager);

        //Should be called only if another test in current PHP process created backup.
        InMemoryStorage::instance()->clear();
    }

    /** {@inheritdoc} */
    protected function setUp()
    {
        parent::setUp();

        $this->entityManager = $this->createEntityManager();
        $backup              = new DoctrineDatabaseBackup($this->entityManager);

        if (!$backup->getBackup()->isBackupCreated()) {
            $backup->getPurger()->purge();

            //your fixtures
            $this->entityManager->persist(new TestProduct('Iron', 99));
            $this->entityManager->flush();

            $backup->getBackup()->create();
        }

        $backup->getBackup()->restore();
    }

    /** {@inheritdoc} */
    protected function tearDown()
    {
        $this->entityManager = null;
    }

    /**
     * Example of creating EntityManager.
     */
    private static function createEntityManager()
    {
        $entityPath = [__DIR__.'/Entity/Product'];

        $config = Setup::createAnnotationMetadataConfiguration($entityPath, false);
        $driver = new AnnotationDriver(new AnnotationReader(), $entityPath);
        AnnotationRegistry::registerLoader('class_exists');
        $config->setMetadataDriverImpl($driver);

        return EntityManager::create(self::getParams(), $config);
    }

    /**
     * Example of setup database before test.
     */
    private static function setupDatabase(EntityManager $entityManager)
    {
        $params = self::getParams();

        $tmpConnection = DriverManager::getConnection($params);
        $tmpConnection->getSchemaManager()->createDatabase($params['path']);

        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->dropDatabase();

        $class = 'Lzakrzewski\DoctrineDatabaseBackup\tests\Integration\Entity\Product\TestProduct';

        $schemaTool->createSchema([$entityManager->getClassMetadata($class)]);
    }

    /**
     * Example of Database connection parameters.
     */
    private static function getParams()
    {
        return [
            'driver'   => 'pdo_sqlite',
            'user'     => 'root',
            'password' => '',
            'path'     => __DIR__.'/database/sqlite.db',
        ];
    }
}
