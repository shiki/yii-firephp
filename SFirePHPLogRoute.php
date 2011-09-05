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

require_once(dirname(__FILE__) . '/SFirePHPUtil.php');

/**
 * Sends Yii log messages to FirePHP. Since this class inherits from CLogRoute,
 * it uses the same method and configuration as the other built-in
 * logging components: CFileLogRoute, CWebLogRoute, etc.
 *
 * @author BJ Basañes <shikishiji@gmail.com>
 * @version 0.3
 */
class SFirePHPLogRoute extends CLogRoute
{
  /**
   * Path to the directory containing the fb.php and FirePHP.class.php files. This cannot
   * be a Yii alias.
   * @var string
   */
  public $libPath;

  /**
   * Output format for trace messages. Allowed placeholders are:
   *     #{category}
   *     #{message}
   *     #{timestamp}
   * @var string
   */
  public $traceFormat = '#{category}: #{message}';

  /**
   * Output format for log labels. This is applicable to log messages other
   * than "trace". Allowed placeholders are:
   *     #{category}
   *     #{timestamp}
   * @var string
   */
  public $labelFormat = '#{category}';

  /**
   * FirePHP options. Available keys are:
   *  - maxObjectDepth: The maximum depth to traverse objects (default: 10)
   *  - maxArrayDepth: The maximum depth to traverse arrays (default: 20)
   *  - useNativeJsonEncode: If true will use json_encode() (default: true)
   *  - includeLineNumbers: If true will include line numbers and filenames (default: false)
   * @var array
   */
  public $options = array(
    'maxObjectDepth'     => 2,
    'maxArrayDepth'      => 5,
    'includeLineNumbers' => false,
  );

  /**
   * Processes log messages and sends them to specific destination.	 *
   * @param array list of messages.  Each array elements represents one message
   * with the following structure:
   * array(
   *   [0] => message (string)
   *   [1] => level (string)
   *   [2] => category (string)
   *   [3] => timestamp (float, obtained by microtime(true));
   */
  protected function processLogs($logs)
  {
    if (!SFirePHPUtil::isSafeToUseFirePHP())
      return;
    
    SFirePHPUtil::loadFirePHP($this->libPath, $this->options);

    foreach ($logs as $log) {
      $method = 'info';
      switch ($log[1]) {
        case CLogger::LEVEL_INFO:
          $method = 'info';
          break;
        case CLogger::LEVEL_ERROR:
          $method = 'error';
          break;
        case CLogger::LEVEL_WARNING:
          $method = 'warn';
          break;
      }

      if ($method == 'trace') {
        // FirePHP's trace method do not include labels
        $trace = str_replace(array('#{category}', '#{timestamp}', '#{message}'),
                   array($log[2], date(DateTime::W3C, $log[3]), $log[0]),
                   $this->traceFormat);
        FB::$method($trace);
      } else {
        $category = str_replace(array('#{category}', '#{timestamp}'),
                      array($log[2], date(DateTime::W3C, $log[3])),
                      $this->labelFormat);
        FB::$method($log[0], $category);
      }
    }
  }
}
