# FirePHP extension for Yii Framework

This extension contains 2 log route classes. The first, `SK\Yii\FirePHP\LogRoute`, processes
standard Yii log messages. The second, `SK\Yii\FirePHP\ProfileLogRoute`, processes profile summaries.
Both classes send all output to [FirePHP](http://www.firephp.org/). The classes work similarly
to [`CWebLogRoute`](http://www.yiiframework.com/doc/api/1.1/CWebLogRoute)
and [`CProfileLogRoute`](http://www.yiiframework.com/doc/api/1.1/CProfileLogRoute).
The only major difference is the target output.

An advantage of using this extension is that logging and profiling work even through AJAX requests.

![](http://shiki.me/public/github/yii-firephp/example.png)

## Requirements

* PHP _5.4+_
* A Yii Framework _1.1.14+_ project.
* Firebug and FirePHP plugins for Firefox. See http://firephp.org. Firebug's _Console_ and
  _Net_ tabs have to be enabled for this to work.
* Set `output_buffering` setting to true in `php.ini`. You might also want to increase the buffer
  size to allow large log sizes.

## Installation

The only supported installation method for now is using [Composer](http://getcomposer.org/).

1. Put this in your `composer.json` and run `composer update` to install it:

  ```json
  {
    "require": {
      "shiki/yii-firephp": "dev-master"
    }
  }
  ```


  This will also automatically install the dependency `firephp/firephp-core`.

2. Make sure you have loaded the Composer autoload file (`vendor/autoload.php`) so the libraries
   can be accessed in your Yii config file. See the `main.php` config file in the `example` project
   on how this can be done.

3. Modify your config file (e.g. `protected/config/main.php`) to include the log route classes.

  ```php
  ....

  'log' => array(
    'class' => 'CLogRouter',
    'routes' => array(
      // the default (file logger)
      array(
        'class' => 'CFileLogRoute',
        'levels' => 'error, warning',
      ),
      // standard log route
      array(
        'class' => '\\SK\\Yii\\FirePHP\\LogRoute',
        'levels' => 'error, warning, info, trace',
      ),
      // profile log route
      array(
        'class' => '\\SK\\Yii\\FirePHP\\ProfileLogRoute',
        'report' => 'summary', // or "callstack"
      ),
    ),
  ),

  ....
  ```

## Standard logging

Once you've got the extension setup in the config, you can use Yii's logging methods to log messages to FirePHP.

```php
// logging an INFO message
Yii::log('This is an info message.', CLogger::LEVEL_INFO);

// logging a WARNING message
Yii::log("You didn't setup a profile, are you really a person?", CLogger::LEVEL_WARNING);

// logging with a CATEGORY (categories are displayed as "labels" in FirePHP -- just an additional info text)
Yii::log('Profile successfully created', CLogger::LEVEL_INFO, 'application.user.profiles');

// tracing simple text
Yii::trace('Loading application.user.profiles.ninja', 'application.user.profiles');

// logging an ERROR
Yii::log('We have successfully determined that you are not a person',
  CLogger::LEVEL_ERROR, 'Any category/label will work');

// If you need to log an array, you can use FirePHP's core methods
FB::warn(array('a' => 'b', 'c' => 'd'), 'an.array.warning');
```

See more about logging [here](http://www.yiiframework.com/doc/guide/1.1/en/topics.logging).


## Profiling

Profiling works by simply using Yii's profiling methods.

```php
Yii::beginProfile('a somewhat slow method');

...
// some function calls here
// more function calls

Yii::beginProfile('nested profile');
// you can also nest profile calls
Yii::endProfile('nested profile');

Yii::endProfile('a somewhat slow method'); // end
```

You can also profile SQL executions. See more about that and profiling in general
[here](http://www.yiiframework.com/doc/guide/1.1/en/topics.logging#performance-profiling).


## Example

To try all these out, there's an example project in the `example` folder. To run it:

1. Install the required libraries using Composer.

        $ cd example
        $ composer install


2. Run with the PHP [built-in webserver](http://php.net/manual/en/features.commandline.webserver.php)

        $ cd example/webroot
        $ php -S localhost:8000

3. Browse [http://localhost:8000](http://localhost:8000) in Firefox. Make sure first that Firebug is opened and the
   Console and Net tabs are enabled. You should be able to see the FirePHP logs in Firebug's console.
   If you don't, try refreshing first.

