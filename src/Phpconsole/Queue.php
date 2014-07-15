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
 * @version 3.1.0
 */

namespace Phpconsole;

class Queue implements LoggerInterface
{
    protected $config;

    protected $queue = array();

    public function __construct(Config &$config = null)
    {
        $this->config = $config ?: new Config;
    }

    public function log($message, $highlight = false)
    {
        if ($this->config->debug) {
            $_ENV['PHPCONSOLE_DEBUG_LOG'][] = array(microtime(true), $message, $highlight);
        }
    }

    public function add(Snippet $snippet)
    {
        if ($snippet->projectApiKey !== null) {
            $this->queue[] = $snippet;
            $this->log('Snippet added to the queue');
        } else {
            $this->log('Project API key not found - snippet not added to the queue', true);
        }

        return $snippet;
    }

    public function flush()
    {
        $queue = $this->queue;
        $this->queue = array();

        $this->log('Queue flushed');

        return $queue;
    }
}
