<?php
namespace Visit\Check\Commands;

use Visit\Check\Command;
use Humbug\SelfUpdate\Updater;

class Rollback extends Command
{
    public $essential = false;
    public $display = 18;
    public $name = 'Rollback';
    public $description = 'Rollback to a previous version, if available.';

    /**
     * @param array $args
     * @throws \Exception
     */
    public function fire(array $args = [])
    {
        $updater = new Updater();
        $result = $updater->rollback();
        if (!$result) {
            // report failure!
            echo 'Rollback was not successfully.' . "\n";
            return false;
        }

        return true;
    }
}