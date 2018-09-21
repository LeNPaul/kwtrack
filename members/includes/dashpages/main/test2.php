<?php
namespace AmazonAdvertisingApi;
require_once '../../AmazonAdvertisingApi/Client.php';
require '../../../database/pdo.inc.php';
require '../helper.inc.php';
use PDO;

shell_exec("php test3.php");

/*
$refreshToken = 'Atzr|IwEBID8Cr8D51I4XzWRU5wdohHUoGJRY1rempo6uwgk_niC5AgqZo_SVul0Nt8V5oU1j8P2T08oPjR8gLKSsWnJuAflfBzcMky0NzBoKcSIYH62WJ4I86G6t4jGxU7fitoLO79TJPFjCoHPXyjnvaNLxFaJPxOaW3t4fLBH9-1RGsAaEdrP0-r85iVNgG_pQE2HA7bl_ZMqWoJbXhww-YEsfMH6tBKXG0S0dMreLkEkdx75eABfzKwdDm9jokTL8YZjkqj1ELRFOwK6Pgv1PsYTvdI2Us1fTw-Bu1n_n4am_vlrK4ntseK_dqFHvrV4_h0aup1hoChA5KZD2ID3fG4e4be4iCRC66QdJxmjv_q_o8RxoZR_bG0vhlkU2rSYKnMnZOj7nkRS2Z6JoRPWRLw7nP8nEfHLRkCQnrOn2PHkrKX7MWTIWt1f-_rkr3ocfvgKfcixFvTc6XmNGg0IYbVidw0thS3-AgSpnGaG0O7Q-W9VZPFRFtas1PltUG69LL0ko2EOz6yW-RG9071MfpUMgre2_TUildA68rlcdikXtNfMtyYNwqvQhlSZ_eVXWGclIpk4XQ39a-5eJiB8HVfsAvgdF';
$profileId = '1215041354659387';
$user_id = 2;

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

// $status = $client->getBiddableKeywordEx('225434316448793');
// $status = json_decode($status['response'], true);
//
// echo '<pre>';
// var_dump($status);
// echo '</pre>';

// $sql = "SELECT sales FROM campaigns WHERE user_id=2";
// $stmt = $pdo->query($sql);
// $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
//
// echo '<pre>';
// var_dump($result);
// echo '</pre>';

/*
$result = $client->requestReport(
  "keywords",
  array("reportDate"    => '20180725',
        "campaignType"  => "sponsoredProducts",
        //"metrics"       => "adGroupId,campaignId,keywordId,keywordText,matchType,impressions,clicks,cost,campaignBudget,attributedUnitsOrdered1d,attributedSales1d"
        "metrics"       => "impressions,clicks,cost,attributedUnitsOrdered1d,attributedSales1d"
  )
);

// Get the report id so we can use it to get the report
$result = json_decode($result['response'], true);

echo '<pre>';
var_dump($result);
echo '<pre>';

$reportId = $result['reportId'];

sleep(10);

// Get the report using the report id
$result = $client->getReport($reportId);
$result = json_decode($result['response'], true);

echo '<pre>';
var_dump($result);
echo '<pre>';


$result = $client->listAdGroups();
echo '<pre>';
var_dump(json_decode($result['response'], true));
echo '</pre>';
*/
/*

$arr = array(
	'id1' => [1,2,3,4,5,6,7,8,9,10],
	'id9' => [10],
	'id9' => [10]
);

//$arr = adjustDayOffset($arr, 10);

foreach ($arr as $key => $value) {
	echo $key . ' => ';
	print_r($value);
	echo ' - '.count($value) . '<br />';

	if (count($value) < 10) {
		echo 'starting<br />';
		// Prepend $numDays-1 0's to $value
		for ($i = 1; $i < 10; $i++) {
			$value[] = 0;
		}
	}

	echo '<br /><br />'.$key . ' => ';
	print_r($value);
	echo ' - '.count($value) . '<br />';
}

// echo '<pre>';
// var_dump($arr);
// echo '</pre>';
*/
?>
