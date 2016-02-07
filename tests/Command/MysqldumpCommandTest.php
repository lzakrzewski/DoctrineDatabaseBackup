<?php

namespace Lzakrzewski\DoctrineDatabaseBackup\tests\Command;

class MysqldumpCommandTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_can_call_full_command_to_dump_database()
    {
        $command = new MysqldumpDummyCommand('dbname', 'host', 'user', 'password');

        $this->assertEquals(
            "mysqldump 'dbname' --no-create-info  --host='host' --user='user' --password='password'",
            $command->run()
        );
    }

    /** @test */
    public function it_can_call_command_to_dump_database_without_password()
    {
        $command = new MysqldumpDummyCommand('dbname', 'host', 'user');

        $this->assertEquals(
            "mysqldump 'dbname' --no-create-info  --host='host' --user='user'",
            $command->run()
        );
    }

    /** @test */
    public function it_can_call_command_to_dump_database_without_password_and_user()
    {
        $command = new MysqldumpDummyCommand('dbname', 'host');

        $this->assertEquals(
            "mysqldump 'dbname' --no-create-info  --host='host'",
            $command->run()
        );
    }

    /** @test */
    public function it_can_call_command_to_dump_database_without_password_user_and_host()
    {
        $command = new MysqldumpDummyCommand('dbname');

        $this->assertEquals(
            "mysqldump 'dbname' --no-create-info ",
            $command->run()
        );
    }
}
