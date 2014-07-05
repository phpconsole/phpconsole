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

class Encryptor
{
    protected $config;
    protected $crypto;

    protected $password;
    protected $version = 1; // v1: AES-256, CBC, OpenSSL-compatible, legierski/aes v0.1.0

    public function __construct(Config &$config = null, \Legierski\AES\AES $crypto = null)
    {
        $this->config = $config ?: new Config;
        $this->crypto = $crypto ?: new \Legierski\AES\AES;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function encrypt($plaintext)
    {
        return $this->crypto->encrypt($plaintext, $this->password);
    }

    public function getVersion()
    {
        return $this->version;
    }
}
