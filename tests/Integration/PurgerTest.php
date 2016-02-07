<?php

namespace Lzakrzewski\DoctrineDatabaseBackup\tests\Integration;

use Lzakrzewski\DoctrineDatabaseBackup\Purger;
use Lzakrzewski\DoctrineDatabaseBackup\Storage\InMemoryStorage;
use Lzakrzewski\DoctrineDatabaseBackup\tests\Integration\Dictionary\SqliteDictionary;

class PurgerTest extends IntegrationTestCase
{
    use SqliteDictionary;

    /** @var Purger */
    private $purger;

    /** @test */
    public function it_purges_database()
    {
        $this->givenDatabaseContainsProducts(5);

        $this->purger->purge();

        $this->assertThatDatabaseIsClear();
    }

    /** @test */
    public function it_purges_database_twice_with_cached_sql()
    {
        $this->givenDatabaseContainsProducts(5);

        $this->purger->purge();

        $this->addProduct();

        $this->purger->purge();

        $this->assertThatDatabaseIsClear();
    }

    /** @test */
    public function it_purges_database_with_related_entities()
    {
        $this->givenDatabaseContainsProducts(5);
        $this->givenDatabaseContainsCategories(5);

        $this->purger->purge();

        $this->assertThatDatabaseIsClear();
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        InMemoryStorage::instance()->clear();

        $this->purger = new Purger($this->entityManager, InMemoryStorage::instance());
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        $this->purger = null;

        parent::tearDown();
    }
}
