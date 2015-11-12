<?php

namespace Lucaszz\DoctrineDatabaseBackup\tests;

use Lucaszz\DoctrineDatabaseBackup\Command;

class CommandTest extends \PHPUnit_Framework_TestCase
{
    /** @var Command */
    private $command;

    /** @test */
    public function it_returns_output_of_command_was_executed_with_success()
    {
        $this->assertEquals("success\n", $this->command->run("echo 'success'"));
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function it_fails_when_command_failed()
    {
        $this->command->run('/bin/false');
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->command = new Command();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        $this->command = null;
    }
}
