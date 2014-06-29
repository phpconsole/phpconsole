<?php

// Visit https://app.phpconsole.com/docs to learn more.

return array(

    'projects' => array( // required

        // Add your name and your project API key from phpconsole.com below
        // You can add as many projects/developers as you want

        'your-name'    => 'your-project-api-key',
        'someone'      => 'someones-project-api-key',
        'someone-else' => 'someone-elses-project-api-key'

        ),

    'encryptionPasswords' => array( // optional

        // Add encryption password to make sure all your data is safely encrypted
        // before leaving your server. Phpconsole's servers don't have access
        // to your passwords and don't store plain-text version of your data.
        // This is separate from your phpconsole.com account's password!

        // 'your-name'    => 'your-encryption-password'

        ),

    // You can specify a default project for your phpconsole installation

    'defaultProject' => 'none' // e.g. 'your-name'

    );
