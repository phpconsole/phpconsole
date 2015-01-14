## Configuration

Here's an example `config.php` file:

```php
<?php

return array(

    'defaultProject' => 'default', // optional

    'projects' => array( // required

        'default' => array(
            'apiKey'             => 'oadUTDzssID9LALP3WXF25XqHd6rqv7Q9fF', // required
            'encryptionPassword' => 'passw0rd' // optional
        ),

        'emails' => array(
            'apiKey'             => 'YC0dmAwmkPDSeZGtBargcWfw52shlIVy867',
            'encryptionPassword' => 'passw0rdH4xor'
        ),

        'backup' => array(
            'apiKey'             => 'sErFSU21641s2YNhbBrJ3erhBUSnyLALP3W'
        )

    ),

    'contextSize' => 10 // optional

);
```

Variable `defaultProject` is used when no project has been specified either within function call or passed using one of the alternative ways (see below). You might want to set it to `none` when default behaviour should be to ignore it (e.g. working on live server).

Array `projects` represents projects created on [phpconsole.com](http://phpconsole.com/). Each project has unique API key (64 chars). You should give it a short, memorable name that you will use while working with Phpconsole.

Variable `encryptionPassword` holds passwords used to encrypt your data with AES-256 before sending it off to Phpconsole's servers. You will be asked to provide the password when displaying data on phpconsole.com.

You can set how much context is being sent to Phpconsole by changing value of `contextSize`. You can also completely disable this feature by setting `isContextEnabled` to `false`. See "Security/privacy concerns" section below.

### Alternative ways to load config

1. Set constant `PHPCONSOLE_CONFIG_LOCATION` and point it to your config file.

2. Load config array after creating Phpconsole object:

    ```php
    $alternativeConfig = array(
        'projects' => array(
            'peter' => array(
                'apiKey' => 'oadUTDzssID9LALP3WXF25XqHd6rqv7Q9fF'
            ),
        )
    );

    $config = new Phpconsole\Config;
    $config->loadFromArray($alternativeConfig);

    $phpconsole = new Phpconsole\Phpconsole($config);
    ```

### Alternative ways to select default project

1. Create cookie executing the following in your browser's developer tools:

    ```javascript
    document.cookie="phpconsole_default_project=peter; expires=Wed, 1 Jan 2048 13:37:00 GMT; path=/";
    ```

2. Create file `.phpconsole_default_project` in the root folder of your project and set name of the project as contents of the file, e.g.

    ```
    peter
    ```

3. Set constant `PHPCONSOLE_DEFAULT_PROJECT` with the name of the project
