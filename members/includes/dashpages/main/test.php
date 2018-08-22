<?php
namespace AmazonAdvertisingApi;
require_once '../../AmazonAdvertisingApi/Client.php';
require '../../../database/pdo.inc.php';
use PDO;

$refreshToken = 'Atzr|IwEBID8Cr8D51I4XzWRU5wdohHUoGJRY1rempo6uwgk_niC5AgqZo_SVul0Nt8V5oU1j8P2T08oPjR8gLKSsWnJuAflfBzcMky0NzBoKcSIYH62WJ4I86G6t4jGxU7fitoLO79TJPFjCoHPXyjnvaNLxFaJPxOaW3t4fLBH9-1RGsAaEdrP0-r85iVNgG_pQE2HA7bl_ZMqWoJbXhww-YEsfMH6tBKXG0S0dMreLkEkdx75eABfzKwdDm9jokTL8YZjkqj1ELRFOwK6Pgv1PsYTvdI2Us1fTw-Bu1n_n4am_vlrK4ntseK_dqFHvrV4_h0aup1hoChA5KZD2ID3fG4e4be4iCRC66QdJxmjv_q_o8RxoZR_bG0vhlkU2rSYKnMnZOj7nkRS2Z6JoRPWRLw7nP8nEfHLRkCQnrOn2PHkrKX7MWTIWt1f-_rkr3ocfvgKfcixFvTc6XmNGg0IYbVidw0thS3-AgSpnGaG0O7Q-W9VZPFRFtas1PltUG69LL0ko2EOz6yW-RG9071MfpUMgre2_TUildA68rlcdikXtNfMtyYNwqvQhlSZ_eVXWGclIpk4XQ39a-5eJiB8HVfsAvgdF';
$profileId = '1215041354659387';
$user_id = 0;

$config = array(
  "clientId" => "amzn1.application-oa2-client.4246e0f086e441259742c758f63ca0bf",
  "clientSecret" => "9c9e07b214926479e14a0781051ecc3ad9b29686d3cef24e15eb130a47cabeb3",
  "refreshToken" => $refreshToken,
  "region" => "na",
  "sandbox" => false,
  );
$client = new Client($config);
$client->profileId = $profileId;

$impressions = [];
$clicks = [];
$ctr = [];
$adSpend = [];
$avgCpc = [];
$unitsSold = [];
$sales = [];
$acos = [];

for ($i = 0; $i < 60; $i++) {
  $impressions[] = [];
  $clicks[] = [];
  $ctr[] = [];
  $adSpend[] = [];
  $avgCpc[] = [];
  $unitsSold[] = [];
  $sales[] = [];
  $acos[] = [];

  // Get date from $i days before today and format it as YYYYMMDD
  $date = date('Ymd', strtotime('-' . $i . ' days'));

  // Request the report from API
  $result = $client->requestReport(
    "campaigns",
    array("reportDate"    => "20180713",
          "campaignType"  => "sponsoredProducts",
          "metrics"       => "campaignId,campaignName,impressions,clicks,cost,campaignBudget,campaignStatus,attributedUnitsOrdered1d,attributedSales1d"
    )
  );

  // Get the report id so we can use it to get the report
  $result = json_decode($result['response'], true);
  $reportId = $result['reportId'];
  var_dump($reportId);

  sleep(15);

  // Get the report using the report id
  $result = $client->getReport($reportId);
  $result = json_decode($result['response'], true);

  echo '<pre>';
  var_dump($result);
  echo '</pre>';

  // Initialize variables that we need in order to calculate ACoS for the DAY
  // These variables only track cost and sales for the day
  $totalCost = 0.0;
  $totalSales = 0.0;

  // Only on the very first iteration of this loop, we will iterate through the array
  // and store campaign name and campaign ID in the database
  if ($i === 0) {
    for ($x = 0; $x < count($result); $x++) {
      $sql = 'INSERT INTO campaigns (user_id, campaign_name, amz_campaign_id, daily_budget) VALUES (:user_id, :campaign_name, :amz_campaign_id, :daily_budget)';
      $stmt = $pdo->prepare($sql);
      $stmt->execute(array(
        ':user_id'          => $user_id,
        ':campaign_name'    => htmlspecialchars($result[$x]['campaignName'], ENT_QUOTES),
        ':amz_campaign_id'  => $result[$x]['campaignId'],
        ':daily_budget'     => $result[$x]['campaignBudget']
      ));
    }
  }

  // Loop to iterate through the report response
  for ($j = 0; $j < count($result); $j++) {
    // Take into account cost, and sales so we can calculate the average later
    if ($result[$j]['cost'] != 0 || $result[$j]['attributedSales1d'] != 0) {
      $totalCost += (double)$result[$j]['cost'];
      $totalSales += (double)$result[$j]['attributedSales1d'];
    }

    // Check if campaign is archived. If it is archived, then we push 0 for all metrics
    if ($result[$j]['campaignStatus'] == 'archived') {
      $impressions[] = 0;
      $clicks[] = 0;
      $ctr[] = 0;
      $adSpend[] = 0.0;
      $avgCpc[] = 0.0;
      $unitsSold[] = 0;
      $sales[] = 0.0;
    }

    $impressions[$i][] = $result[$j]['impressions'];
    $clicks[$i][] = $result[$j]['clicks'];

    // Check if impressions are 0. If impressions are 0, then we know that CTR will also be 0.
    if ($result[$j]['impressions'] == 0) {
      $ctr[$i][] = 0;
    } else {
      $str[$i][] = (double)($result[$j]['clicks'] / $result[$j]['impressions']);
    }

    // Check if clicks are 0. If clicks are 0, then we know that CPC will also be 0.
    if ($result[$j]['clicks'] == 0) {
      $avgCpc[$i][] = 0;
    } else {
      $avgCpc[$i][] = (double)($result[$j]['cost'] / $result[$j]['clicks']);
    }

    // Push ad spend, units sold, and $ sales for the day to our arrays.
    $adSpend[$i][] = $result[$j]['cost'];
    $unitsSold[$i][] = $result[$j]['attributedUnitsOrdered1d'];
    $sales[$i][] = $result[$j]['attributedSales1d'];

  }

  // Calculate ACoS for the day and push it to our array
  if ($totalSales == 0) {
    $acos[] = 0;
  } else {
    $acos[] = (double)($totalCost / $totalSales);
  }


  if ($i === 1) {
    break;
  }
}


// Grab array of campaigns by their campaign ID
$sql = 'SELECT amz_campaign_id FROM campaigns WHERE user_id=' . htmlspecialchars($user_id);
$stmt = $pdo->query($sql);
$result = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo '<pre>';
var_dump($result);
echo '</pre>';

$currentImpressions = new SplFixedArray(60);

// Grab impression data from array and store in their respective campaigns
for ($i = 0; $i < 60; $i++) {
  for ($j = 0; $j < count($impressions[$i]); $j++) {
    $currentImpressions[$j][] = array_shift($impressions[$i]);
  }
}



echo '<pre>';
echo '<hr /><h1>CURRENT IMPRESSIONS</h1><br /><br />';
var_dump($currentImpressions);
echo '<hr /><h1>IMPRESSIONS</h1><br /><br />';
var_dump($impressions);
echo '<hr /><h1>CLICKS</h1><br /><br />';
var_dump($clicks);
echo '<hr /><h1>CTR</h1><br /><br />';
var_dump($ctr);
echo '<hr /><h1>ADSPEND</h1><br /><br />';
var_dump($adSpend);
echo '<hr /><h1>CPC</h1><br /><br />';
var_dump($avgCpc);
echo '<hr /><h1>UNITS SOLD</h1><br /><br />';
var_dump($unitsSold);
echo '<hr /><h1>SALES</h1><br /><br />';
var_dump($sales);
echo '<hr /><h1>ACOS</h1><br /><br />';
var_dump($acos);
echo '</pre>';

?>
