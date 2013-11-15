<?php

require(dirname(__FILE__) . '/../../vendor/autoload.php');

return array(
  'basePath' => dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
  'name' => 'Yii FirePHP Example',

  'preload' => array('log'),

  // autoloading model and component classes
  'import' => array(
    'application.models.*',
    'application.components.*',
  ),

  'modules'=>array(

  ),

  // application components
  'components' => array(
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
  ),
);
