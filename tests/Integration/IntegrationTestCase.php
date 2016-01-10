<?php

namespace Lucaszz\DoctrineDatabaseBackup\tests\Integration;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Tools\Setup;
use Lucaszz\DoctrineDatabaseBackup\tests\Integration\Entity\Category\TestCategory;
use Lucaszz\DoctrineDatabaseBackup\tests\Integration\Entity\Product\TestProduct;

abstract class IntegrationTestCase extends \PHPUnit_Framework_TestCase
{
    /** @var EntityManager */
    protected $entityManager;
    /** @var EntityRepository */
    protected $products;
    /** @var EntityRepository */
    protected $categories;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->entityManager = $this->createEntityManager();

        $this->setupDatabase();

        $this->products   = $this->entityManager->getRepository($this->productClass());
        $this->categories = $this->entityManager->getRepository($this->categoryClass());
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        $this->entityManager = null;
        $this->products      = null;
        $this->categories    = null;
    }

    /**
     * @return EntityManager
     */
    private function createEntityManager()
    {
        $entityPath = [__DIR__.'/Entity'];

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
     * @return TestCategory
     */
    protected function categoryInstance()
    {
        return new TestCategory([]);
    }

    /**
     * @return string
     */
    protected function productClass()
    {
        return get_class($this->productInstance());
    }

    /**
     * @return string
     */
    protected function categoryClass()
    {
        return get_class($this->categoryInstance());
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

    protected function givenDatabaseContainsCategories($categoriesCount)
    {
        for ($categoryCount = 1; $categoryCount <= $categoriesCount; ++$categoryCount) {
            $this->entityManager->persist(new TestCategory($this->products->findAll()));
            $this->entityManager->flush();
        }
    }

    protected function assertThatDatabaseContainProducts($expectedProductsCount)
    {
        $this->assertCount($expectedProductsCount, $this->products->findAll());
    }

    protected function assertThatDatabaseIsClear()
    {
        $this->assertEmpty($this->products->findAll());
        $this->assertEmpty($this->categories->findAll());
    }

    /**
     * @return array
     */
    abstract protected function getParams();

    abstract protected function setupDatabase();
}
