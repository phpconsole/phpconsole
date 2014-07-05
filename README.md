# phpconsole
[![Build Status](https://travis-ci.org/phpconsole/phpconsole.png?branch=master)](https://travis-ci.org/phpconsole/phpconsole)

## What is phpconsole?

In one sentence, phpconsole is a detached logging facility for PHP to aid your daily development routine. What does it mean, exactly?

The main aim of phpconsole is to replace burdensome `print_r()` and `var_dump()` functions with something vastly superior.

Phpconsole lets you send encrypted (AES-256) data to external server, where the data is processed and displayed in user-friendly form. The code is indented and highlighted and you can see additional information, like the address used to trigger the code, the date and time or the location of the code within your project.


Check out [product tour](http://phpconsole.com/tour) and a [quick video](http://vimeo.com/58393977) showing phpconsole in action.

## How easy is it to use phpconsole?

```php
p::send($foo); // that easy!
```

## Requirements

- account on [phpconsole.com](https://app.phpconsole.com/signup/)
- internet connection (to send data back to phpconsole.com server)
- PHP 5.3.6
- cURL

## Installation

### Composer / Packagist

1. Add the following to `require` within your `composer.json` file:

    ```
    "phpconsole/phpconsole": "3.*"
    ```

2. Place your [configuration file](https://github.com/phpconsole/phpconsole/blob/master/src/config/config.php) into **one** of these locations:

    ```
    phpconsole_config.php
    app/config/phpconsole.php
    app/config/packages/phpconsole/phpconsole/config.php
    application/config/phpconsole.php
    ```

3. Update your details in `config.php` (see "Configuration" section below)

4. Execute `composer update` to pull the package into your project

5. Put `use Phpconsole\P as p;` at the top of the file where you intend to use phpconsole

Package details: https://packagist.org/packages/phpconsole/phpconsole

### Laravel 4

1. Follow steps 1-4 for Composer above. Recommended location for `config.php` is:

    ```php
    app/config/packages/phpconsole/phpconsole/config.php
    ```

    You can copy config executing:

    ```
    php artisan config:publish phpconsole/phpconsole
    ```

2. Add the following to `Class Aliases` in `app/config/app.php`:

    ```php
    'Phpconsole' => 'Phpconsole\Phpconsole',
    'p'          => 'Phpconsole\P'
    ```

### Without Composer (e.g. CodeIgniter)

1. Download the [standalone package](http://cdn.phpconsole.com/standalone/phpconsole-standalone.zip) and unzip it

2. Move `vendor/`, `composer.json` and `phpconsole_config.php` into the root folder of your project

3. Update your details in `phpconsole_config.php` (see "Configuration" section below)

4. Add the following to the top of your index.php file (right below `<?php`):

    ```php
    require 'vendor/autoload.php';
    ```

5. Add the following somewhere in your code, before you use phpconsole:

    ```php
    use Phpconsole\P as p;
    ```

6. You can use phpconsole now:

    ```php
    p::send('Hello World!', 'your-name');
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

Array `projects` represents projects created on [phpconsole.com](http://phpconsole.com/). Each project has unique API key (64 chars). You should give it a short, memorable name that you will use while working with phpconsole. A good practice is to use your own name/nickname when working with other developers.

Variable `encryptionPassword` holds passwords used to encrypt your data with AES-256 before sending it off to phpconsole's servers. You will be asked to provide the password when displaying data on phpconsole.com. See "Security/privacy concerns" below.

You can set how much context is being sent to phpconsole by changing value of `contextSize`. You can also completely disable this feature by setting `isContextEnabled` to `false`. See "Security/privacy concerns" section below.

### Alternative ways to load config

1. Set constant `PHPCONSOLE_CONFIG_LOCATION` and point it to your config file.

2. Load config array after creating phpconsole object:

    ```php
    $alternativeConfig = array(
        'projects' => array(
            'peter' => 'oadUTDzssID9LALP3WXF25XqHd6rqv7Q9fF'
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

Here's how to use phpconsole:

```php
// basic usage, send to default project
p::send($foo);

// send to Mat's project
p::send($foo, 'mat');

// send to Andrew's project and mark as error
p::error($foo, 'andrew');

// alternatively you could write
p::send($foo, array('project' => 'andrew', 'type' => 'error'));
```

You can send several types of snippets:

```php
p::send($_SERVER);

p::success('The job has been processed successfully');

p::info('Memory usage: '.$memory_usage);

p::error('Error: '.$error_message);
```

Each type can be set as an option, e.g.

```php
p::send('Done!', array('type' => 'success'));
```

You can always create phpconsole object yourself:

```php
$phpconsole = new Phpconsole;
$phpconsole->send('This is a very important message', 'peter');
```

## Frequently Asked Questions

**Can I use phpconsole to develop locally?**

Totally! Just make sure your server can reach phpconsole.com to send data.

**How to send data to all projects at once?**

```php
p::sendToAll('Super important message for all developers!');
```

**Is there a way to install entire phpconsole locally?**

No, unfortunately the only option right now is to use phpconsole library with phpconsole.com. Worried about privacy? Read "Security/privacy concerns" below.

## Security/privacy concerns

It's been brought to my attention a number of times that developers are wary of sending sensitive data to some random server on the internet, which is completely understandable. This is why phpconsole supports **end-to-end AES-256 encryption**. You can provide a password using `encryptionPasswords` variable in your config file, which will be used to encrypt all data before it leaves your server. The data will be decrypted in your browser on [phpconsole.com](http://phpconsole.com/) after you provide the password. Don't worry, the password is never sent back to phpconsole's servers. Decryption happens in your browser before showing data on the screen. Phpconsole will **never** have access to plain-text version of data sent, it will also **never** store developer's key used to encrypt/decrypt data.

If you have more questions about security of your data, you can reach me at peter@phpconsole.com .

## Troubleshooting

**Class 'p' not found in...**

Try to put the following at the top of your php file:

```php
use Phpconsole\P as p;
```

or access phpconsole like so:

```php
Phpconsole\P::send('Hello world', 'peter');
```

**phpconsole.com doesn't show my data**

Download the [standalone package](http://cdn.phpconsole.com/standalone/phpconsole-standalone.zip) and unzip it. Update your details in `phpconsole_config.php` file and try to execute it.

Make sure you copied your project API key correctly.

Try to send data using your project's short name (usually your own name/nickname) as second parameter, e.g.

```php
p::send('Hello world', 'peter');
```


## Useful links

[phpconsole.com](http://phpconsole.com) - main page

[Getting Started Video](http://vimeo.com/58393977) - Installing phpconsole in under 3 minutes

[Getting Started (with CodeIgniter)](https://docs.google.com/document/d/14LGF1D4WKgw7GlERjDNyktPWfb3MVx_52ZlydqUzZkA/edit) - how to set up CodeIgniter framework to work with phpconsole

Check out our [Product Tour](http://phpconsole.com/tour).

## Author

Peter Legierski - peter@phpconsole.com - [@peterlegierski](https://twitter.com/peterlegierski)

## Changelog

See CHANGELOG.md file

## License

See LICENSE file
