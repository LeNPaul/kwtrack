<?php
/*
 * Helper functions for campaign data manipulation
 */

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
     if ($i === 2) { break; }
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

function getRefreshToken($pdo, $user_id) {
  $sql = 'SELECT refresh_token FROM users WHERE user_id=:user_id';
  $stmt = $pdo->query($sql);
  $result = $stmt->fetchAll(PDO::FETCH_COLUMN);
  var_dump($result);
}

function launchBackgroundProcess($command) {
  // Run command Asynchroniously (in a separate thread)
  if(PHP_OS=='WINNT' || PHP_OS=='WIN32' || PHP_OS=='Windows'){
    // Windows
    $command = 'start "" '. $command;
  } else {
    // Linux/UNIX
    $command = $command .' /dev/null &';
  }
  $handle = popen($command, 'r');
  if($handle!==false){
    pclose($handle);
    return true;
  } else {
    return false;
  }
}
?>
