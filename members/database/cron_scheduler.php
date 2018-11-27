<?php
include 'pdo.inc.php';
date_default_timezone_set('America/Los_Angeles');

// date('w') returns 1 = Sun, 2 = Mon, ..., 7 = Sat
$currentDay  = date('w') - 1;
$currentHour = date('H');

if ($currentHour === 23) {
  /*
   *  Get all campaigns w/ schedules
   *      [  [ c_id, [ schedule ] ]  ]
   * */
  
  $sql          = "SELECT amz_campaign_id, schedule FROM campaigns WHERE schedule<>0;";
  $stmt         = $pdo->query($sql);
  $campaignList = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

echo $currentDay . ' + ' . $currentHour;
?>