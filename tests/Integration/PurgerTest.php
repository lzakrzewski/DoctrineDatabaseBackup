<?php

namespace Lucaszz\DoctrineDatabaseBackup\tests\Backup\Executor;

use Lucaszz\DoctrineDatabaseBackup\Backup\Purger;
use Lucaszz\DoctrineDatabaseBackup\tests\Integration\Dictionary\SqliteDictionary;
use Lucaszz\DoctrineDatabaseBackup\tests\Integration\IntegrationTestCase;

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

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->purger = new Purger($this->entityManager);
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