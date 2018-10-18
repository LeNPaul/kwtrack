<?php
namespace AmazonAdvertisingApi;
require_once '../../AmazonAdvertisingApi/Client.php';
require '../../../database/pdo.inc.php';
require '../helper.inc.php';
use PDO;

// delete from ppc_keywords; delete from ad_groups; delete from campaigns; delete from campaign_neg_kw; delete from adgroup_neg_kw;

function array_search2D($array, $key, $value) {
  return (array_search($value, array_column($array, $key)));
}

$refreshToken = 'Atzr|IwEBID8Cr8D51I4XzWRU5wdohHUoGJRY1rempo6uwgk_niC5AgqZo_SVul0Nt8V5oU1j8P2T08oPjR8gLKSsWnJuAflfBzcMky0NzBoKcSIYH62WJ4I86G6t4jGxU7fitoLO79TJPFjCoHPXyjnvaNLxFaJPxOaW3t4fLBH9-1RGsAaEdrP0-r85iVNgG_pQE2HA7bl_ZMqWoJbXhww-YEsfMH6tBKXG0S0dMreLkEkdx75eABfzKwdDm9jokTL8YZjkqj1ELRFOwK6Pgv1PsYTvdI2Us1fTw-Bu1n_n4am_vlrK4ntseK_dqFHvrV4_h0aup1hoChA5KZD2ID3fG4e4be4iCRC66QdJxmjv_q_o8RxoZR_bG0vhlkU2rSYKnMnZOj7nkRS2Z6JoRPWRLw7nP8nEfHLRkCQnrOn2PHkrKX7MWTIWt1f-_rkr3ocfvgKfcixFvTc6XmNGg0IYbVidw0thS3-AgSpnGaG0O7Q-W9VZPFRFtas1PltUG69LL0ko2EOz6yW-RG9071MfpUMgre2_TUildA68rlcdikXtNfMtyYNwqvQhlSZ_eVXWGclIpk4XQ39a-5eJiB8HVfsAvgdF';
$profileId = '1215041354659387';


function getSnapshot($client, $snapshotId) {
  do {
    $report = $client->getSnapshot($snapshotId);
    $result2 = json_decode($report['response'], true);
    if (array_key_exists('status', $result2)) {
      $status = $result2['status'];
    } else {
      $status = 'DONE';
      $report = $result2;
    }
  } while ($status == 'IN_PROGRESS');
  return $report;
}

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

$kwSnapshot = $client->requestSnapshot(
  "keywords",
  array("stateFilter"  => "enabled,paused,archived",
        "campaignType" => "sponsoredProducts"));

echo '<pre>';
var_dump($kwSnapshot);
echo '</pre>';

$snapshotId = json_decode($kwSnapshot, true);
$snapshotId = $snapshotId['snapshotId'];

$kwSnapshot = getSnapshot($snapshotId);

echo '<pre>';
var_dump($kwSnapshot);
echo '</pre>';


?>
