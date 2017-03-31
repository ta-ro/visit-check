<?php
namespace Visit\Check\Commands;

use Visit\Check\Command;

class Test extends Command
{
    public $essential = true;

    public $display = 3;

    public $name = 'Test Check.';

    public $description = 'Only for testing commands.';

    public function fire(array $args = [])
    {
        print_r($args);

        /*
    	$input = $this->silentPrompt();
        echo $input;
        */

        return true;
    }
}