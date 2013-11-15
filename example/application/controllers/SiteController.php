<?php

class SiteController extends Controller
{
  public function actionIndex()
  {
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

    // Profiling

    Yii::beginProfile('rendering');

    for ($i = 0; $i < 30; $i++)
      $this->runProfilingSampleLoop();

    $this->render('index');
    Yii::endProfile('rendering');
  }

  private function runProfilingSampleLoop()
  {
    Yii::beginProfile('dummy method');
    for ($i = 0; $i < 100; $i++) {
      $a = 1;
    }
    Yii::endProfile('dummy method');
  }
}
