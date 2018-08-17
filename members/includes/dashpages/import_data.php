<?php
// file to import data and store in db
require '../../database/pdo2.inc.php';
$curl = curl_init();

//grab profile for profile ID
$accessToken = $argv[1];
$user_id = $argv[2];
$url = "https://advertising-api.amazon.com/v1/profiles";
$options = array(
  "Content-Type:application/json",
  "Authorization: Bearer $accessToken"
);

curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
curl_setopt($curl, CURLOPT_HTTPHEADER, $options);

$profileResult = curl_exec($curl);
$jsonProfile = json_decode(stripslashes($profileResult), true);
$profileID = $jsonProfile["properties"]["profileID"]["description"];

// Store profileID in users table
$sql = "UPDATE users SET profileID=:profileID WHERE user_id=:user_id" /* access_token=:accessToken */;
$stmt = $pdo->prepare($sql);
$stmt->execute(array(
	':profileID' => $profileID,
  ':user_id'   => $user_id
	// ":accessToken" => $accessToken
));

// Use profileID to get all the campaigns and store them in db
$url = "https://advertising-api.amazon.com/v1/campaigns";
$options = array(
	"Context-Type:application/jason",
	"Authorization: Bearer {$accessToken}",
	"Amazon-Advertising-API-Scope: {$profileID}"
);

curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
curl_setopt($curl, CURLOPT_HTTPHEADER, $options);

// Get and store campaign name, campaign type, campaign id, targetting type, state, daily budget
$campaignResult = curl_exec($curl);
$jsonCampaign = json_decode(stripslashes($campaignResult), true);
foreach($jsonCampaign as $item) {
  // Get values from json
	$name = $item["properties"]["name"]["description"];
	$type = $item["properties"]["campaignType"]["oneOf"];
	$id = $item["properties"]["campaignId"]["description"];
	$budget = $item["properties"]["dailyBudget"]["minimum"];
	$state = $item["properties"]["state"]["oneOf"];
	$targetType = $item["properties"]["targetingType"]["oneOf"];
  // Insert values into db
	$sql = "INSERT INTO campaigns (campaign_name, amz_campaign_id, user_id, campaign_type, targeting_type, state, daily_budget) VALUES (:name, :id, , :user_id, :campaignType, :targetingType, :state, :dailyBudget)";
	$stmt = $pdo->prepare($sql);
	$stmt->execute(array(
	  ':name' => $name,
		':id' => $id,
    ':user_id' => $user_id,
		':campaignType' => $type,
		':targetingType' => $targetType,
		':state' => $state,
		':dailyBudget' => $dailyBudget
	));
}

// Get and store ad groups

// Get and store keywords

// Change active=3 for the user and redirect to dashboard
$sql = "UPDATE users SET active=:active WHERE user_id=:user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(array(
  ':active'   => 3,
  ':user_id'  => $user_id
));

header('location: ../../dashboard.php');
exit();
?>
