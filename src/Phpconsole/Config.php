<?php

/**
 * A detached logging facility for PHP to aid your daily development routine.
 *
 * Watch quick tutorial at: https://vimeo.com/58393977
 *
 * @link http://phpconsole.com
 * @link https://github.com/phpconsole
 * @copyright Copyright (c) 2012 - 2014 phpconsole.com
 * @license See LICENSE file
 * @version 3.2.0
 */

namespace Phpconsole;

class Config implements LoggerInterface
{
    public $debug            = false; // temporary
    public $apiAddress       = 'https://app.phpconsole.com/api/0.3/';
    public $defaultProject   = 'none';
    public $projects         = array();
    public $backtraceDepth   = 2;
    public $isContextEnabled = true;
    public $contextSize      = 10;
    public $captureWith      = 'print_r';

    public function __construct()
    {
        $this->loadFromDefaultLocation();
    }

    public function log($message, $highlight = false)
    {
        if ($this->debug) {
            $_ENV['PHPCONSOLE_DEBUG_LOG'][] = array(microtime(true), $message, $highlight);
        }
    }

    public function loadFromDefaultLocation()
    {
        $defaultLocations = array(
            'phpconsole_config.php',
            'app/config/phpconsole.php',
            'app/config/packages/phpconsole/phpconsole/config.php',
            'config/packages/phpconsole/phpconsole/config.php',
            'application/config/phpconsole.php',
            '../phpconsole_config.php',
            '../app/config/phpconsole.php',
            '../app/config/packages/phpconsole/phpconsole/config.php',
            '../application/config/phpconsole.php',
            dirname(__FILE__).'/phpconsole_config.php'
        );

        if (function_exists('app_path')) {

            $defaultLocations[] = app_path().'/config/phpconsole.php';
            $defaultLocations[] = app_path().'/config/packages/phpconsole/phpconsole/config.php';
        }

        if (defined('PHPCONSOLE_CONFIG_LOCATION')) {
            $this->log('Found \'PHPCONSOLE_CONFIG_LOCATION\' constant - adding to the list of locations to check');
            array_unshift($defaultLocations, PHPCONSOLE_CONFIG_LOCATION);
        }

        foreach ($defaultLocations as $location) {
            if (file_exists($location)) {

                $this->log('Config file found in '.$location);
                return $this->loadFromLocation($location);
            }
        }

        $this->debug = true; // temporary

        $this->log('Config file not found - this is really bad!', true);

        return false;
    }

    public function loadFromLocation($location)
    {
        if (!is_string($location)) {
            throw new Exception('Location should be a string', 1);
        }

        if (!file_exists($location)) {
            throw new Exception('File doesn\'t exist', 1);
        }

        $config = include $location;

        $this->log('Config loaded from file into array');

        return $this->loadFromArray($config);
    }

    public function loadFromArray(array $config)
    {
        foreach ($config as $configItemName => $configItemValue) {

            if (isset($this->{$configItemName})) {

                $this->{$configItemName} = $configItemValue;
            }
        }

        $this->log('Config loaded from array into Config object');

        $this->determineDefaultProject();

        return true;
    }

    protected function determineDefaultProject()
    {
        if (isset($_COOKIE['phpconsole_default_project'])) {

            $this->log('Default project loaded from cookie "phpconsole_default_project"');
            $this->defaultProject = $_COOKIE['phpconsole_default_project'];
        } elseif (file_exists('.phpconsole_default_project')) {

            $this->log('Default project loaded from file .phpconsole_default_project');
            $this->defaultProject = trim(@file_get_contents('.phpconsole_default_project'));
        } elseif (defined('PHPCONSOLE_DEFAULT_PROJECT')) {

            $this->log('Default project loaded from constant "PHPCONSOLE_DEFAULT_PROJECT"');
            $this->defaultProject = PHPCONSOLE_DEFAULT_PROJECT;
        }

        $this->log('Default project determined as "'.$this->defaultProject.'"');
    }

    public function getApiKeyFor($project)
    {
        if (isset($this->projects[$project]) && isset($this->projects[$project]['apiKey'])) {

            $this->log('API key for "'.$project.'" found');
            return $this->projects[$project]['apiKey'];
        } else {

            $this->log('API key for "'.$project.'" not found', true);
            return null;
        }
    }

    public function getEncryptionPasswordFor($project)
    {
        if (isset($this->projects[$project]) && isset($this->projects[$project]['encryptionPassword'])) {

            $this->log('Encryption password for "'.$project.'" found');
            return $this->projects[$project]['encryptionPassword'];
        } else {

            $this->log('Encryption password for "'.$project.'" not found (not specified in config?)', true);
            return null;
        }
    }
}
