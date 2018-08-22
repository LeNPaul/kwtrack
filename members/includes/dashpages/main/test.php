<?php
namespace AmazonAdvertisingApi;
require_once '../../AmazonAdvertisingApi/Client.php';
require '../../../database/pdo.inc.php';
use PDO;

/*
 *  function prepareDbArrays(Array $dataset) --> Array $dbVar
 *    --> Takes $dataset and prepares it for insertion in database
 *
 *      --> Array $dataset --> unprepared array for specific metric
 *      --> Array $dbVar --> prepared array for specific metric
 */
function prepareDbArrays($dataset, $dbVar) {
  for ($i = 0; $i < 60; $i++) {
    if ($i === 2) { break; }
    $secondLoopLimit = count($dataset[$i]);
    for ($j = 0; $j < $secondLoopLimit; $j++) {
      $dbVar[$j][] = array_shift($dataset[$i]);
    }
  }
  return $dbVar;
}

/* TESTING PURPOSES ONLY */
$sql = 'DELETE FROM campaigns';
$stmt = $pdo->prepare($sql);
$stmt->execute();

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

// Each metric array will be storing campaign data like the following in a 2D array:
//    METRIC ARRAY => [ARRAY1( * all data for metric for each campaign * ), ARRAY2(...), ..., ARRAY60(...)]
//    METRIC ARRAY INDEX REPRESENTS 1 DAY OF DATA FOR THAT METRIC FOR ALL CAMPAIGNS
$impressions = [];
$clicks = [];
$ctr = [];
$adSpend = [];
$avgCpc = [];
$unitsSold = [];
$sales = [];
$acos = [];

for ($i = 0; $i < 60; $i++) {
  $impressions[$i] = [];
  $clicks[$i] = [];
  $ctr[$i] = [];
  $adSpend[$i] = [];
  $avgCpc[$i] = [];
  $unitsSold[$i] = [];
  $sales[$i] = [];

  // Get date from $i days before today and format it as YYYYMMDD
  $date = date('Ymd', strtotime('-' . $i . ' days'));


  // Only on the very first iteration of this loop, we will iterate through the array
  // and store campaign name and campaign ID in the database
  if ($i === 0) {
    // Request the report from API with campaign name, campaignId, and campaign budget only
    // for the first iteration
    $result = $client->requestReport(
      "campaigns",
      array("reportDate"    => "20180713", // placeholder date
            "campaignType"  => "sponsoredProducts",
            "metrics"       => "campaignId,campaignName,impressions,clicks,cost,campaignBudget,campaignStatus,attributedUnitsOrdered1d,attributedSales1d"
      )
    );

    // Get the report id so we can use it to get the report
    $result = json_decode($result['response'], true);
    $reportId = $result['reportId'];

    sleep(7);

    // Get the report using the report id
    $result = $client->getReport($reportId);
    $result = json_decode($result['response'], true);

    for ($x = 0; $x < count($result); $x++) {
      $sql = 'INSERT INTO campaigns (user_id, status, campaign_name, amz_campaign_id, daily_budget)
              VALUES (:user_id, :status, :campaign_name, :amz_campaign_id, :daily_budget)';
      $stmt = $pdo->prepare($sql);
      $stmt->execute(array(
        ':user_id'          => $user_id,
        ':status'           => $result[$x]['campaignStatus'],
        ':campaign_name'    => htmlspecialchars($result[$x]['campaignName'], ENT_QUOTES),
        ':amz_campaign_id'  => $result[$x]['campaignId'],
        ':daily_budget'     => $result[$x]['campaignBudget']
      ));
    }
  } else {
    // All other iterations, we request this report to optimize time
    $result = $client->requestReport(
      "campaigns",
      array("reportDate"    => "20180627", // placeholder date
            "campaignType"  => "sponsoredProducts",
            "metrics"       => "campaignId,impressions,clicks,cost,campaignStatus,attributedUnitsOrdered1d,attributedSales1d"
      )
    );

    // Get the report id so we can use it to get the report
    $result = json_decode($result['response'], true);
    $reportId = $result['reportId'];

    sleep(7);

    // Get the report using the report id
    $result = $client->getReport($reportId);
    $result = json_decode($result['response'], true);
  }

  // Initialize variables that we need in order to calculate ACoS for the DAY
  // These variables only track cost and sales for the day
  $totalCost = 0.0;
  $totalSales = 0.0;

  // Loop to iterate through the report response
  for ($j = 0; $j < count($result); $j++) {

    // Take into account cost, and sales so we can calculate the average later
    if ($result[$j]['cost'] != 0 || $result[$j]['attributedSales1d'] != 0) {
      $totalCost += (double)$result[$j]['cost'];
      $totalSales += (double)$result[$j]['attributedSales1d'];
    }

    // Check if campaign is archived/paused. If it is archived/paused, then we push 0 for all metrics
    if ($result[$j]['campaignStatus'] == 'archived' || $result[$j]['campaignStatus'] == 'paused') {
      $impressions[$i][] = 0;
      $clicks[$i][] = 0;
      $ctr[$i][] = 0.0;
      $adSpend[$i][] = 0.0;
      $avgCpc[$i][] = 0.0;
      $unitsSold[$i][] = 0;
      $sales[$i][] = 0.0;
    } else { // If campaign is active, then run this code
      $impressions[$i][] = $result[$j]['impressions'];
      $clicks[$i][] = $result[$j]['clicks'];

      // Check if impressions are 0. If impressions are 0, then we know that CTR will also be 0.
      if ($result[$j]['impressions'] == 0) {
        $ctr[$i][] = 0.0;
      } else {
        $ctr[$i][] = (double)($result[$j]['clicks'] / $result[$j]['impressions']);
      }

      // Check if clicks are 0. If clicks are 0, then we know that CPC will also be 0.
      if ($result[$j]['clicks'] == 0) {
        $avgCpc[$i][] = 0.0;
      } else {
        $avgCpc[$i][] = (double)($result[$j]['cost'] / $result[$j]['clicks']);
      }

      // Push ad spend, units sold, and $ sales for the day to our arrays.
      $adSpend[$i][] = $result[$j]['cost'];
      $unitsSold[$i][] = $result[$j]['attributedUnitsOrdered1d'];
      $sales[$i][] = $result[$j]['attributedSales1d'];
    }
  }

  // Calculate ACoS for the day and push it to our array
  if ($totalSales == 0) {
    $acos[] = 0.0;
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
echo '<hr /><h1>IMPRESSIONS</h1><br /><br />';
var_dump($impressions);
echo '</pre>';


// Declare arrays that we will serialize and store in the database
$dbImpressions = [];
$dbClicks = [];
$dbCtr = [];
$dbAdSpend = [];
$dbAvgCpc = [];
$dbUnitsSold = [];
$dbSales = [];


// Grab impression data from array and store in their respective campaigns
$dbImpressions = prepareDbArrays($impressions, $dbImpressions);
// Grab impression data from array and store in their respective campaigns
$dbClicks = prepareDbArrays($clicks, $dbClicks);
// Grab impression data from array and store in their respective campaigns
// $dbCtr = prepareDbArrays($ctr, $dbCtr);
// Grab impression data from array and store in their respective campaigns
$dbAdSpend = prepareDbArrays($adSpend, $dbAdSpend);
// Grab impression data from array and store in their respective campaigns
$dbAvgCpc = prepareDbArrays($avgCpc, $dbAvgCpc);
// Grab impression data from array and store in their respective campaigns
$dbUnitsSold = prepareDbArrays($unitsSold, $dbUnitsSold);
// Grab impression data from array and store in their respective campaigns
$dbSales = prepareDbArrays($sales, $dbSales);


echo '<pre>';
echo '<hr /><h1>DB IMPRESSIONS</h1><br /><br />';
var_dump($dbImpressions);
echo '<hr /><h1>CLICKS</h1><br /><br />';
var_dump($dbClicks);
echo '<hr /><h1>CTR</h1><br /><br />';
var_dump($ctr);
echo '<hr /><h1>ADSPEND</h1><br /><br />';
var_dump($dbAdSpend);
echo '<hr /><h1>CPC</h1><br /><br />';
var_dump($dbAvgCpc);
echo '<hr /><h1>UNITS SOLD</h1><br /><br />';
var_dump($dbUnitsSold);
echo '<hr /><h1>SALES</h1><br /><br />';
var_dump($dbSales);
echo '<hr /><h1>ACOS</h1><br /><br />';
var_dump($acos);
echo '</pre>';

?>
