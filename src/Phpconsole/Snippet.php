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

class Snippet implements LoggerInterface
{
    protected $config;
    protected $metadataWrapper;
    protected $encryptor;

    public $payload;

    public $type;
    public $project;
    public $projectApiKey;
    public $encryptionVersion;
    public $isEncrypted = false;

    public $fileName;
    public $lineNumber;
    public $context;
    public $address;
    public $hostname;

    public function __construct(Config &$config = null, MetadataWrapper $metadataWrapper = null, Encryptor $encryptor = null)
    {
        $this->config          = $config          ?: new Config;
        $this->metadataWrapper = $metadataWrapper ?: new MetadataWrapper($this->config);
        $this->encryptor       = $encryptor       ?: new Encryptor($this->config);
    }

    public function log($message, $highlight = false)
    {
        if ($this->config->debug) {
            $_ENV['PHPCONSOLE_DEBUG_LOG'][] = array(microtime(true), $message, $highlight);
        }
    }

    public function setPayload($payload)
    {
        $this->payload = $this->preparePayload($payload);

        $this->log('Payload set for snippet');
    }

    public function setOptions($options)
    {
        $options = $this->prepareOptions($options);

        $this->type    = $options['type'];
        $this->project = $options['project'];

        $this->log('Options set for snippet');

        $this->projectApiKey = $this->config->getApiKeyFor($this->project);
    }

    public function setMetadata($metadata = array())
    {
        $metadata = $this->prepareMetadata($metadata);

        $this->fileName   = base64_encode($metadata['fileName']);
        $this->lineNumber = base64_encode($metadata['lineNumber']);
        $this->context    = base64_encode($metadata['context']);
        $this->address    = base64_encode($metadata['address']);
        $this->hostname   = base64_encode($metadata['hostname']);

        $this->log('Metadata set for snippets');
    }

    public function encrypt()
    {
        $password = $this->config->getEncryptionPasswordFor($this->project);

        if ($password !== null) {

            $this->encryptor->setPassword($password);

            $this->log('Password set for encryptor');

            $this->payload    = base64_decode($this->payload);
            $this->fileName   = base64_decode($this->fileName);
            $this->lineNumber = base64_decode($this->lineNumber);
            $this->context    = base64_decode($this->context);
            $this->address    = base64_decode($this->address);
            $this->hostname   = base64_decode($this->hostname);

            $this->payload    = $this->encryptor->encrypt($this->payload);
            $this->fileName   = $this->encryptor->encrypt($this->fileName);
            $this->lineNumber = $this->encryptor->encrypt($this->lineNumber);
            $this->context    = $this->encryptor->encrypt($this->context);
            $this->address    = $this->encryptor->encrypt($this->address);
            $this->hostname   = $this->encryptor->encrypt($this->hostname);

            $this->log('Snippet data encrypted');

            $this->encryptionVersion = $this->encryptor->getVersion();
            $this->isEncrypted = true;
        } else {
            $this->log('Snippet data not encrypted', true);
        }
    }

    protected function preparePayload($payload)
    {
        switch ($this->config->captureWith) {

            case 'print_r':
                $payload = $this->replaceTrueFalseNull($payload);
                $payload = print_r($payload, true);
                break;

            case 'var_dump':
                ob_start();
                var_dump($payload);
                $payload = ob_get_clean();
                break;

            default:
                $payload = 'Function to capture payload with not recognised';
        }

        $payload = base64_encode($payload);

        $this->log('Payload prepared for snippet');

        return $payload;
    }

    protected function prepareOptions($options)
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

        $this->log('Options prepared for snippet');

        return $options;
    }

    protected function prepareMetadata($metadata)
    {
        $backtrace = $this->metadataWrapper->debugBacktrace();
        $depth     = $this->config->backtraceDepth;

        if (!isset($metadata['fileName'])) {
            $metadata['fileName'] = $backtrace[$depth]['file'];
        }

        if (!isset($metadata['lineNumber'])) {
            $metadata['lineNumber'] = $backtrace[$depth]['line'];
        }

        if (!isset($metadata['context'])) {
            $metadata['context'] = $this->readContext($metadata['fileName'], $metadata['lineNumber']);
        }

        if (!isset($metadata['address'])) {
            $metadata['address'] = $this->currentPageAddress();
        }

        if (!isset($metadata['hostname'])) {
            $metadata['hostname'] = $this->metadataWrapper->gethostname();
        }

        return $metadata;
    }

    protected function replaceTrueFalseNull($input)
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

        $this->log('true, false and null values replaced');

        return $input;
    }

    protected function readContext($fileName, $lineNumber)
    {
        $context = array();

        if ($this->config->isContextEnabled && function_exists('file')) {

            $file = $this->metadataWrapper->file($fileName);
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

            $this->log('Context read for snippet');
        }

        return json_encode($context);
    }

    protected function currentPageAddress()
    {
        $server = $this->metadataWrapper->server();
        $isCli  = $this->metadataWrapper->isCliRequest();

        if($isCli) {
            $address = 'n/a';
        } else {

            if (isset($server['HTTPS']) && $server['HTTPS'] == 'on') {
                $address = 'https://';
            } else {
                $address = 'http://';
            }

            if (isset($server['HTTP_HOST'])) {
                $address .= $server['HTTP_HOST'];
            }

            if (isset($server['SERVER_PORT']) && $server['SERVER_PORT'] != '80') {

                $port = $server['SERVER_PORT'];
                $address_end = substr($address, -1*(strlen($port)+1));

                if ($address_end !== ':'.$port) {
                    $address .= ':'.$port;
                }
            }

            if (isset($server['REQUEST_URI'])) {
                $address .= $server['REQUEST_URI'];
            }
        }

        $this->log('Current page address read for snippet');

        return $address;
    }
}
