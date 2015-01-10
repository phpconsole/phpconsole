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

class Dispatcher implements LoggerInterface
{
    protected $config;
    protected $client;

    public function __construct(Config &$config = null, Client $client = null)
    {
        $this->config = $config ?: new Config;
        $this->client = $client ?: new Client($this->config);
    }

    public function log($message, $highlight = false)
    {
        if ($this->config->debug) {
            $_ENV['PHPCONSOLE_DEBUG_LOG'][] = array(microtime(true), $message, $highlight);
        }
    }

    public function dispatch(Queue $queue)
    {
        $snippets = $this->prepareForDispatch($queue->flush());

        if (count($snippets) > 0) {

            $this->log('Snippets found in the queue, preparing POST request');

            try {

                $payload = array(
                    'type'     => Phpconsole::TYPE,
                    'version'  => Phpconsole::VERSION,
                    'snippets' => $snippets
                );

                $this->client->send($payload);

                $this->log('Request successfully sent to the API endpoint');

            } catch (\Exception $e) {
                $this->log('Request failed. Exception message: '.$e->getMessage(), true);
            }
        } else {
            $this->log('No snippets found in the queue, dispatcher exits', true);
        }
    }

    public function prepareForDispatch(array $snippets)
    {
        $snippetsAsArrays = array();

        if (count($snippets) > 0) {

            $this->log('Snippets found, preparing for dispatch');

            foreach ($snippets as $snippet) {

                $snippetsAsArrays[] = array(
                    'payload'           => $snippet->payload,

                    'type'              => $snippet->type,
                    'projectApiKey'     => $snippet->projectApiKey,
                    'encryptionVersion' => $snippet->encryptionVersion,
                    'isEncrypted'       => $snippet->isEncrypted,

                    'fileName'          => $snippet->fileName,
                    'lineNumber'        => $snippet->lineNumber,
                    'context'           => $snippet->context,
                    'address'           => $snippet->address,
                    'hostname'          => $snippet->hostname
                );

                $this->log('Snippet prepared for dispatch');
            }

            $this->log('All snippets prepared for dispatch');
        }

        return $snippetsAsArrays;
    }
}
