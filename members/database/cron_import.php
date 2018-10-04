<?php
include 'pdo.inc.php';

/*

██   ██ ███████ ██      ██████  ███████ ██████  ███████
██   ██ ██      ██      ██   ██ ██      ██   ██ ██
███████ █████   ██      ██████  █████   ██████  ███████
██   ██ ██      ██      ██      ██      ██   ██      ██
██   ██ ███████ ███████ ██      ███████ ██   ██ ███████

*/

/*
 *  function array_search2D(Array $array, Mixed $key, Mixed $value) --> Array
 *    --> Returns index where $key => $value pair is found in a 2D array formatted like:
 *          [ i => [key1 => value1, key2 => value2, keyN => valueN] ]
 */

function array_search2D($array, $key, $value) {
  return (array_search($value, array_column($array, $key)));
}


/*

██   ██ ███████ ██    ██ ██     ██  ██████  ██████  ██████  ███████
██  ██  ██       ██  ██  ██     ██ ██    ██ ██   ██ ██   ██ ██
█████   █████     ████   ██  █  ██ ██    ██ ██████  ██   ██ ███████
██  ██  ██         ██    ██ ███ ██ ██    ██ ██   ██ ██   ██      ██
██   ██ ███████    ██     ███ ███   ██████  ██   ██ ██████  ███████

*/

// query all userIDs from ppc_keywords where active = 5 and store in array (userIDs)
$userIDs   = [];
// $reportIDs = [];

$sql     = "SELECT user_id, profileId, refresh_token FROM users WHERE active=5";
$stmt    = $pdo->query($sql);
$userIDs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// for each userID, request report for yesterday's keywords and from report, store all keyword IDs (reportKeywordIDs)

for ($i = 0; $i < count($userIDs); $i++) {
  $user_id = $userIDs[$i]['user_id'];
  // Instantiate client for advertising API
  $config = array(
  	"clientId" => "amzn1.application-oa2-client.4246e0f086e441259742c758f63ca0bf",
  	"clientSecret" => "9c9e07b214926479e14a0781051ecc3ad9b29686d3cef24e15eb130a47cabeb3",
  	"refreshToken" => $userIDs[$i]['refresh_token'],
  	"region" => "na",
  	"sandbox" => false,
  );
  $client = new Client($config);
  $client->profileId = $userIDs[$i]['profileId'];

  // Request report, then get report ID and append to reportIDs
  $result = $client->requestReport(
    "keywords",
    array("reportDate"    => $date,
          "campaignType"  => "sponsoredProducts",
          "metrics"       => "adGroupId,campaignId,keywordId,keywordText,matchType,impressions,clicks,cost,campaignBudget,attributedUnitsOrdered1d,attributedSales1d"
    )
  );

  $code = $result['code'];

  // Get the report id so we can use it to get the report
  $result2         = json_decode($result['response'], true);
  $reportId        = $result2['reportId'];
  $status = $result2['status'];

  // Keep pinging the report until we get a 200 code
  while ($status == 'IN_PROGRESS') {
  	$result = $client->getReport($reportId);
  	$result = json_decode($result['response'], true);
  	$status = $result['status'];
  }

  // Once the status is NOT "IN_PROGRESS", we know that we have the full report
  $result = $client->getReport($reportId);
  $result = json_decode($result['response'], true);

  // Store all keyword IDs from the report in reportKeywordIDs
  $reportKeywordIDs = [];
  for ($j = 0; $j < count($result); $j++) {
    $reportKeywordIDs[] = $result[$i]['keywordId'];
  }

  // Fetch and store amz_kw_id for the current user in array (dbKeywordIDs)
  $sql          = "SELECT amz_kw_id FROM ppc_keywords WHERE user_id={$user_id}";
  $stmt         = $pdo->query($sql);
  $dbKeywordIDs = $stmt->fetchAll(PDO::FETCH_COLUMN);

  // First check if length of reportKeywordIDs > length of dbKeywordIDs
  if (count($reportKeywordIDs) > count($dbKeywordIDs)) {
    $diff = array_diff($reportKeywordIDs, $dbKeywordIDs);

    // foreach diff, find index of extra keywords in the report.
    for ($a = 0; $a < count($diff); $a++) {
      $kw_id = $diff[$a];
      $index = array_search2D($result, 'keywordId', $kw_id);
       if ($index) {
         // Prepare metric arrays to be inserted into db for keyword
         $impressions = array_fill(0, 59, 0);
         $clicks      = array_fill(0, 59, 0);
         $ctr         = array_fill(0, 59, 0);
         $ad_spend    = array_fill(0, 59, 0);
         $avg_cpc     = array_fill(0, 59, 0);
         $units_sold  = array_fill(0, 59, 0);
         $sales       = array_fill(0, 59, 0);

         // Prepend all metrics to the arrays
         array_unshift($impressions, $result[$index]['impressions']);
         array_unshift($clicks, $result[$index]['clicks']);
         array_unshift($ctr, ($result[$index]['clicks'] == 0) ? 0.0 : ($result[$index]['clicks'] / $result[$index]['impressions']));
         array_unshift($ad_spend, $result[$index]['cost']);
         array_unshift($avg_cpc, ($result[$index]['clicks'] == 0) ? 0.0 : ($result[$index]['cost'] / $result[$index]['clicks']));
         array_unshift($units_sold, $result[$index]['attributedUnitsOrdered1d']);
         array_unshift($sales, $result[$index]['attributedSales1d']);

         // Get bid and status of the current keyword
         $kw = $client->getBiddableKeyword(178376339592907);
         $kw = json_decode($kw['response'], true);

         $sql = "INSERT INTO ppc_keywords (amz_campaign_id, amz_adgroup_id, amz_kw_id, keyword_text, match_type, status, impressions, bid, clicks, ctr, ad_spend, avg_cpc, units_sold, sales)
         VALUES (:amz_campaign_id, :amz_adgroup_id, :amz_kw_id, :keyword_text, :match_type, :status, :impressions, :bid, :clicks, :ctr, :ad_spend, :avg_cpc, :units_sold, :sales)";
         $stmt = $pdo->prepare($sql);
         $stmt->execute(array(
           ":amz_campaign_id" => $result[$index]['campaignId'],
           ":amz_adgroup_id"  => $result[$index]['adGroupId'],
           ":amz_kw_id"       => $kw_id,
           ":keyword_text"    => $result[$index]['keywordText'],
           ":match_type"      => $result[$index]['matchType'],
           ":status"          =>
           ":impressions"     =>
           ":bid"             =>
           ":clicks"          =>
           ":ctr"             =>
           ":ad_spend"        =>
           ":avg_cpc"         =>
           ":units_sold"      =>
           ":sales"           =>
         ));
       } else {
         echo 'An error has occurred.';
         die;
       }
    }

  } else {

  }
}

/*
// Iterate through $reportIDs and obtain keyword report. Store in Array kwReports
$currentReportID = '';
$code = 202;
for ($j = 0; $j < count($reportIDs); $j++) {
  // Keep trying to request the report until we get a "200" code response
  while ($code == 202) {
    $result = $client->getReport($reportIDs[$i]);
    $code   = $result['code'];
  }

}
*/

?>
