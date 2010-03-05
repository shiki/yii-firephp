<?php

/**
 * Description of ShikiFirePHPLogRoute
 *
 * @author Shiki
 */
class ShikiFirePHPLogRoute extends CLogRoute
{
	public $fbPath;
	public $traceFormat = '[#{category}] #{message}';
	public $categoryFormat = '#{category}';

	protected function includeLib()
	{
		if (!isset($this->fbPath))
			require_once(dirname(__FILE__) . '/FirePHPCore-0.3.1/lib/FirePHPCore/fb.php');
		else
			require_once($this->fbPath);
	}

    protected function processLogs($logs)
	{
		$this->includeLib();

		foreach($logs as $log) {
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
					$this->categoryFormat);
				FB::$method($log[0], $category);
			}
		}
		
	}
}