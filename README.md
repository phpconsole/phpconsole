# What is phpconsole?

In one sentence, phpconsole is a detached logging facility for PHP, JS and other environments, with analytical twist, to aid your daily development routine. What does it mean, exactly?

The main aim of phpconsole is to replace burdensome **print_r()** and **var_dump()** functions with something vastly superior.

Phpconsole lets you send data to external server, where the data is processed and displayed in user-friendly form. The code is indented and highlighted and you can see additional information, like the address used to trigger the code, the date and time or the location of the code within your project.

At this moment it only works with PHP, unless you feel like porting the client code to other languages. All client libraries are going to be stored on public GitHub account.

Check out [product tour](http://phpconsole.com/tour) and a [quick video](http://vimeo.com/58393977) showing phpconsole in action.

## How easy is it to use phpconsole?

```php
include_once('path/to/phpconsole/install.php');
p($foo);
```

That easy!

## Requirements

- internet connection (to send data back to phpconsole.com server)
- PHP 5.3.6
- cURL

## Installation

**1. Copy "phpconsole" folder to your project**

**2. Update your details in install.php file**

- Set up domain (developing locally? See FAQ)
- Add user - use API keys visible on your dashboard

**3. Install it with the following code**

```php
include_once('path/to/phpconsole/install.php');
```

**4. Test it**

```php
p('Hello world!', 'your-nickname');
```

**Are you using CodeIgniter, Laravel, Magento or MODX?**

Lucky you, you can find phpconsole libraries created specifically for these framework:
- [CodeIgniter](https://github.com/phpconsole/phpconsole-codeigniter)
- [Laravel](https://github.com/Prologue/Phpconsole)
- [Magento](https://github.com/iamjessu/magePhpconsole)
- [MODX](http://modx.com/extras/package/phpconsole)


## Frequently Asked Questions

**Can I use phpconsole to develop locally?**

Totally! The only issue is that browsers can't save cookies for "http://localhost/" domain. You have at least 2 options:

- Add a new record to your `/etc/hosts` file, e.g. `127.0.0.1 mywebsite.dev` and use that new domain
- Use http://localhost.phpconsole.com/ instead of http://localhost/ (it points to 127.0.0.1, in other words - your localhost)

**How to send data to all users on the project at once?**

Specify second parameter for p()/phpconsole() or pc()/phpcounter() as `all`:

```php
p('Super important message from server!', 'all');
```

## Useful links

[phpconsole.com](http://phpconsole.com) - main page

[Getting Started Guide](https://docs.google.com/document/d/1gdmk6USG5q92tDJjqrC35oYnBhnk6xZ4_Z77vDbdmns/edit) - how to set up basic version of phpconsole (always up to date)

[Getting Started Video](http://vimeo.com/58393977) - Installing phpconsole in under 3 minutes

[Getting Started (with CodeIgniter)](https://docs.google.com/document/d/14LGF1D4WKgw7GlERjDNyktPWfb3MVx_52ZlydqUzZkA/edit) - how to set up CodeIgniter framework to work with phpconsole

Check out our [Product Tour](http://phpconsole.com/tour).

## Changelog

See CHANGELOG.md file

## License

See LICENSE file
