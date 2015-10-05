# DoctrineDatabaseBackup

[![Build Status](https://travis-ci.org/Lucaszz/DoctrineDatabaseBackup.svg)](https://travis-ci.org/Lucaszz/DoctrineDatabaseBackup)

Description
--------

DoctrineDatabaseBackup is simple library for speed up tests in your app.
It could be used for **PHPUnit** tests or **Behat** tests running from command line.
My target was to avoid wasting time for dropping/creating or purging database for each test, so I optimized it.
From my own benchmarks I can see that DoctrineDatabaseBackup can reduce the time of execution tests up to 3 times.

Requirements
------------
```
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
- It can clear database in fast way,
- It can restore database from backup before every test,
- Because I decided to expose methods "isCreated", "create", "restore" and "clearDatabase" usages of this library can be very various.

Installation
--------
Require the library with composer:

```
composer require lucaszz/doctrine-database-backup "~1.0"
```

Usage (PHPUnit example)
--------
```
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->entityManager = $this->createEntityManager();

        $backup = new DoctrineDatabaseBackup($this->entityManager);

        if (!$backup->isCreated()) {
            $backup->clearDatabase();
            $backup->create();
        }

        $backup->restore();
    }
```
[Full working example](https://github.com/Lucaszz/DoctrineDatabaseBackup/blob/master/tests/Integration/ExampleTest.php)

**Notice that before first test of PHP process database should be created.**

Behat example
--------
```
    /**
     * @BeforeScenario
     */
    public function restoreDatabase()
    {
        // "getEntityManager" is your own getter for EntityManager
        $backup = new DoctrineDatabaseBackup($this->getEntityManager());

        if (!$backup->isCreated()) {
            $backup->clearDatabase();
            $backup->create();
        }

        $backup->restore();
    }
```
