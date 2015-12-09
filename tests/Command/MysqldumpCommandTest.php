<?php

namespace Lucaszz\DoctrineDatabaseBackup\tests\Command;

use Lucaszz\DoctrineDatabaseBackup\Command\MysqldumpCommand;

class MysqldumpCommandTest extends \PHPUnit_Framework_TestCase
{
    /** @var MysqldumpCommand  */
    private $command;

    /** @test */
    public function it_can_call_full_command_to_dump_database()
    {
        $this->assertEquals(
            "mysqldump 'dbname' --no-create-info  --host='host' --user='user' --password='password'",
            $this->command->run()
        );
    }

    /** {@inheritdoc} */
    protected function setUp()
    {
        $this->command = new MysqldumpDummyCommand('dbname', 'host', 'user', 'password');
    }

    /** {@inheritdoc} */
    protected function tearDown()
    {
        $this->command = null;
    }
}
