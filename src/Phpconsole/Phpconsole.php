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
 * @version 3.2.1
 */

namespace Phpconsole;

class Phpconsole implements LoggerInterface
{
    const TYPE    = 'php-composer';
    const VERSION = '3.2.1';

    protected $config;
    protected $queue;
    protected $snippetFactory;
    protected $dispatcher;
    protected $debugger;

    protected $log;

    public function __construct(Config $config = null, Queue $queue = null, SnippetFactory $snippetFactory = null, Dispatcher $dispatcher = null, Debugger $debugger = null)
    {
        $this->config         = $config         ?: new Config;
        $this->queue          = $queue          ?: new Queue($this->config);
        $this->snippetFactory = $snippetFactory ?: new SnippetFactory($this->config);
        $this->dispatcher     = $dispatcher     ?: new Dispatcher($this->config);
        $this->debugger       = $debugger       ?: new Debugger($this->config);
    }

    public function log($message, $highlight = false)
    {
        if ($this->config->debug) {
            $_ENV['PHPCONSOLE_DEBUG_LOG'][] = array(microtime(true), $message, $highlight);
        }
    }

    public function send($payload, $options = array())
    {
        $snippet = $this->snippetFactory->create();

        $snippet->setOptions($options);
        $snippet->setPayload($payload);
        $snippet->setMetadata();
        $snippet->encrypt();

        $this->queue->add($snippet);

        return $payload;
    }

    public function sendToAll($payload, $options = array())
    {
        $this->config->backtraceDepth++;

        $projects = $this->config->projects;

        if (is_array($projects) && count($projects) > 0) {
            foreach ($projects as $name => $api_key) {

                $options = array_merge($options, array('project' => $name));

                $this->send($payload, $options);
            }
        }

        $this->config->backtraceDepth--;

        return $payload;
    }

    public function __destruct()
    {
        $this->dispatch();
        $this->displayDebugInfo();
    }

    public function dispatch()
    {
        $this->dispatcher->dispatch($this->queue);
    }

    public function displayDebugInfo()
    {
        $this->debugger->displayDebugInfo();
    }
}
