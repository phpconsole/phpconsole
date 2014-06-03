# phpconsole
[![Build Status](https://travis-ci.org/phpconsole/phpconsole.png?branch=master)](https://travis-ci.org/phpconsole/phpconsole)

## What is phpconsole?

In one sentence, phpconsole is a detached logging facility for PHP to aid your daily development routine. What does it mean, exactly?

The main aim of phpconsole is to replace burdensome `print_r()` and `var_dump()` functions with something vastly superior.

Phpconsole lets you send data to external server, where the data is processed and displayed in user-friendly form. The code is indented and highlighted and you can see additional information, like the address used to trigger the code, the date and time or the location of the code within your project.


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
    "phpconsole/phpconsole": "2.*"
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

1. Move `src/Phpconsole/` folder somewhere into your project (`third_party`, `libraries`, etc)

2. Place your [configuration file](https://github.com/phpconsole/phpconsole/blob/master/src/config/config.php) within the `Phpconsole` folder you just copied and name it `config.php`

3. Update your details in `config.php` (see "Configuration" section below)

4. Include php files in your code

    ```php
    include_once('path/to/Phpconsole/Phpconsole.php');
    include_once('path/to/Phpconsole/P.php');
    ```

## Configuration

Here's an example `config.php` file:

```php
<?php

return array(

    'projects' => array( // required

        'peter'     => 'oadUTDzssID9LALP3WXF25XqHd6rqv7Q9fF',
        'mat'       => 'YC0dmAwmkPDSeZGtBargcWfw52shlIVy867',
        'andrew'    => 'sErFSU21641s2YNhbBrJ3erhBUSnyLALP3W'

        ),

    'defaultProject' => 'peter', // optional

    'contextSize' => 20 // optional
    );
```

Array `projects` represents projects created on [phpconsole.com](http://phpconsole.com/). Each project has unique API key (64 chars). You should give it a short, memorable name that you will use while working with phpconsole. A good practice is to use your own name/nickname when working with other developers.

Variable `defaultProject` is used when no project has been specified either within function call or passed using one of the alternative ways (see below). You might want to set it to `none` when default behaviour should be to ignore it (e.g. working on live server).

You can set how much context is being sent to phpconsole by changing value of `contextSize`. You can also completely disable this feature by setting `isContextEnabled` to `false`. See "Security/privacy concerns" section below if you're not too happy to send this data to external server.

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

It's been brought to my attention several times that developers are wary of sending sensitive data to some random server on the internet, which is completely understandable. To remedy that, one of the upcoming features is going to be **end-to-end encryption** (AES-256), encrypting data before it leaves developer's server and decrypting it in developer's browser. What this means is that (if encryption is enabled) phpconsole.com will **never** have access to plain-text version of data sent, it will also **never** store developer's key used to encrypt/decrypt data.

I'd like to be very clear that I have no intention whatsoever to use your data in a way that you, the developer, wouldn't approve.

Short term solution is to disable context with `context_enabled` (see "Configuration" section above) and remove your data from phpconsole.com's servers within project's settings when you're done working.

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

One of the most common reasons phpconsole.com doesn't receive data from your server is missing certificate info (that's completely separate from https address for your page!) - especially common when developing locally. You should be covered when using Composer, otherwise make sure that `certs_location` is set correctly.

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
