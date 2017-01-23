Minecraft auth service
================

[![Build Status](https://travis-ci.org/Exitialis/Mas.svg?branch=master)](https://travis-ci.org/Exitialis/Mas)
[![Latest Stable Version](https://poser.pugx.org/exitialis/mas/v/stable)](https://packagist.org/packages/exitialis/mas)
[![Total Downloads](https://poser.pugx.org/exitialis/mas/downloads)](https://packagist.org/packages/exitialis/mas)
[![Latest Unstable Version](https://poser.pugx.org/exitialis/mas/v/unstable)](https://packagist.org/packages/exitialis/mas)
[![License](https://poser.pugx.org/exitialis/mas/license)](https://packagist.org/packages/exitialis/mas)
[![Monthly Downloads](https://poser.pugx.org/exitialis/mas/d/monthly)](https://packagist.org/packages/exitialis/mas)

The package designed for your site with your minecraft client integration. The package includes the following features:

- Authenticate users from your site, which can work on engines, like WordPress or DLE.
- Skins and cloaks system for users.
- Default skins and cloaks for all users on your server, that don't have their own.

Navigation
--------

- [Required](#required)
- [Installation](#installation)
- [Usage](#usage)
- [Testing](#testing)
- [Contributing](#contributing)
- [Credits](#credits)
- [License](#license)

Required
--------

- Install Laravel.
- Configure connection to MySQL in Laravel.

Installation
------------


Add the mas package to your `composer.json` file.

``` json
{
    "require": {
        "exitialis/mas": "1.0.*"
    }
}
```

Or via the command line in the root of your Laravel installation.
DON'T USING ROOT USER!

``` bash
$ composer require "exitialis/mas"
```

Add to your `config/app.php` file in Laravel. 

``` 
'providers' => [
        .....
         /*
         * Package Service Providers...
         */
		Exitialis\Mas\MasServiceProvider::class,
        .....
]
```

Via the command line in the root of your Laravel installation.

``` bash
$ php artisan migrate
$ php artisan vendor:publish
```

PROFIT :)

Usage
-----

Edit your file `config/mas.php` under CMS DLE or WordPress.

DLE:
```
    /**
     * Available hashes:
     * wp, dle
     */
    'hash' => 'dle', 

    'repositories' => [
        'user' => [
            'login_column' => 'name',
            'password_column' => 'password',
            'table_name' => 'dle_users',
            'key' => 'user_id',
        ],
    ],
```
WP:
```
    /**
     * Available hashes:
     * wp, dle
     */
    'hash' => 'wp',

    'repositories' => [
        'user' => [
            'login_column' => 'user_login',
            'password_column' => 'user_pass',
            'table_name' => 'bjsvyp8zhw_users',
            'key' => 'ID',
        ],
    ],
```
Create folders in the folder `public` of your Laravel installation:

- textures/cloak
- textures/skin
- cache
- clients/hash
- clients/{NAME_CLIENTS} (HiTech, Sandbox, Etc)

Testing
-------

``` bash
$ phpunit
```
