<?php
namespace AmazonAdvertisingApi;
session_start();
error_reporting(E_ALL); ini_set("error_reporting", E_ALL);
require '../../../database/pdo.inc.php';
require '../helper.inc.php';
require_once '../../AmazonAdvertisingApi/Client.php';
use PDO;
//ignore_user_abort(true);
set_time_limit(0);

//'Atzr|IwEBID8Cr8D51I4XzWRU5wdohHUoGJRY1rempo6uwgk_niC5AgqZo_SVul0Nt8V5oU1j8P2T08oPjR8gLKSsWnJuAflfBzcMky0NzBoKcSIYH62WJ4I86G6t4jGxU7fitoLO79TJPFjCoHPXyjnvaNLxFaJPxOaW3t4fLBH9-1RGsAaEdrP0-r85iVNgG_pQE2HA7bl_ZMqWoJbXhww-YEsfMH6tBKXG0S0dMreLkEkdx75eABfzKwdDm9jokTL8YZjkqj1ELRFOwK6Pgv1PsYTvdI2Us1fTw-Bu1n_n4am_vlrK4ntseK_dqFHvrV4_h0aup1hoChA5KZD2ID3fG4e4be4iCRC66QdJxmjv_q_o8RxoZR_bG0vhlkU2rSYKnMnZOj7nkRS2Z6JoRPWRLw7nP8nEfHLRkCQnrOn2PHkrKX7MWTIWt1f-_rkr3ocfvgKfcixFvTc6XmNGg0IYbVidw0thS3-AgSpnGaG0O7Q-W9VZPFRFtas1PltUG69LL0ko2EOz6yW-RG9071MfpUMgre2_TUildA68rlcdikXtNfMtyYNwqvQhlSZ_eVXWGclIpk4XQ39a-5eJiB8HVfsAvgdF'/*$argv[1]*/;
//'1215041354659387'

// Get user_id from AJAX POST request
$user_id = $_POST['user_id'];

//$user_id = 2;

// Get refresh token and profileId from database using user_id
$sql = 'SELECT refresh_token, profileId FROM users WHERE user_id=' . $user_id;
$stmt = $pdo->query($sql);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
$refreshToken = $result[0]['refresh_token'];
$profileId = $result[0]['profileId'];

/*
$refreshToken = 'Atzr|IwEBID8Cr8D51I4XzWRU5wdohHUoGJRY1rempo6uwgk_niC5AgqZo_SVul0Nt8V5oU1j8P2T08oPjR8gLKSsWnJuAflfBzcMky0NzBoKcSIYH62WJ4I86G6t4jGxU7fitoLO79TJPFjCoHPXyjnvaNLxFaJPxOaW3t4fLBH9-1RGsAaEdrP0-r85iVNgG_pQE2HA7bl_ZMqWoJbXhww-YEsfMH6tBKXG0S0dMreLkEkdx75eABfzKwdDm9jokTL8YZjkqj1ELRFOwK6Pgv1PsYTvdI2Us1fTw-Bu1n_n4am_vlrK4ntseK_dqFHvrV4_h0aup1hoChA5KZD2ID3fG4e4be4iCRC66QdJxmjv_q_o8RxoZR_bG0vhlkU2rSYKnMnZOj7nkRS2Z6JoRPWRLw7nP8nEfHLRkCQnrOn2PHkrKX7MWTIWt1f-_rkr3ocfvgKfcixFvTc6XmNGg0IYbVidw0thS3-AgSpnGaG0O7Q-W9VZPFRFtas1PltUG69LL0ko2EOz6yW-RG9071MfpUMgre2_TUildA68rlcdikXtNfMtyYNwqvQhlSZ_eVXWGclIpk4XQ39a-5eJiB8HVfsAvgdF';
$profileId = '1215041354659387';
*/

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


/***********************************************************
 *
 *
 *					CAMPAIGN IMPORTING
 *
 *
 ***********************************************************/

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

for ($i = 0; $i < 6; $i++) {
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
			array("reportDate"    => $date,
				  "campaignType"  => "sponsoredProducts",
				  "metrics"       => "campaignId,campaignName,impressions,clicks,cost,campaignBudget,campaignStatus,attributedUnitsOrdered1d,attributedSales1d"
			)
		);

	// Get the report id so we can use it to get the report
		$result = json_decode($result['response'], true);
		$reportId = $result['reportId'];

		sleep(120);

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
			array("reportDate"    => $date, // placeholder date
				  "campaignType"  => "sponsoredProducts",
				  "metrics"       => "campaignId,impressions,clicks,cost,campaignStatus,attributedUnitsOrdered1d,attributedSales1d"
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
// Grab array of campaigns by their campaign ID
$sql = 'SELECT amz_campaign_id FROM campaigns WHERE user_id=' . htmlspecialchars($user_id);
$stmt = $pdo->query($sql);
$result = $stmt->fetchAll(PDO::FETCH_COLUMN);

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
storeCampaignArrays($pdo, $dbImpressions, $result, 'impressions');
// Grab clicks data from array and store in their respective campaigns
$dbClicks = prepareDbArrays($clicks, $dbClicks);
storeCampaignArrays($pdo, $dbClicks, $result, 'clicks');
// Grab ctr data from array and store in their respective campaigns
$dbCtr = prepareDbArrays($ctr, $dbCtr);
storeCampaignArrays($pdo, $dbCtr, $result, 'ctr');
// Grab ad spend data from array and store in their respective campaigns
$dbAdSpend = prepareDbArrays($adSpend, $dbAdSpend);
storeCampaignArrays($pdo, $dbAdSpend, $result, 'ad_spend');
// Grab average cpc data from array and store in their respective campaigns
$dbAvgCpc = prepareDbArrays($avgCpc, $dbAvgCpc);
storeCampaignArrays($pdo, $dbAvgCpc, $result, 'avg_cpc');
// Grab units sold data from array and store in their respective campaigns
$dbUnitsSold = prepareDbArrays($unitsSold, $dbUnitsSold);
storeCampaignArrays($pdo, $dbUnitsSold, $result, 'units_sold');
// Grab sales data from array and store in their respective campaigns
$dbSales = prepareDbArrays($sales, $dbSales);
storeCampaignArrays($pdo, $dbSales, $result, 'sales');

/***********************************************************
 *
 *
 *					AD GROUP IMPORTING
 *
 *
 ***********************************************************/

$impressions = [];
$clicks = [];
$ctr = [];
$adSpend = [];
$avgCpc = [];
$unitsSold = [];
$sales = [];
$extraArray = [];

for ($i = 0; $i < 6; $i++) {
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
  // and store adgroup name, adgroup id, and campaign ID in the database
  if ($i === 0) {
    // Request the report from API with adGroup name, adGroup Id and campaign Id only
    // for the first iteration
    $result = $client->requestReport(
      "adGroups",
      array("reportDate"    => $date, // placeholder date
        "campaignType"  => "sponsoredProducts",
        "metrics"       => "campaignId,adGroupName,adGroupId,impressions,clicks,cost,impressions,clicks,attributedUnitsOrdered1d,attributedSales1d"
      )
    );

    // Get the report id so we can use it to get the report
    $result = json_decode($result['response'], true);
    $reportId = $result['reportId'];

    sleep(120);

    // Get the report using the report id
    $result = $client->getReport($reportId);
    $result = json_decode($result['response'], true);

    for ($x = 0; $x < count($result); $x++) {
      $extra = $client->getAdGroup($result[$x]['adGroupId']);
      $extraArray[] = json_decode($extra['response'], true);

      //print_r($extraArray[$x]);

      $sql = 'INSERT INTO ad_groups (user_id, status, default_bid, amz_adgroup_id, amz_campaign_id, ad_group_name)
              VALUES (:user_id, :status, :default_bid, :adgroup_id, :amz_campaign_id, :adgroup_name)';
      $stmt = $pdo->prepare($sql);
      $stmt->execute(array(
        ':user_id'          => $user_id,
        ':status'			      => $extraArray[$x]['state'],
        ':default_bid'	   	=> $extraArray[$x]['defaultBid'],
        ':adgroup_id'	    	=> $extraArray[$x]['adGroupId'],
        ':amz_campaign_id'  => $extraArray[$x]['campaignId'],
        ':adgroup_name'     => $extraArray[$x]['name']
      ));
    }

  } else {
    // All other iterations, we request this report to optimize time
    $result = $client->requestReport(
      "adGroups",
      array("reportDate"    => $date,
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

    // Check if campaign is archived/paused. If it is archived/paused, then we push 0 for all metrics
    if ($extraArray[$j]['state'] == 'archived' || $extraArray[$j]['state'] == 'paused') {
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

// Grab array of adGroups by their adGroup ID
$sql = 'SELECT amz_adgroup_id FROM ad_groups WHERE user_id=' . htmlspecialchars($user_id);
$stmt = $pdo->query($sql);
$result = $stmt->fetchAll(PDO::FETCH_COLUMN);

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
storeAdGroupArrays($pdo, $dbImpressions, $result, 'impressions');
// Grab clicks data from array and store in their respective campaigns
$dbClicks = prepareDbArrays($clicks, $dbClicks);
storeAdGroupArrays($pdo, $dbClicks, $result, 'clicks');
// Grab ctr data from array and store in their respective campaigns
$dbCtr = prepareDbArrays($ctr, $dbCtr);
storeAdGroupArrays($pdo, $dbCtr, $result, 'ctr');
// Grab ad spend data from array and store in their respective campaigns
$dbAdSpend = prepareDbArrays($adSpend, $dbAdSpend);
storeAdGroupArrays($pdo, $dbAdSpend, $result, 'ad_spend');
// Grab average cpc data from array and store in their respective campaigns
$dbAvgCpc = prepareDbArrays($avgCpc, $dbAvgCpc);
storeAdGroupArrays($pdo, $dbAvgCpc, $result, 'avg_cpc');
// Grab units sold data from array and store in their respective campaigns
$dbUnitsSold = prepareDbArrays($unitsSold, $dbUnitsSold);
storeAdGroupArrays($pdo, $dbUnitsSold, $result, 'units_sold');
// Grab sales data from array and store in their respective campaigns
$dbSales = prepareDbArrays($sales, $dbSales);
storeAdGroupArrays($pdo, $dbSales, $result, 'sales');

/***********************************************************
 *
 *
 *					KEYWORD IMPORTING
 *
 *
 ***********************************************************/

importKeywords($pdo, $client, $user_id);

/*================================================================
 *
 *    NEGATIVE KEYWORD IMPORTING
 *
 *===============================================================*/

// Get ad group level negative keywords and store them in db
$result = $client->listNegativeKeywords(array("stateFilter" => "enabled"));
$result = json_decode($result['response'], true);

for ($i = 0; $i < count($result); $i++) {
  $sql = 'INSERT INTO adgroup_neg_kw (kw_id, amz_adgroup_id, keyword_text, state, match_type)
          VALUES (:kw_id, :amz_adgroup_id, :keyword_text, :state, :match_type)';
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(
    ':kw_id'          => $result[$i]['keywordId'],
    ':amz_adgroup_id' => $result[$i]['adGroupId'],
    ':keyword_text'   => $result[$i]['keywordText'],
    ':state'          => $result[$i]['state'],
    ':match_type'     => $result[$i]['matchType']
  ));
}

//Get campaign level negative keywords and store them in db
$result = $client->listCampaignNegativeKeywords(array("stateFilter" => "enabled"));
$result = json_decode($result['response'], true);

for ($i = 0; $i < count($result); $i++) {
  $sql = 'INSERT INTO campaign_neg_kw (kw_id, amz_campaign_id, keyword_text, state, match_type)
          VALUES (:kw_id, :amz_campaign_id, :keyword_text, :state, :match_type)';
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(
    ':kw_id'          => $result[$i]['keywordId'],
    ':amz_campaign_id' => $result[$i]['campaignId'],
    ':keyword_text'   => $result[$i]['keywordText'],
    ':state'          => $result[$i]['state'],
    ':match_type'     => $result[$i]['matchType']
  ));
}
