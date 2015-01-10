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

class Client
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

    public function send($payload)
    {
        $headers     = array('Content-Type: application/x-www-form-urlencoded');
        $post_string = http_build_query($payload);
        $cacertPath  = dirname(__FILE__).'/cacert.pem';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->config->apiAddress);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if (file_exists($cacertPath)) {

            $this->log('cacert.pem file found, verifying API endpoint');

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_CAINFO, $cacertPath);

        } else {

            $this->log('cacert.pem file not found, the API endpoint will not be verified', true);

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        }

        curl_exec($ch);
        $curl_error = curl_error($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($http_code !== 200) {

            throw new \Exception('cURL error code '.$http_code.': '.$curl_error);
        }
    }
}
