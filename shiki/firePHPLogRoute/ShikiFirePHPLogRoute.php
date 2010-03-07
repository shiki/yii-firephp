<?php

/**
 * The FirePHP LogRoute extension for Yii Framework is free software. It is released under the terms of
 * the following BSD License.
 *
 * Copyright (c) 2010, BJ Basañes (shikishiji@gmail.com).
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
 * @copyright Copyright (c) BJ Basañes
 * @author BJ Basañes <shikishiji@gmail.com>
 * @package Shiki
 * @subpackage FirePHPLogRoute
 */

/**
 * Sends Yii log messages to FirePHP. Since this class inherits from CLogRoute,
 * it uses the same method and configuration as the other built-in
 * logging components: CFileLogRoute, CWebLogRoute, etc.
 *
 * @todo additional properties as wrappers for FirePHP's options
 * @todo maybe an access to it's instance? And allowing immediate logging as opposed to delayed logging by Yii
 *
 * @author BJ Basañes <shikishiji@gmail.com>
 * @package Shiki
 * @subpackage FirePHPLogRoute
 * @version 0.1
 */
class ShikiFirePHPLogRoute extends CLogRoute
{
    /**
     * The path alias to FirePHP core lib's fb.php
     * @var string
     */
    public $fbPath;
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
     * Load fb.php. This is called only when processLogs() is called
     *
     */
    protected function includeLib()
    {
        if (!isset($this->fbPath))
            throw new Exception('Please set a path alias to the FirePHP lib path.');
        else
            Yii::import($this->fbPath, true);
    }

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
        $this->includeLib();

        foreach ($logs as $log) {
            $method = 'trace';
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