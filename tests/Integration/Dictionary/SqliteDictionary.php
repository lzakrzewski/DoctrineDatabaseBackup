<?php

namespace Lucaszz\DoctrineDatabaseBackup\tests\Integration\Dictionary;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Tools\SchemaTool;

trait SqliteDictionary
{
    /**
     * @return array
     */
    protected function getParams()
    {
        return [
            'driver'   => 'pdo_sqlite',
            'user'     => 'root',
            'password' => '',
            'path'     => __DIR__.'/../database/sqlite.db',
        ];
    }

    protected function setupDatabase()
    {
        $params = $this->getParams();

        $tmpConnection = DriverManager::getConnection($params);
        $tmpConnection->getSchemaManager()->createDatabase($params['path']);

        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->dropDatabase();

        $productClass  = $this->productClass();
        $categoryClass = $this->categoryClass();

        $schemaTool->createSchema([
            $this->entityManager->getClassMetadata($productClass),
            $this->entityManager->getClassMetadata($categoryClass),
        ]);
    }
}
