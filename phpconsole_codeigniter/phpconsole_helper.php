<?php

/*

Instructions for CodeIgniter:

1. Add your domain info and credentials below
2. Move 'phpconsole/phpconsole.php' file to 'application/libraries' folder
3. Move 'phpconsole_codeigniter/phpconsole_helper.php' to 'application/helpers' folder
4. Add 'phpconsole' to 'Auto-load Libraries' in 'application/config/autoload.php' file
5. Add 'phpconsole' to 'Auto-load Helper Files' in 'application/config/autoload.php' file

*/

function phpconsole_init() {
    $CI =& get_instance();

    if(!$CI->phpconsole->is_initialized()) {

        /*
        ==============================================
        USER'S SETTINGS
        ==============================================
        */

        $CI->phpconsole->set_domain('.your-domain.com');  // don't forget to use leading dot, like so: .your-domain.com
        $CI->phpconsole->add_user('nickname', 'user_api_key', 'project_api_key'); // you can add more developers, just execute another add_user()

    }
}

function phpconsole($data_sent, $user = false) {
    phpconsole_init();

    $CI =& get_instance();
    return $CI->phpconsole->send($data_sent, $user);
}

function phpcounter($number = 1, $user = false) {
    phpconsole_init();

    $CI =& get_instance();
    $CI->phpconsole->count($number = 1, $user);
}

function phpconsole_cookie($name) {
    phpconsole_init();

    $CI =& get_instance();
    $CI->phpconsole->set_user_cookie($name);
}

function phpconsole_destroy_cookie($name) {
    phpconsole_init();

    $CI =& get_instance();
    $CI->phpconsole->destroy_user_cookie($name);
}
