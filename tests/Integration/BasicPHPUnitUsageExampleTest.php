<?php

namespace Lucaszz\DoctrineDatabaseBackup\tests\Integration;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use Lucaszz\DoctrineDatabaseBackup\Backup\SqliteBackup;
use Lucaszz\DoctrineDatabaseBackup\DoctrineDatabaseBackup;
use Lucaszz\DoctrineDatabaseBackup\tests\Integration\Entity\TestProduct;

class BasicPHPUnitUsageExampleTest extends \PHPUnit_Framework_TestCase
{
    /** @var EntityManager */
    private $entityManager;

    public function testThatItAddsProduct()
    {
        $this->entityManager->persist(new TestProduct('Teapot', 25));
        $this->entityManager->flush();

        $this->assertCount(1, $this->entityManager->getRepository('\Lucaszz\DoctrineDatabaseBackup\tests\Integration\Entity\TestProduct')->findAll());
    }

    public function testThatDatabaseIsClear()
    {
        $this->assertCount(0, $this->entityManager->getRepository('\Lucaszz\DoctrineDatabaseBackup\tests\Integration\Entity\TestProduct')->findAll());
    }

    /**
     * Before first test of PHP process database should be created.
     */
    public static function setUpBeforeClass()
    {
        $entityManager = self::createEntityManager();
        self::setupDatabase($entityManager);

        //Should be called only if another test in current PHP process created backup.
        SqliteBackup::clearMemory();
    }

    /** {@inheritdoc} */
    protected function setUp()
    {
        parent::setUp();

        $this->entityManager = $this->createEntityManager();

        $backup = new DoctrineDatabaseBackup($this->entityManager);
        $backup->restoreClearDatabase();
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
        $entityPath = [__DIR__.'/Entity'];

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

        $class = 'Lucaszz\DoctrineDatabaseBackup\tests\Integration\Entity\TestProduct';

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
