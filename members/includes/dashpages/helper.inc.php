<?php
/*
 * Helper functions for campaign data manipulation
 */

 /*----------------------------------------------------------
  *
  *     DATA IMPORT HELPER FUNCTIONS
  *
  *----------------------------------------------------------*/

 /*
  *  function prepareDbArrays(Array $dataset) --> Array $dbVar
  *    --> Takes $dataset and prepares it for insertion in database
  *
  *      --> Array $dataset - unprepared array for specific metric
  *      --> Array $dbVar   - prepared array for specific metric
  */
 function prepareDbArrays($dataset, $dbVar) {
   for ($i = 0; $i < 60; $i++) {
     /* TESTING PURPOSES ONLY. REMOVE BREAK WHEN READY FOR FINAL TESTING PHASE */
     // if ($i === 2) { break; }

     $secondLoopLimit = count($dataset[$i]);
     for ($j = 0; $j < $secondLoopLimit; $j++) {
       $dbVar[$j][] = array_shift($dataset[$i]);
     }
   }
   return $dbVar;
 }

 /*
  *  function storeCampaignArrays(PDO $pdo, Array $dbVar, Array $arrCampaignIds, String $dbColName) --> void
  *    --> Updates campaigns in the database for the metric array $dbVar under column $dbColName
  *
  *      --> PDO $pdo              - database handler
  *      --> Array $dbVar          - Prepared array for specific metric
  *      --> Array $arrCampaignIds - Array of campaign ID's for a specific user
  *      --> String $dbColName     - column name for the table you want to update
  */
 function storeCampaignArrays($pdo, $dbVar, $arrCampaignIds, $dbColName) {
   for ($i = 0; $i < count($arrCampaignIds); $i++) {
     $sql = "UPDATE campaigns SET {$dbColName}=:value WHERE amz_campaign_id=:amz_campaign_id";
     $stmt = $pdo->prepare($sql);
     $stmt->execute(array(
       ':value'            => serialize($dbVar[$i]),
       ':amz_campaign_id'  => $arrCampaignIds[$i]
     ));
   }
 }

 /*
  *  function storeAdGroupArrays(PDO $pdo, Array $dbVar, Array $arrAdGroupIds, String $dbColName) --> void
  *    --> Updates ad groups in the database for the metric array $dbVar under column $dbColName
  *
  *      --> PDO $pdo              - database handler
  *      --> Array $dbVar          - Prepared array for specific metric
  *      --> Array $arrAdGroupIds  - Array of ad group ID's for a specific user
  *      --> String $dbColName     - column name for the table you want to update
  */
 function storeAdGroupArrays($pdo, $dbVar, $arrAdGroupIds, $dbColName) {
    for ($i = 0; $i < count($arrAdGroupIds); $i++) {
      $sql = "UPDATE ad_groups SET {$dbColName}=:value WHERE amz_adgroup_id=:amz_adgroup_id";
      $stmt = $pdo->prepare($sql);
      $stmt->execute(array(
        ':value'            => serialize($dbVar[$i]),
        ':amz_adgroup_id'  => $arrAdGroupIds[$i]
      ));
    }
 }

 /*
  *  function storeKeywordArrays(PDO $pdo, Array $dbVar, Array $arrKeywordIds, String $dbColName) --> void
  *    --> Updates ad groups in the database for the metric array $dbVar under column $dbColName
  *
  *      --> PDO $pdo              - database handler
  *      --> Array $dbVar          - Prepared array for specific metric
  *      --> Array $arrKeywordIds  - Array of keyword ID's for a specific user
  *      --> String $dbColName     - column name for the table you want to update
  */
 function storeKeywordArrays($pdo, $dbVar, $arrKeywordIds, $dbColName) {
   for ($i = 0; $i < count($arrKeywordIds); $i++) {
     $sql = "UPDATE ppc_keywords SET {$dbColName}=:value WHERE amz_kw_id=:amz_kw_id";
     $stmt = $pdo->prepare($sql);
     $stmt->execute(array(
         ':value'            => serialize($dbVar[$i]),
         ':amz_kw_id'        => $arrKeywordIds[$i]
     ));
   }
 }

 /*----------------------------------------------------------
  *
  *     DASHBOARD DISPLAY HELPER FUNCTIONS
  *
  *----------------------------------------------------------*/

/*
 *  function multiUnserialize(Array $arr) --> Array $output
 *    --> Takes array of serialized arrays and returns array of unserialized arrays
 *
 *      --> Array $arr - array of unserialized arrays
 */
function multiUnserialize($arr) {
  for ($i = 0; $i < count($arr); $i++) {
    $arr[$i] = unserialize($arr[$i]);
  }
  return $arr;
}

/*
 * function getMetricData(PDO $pdo, String $metric, Int $user_id) --> Array $output
 *    --> Returns array of all metric data for $metric for the user
 *
 *      --> PDO $pdo       - database handle
 *      --> String $metric - metric to search for in the database
 *      --> Int $user_id   - user ID of the user
 */
function getMetricData($pdo, $metric, $user_id) {
  // Grab metric data for ALL CAMPAIGNS for the user
  $sql = "SELECT {$metric} FROM campaigns WHERE user_id={$user_id}";
  $stmt = $pdo->query($sql);
  return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

/*
 *  function calculateMetrics(Array $metricArr[Array, Array, ..., Array], Int $numDays, String $metric) --> Int $output
 *    --> Outputs a float that represents a metric
 *
 *      --> Array $metricArr    - Array of arrays pulled from the database.
 *                                - Length will be equal to # of campaigns for the user
 *      --> Int $numDays        - number of days to calculate data for
 *      --> Int $output         - summed up total for the metric
 *      --> String $metric      - String that represents which metric we are calculating
 */
function calculateMetrics($metricArr, $numDays, $metric) {
  // Algorithm will pop the end of each array $numDays times and append it to the output array
  // After appending to output array, we use array_filter to calculate the metric needed

  $output = [];

  for ($i = 0; $i < count($metricArr); $i++) {
    // If the output array has the required length, then break the loop
    if (count($output) == $numDays) { break; }
    $output[] = array_pop($metricArr[$i]);
  }

  if ($metric == 'adSpend' || $metric == 'ppcSales') {
    // If the metric being calculated is ad spend or PPC sales, then all we need to do is
    // get the sum of the array
    $output = array_reduce($output, function($carry, $element) { return $carry += $element; });
  }
  
  return $output;
}
?>
