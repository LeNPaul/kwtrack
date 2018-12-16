<?php

class Metric
{
  private $metric           = null;
  private $pdo              = null;
  private $numDays          = null;
  private $user_id          = null;
  private $calculatedMetric = null;
  
  public function __construct($user_id, $metricName, $numDays, $pdo)
  {
    $this->metric  = $metricName;
    $this->pdo     = $pdo;
    $this->numDays = $numDays;
    $this->user_id = $user_id;
  }
  
  public function getMetric()
  { // Not implemented yet
    return $this->calculatedMetric;
  }
  
  public function getMetricArr()
  {
    return $this->sumMetricArr($this->getMetricData($this->metric, $this->user_id));
  }

  /**
   * Function to sum the values of each array inside $metricArr.
   *  Format: Array[A, B, C, ..., n] where A, B, C, ..., n are all decimals
   *  The output of this function is used to plot the dashboard graphs.
   *
   * @param $metricArr[Array, Array, ..., Array]
   * @param Int $numDays
   * @param String $metric
   *
   * @return array
   */
  private function sumMetricArr($metricArr)
  {
    $output = array_fill(0, $this->numDays, 0);
    for ($j = 0; $j < count($metricArr); $j++) {
      for ($i = 0; $i < $this->numDays; $i++) {
        $output[$i] += $metricArr[$j][$i];
      }
    }
    return array_reverse($output);
  }
  
  /**
   *
   * Pulls metric data from the database and outputs as an array[arrays of metric data for each day]
   *  Format: Array[A, B, ..., n] - where each element = Array[m1, m2, ..., mn] - where m = decimal = metric data for the day
   *
   * @param String $dbColName
   *
   * @return array
   */
  private function getMetricData($dbColName)
  {
    $sql = "SELECT {$dbColName} FROM campaigns WHERE user_id={$this->user_id}";
    $stmt = $this->pdo->query($sql);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
    $arr = [];
  
    for ($i = 0; $i < count($result); $i++) {
      $arr[] = unserialize($result[$i][$dbColName]);
    }
    return $arr;
  }
}