<?php

include_once('phpconsole.php');

global $phpconsole;
$phpconsole = new Phpconsole();
$phpconsole->set_backtrace_depth(1);

/*
==============================================
USER'S SETTINGS
==============================================
*/

$phpconsole->set_domain('.your-domain.com');  // don't forget to use leading dot, like so: .your-domain.com
$phpconsole->add_user('nickname', 'project_api_key'); // you can add more developers, just execute another add_user()




function phpconsole($data_sent, $user = false) {
    global $phpconsole;
    return $phpconsole->send($data_sent, $user);
}

function phpconsole_cookie($name) {
    global $phpconsole;
    $phpconsole->set_user_cookie($name);
}

function phpconsole_destroy_cookie($name) {
    global $phpconsole;
    $phpconsole->destroy_user_cookie($name);
}

/*
Shorthand function for lazy developers (author included)
*/

function p($data_sent, $user = false) {
    global $phpconsole;
    return $phpconsole->send($data_sent, $user);
}
