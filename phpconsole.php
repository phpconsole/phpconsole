<?php

/*

http://phpconsole.com
v1

Watch quick tutorial at: https://vimeo.com/58393977

*/

function phpconsole_settings() {

    $users   = array();

    /*
    ==============================================
    USER'S SETTINGS
    ==============================================
    */

    $domain = '.your-domain.com'; // don't forget to use leading dot, like so: .your-domain.com

    $users[] = array('nickname', 'user_api_key', 'project_api_key');
    //$users[] = array('nickname', 'user_api_key', 'project_api_key'); // you can add more developers










    /*
    ==============================================
    */

    $GLOBALS['phpconsole_domain'] = $domain;
    $GLOBALS['phpconsole_users'] = array();
    $GLOBALS['phpconsole_projects'] = array();

    foreach($users as $user) {
        $GLOBALS['phpconsole_users'][$user[0]] = md5($user[1].$domain);
        $GLOBALS['phpconsole_projects'][md5($user[1].$domain)] = $user[2];
        $GLOBALS['phpconsole_user_api_keys'][md5($user[1].$domain)] = $user[1];
    }
}

function phpconsole_curl($url, $params) {

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

function phpconsole_register_shutdown() {
    if(!isset($GLOBALS['phpcounter_initialized'])) {

        phpconsole_settings();

        $GLOBALS['phpconsole_snippets'] = array();
        $GLOBALS['phpconsole_counters'] = array();

        register_shutdown_function('phpconsole_shutdown');

        $GLOBALS['phpcounter_initialized'] = true;
    }
}

function phpconsole_shutdown() {

    $client_code_type = 'php';
    $client_code_version = 1;
    $snippets = $GLOBALS['phpconsole_snippets'];
    $counters = $GLOBALS['phpconsole_counters'];

    $any_snippets = is_array($snippets) && count($snippets) > 0;
    $any_counters = is_array($counters) && count($counters) > 0;

    if($any_snippets || $any_counters) {
        phpconsole_curl('http://app.phpconsole.com/api/0.1/', array(
            'client_code_type' => $client_code_type,
            'client_code_version' => $client_code_version,
            'snippets' => $snippets,
            'counters' => $counters
        ));
    }
}

function phpconsole($data_sent, $user = false) {
    phpconsole_register_shutdown();

    $bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

    $user_hashed_api_key = false;
    $user_api_key = false;
    $project_api_key = false;
    $continue = false;

    if($user === false) {
        if(isset($_COOKIE['phpconsole_user'])) {
            $user_hashed_api_key = $_COOKIE['phpconsole_user'];
        }
    }
    else {
        if(isset($GLOBALS['phpconsole_users'][$user])) {
            $user_hashed_api_key = $GLOBALS['phpconsole_users'][$user];
        }
    }

    if($user_hashed_api_key !== false) {
        if(isset($GLOBALS['phpconsole_projects'][$user_hashed_api_key])) {
            $project_api_key = $GLOBALS['phpconsole_projects'][$user_hashed_api_key];
            $user_api_key = $GLOBALS['phpconsole_user_api_keys'][$user_hashed_api_key];
            $continue = true;
        }
    }

    if($continue) {
        $GLOBALS['phpconsole_snippets'][] =  array(
            'data_sent' => base64_encode(serialize($data_sent)),
            'file_name' => $bt[0]['file'],
            'line_number' => $bt[0]['line'],
            'address' => phpconsole_current_page_address(),
            'user_api_key' => $user_api_key,
            'project_api_key' => $project_api_key
        );
    }

    return $data_sent;
}

function phpcounter($number = 1, $user = false) {
    phpconsole_register_shutdown();

    $user_api_key = false;

    if($user === false) {
        if(isset($_COOKIE['phpconsole_user'])) {
            $user_api_key = $_COOKIE['phpconsole_user'];
        }
    }
    else {
        if(isset($GLOBALS['phpconsole_users'][$user])) {
            $user_api_key = $GLOBALS['phpconsole_users'][$user];
        }
    }

    if($user_api_key !== false) {
        if(!isset($GLOBALS['phpconsole_counters'][$user_api_key][$number])) {
            $GLOBALS['phpconsole_counters'][$user_api_key][$number] = 0;
        }

        $GLOBALS['phpconsole_counters'][$user_api_key][$number]++;
    }
}

function phpconsole_cookie($name) {
    phpconsole_register_shutdown();

    if(isset($GLOBALS['phpconsole_users'][$name])) {
        $user_api_key = $GLOBALS['phpconsole_users'][$name];

        setcookie('phpconsole_user', $user_api_key, time()+60*60*24*365, '/', $GLOBALS['phpconsole_domain']);

        phpconsole('Cookie for user "'.$name.'" and domain "'.$GLOBALS['phpconsole_domain'].'" has been set.', $name);
    }
}

function phpconsole_destroy_cookie($name) {
    phpconsole_register_shutdown();

    if(isset($GLOBALS['phpconsole_users'][$name])) {
        setcookie('phpconsole_user', '', 0, '/', $GLOBALS['phpconsole_domain']);

        phpconsole('Cookie for user "'.$name.'" and domain "'.$GLOBALS['phpconsole_domain'].'" has been destroyed.', $name);
    }
}

function phpconsole_current_page_address() {

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
