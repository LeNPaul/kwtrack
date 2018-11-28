<?php
namespace AmazonAdvertisingApi;
include 'pdo.inc.php';
include dirname(__FILE__) . '/../includes/AmazonAdvertisingApi/Client.php';
use PDO;

date_default_timezone_set('America/Los_Angeles');

// date('w') returns 1 = Sun, 2 = Mon, ..., 7 = Sat
$currentDay  = date('w');
$currentHour = date('H');

echo $currentDay;

//if ($currentHour === 23) {
  /*
   *  Get all campaigns w/ schedules
   *      [  [ c_id, [ schedule ] ]  ]
   * */
  
  $sql          = "SELECT user_id, amz_campaign_id, schedule FROM campaigns WHERE schedule <> '0' ORDER BY user_id ASC;";
  $stmt         = $pdo->query($sql);
  $campaignList = $stmt->fetchAll(PDO::FETCH_ASSOC);
//}

// Stringified JSON -> Array for all schedules
for ($i = 0; $i < count($campaignList); $i++) $campaignList[$i]['schedule'] = json_decode($campaignList[$i]['schedule'], false);

/*
 * Iterate thru each campaign and user. All campaigns for each user are sorted in ascending order.
 * Store the user_id in a var for each iteration. If it changes, then instantiate a new client.
 *
 * */
$old_uid = null;
for ($i = 0;  $i < count($campaignList); $i++) {
  $uid = $campaignList[$i]['user_id'];
  if ($uid === $old_uid) {
  
  } else {
    $old_uid = $uid;
    
    // Get profile ID & refresh token so we can instantiate the client later
    $sql      = "SELECT refresh_token, profileId FROM users WHERE user_id={$uid}";
    $stmt     = $pdo->query($sql);
    $userInfo = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $pid      = $userInfo[0]['profileId'];
    $rt       = $userInfo[0]['refresh_token'];
    
    // Instantiate client for advertising API
    $config = array(
      "clientId" => "amzn1.application-oa2-client.4246e0f086e441259742c758f63ca0bf",
      "clientSecret" => "9c9e07b214926479e14a0781051ecc3ad9b29686d3cef24e15eb130a47cabeb3",
      "refreshToken" => $rt,
      "region" => "na",
      "sandbox" => false,
    );
    $client = new Client($config);
    $client->profileId = $pid;
  
    if ($campaignList[$i]['schedule'][$currentDay][$currentHour] === 0) {
      echo 'client instantiated for user ' . $uid . ', campaign id ' . $campaignList[$i]['amz_campaign_id'] . ' is being paused.<br />';
    } else {
      echo 'client instantiated for user ' . $uid . ', campaign id ' . $campaignList[$i]['amz_campaign_id'] . ' is being enabled.<br />';
    }
  }
}

// Pause/enable campaigns
for ($i = 0; $i < count($campaignList); $i++) {
  if ($campaignList[$i]['schedule'][$currentDay][$currentHour] === 0) {
  
  } else {
  
  }
}
?>