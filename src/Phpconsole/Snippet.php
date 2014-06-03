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

class Snippet
{
    protected $config;

    public $payload = null;

    public $type;
    public $projectApiKey;

    public $fileName;
    public $lineNumber;
    public $context;
    public $address;

    public function __construct(Config &$config = null)
    {
        if (is_null($config)) {
            $config = new Config;
        }

        $this->config = $config;
    }

    public function setPayload($payload)
    {
        $this->setMetadata();

        $this->payload = $this->preparePayload($payload);
    }

    public function setOptions($options)
    {
        $options = $this->prepareOptions($options);

        $this->type = $options['type'];
        $this->projectApiKey = $this->config->getApiKeyFor($options['project']);
    }

    private function setMetadata()
    {
        $bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $backtraceDepth = $this->config->backtraceDepth;

        $this->fileName    = $bt[$backtraceDepth]['file'];
        $this->lineNumber  = $bt[$backtraceDepth]['line'];
        $this->context     = $this->readContext($this->fileName, $this->lineNumber);
        $this->address     = $this->currentPageAddress();
    }

    private function preparePayload($payload)
    {
        $payload = $this->replaceTrueFalseNull($payload);
        $payload = print_r($payload, true);
        $payload = base64_encode($payload);

        return $payload;
    }

    private function prepareOptions($options)
    {
        if (is_string($options)) {
            $options = array('project' => $options);
        }

        if (!isset($options['project'])) {
            $options['project'] = $this->config->defaultProject;
        }

        if (!isset($options['type'])) {
            $options['type'] = 'normal';
        }

        return $options;
    }

    private function replaceTrueFalseNull($input)
    {
        if (is_array($input)) {
            if (count($input) > 0) {
                foreach ($input as $key => $value) {
                    $input[$key] = $this->replaceTrueFalseNull($value);
                }
            }
        } elseif (is_object($input)) {
            if (count($input) > 0) {
                foreach ($input as $key => $value) {
                    $input->$key = $this->replaceTrueFalseNull($value);
                }
            }
        }

        if ($input === true) {
            $input = 'true';
        } elseif ($input === false) {
            $input = 'false';
        } elseif ($input === null) {
            $input = 'null';
        }

        return $input;
    }

    private function readContext($fileName, $lineNumber)
    {
        $context = array();

        if ($this->config->isContextEnabled && function_exists('file')) {

            $file = file($fileName);
            $contextSize = $this->config->contextSize;

            $contextFrom = $lineNumber - $contextSize - 1;
            $contextTo   = $lineNumber + $contextSize - 1;

            for ($i = $contextFrom; $i <= $contextTo; $i++) {

                if ($i < 0 || $i >= count($file)) {
                    $context[] = '';
                } else {
                    $context[] = $file[$i];
                }
            }
        }

        return base64_encode(json_encode($context));
    }

    private function currentPageAddress()
    {
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $address = 'https://';
        } else {
            $address = 'http://';
        }

        if (isset($_SERVER['HTTP_HOST'])) {
            $address .= $_SERVER['HTTP_HOST'];
        }

        if (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != '80') {

            $port = $_SERVER['SERVER_PORT'];
            $address_end = substr($address, -1*(strlen($port)+1));

            if ($address_end !== ':'.$port) {
                $address .= ':'.$port;
            }
        }

        if (isset($_SERVER['REQUEST_URI'])) {
            $address .= $_SERVER['REQUEST_URI'];
        }

        return $address;
    }
}
