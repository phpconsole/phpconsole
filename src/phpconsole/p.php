<?php

/**
 * http://phpconsole.com
 *
 * A detached logging facility for PHP to aid your daily development routine.
 *
 * Watch quick tutorial at: https://vimeo.com/58393977
 *
 * @link https://github.com/phpconsole
 * @copyright Copyright (c) 2012 - 2014 phpconsole.com
 * @license See LICENSE file
 * @version 2.0.0
 */

namespace phpconsole;

class p {

    private static $is_set_up;
    private static $phpconsole;

    public static function send($payload, $options = array())
    {
        self::setup();

        return self::$phpconsole->send($payload, $options);
    }

    public static function success($payload, $options = array())
    {
        self::setup();

        $options = array_merge(array('type' => 'success'), $options);

        return self::$phpconsole->send($payload, $options);
    }

    public static function successinfo($payload, $options = array())
    {
        self::setup();

        $options = array_merge(array('type' => 'info'), $options);

        return self::$phpconsole->send($payload, $options);
    }

    public static function error($payload, $options = array())
    {
        self::setup();

        $options = array_merge(array('type' => 'error'), $options);

        return self::$phpconsole->send($payload, $options);
    }

    public static function sendToAll($payload, $options = array())
    {
        self::setup();

        return self::$phpconsole->sendToAll($payload, $options);
    }

    private static function setup()
    {
        if(self::$is_set_up !== true) {

            self::$phpconsole = new phpconsole();
            self::$phpconsole->loadConfig(array('backtrace_depth' => 1));

            self::$is_set_up = true;
        }
    }
}
