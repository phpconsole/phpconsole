# Upgrading Phpconsole

## From 2.x.x to 3.0.0

#### Config file

The config file has changed. Here's the old format:

```php
return array(

    'default_project' => 'peter',

    'projects' => array(

        'peter' => 'oadUTDzssID9LALP3WXF25XqHd6rqv7Q9fF'

    ),

    'context_size' => 10
);
```

and here's the new format:

```php
return array(

    'defaultProject' => 'peter',

    'projects' => array(

        'peter' => array(
            'apiKey'             => 'oadUTDzssID9LALP3WXF25XqHd6rqv7Q9fF',
            'encryptionPassword' => 'p4ssw0rd' // optional
        ),

    ),

    'contextSize' => 10
);
```

#### Namespace and Class names

Phpconsole package is now PSR2 compatible. You will have to replace all occurrences of `phpconsole/phpconsole` and `phpconsole/p` with `Phpconsole/Phpconsole` and `Phpconsole/P` respectively.

For most people this will mean updating the code from:

```php
use phpconsole\phpconsole;
use phpconsole\p;
```

to:

```php
use Phpconsole\Phpconsole;
use Phpconsole\P as p;
```

or, for Laravel developers, updating `Class Aliases` in `app/config/app.php` to:

```php
'Phpconsole' => 'Phpconsole\Phpconsole',
'p'          => 'Phpconsole\P'
```

and creating Phpconsole object with `new Phpconsole` instead of `new phpconsole`.

You can still use the shorthand `p::send($foo);`
