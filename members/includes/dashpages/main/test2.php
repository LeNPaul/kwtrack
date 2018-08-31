<?php
namespace AmazonAdvertisingApi;
require_once '../../AmazonAdvertisingApi/Client.php';
require '../../../database/pdo.inc.php';
require '../helper.inc.php';
use PDO;



// TESTING PURPOSES ONLY
$sql = 'DELETE FROM adgroup_neg_kw';
$stmt = $pdo->prepare($sql);
$stmt->execute();

// TESTING PURPOSES ONLY
$sql = 'DELETE FROM campaign_neg_kw';
$stmt = $pdo->prepare($sql);
$stmt->execute();

$refreshToken = 'Atzr|IwEBID8Cr8D51I4XzWRU5wdohHUoGJRY1rempo6uwgk_niC5AgqZo_SVul0Nt8V5oU1j8P2T08oPjR8gLKSsWnJuAflfBzcMky0NzBoKcSIYH62WJ4I86G6t4jGxU7fitoLO79TJPFjCoHPXyjnvaNLxFaJPxOaW3t4fLBH9-1RGsAaEdrP0-r85iVNgG_pQE2HA7bl_ZMqWoJbXhww-YEsfMH6tBKXG0S0dMreLkEkdx75eABfzKwdDm9jokTL8YZjkqj1ELRFOwK6Pgv1PsYTvdI2Us1fTw-Bu1n_n4am_vlrK4ntseK_dqFHvrV4_h0aup1hoChA5KZD2ID3fG4e4be4iCRC66QdJxmjv_q_o8RxoZR_bG0vhlkU2rSYKnMnZOj7nkRS2Z6JoRPWRLw7nP8nEfHLRkCQnrOn2PHkrKX7MWTIWt1f-_rkr3ocfvgKfcixFvTc6XmNGg0IYbVidw0thS3-AgSpnGaG0O7Q-W9VZPFRFtas1PltUG69LL0ko2EOz6yW-RG9071MfpUMgre2_TUildA68rlcdikXtNfMtyYNwqvQhlSZ_eVXWGclIpk4XQ39a-5eJiB8HVfsAvgdF';
$profileId = '1215041354659387';
$user_id = 0;

$config = array(
  "clientId" => "amzn1.application-oa2-client.4246e0f086e441259742c758f63ca0bf",
  "clientSecret" => "9c9e07b214926479e14a0781051ecc3ad9b29686d3cef24e15eb130a47cabeb3",
  "refreshToken" => $refreshToken,
  "region" => "na",
  "sandbox" => false,
);
$client = new Client($config);
$client->profileId = $profileId;

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

?>
