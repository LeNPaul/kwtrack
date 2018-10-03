<?php
include 'pdo.inc.php';

// query all userIDs from ppc_keywords where active = 5 and store in array (userIDs)
$userIDs = [];
$sql = "SELECT user_id, profileId, refresh_token FROM users WHERE active=5";
$stmt = $pdo->query($sql);
$userIDs = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo '<pre>';
var_dump($userIDs);
echo '</pre>';

// for each userID, request report for yesterday's keywords and from report, store all keyword IDs (reportKeywordID)
/*
for ($i = 0; $i < count($userIDs); $i++) {
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
}

*/
?>
