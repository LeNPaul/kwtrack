<?php
namespace AmazonAdvertisingApi;
error_reporting(E_ALL); ini_set("error_reporting", E_ALL);
require '../../../database/pdo.inc.php';
require_once '../../AmazonAdvertisingApi/Client.php';

$refreshToken = 'Atzr|IwEBID8Cr8D51I4XzWRU5wdohHUoGJRY1rempo6uwgk_niC5AgqZo_SVul0Nt8V5oU1j8P2T08oPjR8gLKSsWnJuAflfBzcMky0NzBoKcSIYH62WJ4I86G6t4jGxU7fitoLO79TJPFjCoHPXyjnvaNLxFaJPxOaW3t4fLBH9-1RGsAaEdrP0-r85iVNgG_pQE2HA7bl_ZMqWoJbXhww-YEsfMH6tBKXG0S0dMreLkEkdx75eABfzKwdDm9jokTL8YZjkqj1ELRFOwK6Pgv1PsYTvdI2Us1fTw-Bu1n_n4am_vlrK4ntseK_dqFHvrV4_h0aup1hoChA5KZD2ID3fG4e4be4iCRC66QdJxmjv_q_o8RxoZR_bG0vhlkU2rSYKnMnZOj7nkRS2Z6JoRPWRLw7nP8nEfHLRkCQnrOn2PHkrKX7MWTIWt1f-_rkr3ocfvgKfcixFvTc6XmNGg0IYbVidw0thS3-AgSpnGaG0O7Q-W9VZPFRFtas1PltUG69LL0ko2EOz6yW-RG9071MfpUMgre2_TUildA68rlcdikXtNfMtyYNwqvQhlSZ_eVXWGclIpk4XQ39a-5eJiB8HVfsAvgdF'/*$argv[1]*/;
$user_id = 2/*$argv[2]*/;
$profileId = '1215041354659387';

echo ($argv);
/*
// Instantiate client for advertising API
$config = array(
  "clientId" => "amzn1.application-oa2-client.4246e0f086e441259742c758f63ca0bf",
  "clientSecret" => "9c9e07b214926479e14a0781051ecc3ad9b29686d3cef24e15eb130a47cabeb3",
  "refreshToken" => $refreshToken,
  "region" => "na",
  "sandbox" => false,
  );
$client = new Client($config);
$client->profileId = $profileId;
echo "finished";

*/

/*
// First, grab campaigns and store them in db
$result = $client->listCampaigns(array("stateFilter" => "enabled", "stateFilter" => "paused"));
$campJson = $result['response'];
$campaigns = json_decode($campJson, true);
echo count($campaigns);

// Iterate through campaign array and add them to db w/ foreign key user_id
for ($i = 0; $i < count($campaigns); $i++) {
  $sql = 'INSERT INTO campaigns
          VALUES(:campaign_name, :amz_campaign_id, :user_id, :campaign_type, :targeting_type, :state, :daily_budget)';
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(
    ':campaign_name'   => htmlspecialchars($campaigns[$i]['name'], ENT_QUOTES),
    ':amz_campaign_id' => $campaigns[$i]['campaignId'],
    ':user_id'         => $user_id,
    ':campaign_type'   => $campaigns[$i]['campaignType'],
    ':targeting_type'  => $campaigns[$i]['targetingType'],
    ':state'           => $campaigns[$i]['state'],
    ':daily_budget'    => $campaigns[$i]['dailyBudget']
  ));
}

*/

/*
// Second, grab ad groups and store them in db
$result = $client->listAdGroups(array("stateFilter" => "enabled", "stateFilter" => "paused"));
$adgrJson = $result['response'];
$adgroups = json_decode($adgrJson, true);

echo '<pre>';
var_dump($adgroups);
echo '</pre>';

for ($i = 0; $i < count($adgroups); $i++) {
  $sql = 'INSERT INTO ad_groups
          VALUES (:amz_adgroup_id, :ad_group_name, :amz_campaign_id, :default_bid, :state)';
  $stmt = $pdo->prepare($sql);
  // $stmt->execute(array(
  //   ':amz_adgroup_id' = $adgroups[$i],
  //   ':ad_group_name' = ,
  //   ':amz_campaign_id' = ,
  //   ':default_bid' = ,
  //   ':state' =
  //));
}
*/