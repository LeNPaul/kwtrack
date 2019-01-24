<?php
namespace AmazonAdvertisingApi;
include 'pdo.inc.php';
require_once dirname(__FILE__) . '/../includes/AmazonAdvertisingApi/Client.php';
use PDO;

function newAdClient(&$client, $refreshToken, $profileId) {
  $config = array(
    "clientId" => "amzn1.application-oa2-client.4246e0f086e441259742c758f63ca0bf",
    "clientSecret" => "9c9e07b214926479e14a0781051ecc3ad9b29686d3cef24e15eb130a47cabeb3",
    "refreshToken" => $refreshToken,
    "region" => "na",
    "sandbox" => false,
  );
  $client = new Client($config);
  $client->profileId = $profileId;
}

function toggleCampaign(&$client, &$fp, $state, $uid, $campaignList, $currentHour, $currentDay, $i) {
  $r = $client->updateCampaigns(array(
    array(
      "campaignId" => (float)$campaignList[$i]['amz_campaign_id'],
      "state"      => $state
    )));

  $log =
    ($r['code'] != 207)
      ? "------ Error occurred for campaign (ID: {$campaignList[$i]['amz_campaign_id']})" . "\n"
      . "    |________ Error Response: " . $r['response'] . "\n"
      : "-- Client instantiated for user {$uid}. Campaign (ID {$campaignList[$i]['amz_campaign_id']}) successfully {$state} at {$currentHour}:00 on day #{$currentDay}" . "\n";

  fwrite($fp, $log);
}

date_default_timezone_set('America/Los_Angeles');

// date('w') returns 1 = Sun, 2 = Mon, ..., 7 = Sat
$currentDay  = date('w');
$currentHour = date('H');
$client      = null;
$fp          = fopen('scheduler_log', 'w');

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
 * Iterate through each campaign and user. All campaigns for each user are sorted in ascending order.
 * Store the user_id in a var for each iteration. If it changes, then instantiate a new client.
 *
 * */
$old_uid = null;
for ($i = 0;  $i < count($campaignList); $i++) {
  $uid = $campaignList[$i]['user_id'];
  if ($uid === $old_uid) {

    if ($campaignList[$i]['schedule'][$currentHour][$currentDay] === 0) {
      toggleCampaign($client, $fp, 'paused', $uid, $campaignList, $currentHour, $currentDay, $i);
    } else {
      toggleCampaign($client, $fp, 'enabled', $uid, $campaignList, $currentHour, $currentDay, $i);
    }

  } else {
    $old_uid = $uid;

    // Get profile ID & refresh token so we can instantiate the client later
    $sql      = "SELECT refresh_token, profileId FROM users WHERE user_id={$uid}";
    $stmt     = $pdo->query($sql);
    $userInfo = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $pid      = $userInfo[0]['profileId'];
    $rt       = $userInfo[0]['refresh_token'];

    try {
      // Instantiate client for advertising API
      newAdClient($client, $rt, $pid);

      if ($campaignList[$i]['schedule'][$currentHour][$currentDay] === 0) {
        toggleCampaign($client, $fp, 'paused', $uid, $campaignList, $currentHour, $currentDay, $i);
      } else {
        toggleCampaign($client, $fp, 'enabled', $uid, $campaignList, $currentHour, $currentDay, $i);
      }

    } catch (\Exception $e) {
      echo "<b>Message: </b>" . $e->getMessage() . '<br>';
      echo '<b>Error on line </b>' . $e->getLine() . '<br>';
      echo '<b>In file: </b>' . $e->getFile() . '<br>';
      echo '<b>Trace: </b>' . $e->getTrace();
    }
  }
}

fclose($fp);
?>
