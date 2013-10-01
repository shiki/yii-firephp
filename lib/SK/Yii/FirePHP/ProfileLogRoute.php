<?php

namespace SK\Yii\FirePHP;

/**
 * Sends profiling results to FirePHP instead of the page view. This works
 * just like CProfileLogRoute. The only difference is the target output.
 *
 * @author Shiki <bj@basanes.net>
 * @version 0.1
 */
class ProfileLogRoute extends \CProfileLogRoute
{
  /**
   * Displays the log messages. Overridden to remove the check for isAjaxRequest.
   * @param array $logs list of log messages
   */
  public function processLogs($logs)
  {
    $app = \Yii::app();
    if (!($app instanceof \CWebApplication) || !Util::isSafeToUseFirePHP())
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
      . 'Time: ' . sprintf('%0.5f', \Yii::getLogger()->getExecutionTime()) . 's, '
      . 'Memory: ' . number_format(\Yii::getLogger()->getMemoryUsage() / 1024) . 'KB'
      . ')';

    $firephp = \FirePHP::getInstance(true);
    $firephp->table($tableLabel, $table);
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

    $firephp = \FirePHP::getInstance(true);
    $firephp->table('Profiling Callstack Report', $table);
  }

  /**
   *
   */
  private function renderSQLStats()
  {
    $stats = \Yii::app()->getDb()->getStats();

    // Using a table because groups are broken as of FirePHP 0.7.4 and FireBug 1.12.2
    // https://github.com/firephp/firephp-extension/issues/13
    $tableData = array(
      array('what', 'stats'),
      array('total executed', $stats[0]),
      array('total time spent', $stats[1]),
    );
    $tableLabel = "SQL Stats (Executed: $stats[0], Time: $stats[1])";

    $firephp = \FirePHP::getInstance(true);
    $firephp->table($tableLabel, $tableData);
  }
}

