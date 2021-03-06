# DoctrineDatabaseBackup

[![Build Status](https://travis-ci.org/lzakrzewski/DoctrineDatabaseBackup.svg)](https://travis-ci.org/lzakrzewski/DoctrineDatabaseBackup) [![Latest Stable Version](https://poser.pugx.org/lucaszz/doctrine-database-backup/v/stable)](https://packagist.org/packages/lucaszz/doctrine-database-backup) [![Total Downloads](https://poser.pugx.org/lucaszz/doctrine-database-backup/downloads)](https://packagist.org/packages/lucaszz/doctrine-database-backup) [![Coverage Status](https://coveralls.io/repos/lzakrzewski/DoctrineDatabaseBackup/badge.svg?branch=master&service=github)](https://coveralls.io/github/lzakrzewski/DoctrineDatabaseBackup?branch=master) 

DoctrineDatabaseBackup is simple library for speed up tests in your app.
It could be used for **PHPUnit** tests or **Behat** tests running from command line.
My target was to avoid wasting time for dropping/creating or purging database for each test, so I optimized it.

This library puts contents of database to memory and share it between every tests.

**Notice:** I don't recommend to use this library with large fixtures because it can cause huge memory usage.
I prefer to run tests with minimal database setup because it is more readable for me and it have better performance.

Requirements
------------
```json
  "require": {
    "php": ">=5.4",
    "doctrine/orm": "~2.3",
    "symfony/process": "~2.3"
  },
```

Features
--------
- It supports **SqlitePlatform** and **MySqlPlatform**,
- It can create database backup per PHP process,
- It can purge database in fast way,
- It can restore database from backup before every test,
- It can restore clear database before every test.

Installation
--------
Require the library with composer:

```sh
composer require lzakrzewski/doctrine-database-backup "~1.2"
```

Basic usage (PHPUnit example)
--------
```php
/** {@inheritdoc} */
protected function setUp()
{
    parent::setUp();

    $this->entityManager = $this->createEntityManager();

    $backup = new DoctrineDatabaseBackup($this->entityManager);
    $backup->restore();
}
```

This database setup prepares clear database before every test.
[Full working example](https://github.com/lzakrzewski/DoctrineDatabaseBackup/blob/master/tests/Integration/BasicPHPUnitUsageExampleTest.php).

Advanced usage (PHPUnit example)
--------
```php
/** {@inheritdoc} */
protected function setUp()
{
    parent::setUp();

    $this->entityManager = $this->createEntityManager();
    $backup = new DoctrineDatabaseBackup($this->entityManager);
    
    $backup->restore(function (EntityManager $entityManager) {
        //your fixtures
        $entityManager->persist(new TestProduct('Iron', 99));
        $entityManager->flush();
    });
}
```

This database setup database with your fixtures before every test.
[Full working example](https://github.com/lzakrzewski/DoctrineDatabaseBackup/blob/master/tests/Integration/AdvancedPHPUnitUsageExampleTest.php).

**Notice:** that before first test of PHP process database should be created.

Behat example
--------
```php
/** @BeforeScenario*/
public function restoreDatabase()
{
    // "getEntityManager" is your own getter for EntityManager
    $backup = new DoctrineDatabaseBackup($this->getEntityManager());
    $backup->restore();
}
```
