<?php
namespace Visit\Check\Commands;

use Visit\Check\Command;
use Humbug\SelfUpdate\Updater;

class Update extends Command
{
    public $essential = false;

    //public $display = 1;

    public $name = 'Update';

    public $description = 'Updates the program paket if a new version is available.';

    public function fire(array $args = [])
    {
        $updater = new Updater();
        $updater->getStrategy()->setPharUrl('https://ta-ro.github.io/visit-check/visit.phar');
        $updater->getStrategy()->setVersionUrl('https://ta-ro.github.io/visit-check/visit.phar.version');
        try {
            $result = $updater->update();
            if (!$result) {
                // No update needed!
                echo 'No update needed.';
                return true;
            }

            $new = $updater->getNewVersion();
            $old = $updater->getOldVersion();
            printf('Updated from %s to %s', $old, $new);
            return true;
        } catch (\Exception $e) {
            // Report an error!
            echo $e->getMessage();
        }
        return false;
    }
}