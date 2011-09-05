FirePHP extension for Yii Framework
============================================

This extension contains 2 log route classes. The first, 
[SFirePHPLogRoute](https://github.com/shiki/yii-firephp/blob/master/SFirePHPLogRoute.php) processes 
standard Yii log messages. The second, 
[SFirePHPProfileLogRoute](https://github.com/shiki/yii-firephp/blob/master/SFirePHPProfileLogRoute.php) 
processes profile summaries. Both classes send all output to FirePHP. The classes work similarly 
to [CWebLogRoute](http://www.yiiframework.com/doc/api/1.1/CWebLogRoute) 
and [CProfileLogRoute](http://www.yiiframework.com/doc/api/1.1/CProfileLogRoute). 
The only major difference is the target output.

An advantage of using this extension is that logging and profiling work even through AJAX
requests. Logging of arrays is also possible.

This extension currently supports FirePHPCore 0.3.2. Support for FirePHP 1.0 is planned.

Requirements
------------

* A Yii Framework project
* Install Firebug and FirePHP plugins for Firefox. See http://firephp.org. 
* Set output_buffering setting to true in php.ini. 

Installation
------------

1. Download and extract the contents to a folder under your extensions directory. 
   This can be "/protected/extensions/firephp".
2. Modify your config file to include the log route classes.

##### config file code (i.e. /protected/config/main.php)    
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
                'class' => 'ext.firephp.SFirePHPLogRoute', 
                'levels' => 'error, warning, info, trace',
            ),
            // profile log route
            array(
                'class' => 'ext.firephp.SFirePHPProfileLogRoute',
                'report' => 'summary' // or "callstack"
            ),
        ),
    ),

    ....

Standard logging
-----

Once you've got the extension setup in the config, you can use Yii's logging methods to log messages to FirePHP.

    // logging an INFO message (arrays will work and looks awesome in FirePHP)
    Yii::log(array('username' => 'Shiki', 'profiles' => array('twidl', 'twitter', 'facebook')), CLogger::LEVEL_INFO);

    // logging a WARNING message
    Yii::log("You didn't setup a profile, are you really a person?", CLogger::LEVEL_WARNING);

    // logging with a CATEGORY (categories are displayed as "labels" in FirePHP -- just an additional info text)
    Yii::log('Profile successfully created', CLogger::LEVEL_INFO, 'application.user.profiles');

    // tracing simple text
    Yii::trace('Loading application.user.profiles.ninja', 'application.user.profiles');

    // logging an ERROR
    Yii::log('We have successfully determined that you are not a person', CLogger::LEVEL_ERROR, 'Any category/label will work');

See more about logging [here](http://www.yiiframework.com/doc/guide/1.1/en/topics.logging).


Profiling
-----

Profiling works by simply using Yii's profiling methods.

    Yii::beginProfile('a somewhat slow method');

    ...
    // some function calls here
    // more function calls 

    Yii::beginProfile('nested profile');
    // you can also nest profile calls
    Yii::endProfile('nested profile');

    Yii::endProfile('a somewhat slow method'); // end

You can also profile SQL executions. See more about that and profiling in general
[here](http://www.yiiframework.com/doc/guide/1.1/en/topics.logging#performance-profiling).

-end-
