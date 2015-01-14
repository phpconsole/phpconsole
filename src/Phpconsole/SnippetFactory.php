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
 * @version 3.3.0
 */

namespace Phpconsole;

class SnippetFactory implements LoggerInterface
{
    protected $config;

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

    public function create()
    {
        $snippet = new Snippet($this->config);

        $this->log('Snippet created');

        return $snippet;
    }
}
