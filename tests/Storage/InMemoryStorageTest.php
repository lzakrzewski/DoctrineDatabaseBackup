<?php

namespace Lucaszz\DoctrineDatabaseBackup\tests\Storage;

use Lucaszz\DoctrineDatabaseBackup\Storage\InMemoryStorage;

class InMemoryStorageTest extends \PHPUnit_Framework_TestCase
{
    /** @var InMemoryStorage */
    private $storage;

    /** @test */
    public function it_is_singleton()
    {
        $this->assertSame(InMemoryStorage::instance(), $this->storage);
    }

    /** @test */
    public function it_reads()
    {
        $value = new \stdClass();

        $this->storage->put('test', $value);

        $this->assertSame($value, $this->storage->read('test'));
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function it_fails_when_it_has_not_object()
    {
        $this->storage->read('test');
    }

    /** @test */
    public function it_has_object()
    {
        $value = new \stdClass();

        $this->storage->put('test', $value);

        $this->assertTrue($this->storage->has('test'));
    }

    /** @test */
    public function it_has_not_shared_value_by_default()
    {
        $this->assertFalse($this->storage->has('test'));
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function it_can_be_cleared()
    {
        $value = new \stdClass();
        $this->storage->put('test', $value);
        $this->storage->clear();

        $this->storage->read('test');
    }

    /** {@inheritdoc} */
    protected function setUp()
    {
        $this->storage = InMemoryStorage::instance();
        $this->storage->clear();
    }

    /** {@inheritdoc} */
    protected function tearDown()
    {
        $this->storage = null;
    }
}
