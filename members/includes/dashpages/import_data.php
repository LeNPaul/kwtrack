<?php
// file to import data and store in db
session_start();
$curl = curl_init();

//grab profile for profile ID
$accessToken = $_GET[$argv[1]];
$url = "https://advertising-api.amazon.com/v1/profiles";
$options = array(
  "Content-Type:application/json",
  "Authorization: Bearer {$accessToken}"
);

curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
curl_setopt($curl, CURLOPT_HTTPHEADER, $options);

$profileResult = curl_exec($curl);
$jsonProfile = json_decode(stripslashes($profileResult), true);
$profileID = $jsonProfile["properties"]["profileID"]["description"];

// store profileID in users db
$sql = "UPDATE users SET user_id=:profileID WHERE access_token=:accessToken";
$stmt = $pdo->prepare($sql);
$stmt->execute(array(
	":profileID" => $profileID,
	":accessToken" => $accessToken
));

//use profileID to get all the campaigns and store them in db
$url = "https://advertising-api.amazon.com/v1/campaigns"
$options = array(
	"Context-Type:application/jason",
	"Authorization: Bearer {$accessToken}",
	"Amazon-Advertising-API-Scope: {$profileID}"
);

curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
curl_setopt($curl, CURLOPT_HTTPHEADER, $options);

//get and store campaign name, campaign type, campaign id, targetting type, state, daily budget
$campaignResult = curl_exec($curl);
$jsonCampaign = json_decode(stripslashes($campaignResult), true);
foreach($jsonCampaign as $campaign) {
	$name = $campaign["properties"]["name"]["description"];
	$type = $campaign["properties"]["campaignType"]["oneOf"];
	$id = $campaign["properties"]["campaignId"]["description"];
	$budget = $campaign["properties"]["dailyBudget"]["minimum"];
	$state = $campaign["properties"]["state"]["oneOf"];
	$targetType = $campaign["properties"]["targetingType"]["oneOf"];
	$sql = "INSERT INTO campaigns (campaign_name, amz_campaign_id, campaign_type, targeting_type, state, daily_budget) VALUES (:name, :id, :campaignType, :targetingType, :state, :dailyBudget)";
	$stmt = $pdo->prepare($sql);
	$stmt->execute(array(
		":name" => $name,
		":id" => $id,
		":campaignType" => $type,
		":targetingType" => $targetType,
		":state" => $state,
		":dailyBudget" => $dailyBudget
	));
}
?>