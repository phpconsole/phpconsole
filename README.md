# Phpconsole
[![Build Status](https://travis-ci.org/phpconsole/phpconsole.png?branch=master)](https://travis-ci.org/phpconsole/phpconsole)

## What is Phpconsole?

In one sentence, Phpconsole is a detached logging facility for PHP to aid your daily development routine. What does it mean, exactly?

The main aim of Phpconsole is to replace burdensome `print_r()` and `var_dump()` functions with something vastly superior.

Phpconsole lets you send encrypted (AES-256) data to external server, where the data is processed and displayed in user-friendly form. The code is indented and highlighted and you can see additional information, like the address used to trigger the code, the date and time or the location of the code within your project.

You can collaborate on the project with others, just add them to the Organisation and they will have access to all projects within it.


Check out [product tour](http://phpconsole.com/tour) and a [quick video](https://www.youtube.com/watch?v=zSIIWsZyuMY) showing Phpconsole in action.

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

1. Log into your project at [phpconsole.com](https://app.phpconsole.com/login/) and click `Download phpconsole.php file` button

2. Move `phpconsole.php` file into your project

3. Require the file and start using Phpconsole right away:

    ```php
    require 'path/to/phpconsole.php';
    p('Hello World!');
    ```
    As you can see, there's no need to configure Phpconsole when using the `Simple installation` flow.

4. (optional, recommended) Download [cacert.pem](http://curl.haxx.se/ca/cacert.pem) file and put it in the same folder as `phpconsole.php` - it will allow for API endpoint verification, but it's not required to start using Phpconsole.

### Advanced installation - Composer / Packagist

1. Add the following to `require` within your `composer.json` file:

    ```
    "phpconsole/phpconsole": "3.*"
    ```

2. Execute `composer update` to pull the package into your project

3. Place your [configuration file](https://github.com/phpconsole/phpconsole/blob/master/src/config/config.php) into **one** of these locations:

    ```
    phpconsole_config.php
    app/config/phpconsole.php
    app/config/packages/phpconsole/phpconsole/config.php
    config/packages/phpconsole/phpconsole/config.php
    application/config/phpconsole.php
    ```

4. Update your details in `config.php` (see "Configuration" section below)

Package details: https://packagist.org/packages/phpconsole/phpconsole

### Advanced installation - Laravel 4

1. Add the following to `require` within your `composer.json` file:

    ```
    "phpconsole/phpconsole": "3.*"
    ```

2. Execute `composer update` to pull the package into your project

3. Add config file by executing

    ```
    php artisan config:publish phpconsole/phpconsole
    ```

    You will find it in

    ```php
    app/config/packages/phpconsole/phpconsole/config.php
    ```

4. Update your details in `config.php` (see "Configuration" section below)

## Configuration

`Simple installation` doesn't require any configuration. For details about configuring package for Composer / Laravel, see the [Configuration file](CONFIGURATION.md).

## Usage

Here's how to use Phpconsole:

```php
// basic usage, send to default project
p($foo);

// send to project 'emails'
p($foo, 'emails');

// send to project 'backup' and mark as error
p($foo, array('project' => 'backup', 'type' => 'error'));
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
$phpconsole->send('This is a very important message');
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

Phpconsole supports **end-to-end AES-256 encryption**. You can provide a password using `encryptionPasswords` variable in your config file, which will be used to encrypt all data before it leaves your server. The data will be decrypted in your browser on [phpconsole.com](http://phpconsole.com/) after you provide the password. The password is never sent back to Phpconsole's servers. Decryption happens in your browser before showing data on the screen. Phpconsole will **never** have access to plain-text version of data sent, it will also **never** store developer's key used to encrypt/decrypt data.

If you have more questions about security of your data, you can reach me at peter@phpconsole.com .

## Troubleshooting

**Class 'p' not found in...**

Make sure you don't use deprecated `p::send()`, replace it with `p()`.

**phpconsole.com doesn't show my data**

Starting from version 3.1.0, Phpconsole has a "debug mode" that you can enable by changing `debug` option in your config file from `false` to `true`. When attempting to send data through Phpconsole, a `Phpconsole debug info` section is going to be appended to your page, showing a log of events in the system (pay attention to the ones highligted) and a bunch of basic information about your setup. Make sure that the config visible there matches what you have in your file.

You can always reach out to support@phpconsole.com, attaching the info from the debug mode.

## Useful links

[phpconsole.com](http://phpconsole.com) - main page

[Getting Started Video](https://www.youtube.com/watch?v=zSIIWsZyuMY) - Installing Phpconsole in under 2 minutes

[Getting Started (with CodeIgniter)](https://docs.google.com/document/d/14LGF1D4WKgw7GlERjDNyktPWfb3MVx_52ZlydqUzZkA/edit) - how to set up CodeIgniter framework to work with Phpconsole

Check out our [Product Tour](http://phpconsole.com/tour).

## Author

Peter Legierski - peter@phpconsole.com - [@peterlegierski](https://twitter.com/peterlegierski)

## Changelog

See CHANGELOG.md file

## License

See LICENSE file
