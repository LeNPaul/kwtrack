<?php
namespace AmazonAdvertisingApi;
require_once '../../AmazonAdvertisingApi/Client.php';
require '../../../database/pdo.inc.php';
require '../helper.inc.php';
use PDO;

$refreshToken = 'Atzr|IwEBID8Cr8D51I4XzWRU5wdohHUoGJRY1rempo6uwgk_niC5AgqZo_SVul0Nt8V5oU1j8P2T08oPjR8gLKSsWnJuAflfBzcMky0NzBoKcSIYH62WJ4I86G6t4jGxU7fitoLO79TJPFjCoHPXyjnvaNLxFaJPxOaW3t4fLBH9-1RGsAaEdrP0-r85iVNgG_pQE2HA7bl_ZMqWoJbXhww-YEsfMH6tBKXG0S0dMreLkEkdx75eABfzKwdDm9jokTL8YZjkqj1ELRFOwK6Pgv1PsYTvdI2Us1fTw-Bu1n_n4am_vlrK4ntseK_dqFHvrV4_h0aup1hoChA5KZD2ID3fG4e4be4iCRC66QdJxmjv_q_o8RxoZR_bG0vhlkU2rSYKnMnZOj7nkRS2Z6JoRPWRLw7nP8nEfHLRkCQnrOn2PHkrKX7MWTIWt1f-_rkr3ocfvgKfcixFvTc6XmNGg0IYbVidw0thS3-AgSpnGaG0O7Q-W9VZPFRFtas1PltUG69LL0ko2EOz6yW-RG9071MfpUMgre2_TUildA68rlcdikXtNfMtyYNwqvQhlSZ_eVXWGclIpk4XQ39a-5eJiB8HVfsAvgdF';
$profileId = '1215041354659387';
$user_id = 2;

$impressions = [];
$clicks = [];
$ctr = [];
$adSpend = [];
$avgCpc = [];
$unitsSold = [];
$sales = [];

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
      "keywords",
      array("reportDate"    => $date,
            "campaignType"  => "sponsoredProducts",
            "metrics"       => "adGroupId,campaignId,keywordId,keywordText,matchType,impressions,clicks,cost,campaignBudget,attributedUnitsOrdered1d,attributedSales1d"
      )
    );

    // Get the report id so we can use it to get the report
    $result = json_decode($result['response'], true);
    $reportId = $result['reportId'];

    sleep(120);

    // Get the report using the report id
    $result = $client->getReport($reportId);
    $result = json_decode($result['response'], true);

    // Insert keywords into database
    for ($x = 0; $x < count($result); $x++) {

      // Get status and bid for each keyword
      $kw_id = $result[$x]['keywordId'];
      $status = $client->getBiddableKeyword($kw_id);
      $status = json_decode($status['response'], true);
      $bid = $status['bid'];
      $status = $status['state'];

      $sql = 'INSERT INTO ppc_keywords (user_id, status, bid, keyword_text, amz_campaign_id, amz_adgroup_id, amz_kw_id, match_type)
              VALUES (:user_id, :status, :bid, :keyword_text, :amz_campaign_id, :amz_adgroup_id, :amz_kw_id, :match_type)';
      $stmt = $pdo->prepare($sql);
      $stmt->execute(array(
        ':user_id'          => $user_id,
        ':status'           => $status,
        ':bid'              => $bid,
        ':keyword_text'     => $result[$x]['keywordText'],
        ':amz_campaign_id'  => $result[$x]['campaignId'],
        ':amz_adgroup_id'   => $result[$x]['adGroupId'],
        ':amz_kw_id'        => $result[$x]['keywordId'],
        ':match_type'       => $result[$x]['matchType']
      ));
    }
  } else {
    // All other iterations, we request this report to optimize time
    $result = $client->requestReport(
      "keywords",
      array("reportDate"    => $date, // placeholder date
            "campaignType"  => "sponsoredProducts",
            "metrics"       => "impressions,clicks,cost,attributedUnitsOrdered1d,attributedSales1d"
      )
    );

    // Get the report id so we can use it to get the report
    $result = json_decode($result['response'], true);
    $reportId = $result['reportId'];

    sleep(120);

    // Get the report using the report id
    $result = $client->getReport($reportId);
    $result = json_decode($result['response'], true);
  }

  // Loop to iterate through the report response
  for ($j = 0; $j < count($result); $j++) {

    // Get status for each keyword
    $kw_id = $result[$j]['keywordId'];
    $status = $client->getBiddableKeyword($kw_id);
    $status = json_decode($status['response'], true);
    $status = $status['state'];

    // Check if keyword is archived/paused. If it is archived/paused, then we push 0 for all metrics
    if ($status == 'archived' || $status == 'paused') {
      $impressions[$i][] = 0;
      $clicks[$i][] = 0;
      $ctr[$i][] = 0.0;
      $adSpend[$i][] = 0.0;
      $avgCpc[$i][] = 0.0;
      $unitsSold[$i][] = 0;
      $sales[$i][] = 0.0;
    } else { // If keyword is active, then run this code
      $impressions[$i][] = $result[$j]['impressions'];
      $clicks[$i][] = $result[$j]['clicks'];

      // Check if impressions are 0. If impressions are 0, then we know that CTR will also be 0.
      if ($result[$j]['impressions'] == 0) {
        $ctr[$i][] = 0.0;
      } else {
        $ctr[$i][] = round(($result[$j]['clicks'] / $result[$j]['impressions']), 2);
      }

      // Check if clicks are 0. If clicks are 0, then we know that CPC will also be 0.
      if ($result[$j]['clicks'] == 0) {
        $avgCpc[$i][] = 0.0;
      } else {
        $avgCpc[$i][] = round(($result[$j]['cost'] / $result[$j]['clicks']), 2);
      }

      // Push ad spend, units sold, and $ sales for the day to our arrays.
      $adSpend[$i][] = round($result[$j]['cost'], 2);
      $unitsSold[$i][] = $result[$j]['attributedUnitsOrdered1d'];
      $sales[$i][] = $result[$j]['attributedSales1d'];
    }
  }
}


// Grab array of keywords by their keyword ID
$sql = 'SELECT amz_kw_id FROM ppc_keywords WHERE user_id=' . htmlspecialchars($user_id);
$stmt = $pdo->query($sql);
$result = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo '<pre>';
var_dump($result);
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
storeKeywordArrays($pdo, $dbImpressions, $result, 'impressions');
// Grab clicks data from array and store in their respective campaigns
$dbClicks = prepareDbArrays($clicks, $dbClicks);
storeKeywordArrays($pdo, $dbClicks, $result, 'clicks');
// Grab ctr data from array and store in their respective campaigns
$dbCtr = prepareDbArrays($ctr, $dbCtr);
storeKeywordArrays($pdo, $dbCtr, $result, 'ctr');
// Grab ad spend data from array and store in their respective campaigns
$dbAdSpend = prepareDbArrays($adSpend, $dbAdSpend);
storeKeywordArrays($pdo, $dbAdSpend, $result, 'ad_spend');
// Grab average cpc data from array and store in their respective campaigns
$dbAvgCpc = prepareDbArrays($avgCpc, $dbAvgCpc);
storeKeywordArrays($pdo, $dbAvgCpc, $result, 'avg_cpc');
// Grab units sold data from array and store in their respective campaigns
$dbUnitsSold = prepareDbArrays($unitsSold, $dbUnitsSold);
storeKeywordArrays($pdo, $dbUnitsSold, $result, 'units_sold');
// Grab sales data from array and store in their respective campaigns
$dbSales = prepareDbArrays($sales, $dbSales);
storeKeywordArrays($pdo, $dbSales, $result, 'sales');

?>
