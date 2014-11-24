# Phpconsole
[![Build Status](https://travis-ci.org/phpconsole/phpconsole.png?branch=master)](https://travis-ci.org/phpconsole/phpconsole)

## What is Phpconsole?

In one sentence, Phpconsole is a detached logging facility for PHP to aid your daily development routine. What does it mean, exactly?

The main aim of Phpconsole is to replace burdensome `print_r()` and `var_dump()` functions with something vastly superior.

Phpconsole lets you send encrypted (AES-256) data to external server, where the data is processed and displayed in user-friendly form. The code is indented and highlighted and you can see additional information, like the address used to trigger the code, the date and time or the location of the code within your project.


Check out [product tour](http://phpconsole.com/tour) and a [quick video](http://vimeo.com/58393977) showing Phpconsole in action.

## How easy is it to use Phpconsole?

```php
p($foo); // that easy!
```

## Requirements

- account on [phpconsole.com](https://app.phpconsole.com/signup/)
- internet connection (to send data back to phpconsole.com server)
- PHP 5.3.0 or later
- cURL

## Installation

### Simple installation - standalone package

1. Download the [standalone package](http://cdn.phpconsole.com/standalone/phpconsole-standalone.zip) and unzip it

2. Move `phpconsole.php` file into your project

3. Add your project API key to the top of `phpconsole.php` file

4. You can use Phpconsole now:

    ```php
    require 'path/to/phpconsole.php';
    p('Hello World!');
    ```

5. (optional, recommended) Download [cacert.pem](http://curl.haxx.se/ca/cacert.pem) file and put it in the same folder as `phpconsole.php` - it will allow for API endpoint verification, but it's not required to start using Phpconsole.

### Advanced installation - Composer / Packagist

1. Add the following to `require` within your `composer.json` file:

    ```
    "phpconsole/phpconsole": "3.*"
    ```

2. Place your [configuration file](https://github.com/phpconsole/phpconsole/blob/master/src/config/config.php) into **one** of these locations:

    ```
    phpconsole_config.php
    app/config/phpconsole.php
    app/config/packages/phpconsole/phpconsole/config.php
    config/packages/phpconsole/phpconsole/config.php
    application/config/phpconsole.php
    ```

3. Update your details in `config.php` (see "Configuration" section below)

4. Execute `composer update` to pull the package into your project

Package details: https://packagist.org/packages/phpconsole/phpconsole

### Advanced installation - Laravel 4

1. Follow steps 1-4 for Composer above. Recommended location for `config.php` is:

    ```php
    app/config/packages/phpconsole/phpconsole/config.php
    ```

    You can copy config executing:

    ```
    php artisan config:publish phpconsole/phpconsole
    ```

## Configuration

Here's an example `config.php` file:

```php
<?php

return array(

    'defaultProject' => 'peter', // optional

    'projects' => array( // required

        'peter' => array(
            'apiKey'             => 'oadUTDzssID9LALP3WXF25XqHd6rqv7Q9fF', // required
            'encryptionPassword' => 'passw0rd' // optional
        ),

        'george' => array(
            'apiKey'             => 'YC0dmAwmkPDSeZGtBargcWfw52shlIVy867',
            'encryptionPassword' => 'passw0rdH4xor'
        ),

        'tim' => array(
            'apiKey'             => 'sErFSU21641s2YNhbBrJ3erhBUSnyLALP3W'
        )

    ),

    'contextSize' => 10 // optional

);
```

Variable `defaultProject` is used when no project has been specified either within function call or passed using one of the alternative ways (see below). You might want to set it to `none` when default behaviour should be to ignore it (e.g. working on live server).

Array `projects` represents projects created on [phpconsole.com](http://phpconsole.com/). Each project has unique API key (64 chars). You should give it a short, memorable name that you will use while working with Phpconsole. A good practice is to use your own name/nickname when working with other developers.

Variable `encryptionPassword` holds passwords used to encrypt your data with AES-256 before sending it off to Phpconsole's servers. You will be asked to provide the password when displaying data on phpconsole.com. See "Security/privacy concerns" below.

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

## Usage

Here's how to use Phpconsole:

```php
// basic usage, send to default project
p($foo);

// send to George's project
p($foo, 'george');

// send to Tim's project and mark as error
p($foo, array('project' => 'tim', 'type' => 'error'));
```

You can send several types of snippets:

```php
p($_SERVER);

p('The job has been processed successfully', array('type' => 'success'));

p('Memory usage: '.$memory_usage, array('type' => 'info'));

p('Error: '.$error_message, array('type' => 'error'));
```

You can always create Phpconsole object yourself:

```php
$phpconsole = new \Phpconsole\Phpconsole;
$phpconsole->send('This is a very important message', 'peter');
```

## Frequently Asked Questions

**Can I use Phpconsole to develop locally?**

Totally! Just make sure your server can reach phpconsole.com to send data.

**How to send data to all projects at once?**

```php
$phpconsole = new \Phpconsole\Phpconsole;
$phpconsole->sendToAll('Super important message for all developers!');
```

**Is there a way to install entire Phpconsole locally?**

No, unfortunately the only option right now is to use Phpconsole library with phpconsole.com. Worried about privacy? Read "Security/privacy concerns" below.

## Security/privacy concerns

It's been brought to my attention a number of times that developers are wary of sending sensitive data to some random server on the internet, which is completely understandable. This is why Phpconsole supports **end-to-end AES-256 encryption**. You can provide a password using `encryptionPasswords` variable in your config file, which will be used to encrypt all data before it leaves your server. The data will be decrypted in your browser on [phpconsole.com](http://phpconsole.com/) after you provide the password. Don't worry, the password is never sent back to Phpconsole's servers. Decryption happens in your browser before showing data on the screen. Phpconsole will **never** have access to plain-text version of data sent, it will also **never** store developer's key used to encrypt/decrypt data.

If you have more questions about security of your data, you can reach me at peter@phpconsole.com .

## Troubleshooting

**Class 'p' not found in...**

Make sure you don't use deprecated `p::send()`, replace it with `p()`.

**phpconsole.com doesn't show my data**

Starting from version 3.1.0, Phpconsole has a "debug mode" that you can enable by changing `debug` option in your config file from `false` to `true`. When attempting to send data through Phpconsole, a `Phpconsole debug info` section is going to be appended to your page, showing a log of events in the system (pay attention to the ones highligted) and a bunch of basic information about your setup. Make sure that the config visible there matches what you have in your file.

You can always reach out to support@phpconsole.com, attaching the info from the debug mode.

## Useful links

[phpconsole.com](http://phpconsole.com) - main page

[Getting Started Video](http://vimeo.com/58393977) - Installing Phpconsole in under 3 minutes

[Getting Started (with CodeIgniter)](https://docs.google.com/document/d/14LGF1D4WKgw7GlERjDNyktPWfb3MVx_52ZlydqUzZkA/edit) - how to set up CodeIgniter framework to work with Phpconsole

Check out our [Product Tour](http://phpconsole.com/tour).

## Author

Peter Legierski - peter@phpconsole.com - [@peterlegierski](https://twitter.com/peterlegierski)

## Changelog

See CHANGELOG.md file

## License

See LICENSE file
