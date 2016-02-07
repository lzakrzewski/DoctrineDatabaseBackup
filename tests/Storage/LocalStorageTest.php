<?php

namespace Lzakrzewski\DoctrineDatabaseBackup\tests\Storage;

use Lzakrzewski\DoctrineDatabaseBackup\Storage\LocalStorage;
use org\bovigo\vfs\vfsStream;

class LocalStorageTest extends \PHPUnit_Framework_TestCase
{
    /** @var LocalStorage */
    private $storage;

    /** @test */
    public function it_can_read_file()
    {
        $this->givenFileExists('vfs://project/source.db', 'contents');

        $this->assertEquals('contents', $this->storage->read('vfs://project/source.db'));
    }

    /**
     * @test
     *
     * @expectedException \RuntimeException
     */
    public function it_fails_during_reading_when_file_does_not_exists()
    {
        $this->storage->read('vfs://project/source.db');
    }

    /** @test */
    public function it_can_write_new_file()
    {
        $this->storage->put('vfs://project/source.db', 'contents');

        $this->assertEquals('contents', $this->storage->read('vfs://project/source.db'));
    }

    /** @test */
    public function it_can_write_existing_file()
    {
        $this->givenFileExists('vfs://project/source.db', 'old-contents');

        $this->storage->put('vfs://project/source.db', 'contents');

        $this->assertEquals('contents', $this->storage->read('vfs://project/source.db'));
    }

    /**
     * @test
     *
     * @expectedException \RuntimeException
     */
    public function it_can_not_write_file_to_not_existing_directory()
    {
        $this->storage->put('vfs://project/not-existing/source.db', 'contents');
    }

    /** @test */
    public function it_checks_if_file_exists()
    {
        $this->givenFileExists('vfs://project/source.db');

        $this->assertTrue($this->storage->has('vfs://project/source.db'));
    }

    /** @test */
    public function it_checks_if_file_does_not_exists()
    {
        $this->assertFalse($this->storage->has('vfs://project/source.db'));
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        vfsStream::setup('project');

        $this->storage = new LocalStorage();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        $this->storage = null;
    }

    private function givenFileExists($fileName, $contents = 'contents')
    {
        file_put_contents($fileName, $contents);
    }
}
