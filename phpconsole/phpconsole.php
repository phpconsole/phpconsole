<?php

/**
 * http://phpconsole.com
 *
 * A detached logging facility for PHP, JS and other environments to aid your daily development routine.
 *
 * Watch quick tutorial at: https://vimeo.com/58393977
 *
 * @link https://github.com/phpconsole
 * @copyright Copyright (c) 2012 - 2013 phpconsole.com
 * @license See LICENSE file
 * @version 1.6.0
 */


class Phpconsole {

    private $version;
    private $type;
    private $api_address;
    private $domain;
    private $users;
    private $projects;
    private $initialized;
    private $snippets;
    private $backtrace_depth;
    private $context_enabled;
    private $context_size;
    private $replace_true_false_null;
    private $auto_recognition_enabled;
    private $path_to_cert;

    /*
    ================
    PUBLIC FUNCTIONS
    ================
    */

    /**
     * Constructor - sets preferences
     */
    public function __construct() {

        $this->version = '1.6.0';
        $this->type = 'php';
        $this->api_address = 'https://app.phpconsole.com/api/0.1/';
        $this->domain = false;
        $this->users = array();
        $this->projects = array();
        $this->initialized = false;
        $this->snippets = array();
        $this->backtrace_depth = 0;
        $this->context_enabled = true;
        $this->context_size = 10;
        $this->replace_true_false_null = true;
        $this->auto_recognition_enabled = false;
        $this->path_to_cert = dirname(__FILE__).'/cacert.pem';
    }

    /**
     * Add user (developer)
     *
     * @access  public
     * @param   string
     * @param   string
     * @param   string
     * @return  void
     */
    public function add_user($user, $project_api_key) {

        $this->users[] = $user;
        $this->projects[md5($user)] = $project_api_key;
    }

    /**
     * User defined php shutdown function
     *
     * @access  public
     * @param   object
     * @return  void
     */
    public static function shutdown($object) {

        $any_snippets = is_array($object->snippets) && count($object->snippets) > 0;

        if($any_snippets) {
            $object->_curl($object->api_address, array(
                'client_code_version' => $object->version,
                'client_code_type' => $object->type,
                'snippets' => $object->snippets
            ));
        }
    }

    /**
     * Add data to phpconsole's local queue
     *
     * @access  public
     * @param   mixed
     * @param   string
     * @return  mixed
     */
    public function send($data_sent, $user = false) {

        $this->_register_shutdown();

        $continue = false;

        if($user === 'all') {

            $this->set_backtrace_depth($this->backtrace_depth+1);

            foreach($this->users as $user) {
                $this->send($data_sent, $user);
            }

            $this->set_backtrace_depth($this->backtrace_depth-1);
        }
        else if($user === false) {

            if($this->auto_recognition_enabled && $this->_is_set_cookie('phpconsole_user')) {
                $user = $this->_read_cookie('phpconsole_user');
            }
            else {
                $user = $this->users[0];
            }
        }

        if($user !== false) {

            if(in_array($user, $this->users)) {
                $project_api_key = $this->projects[md5($user)];
                $continue = true;
            }
        }

        if($continue) {

            $bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

            if($this->replace_true_false_null) {
                $data_sent = $this->_replace_true_false_null($data_sent);
            }

            $data_sent_encoded = base64_encode(print_r($data_sent, true));
            $file_name = $bt[$this->backtrace_depth]['file'];
            $line_number = $bt[$this->backtrace_depth]['line'];
            $context = $this->_read_context($file_name, $line_number);
            $address = $this->_current_page_address();

            $this->snippets[] =  array(
                'data_sent' => $data_sent_encoded,
                'context' => $context,
                'file_name' => $file_name,
                'line_number' => $line_number,
                'address' => $address,
                'project_api_key' => $project_api_key
            );
        }

        return $data_sent;
    }

    /**
     * Save cookie (that allows for identification) in user's browser
     *
     * @access  public
     * @param   string
     * @return  void
     */
    public function set_user_cookie($user) {

        $this->_register_shutdown();

        if(in_array($user, $this->users)) {

            if($this->auto_recognition_enabled) {

                $this->_set_cookie('phpconsole_user', $user, time()+60*60*24*365);
                $this->send('Cookie for user "'.$user.'" and domain "'.$this->domain.'" has been set.', $user);
            }
            else {
                $this->send('Auto recognition is not enabled! Enable with: enable_auto_recognition($domain);', $user);
            }
        }
        else {
            $this->send('User "'.$user.'" not found', 'all');
        }
    }

    /**
     * Destroy cookie (that allows for identification) in user's browser
     *
     * @access  public
     * @return  void
     */
    public function unset_user_cookie() {

        $this->_register_shutdown();

        $this->_set_cookie('phpconsole_user', '', 0);
    }

    /**
     * Check if phpconsole is initialized
     *
     * @access  public
     * @return  bool
     */
    public function is_initialized() {
        return $this->initialized;
    }

    /**
     * Set backtrace depth to determine correct file and line number that called send()
     *
     * @access  public
     * @param   int
     * @return  void
     */
    public function set_backtrace_depth($depth) {

        $this->backtrace_depth = $depth;
    }

    /**
     * Disable sending context for function that sends data
     *
     * @access  public
     * @return  void
     */
    public function disable_context() {

        $this->context_enabled = false;
    }

    /**
     * Set size of context - number of lines above and below line that sends data
     *
     * @access  public
     * @param   int
     * @return  void
     */
    public function set_context_size($context_size) {

        $this->context_size = $context_size;
    }

    /**
     * Disable function replacing true/false/null with their text representations
     *
     * @access  public
     * @return  void
     */
    public function disable_replace_true_false_null() {

        $this->replace_true_false_null = false;
    }

    /**
     * Enable auto recognition for specified domain (no leading dot required)
     *
     * @access  public
     * @param   string
     * @return  void
     */
    public function enable_auto_recognition($domain) {

        $this->auto_recognition_enabled = true;
        $this->domain = $domain;
    }

    /**
     * Set path to certificates to avoid issues with cURL and SSL (i.e. 'certs/cacert.pem')
     *
     * @access  public
     * @param   string
     * @return  void
     */
    public function set_path_to_cert($path) {

        $this->path_to_cert = $path;
    }

    /*
    =================
    PRIVATE FUNCTIONS
    =================
    */

    /**
     * cURL to selected address with provided parameters
     *
     * @access  private
     * @param   string
     * @param   array
     * @return  void
     */
    private function _curl($url, $params) {

        $post_string = http_build_query($params);
        $headers = array('Content-Type: application/x-www-form-urlencoded');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_CAINFO, $this->path_to_cert);

        curl_exec($ch);
        $curl_error = curl_error($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if($http_code !== 200) {
            trigger_error(htmlentities('phpconsole: cURL error code '.$http_code.': '.$curl_error));
        }
    }

    /**
     * Register shutdown function and mark as initialized
     *
     * @access  private
     * @return  void
     */
    private function _register_shutdown() {

        if(!$this->is_initialized()) {

            register_shutdown_function('phpconsole::shutdown', $this);

            $this->initialized = true;
        }
    }

    /**
     * Check if cookie exists
     *
     * @access  private
     * @param   string
     * @return  bool
     */
    private function _is_set_cookie($name) {
        return isset($_COOKIE[$name]);
    }

    /**
     * Read cookie
     *
     * @access  private
     * @param   string
     * @return  string
     */
    private function _read_cookie($name) {
        return $_COOKIE[$name];
    }

    /**
     * Set cookie
     *
     * @access  private
     * @param   string
     * @param   string
     * @param   int
     * @return  void
     */
    private function _set_cookie($name, $value, $time) {
        setcookie($name, $value, $time, '/', '.'.$this->domain);
    }

    /**
     * Get full address for current page
     *
     * @access  private
     * @return  string
     */
    private function _current_page_address() {

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
            $address .= ':'.$_SERVER['SERVER_PORT'];
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
    private function _read_context($file_name, $line_number) {

        $context = array();

        if($this->context_enabled && function_exists('file')) {
            $file = file($file_name);

            $context_from = $line_number - $this->context_size - 1;
            $context_to = $line_number + $this->context_size - 1;

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
    private function _replace_true_false_null($input) {

        if(is_array($input)) {
            if(count($input) > 0) {
                foreach($input as $key => $value) {
                    $input[$key] = $this->_replace_true_false_null($value);
                }
            }
        }
        else if(is_object($input)) {
            if(count($input) > 0) {
                foreach($input as $key => $value) {
                    $input->$key = $this->_replace_true_false_null($value);
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

}
