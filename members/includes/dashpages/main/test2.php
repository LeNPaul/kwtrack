<?php
namespace AmazonAdvertisingApi;
require_once '../../AmazonAdvertisingApi/Client.php';
require '../../../database/pdo.inc.php';
require '../helper.inc.php';
use PDO;

function array_search2D($array, $key, $value) {
  return (array_search($value, array_column($array, $key)));
}

$refreshToken = 'Atzr|IwEBID8Cr8D51I4XzWRU5wdohHUoGJRY1rempo6uwgk_niC5AgqZo_SVul0Nt8V5oU1j8P2T08oPjR8gLKSsWnJuAflfBzcMky0NzBoKcSIYH62WJ4I86G6t4jGxU7fitoLO79TJPFjCoHPXyjnvaNLxFaJPxOaW3t4fLBH9-1RGsAaEdrP0-r85iVNgG_pQE2HA7bl_ZMqWoJbXhww-YEsfMH6tBKXG0S0dMreLkEkdx75eABfzKwdDm9jokTL8YZjkqj1ELRFOwK6Pgv1PsYTvdI2Us1fTw-Bu1n_n4am_vlrK4ntseK_dqFHvrV4_h0aup1hoChA5KZD2ID3fG4e4be4iCRC66QdJxmjv_q_o8RxoZR_bG0vhlkU2rSYKnMnZOj7nkRS2Z6JoRPWRLw7nP8nEfHLRkCQnrOn2PHkrKX7MWTIWt1f-_rkr3ocfvgKfcixFvTc6XmNGg0IYbVidw0thS3-AgSpnGaG0O7Q-W9VZPFRFtas1PltUG69LL0ko2EOz6yW-RG9071MfpUMgre2_TUildA68rlcdikXtNfMtyYNwqvQhlSZ_eVXWGclIpk4XQ39a-5eJiB8HVfsAvgdF';
$profileId = '1215041354659387';


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

$kw = $client->getBiddableKeyword(178376339592907);

echo '<pre>';
var_dump(json_decode($kw['response'], true));
echo '</pre>';

/*
$result = $client->requestReport(
	"keywords",
	array("reportDate"    => "20180907",
				"campaignType"  => "sponsoredProducts",
				"metrics"       => "adGroupId,campaignId,keywordId,keywordText,matchType,impressions,clicks,cost,campaignBudget,attributedUnitsOrdered1d,attributedSales1d",
				"segment"				=> "query"
	)
);

// Get the report id so we can use it to get the report
$result2         = json_decode($result['response'], true);
$reportId        = $result2['reportId'];
$status = $result2['status'];

// Keep pinging the report until we get a 200 code
while ($status == 'IN_PROGRESS') {
	$result = $client->getReport($reportId);
	$result = json_decode($result['response'], true);

	$status = $result['status'];
	echo $status.'<br />';
}

// Get the report using the report id
$result = $client->getReport($reportId);
$result = json_decode($result['response'], true);

echo '<pre>';
var_dump($result);
echo '</pre>';

$index = array_search2D($result, 'keywordId', 253);
var_dump($index);
*/
//
// echo '<pre>';
// var_dump($result[$index]);
// echo '</pre>';


?>
