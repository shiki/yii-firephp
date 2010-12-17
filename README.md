FirePHP LogRoute extension for Yii Framework
============================================

This extension sends Yii log messages to FirePHP. It inherits from CLogRoute and uses the same method and configuration as the other built-in logging components: CFileLogRoute, CWebLogRoute, etc.

Requirements
------------

* A Yii Framework project
* Install Firebug and FirePHP plugins for Firefox. See http://firephp.org
* Set output_buffering setting to true in php.ini. 

Installation
------------

1. Download and extract the "shiki" folder to your extensions directory. This is usually "/protected/extensions".
2. Download the FirePHP core class and put it somewhere in your "/protected" directory. I usually put these files in /protected/vendors.
3. Modify your config file to include this LogRoute class and set the "libPath" property to the directory containing the FirePHP.class.php and fb.php files.
This cannot be a Yii alias.:

##### config file code (i.e. /protected/config/main.php)    
    ....

    'log'=>array(
        'class'=>'CLogRouter',
        'routes'=>array(
            // the default (file logger)
            array(
                'class'=>'CFileLogRoute',
                'levels'=>'error, warning',
            ),
            // the FirePHP LogRoute
            array(
                'class' => 'ext.shiki.firePHPLogRoute.ShikiFirePHPLogRoute', // "ext" alias points to /protected/extensions   
                'libPath' => dirname(__FILE__) . '/path/to/firephp/lib',
            ),
        ),
    ),

    ....

Usage
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


-end-
