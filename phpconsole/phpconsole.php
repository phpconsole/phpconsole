<?php

/*

http://phpconsole.com
v1

Watch quick tutorial at: https://vimeo.com/58393977

*/

//TODO: add comments to all functions

class Phpconsole {

    private $version;
    private $type;
    private $api_address;
    private $domain;
    private $users;
    private $user_api_keys;
    private $projects;
    private $initialized;
    private $snippets;
    private $counters;

    /*
    ================
    PUBLIC FUNCTIONS
    ================
    */

    public function __construct() {

        $this->version = '1.1';
        $this->type = 'php';
        $this->api_address = 'http://app.phpconsole.com/api/0.1/';
        $this->domain = false;
        $this->users = array();
        $this->user_api_keys = array();
        $this->projects = array();
        $this->initialized = false;
        $this->snippets = array();
        $this->counters = array();
    }

    public function set_domain($domain) {

        $this->domain = $domain;
    }

    public function add_user($nickname, $user_api_key, $project_api_key) {

        if($this->domain === false) {
            throw new Exception('Domain variable not set.');
        }

        $user_hash = md5($user_api_key.$this->domain);

        $this->users[$nickname] = $user_hash;
        $this->user_api_keys[$user_hash] = $user_api_key;
        $this->projects[$user_hash] = $project_api_key;
    }

    public function shutdown() {

        $any_snippets = is_array($this->snippets) && count($this->snippets) > 0;
        $any_counters = is_array($this->counters) && count($this->counters) > 0;

        if($any_snippets || $any_counters) {
            $this->_curl($this->api_address, array(
                'client_code_version' => $this->version,
                'client_code_type' => $this->type,
                'snippets' => $this->snippets,
                'counters' => $this->counters
            ));
        }
    }

    public function send($data_sent, $user = false) {

        $this->_register_shutdown();

        //TODO: do i really need ot have this flag here? does it change anything?
        $bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        $user_hashed_api_key = false;
        $user_api_key = false;
        $project_api_key = false;
        $continue = false;

        if($user === false) {
            if($this->_is_set_cookie('phpconsole_user')) {
                $user_hashed_api_key = $this->_read_cookie('phpconsole_user');
            }
        }
        else {
            if(isset($this->users[$user])) {
                $user_hashed_api_key = $this->users[$user];
            }
        }

        if($user_hashed_api_key !== false) {
            if(isset($this->projects[$user_hashed_api_key])) {
                $project_api_key = $this->projects[$user_hashed_api_key];
                $user_api_key = $this->user_api_keys[$user_hashed_api_key];
                $continue = true;
            }
        }

        if($continue) {
            $this->snippets[] =  array(
                'data_sent' => base64_encode(serialize($data_sent)),
                'file_name' => $bt[0]['file'],
                'line_number' => $bt[0]['line'],
                'address' => $this->_current_page_address(),
                'user_api_key' => $user_api_key,
                'project_api_key' => $project_api_key
            );
        }

        return $data_sent;
    }

    public function count($number = 1, $user = false) {

        $this->_register_shutdown();

        $user_api_key = false;

        if($user === false) {
            if($this->_is_set_cookie('phpconsole_user')) {
                $user_api_key = $this->_read_cookie('phpconsole_user');
            }
        }
        else {
            if(isset($this->users[$user])) {
                $user_api_key = $this->users[$user];
            }
        }

        if($user_api_key !== false) {
            if(!isset($this->counters[$user_api_key][$number])) {
                $this->counters[$user_api_key][$number] = 0;
            }

            $this->counters[$user_api_key][$number]++;
        }
    }

    public function set_user_cookie($name) {

        $this->_register_shutdown();

        if(isset($this->users[$name])) {
            $user_api_key = $this->users[$name];


            $this->_set_cookie('phpconsole_user', $user_api_key, time()+60*60*24*365);

            $this->send('Cookie for user "'.$name.'" and domain "'.$this->domain.'" has been set.', $name);
        }
    }

    public function destroy_user_cookie($name) {

        $this->_register_shutdown();

        if(isset($this->users[$name])) {
            setcookie('phpconsole_user', '', 0, '/', $GLOBALS['phpconsole_domain']);

            $this->_set_cookie('phpconsole_user', $user_api_key, 0);

            $this->send('Cookie for user "'.$name.'" and domain "'.$this->domain.'" has been destroyed.', $name);
        }
    }

    public function is_initialized() {
        return $this->initialized;
    }

    /*
    =================
    PRIVATE FUNCTIONS
    =================
    */

    private function _curl($url, $params) {

        $post_string = http_build_query($params);
        $headers = array('Content-Type: application/x-www-form-urlencoded');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_exec($ch);
        $curl_error = curl_error($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        /*
         * TODO: DO SOMETHING IF THERE ARE ERRORS
         */
    }

    private function _register_shutdown() {

        if(!is_initialized()) {

            $this->_settings();

            register_shutdown_function('phpconsole::shutdown');

            $this->initialized = true;
        }
    }

    private function _is_set_cookie($name) {
        return isset($_COOKIE[$name]);
    }

    private function _read_cookie($name) {
        return $_COOKIE[$name];
    }

    private function _set_cookie($name, $value, $time) {
        setcookie($name, $value, $time, '/', $this->domain);
    }

    private function _current_page_address() {

        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $address = 'https://';
        }
        else {
            $address = 'http://';
        }

        $address .= $_SERVER['SERVER_NAME'];

        if($_SERVER['SERVER_PORT'] != '80') {
            $address .= ':'.$_SERVER['SERVER_PORT'];
        }

        $address .= $_SERVER['REQUEST_URI'];

        return $address;
    }

}
