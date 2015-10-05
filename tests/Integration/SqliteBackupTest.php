<?php

namespace Lucaszz\DoctrineDatabaseBackup\tests\Integration;

use Lucaszz\DoctrineDatabaseBackup\Backup\DoctrineDatabaseBackup;
use Lucaszz\DoctrineDatabaseBackup\tests\Integration\Dictionary\SqliteDictionary;

class SqliteBackupTest extends IntegrationTestCase
{
    use SqliteDictionary;

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

    /** @test */
    public function it_can_clear_database()
    {
        $this->givenDatabaseContainsProducts(5);

        $this->backup->clearDatabase();

        $this->assertThatDatabaseIsClear();
    }

    /** @test */
    public function it_confirms_that_backup_was_created()
    {
        $this->backup->create();

        $this->assertTrue($this->backup->isCreated());
    }

    /** @test */
    public function it_confirms_that_backup_was_not_created()
    {
        $this->assertFalse($this->backup->isCreated());
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->backup = new DoctrineDatabaseBackup($this->entityManager);

        $this->refreshSqliteExecutor();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        $this->backup = null;

        parent::tearDown();
    }

    private function refreshSqliteExecutor()
    {
        $executorClass = '\Lucaszz\DoctrineDatabaseBackup\Backup\Executor\SqliteExecutor';
        $reflection = new \ReflectionClass($executorClass);
        $property = $reflection->getProperty('contents');
        $property->setAccessible(true);

        $property->setValue(null);
        $property->setAccessible(false);
    }
}
