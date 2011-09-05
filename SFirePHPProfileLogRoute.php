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
 * Sends profiling results to FirePHP instead of the page view. This works
 * just like CProfileLogRoute. The only difference is the target output.
 *
 * @author BJ Basañes <shikishiji@gmail.com>
 * @version 0.1
 */
class SFirePHPProfileLogRoute extends CProfileLogRoute
{
  /**
   * Path to the directory containing the fb.php and FirePHP.class.php files. This cannot
   * be a Yii alias.
   * @var string
   */
  public $libPath;
  
  /**
	 * Displays the log messages. Overridden to remove the check for isAjaxRequest.
	 * @param array $logs list of log messages
	 */
	public function processLogs($logs)
	{
		$app = Yii::app();
		if(!($app instanceof CWebApplication) || !SFirePHPUtil::isSafeToUseFirePHP())
      return;

		if ($this->getReport() === 'summary')
      $this->displaySummary($logs);
		else
      $this->displayCallstack($logs);
	}
  
  /**
   * Directs output to FirePHP instead of the page view.
   * @param string $view
   * @param array $data 
   */
  protected function render($view, $data)
  {
    SFirePHPUtil::loadFirePHP($this->libPath);
    
    if ($this->getReport() === 'summary')
      $this->renderSummary($data);
    else
      $this->renderCallstack($data);
  }
  
  /**
   *
   * @param array $data 
   */
  private function renderSummary($data)
  {
    $this->renderSQLStats();
    
    $table = array(array('Procedure', 'Count', 'Total (s)', 'Avg. (s)', 'Min. (s)', 'Max. (s)'));
    foreach ($data as $entry) {      
      $table[] = array(
        $entry[0],                               // procedure
        $entry[1],                               // count
        sprintf('%0.5f', $entry[4]),             // total
        sprintf('%0.5f', $entry[4] / $entry[1]), // average
        sprintf('%0.5f', $entry[2]),             // min
        sprintf('%0.5f', $entry[3]),             // max
      );
    }
    
    $tableLabel = 'Profiling Summary Report ('
                . 'Time: ' . sprintf('%0.5f', Yii::getLogger()->getExecutionTime()) . 's, '
                . 'Memory: ' . number_format(Yii::getLogger()->getMemoryUsage() / 1024) . 'KB'
                . ')';
    
    FB::table($tableLabel, $table);       
  }
  
  /**
   *
   * @param array $data 
   */
  private function renderCallstack($data)
  {
    $this->renderSQLStats();
    
    $table = array(array('Procedure', 'Time (s)'));
    foreach ($data as $entry) {
      $spaces = str_repeat('> ', $entry[2]);
      $table[] = array(
        $spaces . '' . $entry[0],         // procedure
        sprintf('%0.5f', $entry[1]), // time
      );
    }
    
    FB::table('Profiling Callstack Report', $table);    
  }
  
  /**
   * 
   */
  private function renderSQLStats()
  {
    $stats = Yii::app()->getDb()->getStats();
    FB::group('SQL Stats');
    FB::log($stats[0], 'total executed');
    FB::log($stats[1], 'total time spent');
    FB::groupEnd();
  }
}
