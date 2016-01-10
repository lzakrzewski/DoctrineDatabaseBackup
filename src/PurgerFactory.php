<?php

namespace Lucaszz\DoctrineDatabaseBackup;

use Doctrine\ORM\EntityManager;
use Lucaszz\DoctrineDatabaseBackup\Storage\InMemoryStorage;

final class PurgerFactory
{
    public static function instance(EntityManager $entityManager)
    {
        return new Purger($entityManager, InMemoryStorage::instance());
    }

    private function __construct()
    {
    }
}
