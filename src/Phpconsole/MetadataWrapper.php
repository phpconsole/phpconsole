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
 * @version 3.0.3
 */

namespace Phpconsole;

class MetadataWrapper
{
    protected $config;

    public function __construct(Config &$config = null)
    {
        $this->config = $config ?: new Config;
    }

    public function server()
    {
        return $_SERVER;
    }

    public function file($fileName)
    {
        return file($fileName);
    }

    public function debugBacktrace()
    {
        return debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
    }

    public function gethostname()
    {
        return gethostname();
    }
}
