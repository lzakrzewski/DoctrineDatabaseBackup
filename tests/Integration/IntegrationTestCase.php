<?php

namespace Lucaszz\DoctrineDatabaseBackup\tests\Integration;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Tools\Setup;
use Lucaszz\DoctrineDatabaseBackup\tests\Integration\Entity\TestProduct;

abstract class IntegrationTestCase extends \PHPUnit_Framework_TestCase
{
    /** @var EntityManager */
    protected $entityManager;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->entityManager = $this->createEntityManager();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        $this->entityManager = null;
    }

    /**
     * @return EntityManager
     */
    protected function createEntityManager()
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
        return new TestProduct('test', 99.99);
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

    protected function assertThatDatabaseIsClear()
    {
        $this->assertEmpty($this->entityManager->getRepository($this->productClass())->findAll());
    }

    /**
     * @return array
     */
    abstract protected function getParams();

    abstract protected function setupDatabase();
}
