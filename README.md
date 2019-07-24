#Laravel Interactions

##Requirements

Make sure all dependencies have been installed before moving on:

* [PHP](http://php.net/manual/en/install.php) >= 7.0

Pull the package via Composer:
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
composer require bigdropinc/laravel-interactions "~1.0.0"
```

or add

```
"bigdropinc/laravel-interactions": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply use it in your code by  :

```php
Interaction::create(request()->all())->run();
