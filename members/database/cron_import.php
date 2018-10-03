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
