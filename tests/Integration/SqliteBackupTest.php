<?php

namespace Lucaszz\DoctrineDatabaseBackup\tests\Integration;

use Lucaszz\DoctrineDatabaseBackup\Backup\DoctrineDatabaseBackup;
use Lucaszz\DoctrineDatabaseBackup\Backup\Executor\SqliteExecutor;
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

        $this->backup->getExecutor()->create();
        $this->addProduct();

        $this->backup->restoreClearDatabase();

        $this->assertThatDatabaseIsClear();
    }

    /** @test */
    public function it_can_restore_database_with_data()
    {
        $this->givenDatabaseContainsProducts(5);

        $this->backup->getExecutor()->create();
        $this->addProduct();

        $this->backup->getExecutor()->restore();

        $this->assertThatDatabaseContainProducts(5);
    }

    /** @test */
    public function it_can_clear_database()
    {
        $this->givenDatabaseContainsProducts(5);

        $this->backup->restoreClearDatabase();

        $this->assertThatDatabaseIsClear();
    }

    /** @test */
    public function it_confirms_that_backup_was_created()
    {
        $this->backup->getExecutor()->create();

        $this->assertTrue($this->backup->getExecutor()->isBackupCreated());
    }

    /** @test */
    public function it_confirms_that_backup_was_not_created()
    {
        $this->assertFalse($this->backup->getExecutor()->isBackupCreated());
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
        SqliteExecutor::clearMemory();
    }
}
