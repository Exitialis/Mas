Minecraft auth service
================

[![Build Status](https://travis-ci.org/Exitialis/Mas.svg?branch=master)](https://travis-ci.org/Exitialis/Mas)
[![Latest Stable Version](https://poser.pugx.org/exitialis/mas/v/stable)](https://packagist.org/packages/exitialis/mas)
[![Total Downloads](https://poser.pugx.org/exitialis/mas/downloads)](https://packagist.org/packages/exitialis/mas)
[![Latest Unstable Version](https://poser.pugx.org/exitialis/mas/v/unstable)](https://packagist.org/packages/exitialis/mas)
[![License](https://poser.pugx.org/exitialis/mas/license)](https://packagist.org/packages/exitialis/mas)
[![Monthly Downloads](https://poser.pugx.org/exitialis/mas/d/monthly)](https://packagist.org/packages/exitialis/mas)

:package_description

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


Testing
-------

``` bash
$ phpunit
```
