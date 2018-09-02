<?php
namespace AmazonAdvertisingApi;
session_start();
require '../../../database/pdo.inc.php';
//require '../helper.inc.php';
require_once '../../AmazonAdvertisingApi/Client.php';
error_reporting(E_ALL); ini_set("error_reporting", E_ALL);
use PDO;

// Insert profileID in database for the user and set active level to 3
var_dump($_POST);
$profileId = $_POST['selectedProfile'];
$profileId = $profileId[0];
$sql = 'UPDATE users SET profileId=:profileId, active=:level WHERE user_id=:user_id';
$stmt = $pdo->prepare($sql);
$stmt->execute(array(
  ':profileId' => $profileId,
  ':level'     => 3,
  ':user_id'    => $_SESSION['user_id']
));

// Get refresh token
$sql = 'SELECT refresh_token FROM users WHERE user_id=' . $_SESSION['user_id'];
$stmt = $pdo->query($sql);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
$refreshToken = $result[0]['refresh_token'];
$user_id = $_SESSION['user_id'];

//import_data();


//function import_data(){
	// TODO: integrate campaign, adgroup, and keyword import code from test.php here
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

	// First, grab campaigns and store them in db
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

			sleep(8);

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
				array("reportDate"    => "20180727", // placeholder date
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

	// Second, grab ad groups and store them in db

//} //end function

// Redirect to dashboard
// header('location: ../../../dashboard.php');
// exit();