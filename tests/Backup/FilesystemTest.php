<?php

namespace Lucaszz\DoctrineDatabaseBackup\tests\Backup;

use Lucaszz\DoctrineDatabaseBackup\Backup\Filesystem;
use org\bovigo\vfs\vfsStream;

class FilesystemTest extends \PHPUnit_Framework_TestCase
{
    /** @var Filesystem */
    private $filesystem;

    /** @test */
    public function it_creates_directory()
    {
        $this->filesystem->prepareDir('vfs://project/dir');

        $this->assertThatDirectoryExists('vfs://project/dir');
    }

    /** @test */
    public function it_cleans_directory()
    {
        $this->givenGarbageFileExistsInDirectory('vfs://project/dir');

        $this->filesystem->prepareDir('vfs://project/dir');

        $this->assertThatDirectoryIsEmpty('vfs://project/dir');
    }

    /** @test */
    public function it_copies()
    {
        $this->givenFileExists('vfs://project/source.db');

        $this->filesystem->copy('vfs://project/source.db', 'vfs://project/destination.db');

        $this->assertThatFileExists('vfs://project/destination.db');
    }

    /** @test */
    public function it_can_copy_twice()
    {
        $this->givenFileExists('vfs://project/source.db');

        $this->filesystem->copy('vfs://project/source.db', 'vfs://project/destination.db');
        $this->filesystem->copy('vfs://project/source.db', 'vfs://project/destination.db');

        $this->assertThatFileExists('vfs://project/destination.db');
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
     * @test
     *
     * @expectedException \RuntimeException
     */
    public function it_fails_during_creating_copy_when_source_file_does_not_exist()
    {
        $this->filesystem->copy('vfs://project/non-existing.db', 'vfs://project/sqlite.db');
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

    private function givenGarbageFileExistsInDirectory($directory)
    {
        mkdir($directory);

        file_put_contents($directory.'/garbage', 'garbage-garbage');
    }

    private function givenFileExists($fileName)
    {
        file_put_contents($fileName, 'contents');
    }

    private function assertThatDirectoryExists($directory)
    {
        $this->assertTrue(file_exists($directory));
    }

    private function assertThatFileExists($fileName)
    {
        $this->assertTrue(file_exists($fileName));
    }

    private function assertThatDirectoryIsEmpty($directory)
    {
        $this->assertCount(0, array_diff(scandir($directory), array('.', '..')));
    }
}
