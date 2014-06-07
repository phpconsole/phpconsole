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
 * @version 2.0.1
 */

namespace Phpconsole;

class Config
{
    public $apiAddress       = 'https://app.phpconsole.com/api/0.2/';
    public $projects         = array();
    public $defaultProject   = 'none';
    public $backtraceDepth   = 2;
    public $isContextEnabled = true;
    public $contextSize      = 10;

    public function __construct()
    {
        $this->loadFromDefaultLocation();
    }

    public function loadFromDefaultLocation()
    {
        $defaultLocations = array(
            'phpconsole_config.php',
            'app/config/phpconsole.php',
            'app/config/packages/phpconsole/phpconsole/config.php',
            'application/config/phpconsole.php',
            dirname(__FILE__).'/phpconsole_config.php'
        );

        if (defined('PHPCONSOLE_CONFIG_LOCATION')) {
            array_unshift($defaultLocations, PHPCONSOLE_CONFIG_LOCATION);
        }

        foreach ($defaultLocations as $location) {
            if (file_exists($location)) {
                return $this->loadFromLocation($location);
            }
        }

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

        return $this->loadFromArray($config);
    }

    public function loadFromArray(array $config)
    {
        foreach ($config as $configItemName => $configItemValue) {

            if (isset($this->{$configItemName})) {

                $this->{$configItemName} = $configItemValue;
            }
        }

        $this->determineDefaultProject();

        return true;
    }

    protected function determineDefaultProject()
    {
        if (isset($_COOKIE['phpconsole_default_project'])) {

            $this->defaultProject = $_COOKIE['phpconsole_default_project'];
        } elseif (file_exists('.phpconsole_default_project')) {

            $this->defaultProject = trim(@file_get_contents('.phpconsole_default_project'));
        } elseif (defined('PHPCONSOLE_DEFAULT_PROJECT')) {

            $this->defaultProject = PHPCONSOLE_DEFAULT_PROJECT;
        }
    }

    public function getApiKeyFor($project)
    {
        if (isset($this->projects[$project])) {

            return $this->projects[$project];
        } else {

            return null;
        }
    }
}
