<?php

namespace Lzakrzewski\DoctrineDatabaseBackup\tests\Integration;

use Doctrine\ORM\EntityManager;
use Lzakrzewski\DoctrineDatabaseBackup\DoctrineDatabaseBackup;
use Lzakrzewski\DoctrineDatabaseBackup\Storage\InMemoryStorage;
use Lzakrzewski\DoctrineDatabaseBackup\tests\Integration\Dictionary\SqliteDictionary;

class SqliteBackupTest extends IntegrationTestCase
{
    use SqliteDictionary;

    /** @var DoctrineDatabaseBackup */
    private $backup;

    /** @test */
    public function it_can_restore_clear_database()
    {
        $this->givenDatabaseIsClear();

        $this->backup->getBackup()->create();
        $this->addProduct();

        $this->backup->restore();

        $this->assertThatDatabaseIsClear();
    }

    /** @test */
    public function it_can_restore_database_with_data()
    {
        $this->givenDatabaseContainsProducts(5);

        $this->backup->getBackup()->create();
        $this->addProduct();

        $this->backup->getBackup()->restore();

        $this->assertThatDatabaseContainProducts(5);
    }

    /** @test */
    public function it_can_restore_database_with_callback()
    {
        $this->givenDatabaseIsClear();

        $this->backup->restore(function (EntityManager $entityManager) {
            $product1 = $this->productInstance();
            $product2 = $this->productInstance();

            $entityManager->persist($product1);
            $entityManager->persist($product2);

            $entityManager->flush();
        });

        $this->assertThatDatabaseContainProducts(2);
    }

    /** @test */
    public function it_can_clear_database()
    {
        $this->givenDatabaseContainsProducts(5);

        $this->backup->restore();

        $this->assertThatDatabaseIsClear();
    }

    /** @test */
    public function it_confirms_that_backup_was_created()
    {
        $this->backup->getBackup()->create();

        $this->assertTrue($this->backup->getBackup()->isBackupCreated());
    }

    /** @test */
    public function it_confirms_that_backup_was_not_created()
    {
        $this->assertFalse($this->backup->getBackup()->isBackupCreated());
    }

    /** {@inheritdoc} */
    protected function setUp()
    {
        parent::setUp();

        $this->backup = new DoctrineDatabaseBackup($this->entityManager);

        $this->givenMemoryIsClear();
    }

    /** {@inheritdoc} */
    protected function tearDown()
    {
        $this->backup = null;

        parent::tearDown();
    }

    private function givenMemoryIsClear()
    {
        InMemoryStorage::instance()->clear();
    }
}
