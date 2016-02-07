<?php

/*
 * This class was copied from
 * https://github.com/doctrine/data-fixtures/blob/master/lib/Doctrine/Common/DataFixtures/Purger/ORMPurger.php
 * and modified to my needs. 
 * 
 * Thanks for:
 * <http://www.doctrine-project.org>
 * and Jonathan H. Wage <jonwage@gmail.com>, Benjamin Eberlei <kontakt@beberlei.de>
 */

namespace Lzakrzewski\DoctrineDatabaseBackup;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Internal\CommitOrderCalculator;
use Doctrine\ORM\Mapping\ClassMetadata;
use Lzakrzewski\DoctrineDatabaseBackup\Storage\InMemoryStorage;

class Purger
{
    const PURGER_KEY = 'purger';

    /** @var EntityManager */
    private $entityManager;
    /** @var InMemoryStorage */
    private $memoryStorage;

    /**
     * @param EntityManager   $entityManager
     * @param InMemoryStorage $memoryStorage
     */
    public function __construct(EntityManager $entityManager, InMemoryStorage $memoryStorage)
    {
        $this->entityManager = $entityManager;
        $this->memoryStorage = $memoryStorage;
    }

    public function purge()
    {
        if (false === $this->memoryStorage->has(self::PURGER_KEY)) {
            $this->memoryStorage->put(self::PURGER_KEY, $this->purgeSql());
        }

        $this->execute($this->memoryStorage->read(self::PURGER_KEY));
    }

    private function execute($sql)
    {
        $connection = $this->entityManager->getConnection();
        $connection->beginTransaction();

        $connection->exec($sql);

        $connection->commit();
    }

    private function purgeSql()
    {
        $sql       = '';
        $classes   = [];
        $metadatas = $this->entityManager->getMetadataFactory()->getAllMetadata();

        foreach ($metadatas as $metadata) {
            if (!$metadata->isMappedSuperclass && !(isset($metadata->isEmbeddedClass) && $metadata->isEmbeddedClass)) {
                $classes[] = $metadata;
            }
        }

        $commitOrder = $this->getCommitOrder($classes);

        // Get platform parameters
        $platform = $this->entityManager->getConnection()->getDatabasePlatform();

        // Drop association tables first
        $orderedTables = $this->getAssociationTables($commitOrder, $platform);

        // Drop tables in reverse commit order
        for ($i = count($commitOrder) - 1; $i >= 0; --$i) {
            $class = $commitOrder[$i];

            if (($class->isInheritanceTypeSingleTable() && $class->name != $class->rootEntityName) ||
                (isset($class->isEmbeddedClass) && $class->isEmbeddedClass) ||
                $class->isMappedSuperclass
            ) {
                continue;
            }

            $orderedTables[] = $this->getTableName($class, $platform);
        }

        foreach ($orderedTables as $tbl) {
            $sql .= sprintf('DELETE FROM %s;', $tbl);
        }

        return $sql;
    }

    private function getCommitOrder(array $classes)
    {
        $calc = new CommitOrderCalculator();

        foreach ($classes as $class) {
            $calc->addClass($class);

            // $class before its parents
            foreach ($class->parentClasses as $parentClass) {
                $parentClass = $this->entityManager->getClassMetadata($parentClass);

                if (!$calc->hasClass($parentClass->name)) {
                    $calc->addClass($parentClass);
                }

                $calc->addDependency($class, $parentClass);
            }

            foreach ($class->associationMappings as $assoc) {
                if ($assoc['isOwningSide']) {
                    $targetClass = $this->entityManager->getClassMetadata($assoc['targetEntity']);

                    if (!$calc->hasClass($targetClass->name)) {
                        $calc->addClass($targetClass);
                    }

                    // add dependency ($targetClass before $class)
                    $calc->addDependency($targetClass, $class);

                    // parents of $targetClass before $class, too
                    foreach ($targetClass->parentClasses as $parentClass) {
                        $parentClass = $this->entityManager->getClassMetadata($parentClass);

                        if (!$calc->hasClass($parentClass->name)) {
                            $calc->addClass($parentClass);
                        }

                        $calc->addDependency($parentClass, $class);
                    }
                }
            }
        }

        return $calc->getCommitOrder();
    }

    private function getAssociationTables(array $classes, AbstractPlatform $platform)
    {
        $associationTables = [];

        foreach ($classes as $class) {
            foreach ($class->associationMappings as $assoc) {
                if ($assoc['isOwningSide'] && $assoc['type'] == ClassMetadata::MANY_TO_MANY) {
                    $associationTables[] = $this->getJoinTableName($assoc, $class, $platform);
                }
            }
        }

        return $associationTables;
    }

    private function getTableName($class, $platform)
    {
        if (isset($class->table['schema']) && !method_exists($class, 'getSchemaName')) {
            return $class->table['schema'].'.'.$this->entityManager->getConfiguration()->getQuoteStrategy()->getTableName($class, $platform);
        }

        return $this->entityManager->getConfiguration()->getQuoteStrategy()->getTableName($class, $platform);
    }

    private function getJoinTableName($assoc, $class, $platform)
    {
        if (isset($assoc['joinTable']['schema']) && !method_exists($class, 'getSchemaName')) {
            return $assoc['joinTable']['schema'].'.'.$this->entityManager->getConfiguration()->getQuoteStrategy()->getJoinTableName($assoc, $class, $platform);
        }

        return $this->entityManager->getConfiguration()->getQuoteStrategy()->getJoinTableName($assoc, $class, $platform);
    }
}
