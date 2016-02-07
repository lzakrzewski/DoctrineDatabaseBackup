<?php

namespace Lzakrzewski\DoctrineDatabaseBackup\tests;

use Doctrine\ORM\EntityManager;
use Lzakrzewski\DoctrineDatabaseBackup\PurgerFactory;
use Prophecy\Prophecy\ObjectProphecy;

class PurgerFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var EntityManager|ObjectProphecy  */
    private $entityManager;

    /** @test */
    public function it_creates_instance_of_purger()
    {
        $this->assertInstanceOf('\Lzakrzewski\DoctrineDatabaseBackup\Purger', PurgerFactory::instance($this->entityManager->reveal()));
    }

    /** {@inheritdoc} */
    protected function setUp()
    {
        $this->entityManager = $this->prophesize('\Doctrine\ORM\EntityManager');
    }

    /** {@inheritdoc} */
    protected function tearDown()
    {
        $this->entityManager = null;
    }
}
