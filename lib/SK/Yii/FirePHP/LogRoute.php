<?php

namespace SK\Yii\FirePHP;

/**
 * Sends Yii log messages to FirePHP. Since this class inherits from CLogRoute,
 * it uses the same method and configuration as the other built-in
 * logging components: CFileLogRoute, CWebLogRoute, etc.
 *
 * @author Shiki <bj@basanes.net>
 * @version 0.3
 */
class LogRoute extends \CLogRoute
{
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
    if (!Util::isSafeToUseFirePHP())
      return;

    foreach ($logs as $log) {
      $method = 'info';
      switch ($log[1]) {
        case \CLogger::LEVEL_INFO:
          $method = 'info';
          break;
        case \CLogger::LEVEL_ERROR:
          $method = 'error';
          break;
        case \CLogger::LEVEL_WARNING:
          $method = 'warn';
          break;
      }

      if ($method == 'trace') {
        // FirePHP's trace method do not include labels
        $trace = str_replace(array('#{category}', '#{timestamp}', '#{message}'),
          array($log[2], date(\DateTime::W3C, $log[3]), $log[0]),
          $this->traceFormat);
        \FB::$method($trace);
      } else {
        $category = str_replace(array('#{category}', '#{timestamp}'),
          array($log[2], date(\DateTime::W3C, $log[3])),
          $this->labelFormat);
        \FB::$method($log[0], $category);
      }
    }
  }
}

