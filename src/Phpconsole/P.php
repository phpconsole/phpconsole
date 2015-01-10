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
 * @version 3.2.2
 */

namespace Phpconsole;

class P
{
    protected static $phpconsole = null;

    public static function send($payload, $options = array())
    {
        return self::getPhpconsole()->send($payload, $options);
    }

    public static function success($payload, $options = array())
    {
        if (is_string($options)) {
            $options = array('project' => $options);
        }

        $options = array_merge(array('type' => 'success'), $options);

        return self::getPhpconsole()->send($payload, $options);
    }

    public static function info($payload, $options = array())
    {
        if (is_string($options)) {
            $options = array('project' => $options);
        }

        $options = array_merge(array('type' => 'info'), $options);

        return self::getPhpconsole()->send($payload, $options);
    }

    public static function error($payload, $options = array())
    {
        if (is_string($options)) {
            $options = array('project' => $options);
        }

        $options = array_merge(array('type' => 'error'), $options);

        return self::getPhpconsole()->send($payload, $options);
    }

    public static function sendToAll($payload, $options = array())
    {
        return self::getPhpconsole()->sendToAll($payload, $options);
    }

    protected static function getPhpconsole()
    {
        if (is_null(self::$phpconsole)) {

            $config = new Config;
            $config->loadFromArray(array(
                'backtraceDepth' => 3
            ));

            self::$phpconsole = new Phpconsole($config);
        }

        return self::$phpconsole;
    }
}
