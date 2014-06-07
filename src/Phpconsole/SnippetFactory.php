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

class SnippetFactory
{
    protected $config;

    public function __construct(Config &$config = null)
    {
        $this->config = $config ?: new Config;
    }

    public function create()
    {
        return new Snippet($this->config);
    }
}
