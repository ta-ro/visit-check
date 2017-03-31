<?php

namespace Visit\Check;

abstract class Command
{
    const TAB_SIZE = 8;

    protected $version = '1.0.0';
    
    public $essential = false;

    public $display = 65535;

    public $name = 'CommandName';

    public $description = 'CLI description';

    public $tag = [
        'color' => '',
        'text' => ''
    ];

    /**
     * Cache references to other commands
     *
     * @var array
     */
    public static $cache = [];

    /**
     * Execute a command
     * @return bool
     */
    abstract public function fire(array $args = []);

    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Return the size of the current terminal window
     * @return array (int, int)
     */
    public function getScreenSize()
    {
        return [
            'width' => 80,
            'height' => 25
        ];
    }

    public function colorize()
    {
        if (preg_match('/^win/i', PHP_OS)) {
            return [
                '' => '',
                'red' => '',
                'green' => '',
                'blue' => '',
                'cyan' => '',
                'silver' => '',
                'yellow' => ''
            ];       
        } else {
            return [
                '' => "\033[0;39m",
                'red' => "\033[0;31m",
                'green' => "\033[0;32m",
                'blue' => "\033[1;34m",
                'cyan' => "\033[1;36m",
                'silver' => "\033[0;37m",
                'yellow' => "\033[0;93m"
            ];
        }
    }

    /**
     * Return a command
     * @param string $name
     * @param boolean $cache
     * @return Command (derived class)
     */
    public function getCommandObject($name, $cache = true)
    {
        return self::getCommandStatic($name, $cache);
    }

    /**
     * Return a command (statically callable)
     * @param string $name
     * @param boolean $cache
     * @return \Zabbic\Phpcheck\Command
     */
    public static function getCommandStatic($name, $cache = true)
    {
        $_name = '\\Visit\\Check\\Commands\\' . ucfirst($name);

        if (!class_exists($_name)) {
            throw new \Exception("Check $name does not exists");
        }

        if (!empty(self::$cache[$name])) {
            return self::$cache[$name];
        }
        
        if ($cache) {
            self::$cache[$name] = new $_name;
            return self::$cache[$name];
        }
        return new $_name;
    }

    /**
     * Prompt the user for an input value
     *
     * @param string $text
     * @return string
     */
    final protected function prompt($text)
    {
        static $fp = null;
        if ($fp === null) {
            $fp = fopen('php://stdin', 'r');
        }
        
        echo $text;
        
        if (function_exists('mb_substr')) {
            return mb_substr(fgets($fp), 0, -1, '8bit');
        }
        return substr(fgets($fp), 0, -1);
    }

    /**
     * Interactively prompts for input without echoing to the terminal.
     * Requires a bash shell or Windows and won't work with
     * safe_mode settings (Uses `shell_exec`)
     *
     * @ref http://www.sitepoint.com/interactive-cli-password-prompt-in-php/
     */
    final protected function silentPrompt($text = "Enter Password:")
    {
        if (preg_match('/^win/i', PHP_OS)) {
            $vbscript = sys_get_temp_dir() . 'prompt_password.vbs';
            file_put_contents($vbscript, 'wscript.echo(InputBox("'. addslashes($text) . '", "", "password here"))');
            $command = "cscript //nologo " . escapeshellarg($vbscript);
            $password = rtrim(shell_exec($command));
            unlink($vbscript);
            
            return $password;
        } else {
            $command = "/usr/bin/env bash -c 'echo OK'";
            if (rtrim(shell_exec($command)) !== 'OK') {
                throw new \Exception("Can't invoke bash");
            }
            $command = "/usr/bin/env bash -c 'read -s -p \"". addslashes($text). "\" mypassword && echo \$mypassword'";
            $password = rtrim(shell_exec($command));
            echo "\n";

            return $password;
        }
    }

    /**
     * Display the usage information for this command.
     *
     * @param array $args - CLI arguments
     * @echo
     * @return bool
     */
    public function usageInfo(array $args = [])
    {
        unset($args);
        $TAB = str_repeat(' ', self::TAB_SIZE);
        $HTAB = str_repeat(' ', (int) ceil(self::TAB_SIZE / 2));

        echo $HTAB, 'Zabbix / Phpcheck - ', $this->name, "\n\n";
        echo $TAB, $this->description, "\n\n";
        
        return true;
    }
}