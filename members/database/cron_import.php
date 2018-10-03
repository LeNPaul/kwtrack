<?php
include 'pdo.inc.php';


/*

██   ██ ███████ ██    ██ ██     ██  ██████  ██████  ██████  ███████
██  ██  ██       ██  ██  ██     ██ ██    ██ ██   ██ ██   ██ ██
█████   █████     ████   ██  █  ██ ██    ██ ██████  ██   ██ ███████
██  ██  ██         ██    ██ ███ ██ ██    ██ ██   ██ ██   ██      ██
██   ██ ███████    ██     ███ ███   ██████  ██   ██ ██████  ███████

*/

// query all userIDs from ppc_keywords where active = 5 and store in array (userIDs)
$userIDs   = [];
$reportIDs = [];

$sql     = "SELECT user_id, profileId, refresh_token FROM users WHERE active=5";
$stmt    = $pdo->query($sql);
$userIDs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// for each userID, request report for yesterday's keywords and from report, store all keyword IDs (reportKeywordID)
for ($i = 0; $i < count($userIDs); $i++) {
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

  // Get the report id so we can use it to get the report
  $result      = json_decode($result['response'], true);
  $reportId    = $result['reportId'];
  $reportIDs[] = $reportId;
}

// Iterate through $reportIDs and obtain keyword report
for () {

}

?>
