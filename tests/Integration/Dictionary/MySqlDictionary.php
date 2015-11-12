<?php

namespace Lucaszz\DoctrineDatabaseBackup\tests\Integration\Dictionary;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Tools\SchemaTool;

trait MySqlDictionary
{
    /**
     * @return array
     */
    protected function getParams()
    {
        return [
            'driver'   => 'pdo_mysql',
            'user'     => 'root',
            'password' => '',
            'dbname'   => 'doctrine-database-test',
        ];
    }

    protected function setupDatabase()
    {
        $params = $this->getParams();
        $name   = $params['dbname'];

        unset($params['dbname']);

        $tmpConnection = DriverManager::getConnection($params);
        $nameEscaped   = $tmpConnection->getDatabasePlatform()->quoteSingleIdentifier($name);

        if (in_array($name, $tmpConnection->getSchemaManager()->listDatabases())) {
            $tmpConnection->getSchemaManager()->dropDatabase($nameEscaped);
        }

        $tmpConnection->getSchemaManager()->createDatabase($nameEscaped);

        $class      = $this->productClass();
        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->createSchema([$this->entityManager->getClassMetadata($class)]);
    }
}
