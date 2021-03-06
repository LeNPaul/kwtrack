<?php
namespace AmazonAdvertisingApi;
require_once '../../AmazonAdvertisingApi/Client.php';
require '../../../database/pdo.inc.php';
require '../helper.inc.php';
use PDO;

ini_set('precision', 30);


// delete from ppc_keywords; delete from ad_groups; delete from campaigns; delete from campaign_neg_kw; delete from adgroup_neg_kw;

$refreshToken = 'Atzr|IwEBID8Cr8D51I4XzWRU5wdohHUoGJRY1rempo6uwgk_niC5AgqZo_SVul0Nt8V5oU1j8P2T08oPjR8gLKSsWnJuAflfBzcMky0NzBoKcSIYH62WJ4I86G6t4jGxU7fitoLO79TJPFjCoHPXyjnvaNLxFaJPxOaW3t4fLBH9-1RGsAaEdrP0-r85iVNgG_pQE2HA7bl_ZMqWoJbXhww-YEsfMH6tBKXG0S0dMreLkEkdx75eABfzKwdDm9jokTL8YZjkqj1ELRFOwK6Pgv1PsYTvdI2Us1fTw-Bu1n_n4am_vlrK4ntseK_dqFHvrV4_h0aup1hoChA5KZD2ID3fG4e4be4iCRC66QdJxmjv_q_o8RxoZR_bG0vhlkU2rSYKnMnZOj7nkRS2Z6JoRPWRLw7nP8nEfHLRkCQnrOn2PHkrKX7MWTIWt1f-_rkr3ocfvgKfcixFvTc6XmNGg0IYbVidw0thS3-AgSpnGaG0O7Q-W9VZPFRFtas1PltUG69LL0ko2EOz6yW-RG9071MfpUMgre2_TUildA68rlcdikXtNfMtyYNwqvQhlSZ_eVXWGclIpk4XQ39a-5eJiB8HVfsAvgdF';
$profileId 		= '1215041354659387';

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

/*$result = $client->createProductAds(array(
  array(
    "campaignId" => (float)"43495976513084",
    "adGroupId" => (float)"155569325761026",
    "sku" => "B01NBWV385",
    "state" => "enabled"
  ),
  array(
    "campaignId" => (float)"43495976513084",
    "adGroupId" => (float)"155569325761026",
    "sku" => "B072PRDZMR",
    "state" => "enabled"
  )
));

var_dump($result);
echo '<hr />';
var_dump(json_decode($result['response'], true, 512, JSON_BIGINT_AS_STRING));
echo '<hr />';
echo '<hr />';*/

var_dump(json_decode($client->listAdGroups(array("campaignIdFilter"=>274044229202865))['response'], true, 512, JSON_BIGINT_AS_STRING));

$result = $client->createNegativeKeywords(array(
  array(
    "campaignId" => (float)"274044229202865",
    "adGroupId" => 201030223757113,
    "keywordText" => "AnotherKeyword",
    "matchType" => "negativeExact",
    "state" => "enabled"
  ),
  array(
    "campaignId" => (float)"274044229202865",
    "adGroupId" => 201030223757113,
    "keywordText" => "asdfAnotherKeyword",
    "matchType" => "negativeExact",
    "state" => "enabled"
  )
));


var_dump($result);
echo '<hr />';
var_dump(json_decode($result['response'], true, 512, JSON_BIGINT_AS_STRING));
?>
