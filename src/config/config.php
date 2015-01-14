<?php

// Visit https://app.phpconsole.com/docs to learn more

return array(

    // Change to true to start debugging Phpconsole. Debug info will be displayed after the page loads

    'debug' => false,



    // You can specify a default project for your Phpconsole installation
    // Set to 'none' to disable phpconsole when no project explicitly specified

    'defaultProject' => 'default',



    // Add your project's API key below

    'projects' => array(

        'default' => array(
            'apiKey'             => '64-chars-long-project-api-key', // required, copy it from phpconsole.com project
            //'encryptionPassword' => 'r4nd0m3ncrypti0np4ssw0rd'       // optional, for AES-256 encryption of your data
        )

        // You can add more projects here

    ),



    // How many lines of code around executed Phpconsole method should be
    // sent over to your phpconsole.com project. Concerned about the safety
    // of your code? Enable encryption by choosing your password above

    'contextSize' => 10,



    // Choose which function to use to capture data output:
    // - print_r
    // - var_dump

    'captureWith' => 'print_r'

);
