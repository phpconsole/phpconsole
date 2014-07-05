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
 * @version 3.0.1
 */

namespace Phpconsole;

class Queue
{
    protected $config;

    protected $queue = array();

    public function __construct(Config &$config = null)
    {
        $this->config = $config ?: new Config;
    }

    public function add(Snippet $snippet)
    {
        if ($snippet->projectApiKey !== null) {
            $this->queue[] = $snippet;
        }

        return $snippet;
    }

    public function flush()
    {
        $queue = $this->queue;
        $this->queue = array();

        return $queue;
    }
}
