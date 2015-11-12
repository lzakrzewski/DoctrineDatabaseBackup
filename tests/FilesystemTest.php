<?php

namespace Lucaszz\DoctrineDatabaseBackup\tests;

use Lucaszz\DoctrineDatabaseBackup\Filesystem;
use org\bovigo\vfs\vfsStream;

class FilesystemTest extends \PHPUnit_Framework_TestCase
{
    /** @var Filesystem */
    private $filesystem;

    /** @test */
    public function it_can_read_file()
    {
        $this->givenFileExists('vfs://project/source.db', 'contents');

        $this->assertEquals('contents', $this->filesystem->read('vfs://project/source.db'));
    }

    /**
     * @test
     *
     * @expectedException \RuntimeException
     */
    public function it_fails_during_reading_when_file_does_not_exists()
    {
        $this->filesystem->read('vfs://project/source.db');
    }

    /** @test */
    public function it_can_write_new_file()
    {
        $this->filesystem->write('vfs://project/source.db', 'contents');

        $this->assertEquals('contents', $this->filesystem->read('vfs://project/source.db'));
    }

    /** @test */
    public function it_can_write_existing_file()
    {
        $this->givenFileExists('vfs://project/source.db', 'old-contents');

        $this->filesystem->write('vfs://project/source.db', 'contents');

        $this->assertEquals('contents', $this->filesystem->read('vfs://project/source.db'));
    }

    /** @test */
    public function it_checks_if_file_exists()
    {
        $this->givenFileExists('vfs://project/source.db');

        $this->assertTrue($this->filesystem->exists('vfs://project/source.db'));
    }

    /** @test */
    public function it_checks_if_file_does_not_exists()
    {
        $this->assertFalse($this->filesystem->exists('vfs://project/source.db'));
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        vfsStream::setup('project');

        $this->filesystem = new Filesystem();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        $this->filesystem = null;
    }

    private function givenFileExists($fileName, $contents = 'contents')
    {
        file_put_contents($fileName, $contents);
    }
}
