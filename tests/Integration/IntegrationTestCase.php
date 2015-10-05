<?php

namespace Lucaszz\DoctrineDatabaseBackup\tests\Integration;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Tools\Setup;
use Lucaszz\DoctrineDatabaseBackup\tests\Integration\Entity\TestProduct;

abstract class IntegrationTestCase extends \PHPUnit_Framework_TestCase
{
    /** @var EntityManager */
    protected $entityManager;
    /** @var EntityRepository */
    protected $repository;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->entityManager = $this->createEntityManager();

        $this->setupDatabase();

        $this->repository = $this->entityManager->getRepository($this->productClass());
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        $this->entityManager = null;
        $this->repository = null;
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
    protected function productInstance()
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

    protected function addProduct()
    {
        $this->entityManager->persist($this->productInstance());
        $this->entityManager->flush();
    }

    protected function givenDatabaseIsClear()
    {
    }

    protected function givenDatabaseContainsProducts($productsCount)
    {
        $this->givenDatabaseIsClear();

        for ($productCount = 1; $productCount <= $productsCount; ++$productCount) {
            $this->addProduct();
        }
    }

    protected function assertThatDatabaseContainProducts($expectedProductsCount)
    {
        $this->assertCount($expectedProductsCount, $this->repository->findAll());
    }

    protected function assertThatDatabaseIsClear()
    {
        $this->assertEmpty($this->repository->findAll());
    }

    /**
     * @return array
     */
    abstract protected function getParams();

    abstract protected function setupDatabase();
}
