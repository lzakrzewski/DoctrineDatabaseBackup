<?php

namespace Lucaszz\DoctrineDatabaseBackup\tests\Integration;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Tools\SchemaTool;

class SqliteBackupTest extends BackupTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getParams()
    {
        return array(
            'driver' => 'pdo_sqlite',
            'user' => 'root',
            'password' => '',
            'path' => __DIR__.'/database/sqlite.db',
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function setupDatabase()
    {
        $params = $this->getParams();

        $tmpConnection = DriverManager::getConnection($params);
        $tmpConnection->getSchemaManager()->createDatabase($params['path']);

        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->dropDatabase();

        $class = $this->productClass();
        $schemaTool->createSchema(array($this->entityManager->getClassMetadata($class)));
    }
}
