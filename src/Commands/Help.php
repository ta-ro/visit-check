<?php
namespace Visit\Check\Commands;

use Visit\Check\Command;

class Help extends Command
{
    public $essential = false;
    
    public $display = 1;
    
    public $name = 'Command Reference';
    
    public $description = 'Display information about visit command line options.';
    
    public $showAll = true;
    
    private $label = [
        'topCommands' => 'Popular Commands:',
        'allCommands' => 'All Commands:'
    ];
    
    private $commands = [];

    public function __construct(array $commands = [])
    {
        $this->commands = $commands;
    }

    /**
     * Execute the help command
     *
     * @param array $args - CLI arguments
     * @echo
     * @return bool
     * @throws \Exception
     */
    public function fire(array $args = [])
    {
        $HTAB = str_repeat(' ', (int) ceil(self::TAB_SIZE / 2));
        echo "\n", $HTAB, $this->colorize()['blue'], "visit v". $this->getVersion(), $this->colorize()[''], "\n\n";

        $command = !empty($args[0]) ? $args[0] : null;
        if (!empty($command)) {
            if (empty($this->commands[$command])) {
                throw new \Exception('Command ' . $command . ' not found!');
            }

            $com = $this->getCommandObject($this->commands[$command]);
            $com->usageInfo($args);
            echo $this->colorize()[''];
            return true;
        }

        $this->usageInfo($args);
        $w = $this->getScreenSize()['width'];
        echo "\n", str_repeat('_', $w - 1), "\n", $this->colorize()[''];
        
        return true;
    }

    /**
     * Display the main help menu
     *
     * @echo
     * @return void
     */
    public function helpMenu()
    {
        $essential = [];
        $coms = [];
        $columns = [8, 4, 11];

        foreach ($this->commands as $i => $name) {
            if (strlen($i) > $columns[0]) {
                $columns[0] = strlen($i);
            }

            if ($name === 'Help') {
                if (strlen($this->name) > $columns[1]) {
                    $columns[1] = strlen($this->name);
                }
                if (strlen($this->description) > $columns[2]) {
                    $columns[2] = strlen($this->description);
                }

                $coms[$i] = [
                    'name' => $this->name,
                    'description' => $this->description,
                    'display' => $this->display
                ];
            } else {
                $com = $this->getCommandObject($name);
                if (strlen($com->name) > $columns[1]) {
                    $columns[1] = strlen($com->name);
                }

                // $descr is just for length calculations
                // $details is with the tag
                $descr = $com->description;
                $details = $com->description;
                if (!empty($com->tag['text'])) {
                    $descr = '['. $com->tag['text'] . '] ' . $descr;
                    $details = $this->colorize()[$com->tag['color']] .
                        '[' .
                        $com->tag['text'] .
                        ']' .
                        $this->c()[''] .
                        ' ' .
                        $com->description;
                }
                if (strlen($descr) > $columns[2]) {
                    $columns[2] = strlen($descr);
                }

                if ($com->essential) {
                    $essential[$i] = [
                        'name' => $com->name,
                        'description' => $details,
                        'display' => $com->display
                    ];
                }

                $coms[$i] = [
                    'name' => $com->name,
                    'description' => $details,
                    'display' => $com->display
                ];
                unset($com);
            }
        }

        //uasort($essential, [$this, 'sortCommands']);
        //uasort($essential);
        //uasort($coms, [$this, 'sortCommands']);
        //uasort($coms);

        $width = $this->getScreenSize()['width'];

        // $desiredWidth = array_sum($columns) + (3 * self::TAB_SIZE);
        $wrap = $width - $columns[1] - $columns[0] - (3 * self::TAB_SIZE) - 1;

        // Prevent wrapping because of newline characters
        --$columns[2];

        $repeatPad = str_repeat(' ', $columns[0] + $columns[1] + (3 * self::TAB_SIZE));
        $TAB = str_repeat(' ', self::TAB_SIZE);
        $HTAB = str_repeat(' ', (int) ceil(self::TAB_SIZE / 2));

        $header = $this->colorize()['blue'].
            $TAB .
            str_pad('Command', $columns[0], ' ', STR_PAD_RIGHT) .
            $TAB .
            str_pad('Name', $columns[1], ' ', STR_PAD_RIGHT) .
            $TAB .
            'Description' .
            $this->colorize()[''] .
            "\n" .
            $TAB . str_repeat('=', $width - self::TAB_SIZE - 1) . "\n";

        echo $this->colorize()[''], $HTAB, "Usage:\n";
        echo $TAB, $this->colorize()['cyan'], "visit [command]", $this->colorize()[''], "\n";
        echo $TAB, $HTAB, "Run the command.";
        echo "\n\n";
        echo $TAB, $this->colorize()['cyan']."visit help [command]", $this->colorize()[''], "\n";
        echo $TAB, $HTAB, "Display usage information for a specific command.";
        echo "\n\n";
        echo $HTAB, $this->label['topCommands'], "\n";
        echo $header;

        $newline = false;
        foreach ($essential as $k => $com) {
            if ($newline) {
                echo "\n", $TAB, str_repeat('-', $width - self::TAB_SIZE - 1), "\n";
            }
            
            echo $TAB;
            echo $this->colorize()['green'] . str_pad($k, $columns[0], ' ', STR_PAD_RIGHT) . $this->colorize()[''];
            echo $TAB;
            echo str_pad($com['name'], $columns[1], ' ', STR_PAD_RIGHT);
            echo $TAB;
            echo wordwrap($com['description'], $wrap, "\n" . $repeatPad, true);
            
            $newline = true;
        }

        if (!$this->showAll) {
            echo "\n\n", $HTAB, 'To view all of the available commands, run this command: ';
            echo $this->colorize()['cyan'], 'visit help', $this->colorize()[''];
            
            return;
        }

        echo "\n\n", $HTAB, $this->label['allCommands'], "\n";
        echo $header;

        $nl = false;
        foreach ($coms as $k => $com) {
            if ($nl) {
                echo "\n", $TAB, str_repeat('-', $width - self::TAB_SIZE - 1), "\n";
            }
            echo $TAB;
            echo $this->colorize()['green'], str_pad($k, $columns[0], ' ', STR_PAD_RIGHT), $this->colorize()[''];
            echo $TAB;
            echo str_pad($com['name'], $columns[1], ' ', STR_PAD_RIGHT);
            echo $TAB;
            echo wordwrap($com['description'], $wrap, "\n" . $repeatPad, true);
            
            $nl = true;
        }
    }

    /**
     * Used for uasort() calls in this class
     *
     * @param array $a
     * @param array $b
     * @return int
     */
    /*
    public function sortCommands(array $a, array $b)
    {
        if ($a['display'] > $b['display']) {
            return 1;
        }
        if ($a['display'] < $b['display']) {
            return -1;
        }
        return (int) ($a['name'] <=> $b['name']);
    }
    */

    /**
     * Display the usage information for this command.
     *
     * @param array $args - CLI arguments
     * @echo
     * @return void
     */
    public function usageInfo(array $args = [])
    {
        if (count($args) == 0) {
            $this->helpMenu();
            return;
        }

        if (strtolower($args[0]) !== 'help') {
            foreach ($this->commands as $i => $name) {
                if (strtolower($args[0]) === $i) {
                    $com = $this->getCommandObject($name);
                    $com->usageInfo(array_values(array_slice($args, 1)));
                    return;
                }
            }
        }

        // Now let's actually print the usage info for this class
        $TAB = str_repeat(' ', self::TAB_SIZE);
        $HTAB = str_repeat(' ', (int) ceil(self::TAB_SIZE / 2));

        echo $HTAB, $this->name, "\n";
        echo $TAB, $this->description, "\n\n";
        echo $HTAB, "How to use this command:\n";
        //echo $TAB, $this->c['cyan'], "visit ", $this->c[''], "\n";
        echo $TAB, $this->colorize()['cyan'], "visit help", $this->colorize()[''], "\n";
        echo $TAB, $HTAB, "List all of the commands available to hangar.";
        echo "\n";
        echo $TAB, $this->colorize()['cyan']."visit help [command]", $this->colorize()[''], "\n";
        echo $TAB, $HTAB, "Display usage information for a specific command.";
        echo "\n";
        echo "\n";
    }
}