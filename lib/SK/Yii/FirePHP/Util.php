<?php

namespace SK\Yii\FirePHP;

/**
 * A utility class used by LogRoute and ProfileLogRoute.
 *
 * @author Shiki <bj@basanes.net>
 */
class Util
{
  /**
   * http://github.com/shiki/yii-firephp/issues#issue/1
   *
   * This error gets thrown "Fatal error: Exception thrown without a stack frame in Unknown on line 0"
   * if FirePHP tries to throw an exception when we are already under an exception handler and
   * headers were already sent.
   *
   * You can check this function first if we can safely send logs to FirePHP.
   *
   * @return boolean
   */
  public static function isSafeToUseFirePHP()
  {
    return !\Yii::app()->getErrorHandler()->getError() && !headers_sent();
  }
}

