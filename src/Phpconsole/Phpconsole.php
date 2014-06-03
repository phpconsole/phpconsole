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

class Phpconsole
{
    const VERSION = '2.0.1';

    protected $config;
    protected $queue;
    protected $snippetFactory;
    protected $dispatcher;

    public function __construct(Config $config = null, Queue $queue = null, SnippetFactory $snippetFactory = null, Dispatcher $dispatcher = null)
    {
        if (is_null($config)) {
            $config = new Config;
        }

        if (is_null($queue)) {
            $queue = new Queue($config);
        }

        if (is_null($snippetFactory)) {
            $snippetFactory = new SnippetFactory($config);
        }

        if (is_null($dispatcher)) {
            $dispatcher = new Dispatcher($config);
        }

        $this->config         = $config;
        $this->queue          = $queue;
        $this->snippetFactory = $snippetFactory;
        $this->dispatcher     = $dispatcher;
    }

    public function __destruct()
    {
        $this->dispatch();
    }

    public function send($payload, $options = array())
    {
        $snippet = $this->snippetFactory->create();
        $snippet->setOptions($options);
        $snippet->setPayload($payload);

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

    public function dispatch()
    {
        $this->dispatcher->dispatch($this->queue);
    }
}
