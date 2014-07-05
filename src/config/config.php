<?php

// Visit https://app.phpconsole.com/docs to learn more

return array(

    // You can specify a default project for your Phpconsole installation
    // Set to 'none' to disable phpconsole when no project explicitly specified

    'defaultProject' => 'yourName',



    // Add your name and your project API key from phpconsole.com below
    // You can add as many projects/developers as you want

    'projects' => array(

        'yourName' => array(
            'apiKey'             => '64-chars-long-project-api-key', // required, copy it from phpconsole.com project
            'encryptionPassword' => 'r4nd0m3ncrypti0np4ssw0rd'       // optional, for AES-256 encryption of your data
        ),

        'someoneElse' => array(
            'apiKey'             => 'another-64-chars-long-project-api-key',
            'encryptionPassword' => 'str0ngp4ssw0rd'
        )

    ),



    // How many lines of code around executed Phpconsole method should be
    // sent over to your phpconsole.com project. Concerned about the safety
    // of your code? Enable encryption by choosing your password above

    'contextSize' => 10

);
