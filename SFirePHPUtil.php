<?php

/**
 * The FirePHP extension for Yii Framework is free software. It is released under the terms of
 * the following BSD License.
 *
 * Copyright (c) 2010-2011, BJ Basañes (shikishiji@gmail.com).
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, are
 * permitted provided that the following conditions are met:
 *
 *    1. Redistributions of source code must retain the above copyright notice, this list of
 *       conditions and the following disclaimer.
 *
 *    2. Redistributions in binary form must reproduce the above copyright notice, this list
 *       of conditions and the following disclaimer in the documentation and/or other materials
 *       provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY <COPYRIGHT HOLDER> ``AS IS'' AND ANY EXPRESS OR IMPLIED
 * WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND
 * FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL <COPYRIGHT HOLDER> OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF
 * ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * The views and conclusions contained in the software and documentation are those of the
 * authors and should not be interpreted as representing official policies, either expressed
 * or implied, of <copyright holder>.
 *
 */

/**
 * A utility class used by SFirePHPLogRoute and SFirePHPProfileLogRoute.
 *
 * @author Shiki
 */
class SFirePHPUtil 
{
  /**
   * Ensures that FirePHPCore is loaded and sets some options for it.
   * @param string $libPath Path to the directory containing the fb.php and FirePHP.class.php files.
   * @param array $options 
   */
  public static function loadFirePHP($libPath, $options = null)
  {
    if ((!class_exists('FirePHP', false) || !class_exists('FB', false)) && empty($libPath))
      $libPath = dirname(__FILE__) . '/firephp/lib/FirePHPCore';
    
    if (!class_exists('FirePHP', false))
      require_once($libPath . '/FirePHP.class.php');
    if (!class_exists('FB', false))
      require_once($libPath . '/fb.php');
      
    FB::setOptions(empty($options) ? array() : $options);
  }
  
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
    return !Yii::app()->getErrorHandler()->getError() && !headers_sent();      
  }
}
