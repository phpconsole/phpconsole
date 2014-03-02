<?php

/**
 * http://phpconsole.com
 *
 * A detached logging facility for PHP to aid your daily development routine.
 *
 * Watch quick tutorial at: https://vimeo.com/58393977
 *
 * @link https://github.com/phpconsole
 * @copyright Copyright (c) 2012 - 2014 phpconsole.com
 * @license See LICENSE file
 * @version 2.0.0
 */

namespace phpconsole;

class phpconsole {

    private $config;
    private $snippets;

    /**
     * Constructor - sets preferences
     *
     * @access  public
     * @param   mixed
     * @return  void
     */
    public function __construct($config = false)
    {
        define('VERSION', '2.0.0');

        $this->config = array(
            'api_address'             => 'https://app.phpconsole.com/api/0.2/',
            'projects'                => array(),
            'default_project'         => 'none',
            'backtrace_depth'         => 0,
            'context_enabled'         => true,
            'context_size'            => 10,
            'debug'                   => false
            );

        $this->snippets = array();

        $this->loadConfig($config);
    }

    /**
     * Destructor - send data to phpconsole's servers
     *
     * @access  public
     * @return  void
     */
    public function __destruct()
    {
        $this->flush();
    }

    /**
     * Load config from parameter or file
     *
     * @access  private
     * @param   mixed
     * @return  void
     */
    public function loadConfig($config = false)
    {
        $config_locations = array(
            'phpconsole_config.php',
            'app/config/phpconsole.php',
            'app/config/packages/phpconsole/phpconsole/config.php'
            );

        if(is_array($config)) {
            $config_values = $config;
        }
        elseif(is_string($config) && file_exists($config)) {
            $config_values = include $config;
        }
        elseif(defined('PHPCONSOLE_CONFIG_LOCATION') && file_exists(PHPCONSOLE_CONFIG_LOCATION)) {
            $config_values = include PHPCONSOLE_CONFIG_LOCATION;
        }
        elseif($config === false) {
            foreach($config_locations as $location) {
                if(file_exists($location)) {
                    $config_values = include $location;
                    break;
                }
            }
        }

        if(isset($config_values) && is_array($config_values) && count($config_values) > 0) {
            $this->config = array_merge($this->config, $config_values);
        }
    }

    /**
     * Add data to phpconsole's local queue
     *
     * @access  public
     * @param   mixed
     * @param   mixed
     * @return  mixed
     */
    public function send($payload, $options = array())
    {
        $options = $this->prepareOptions($options);

        if(isset($this->config['projects'][$options['project']])) {

            $bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

            $file_name   = $bt[$this->config['backtrace_depth']]['file'];
            $line_number = $bt[$this->config['backtrace_depth']]['line'];

            $this->snippets[] =  array(
                'payload'         => $this->preparePayload($payload),
                'file_name'       => $file_name,
                'line_number'     => $line_number,
                'context'         => $this->readContext($file_name, $line_number),
                'address'         => $this->currentPageAddress(),
                'type'            => $options['type'],
                'project_api_key' => $this->config['projects'][$options['project']]
            );
        }

        return $payload;
    }

    /**
     * Add data to phpconsole's local queue, for each specified project
     *
     * @access  public
     * @param   mixed
     * @param   mixed
     * @return  mixed
     */
    public function sendToAll($payload, $options = array())
    {
        $this->config['backtrace_depth']++;

        foreach($this->config['projects'] as $name => $api_key) {
            $options = array_merge($options, array('project' => $name));

            $this->send($payload, $options);
        }

        $this->config['backtrace_depth']--;

        return $payload;
    }

    /**
     * Send data to phpconsole's servers
     *
     * @access  public
     * @return  void
     */
    public function flush()
    {
        if(is_array($this->snippets) && count($this->snippets) > 0) {

            $params = array(
                'version'  => VERSION,
                'type'     => 'php',
                'snippets' => $this->snippets
                );

            $this->curl($params);
        }
    }

    /**
     * Get full address for current page
     *
     * @access  private
     * @return  string
     */
    private function currentPageAddress()
    {
        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $address = 'https://';
        }
        else {
            $address = 'http://';
        }

        if(isset($_SERVER['HTTP_HOST'])) {
            $address .= $_SERVER['HTTP_HOST'];
        }

        if(isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != '80') {

            $port = $_SERVER['SERVER_PORT'];
            $address_end = substr($address, -1*(strlen($port)+1));

            if($address_end !== ':'.$port) {
                $address .= ':'.$port;
            }
        }

        if(isset($_SERVER['REQUEST_URI'])) {
            $address .= $_SERVER['REQUEST_URI'];
        }

        return $address;
    }

    /**
     * Read context for function that sends data
     *
     * @access  private
     * @param   string
     * @param   int
     * @return  string
     */
    private function readContext($file_name, $line_number)
    {
        $context = array();

        if($this->config['context_enabled'] && function_exists('file')) {
            $file = file($file_name);

            $context_from = $line_number - $this->config['context_size'] - 1;
            $context_to = $line_number + $this->config['context_size'] - 1;

            for($i = $context_from; $i <= $context_to; $i++) {

                if($i < 0 || $i >= count($file)){
                    $context[] = '';
                }
                else {
                    $context[] = $file[$i];
                }
            }
        }

        return base64_encode(json_encode($context));
    }

    /**
     * Replace values that can't be printed on the screen with their text representation
     *
     * @access  private
     * @param   mixed
     * @return  mixed
     */
    private function replaceTrueFalseNull($input)
    {
        if(is_array($input)) {
            if(count($input) > 0) {
                foreach($input as $key => $value) {
                    $input[$key] = $this->replaceTrueFalseNull($value);
                }
            }
        }
        else if(is_object($input)) {
            if(count($input) > 0) {
                foreach($input as $key => $value) {
                    $input->$key = $this->replaceTrueFalseNull($value);
                }
            }
        }

        if($input === true) {
            $input = 'true';
        }
        else if($input === false) {
            $input = 'false';
        }
        else if($input === null) {
            $input = 'null';
        }

        return $input;
    }

    /**
     * cURL to phpconsole's server with provided parameters
     *
     * @access  private
     * @param   array
     * @return  void
     */
    private function curl($params)
    {
        $post_string = http_build_query($params);
        $headers = array('Content-Type: application/x-www-form-urlencoded');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->config['api_address']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__).'/../../cacert.pem');

        curl_exec($ch);
        $curl_error = curl_error($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if($http_code !== 200) {
            trigger_error(htmlentities('phpconsole: cURL error code '.$http_code.': '.$curl_error));
        }
    }

    /**
     * Prepare payload before adding it to queue
     *
     * @access  private
     * @param   mixed
     * @return  mixed
     */
    private function preparePayload($payload)
    {
        $payload = $this->replaceTrueFalseNull($payload);
        $payload = print_r($payload, true);
        $payload = base64_encode($payload);

        return $payload;
    }

    /**
     * Prepare options - determine project
     *
     * @access  private
     * @param   mixed
     * @return  mixed
     */
    private function prepareOptions($options)
    {
        if(is_string($options)) {
            $options = array('project' => $options);
        }

        if(!isset($options['project'])) {

            if(isset($_COOKIE['phpconsole_default_project'])) {

                $options['project'] = $_COOKIE['phpconsole_default_project'];
            }
            elseif(file_exists('.phpconsole_default_project')) {

                $options['project'] = trim(@file_get_contents('.phpconsole_default_project'));
            }
            elseif(defined('PHPCONSOLE_DEFAULT_PROJECT')) {

                $options['project'] = PHPCONSOLE_DEFAULT_PROJECT;
            }
            else {
                $options['project'] = $this->config['default_project'];
            }
        }

        if(!isset($options['type'])) {
            $options['type'] = 'normal';
        }

        return $options;
    }

}
