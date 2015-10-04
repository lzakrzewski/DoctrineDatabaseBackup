<?php

namespace Lucaszz\DoctrineDatabaseBackup\tests\Integration;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Tools\Setup;
use Lucaszz\DoctrineDatabaseBackup\Backup\DoctrineDatabaseBackup;
use Lucaszz\DoctrineDatabaseBackup\tests\Integration\Entity\TestProduct;

abstract class BackupTestCase extends \PHPUnit_Framework_TestCase
{
    /** @var EntityManager */
    protected $entityManager;
    /** @var EntityRepository */
    private $repository;
    /** @var DoctrineDatabaseBackup */
    private $backup;

    /** @test */
    public function it_can_restore_clear_database()
    {
        $this->givenDatabaseIsClear();

        $this->backup->create();
        $this->addProduct();

        $this->backup->restore();

        $this->assertThatDatabaseIsClear();
    }

    /** @test */
    public function it_can_restore_database_with_data()
    {
        $this->givenDatabaseContainsProducts(5);

        $this->backup->create();
        $this->addProduct();

        $this->backup->restore();

        $this->assertThatDatabaseContainProducts(5);
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->entityManager = $this->createEntityManager();

        $this->setupDatabase();

        $this->repository = $this->entityManager->getRepository($this->productClass());
        $this->backup = new DoctrineDatabaseBackup($this->entityManager->getConnection());
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        $this->entityManager = null;
        $this->repository = null;
        $this->backup = null;
    }

    /**
     * @return EntityManager
     */
    private function createEntityManager()
    {
        $entityPath = array(__DIR__.'/Entity');

        $config = Setup::createAnnotationMetadataConfiguration($entityPath, false);
        $driver = new AnnotationDriver(new AnnotationReader(), $entityPath);
        AnnotationRegistry::registerLoader('class_exists');
        $config->setMetadataDriverImpl($driver);

        return EntityManager::create($this->getParams(), $config);
    }

    /**
     * @return TestProduct
     */
    private function productInstance()
    {
        return new TestProduct(uniqid(), rand(1, 1000) / 3);
    }

    /**
     * @return string
     */
    protected function productClass()
    {
        return get_class($this->productInstance());
    }

    private function addProduct()
    {
        $this->entityManager->persist($this->productInstance());
        $this->entityManager->flush();
    }

    private function givenDatabaseIsClear()
    {
    }

    private function givenDatabaseContainsProducts($productsCount)
    {
        $this->givenDatabaseIsClear();

        for ($productCount = 1; $productCount <= $productsCount; ++$productCount) {
            $this->addProduct();
        }
    }

    private function assertThatDatabaseContainProducts($expectedProductsCount)
    {
        $this->assertCount($expectedProductsCount, $this->repository->findAll());
    }

    private function assertThatDatabaseIsClear()
    {
        $this->assertEmpty($this->repository->findAll());
    }

    /**
     * @return array
     */
    abstract protected function getParams();

    abstract protected function setupDatabase();
}
