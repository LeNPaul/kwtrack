<?php
namespace AmazonAdvertisingApi;
session_start();
error_reporting(E_ALL); ini_set("error_reporting", E_ALL);
require '../../../database/pdo.inc.php';
require '../helper.inc.php';
require_once '../../AmazonAdvertisingApi/Client.php';
use PDO;
set_time_limit(0);

//'Atzr|IwEBID8Cr8D51I4XzWRU5wdohHUoGJRY1rempo6uwgk_niC5AgqZo_SVul0Nt8V5oU1j8P2T08oPjR8gLKSsWnJuAflfBzcMky0NzBoKcSIYH62WJ4I86G6t4jGxU7fitoLO79TJPFjCoHPXyjnvaNLxFaJPxOaW3t4fLBH9-1RGsAaEdrP0-r85iVNgG_pQE2HA7bl_ZMqWoJbXhww-YEsfMH6tBKXG0S0dMreLkEkdx75eABfzKwdDm9jokTL8YZjkqj1ELRFOwK6Pgv1PsYTvdI2Us1fTw-Bu1n_n4am_vlrK4ntseK_dqFHvrV4_h0aup1hoChA5KZD2ID3fG4e4be4iCRC66QdJxmjv_q_o8RxoZR_bG0vhlkU2rSYKnMnZOj7nkRS2Z6JoRPWRLw7nP8nEfHLRkCQnrOn2PHkrKX7MWTIWt1f-_rkr3ocfvgKfcixFvTc6XmNGg0IYbVidw0thS3-AgSpnGaG0O7Q-W9VZPFRFtas1PltUG69LL0ko2EOz6yW-RG9071MfpUMgre2_TUildA68rlcdikXtNfMtyYNwqvQhlSZ_eVXWGclIpk4XQ39a-5eJiB8HVfsAvgdF'/*$argv[1]*/;
//'1215041354659387'

// Get user_id from AJAX POST request
$user_id = $_POST['user_id'];
$user_id = 2;

// Get refresh token and profileId from database using user_id
$sql = 'SELECT refresh_token, profileId FROM users WHERE user_id=' . $user_id;
$stmt = $pdo->query($sql);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
$refreshToken = $result[0]['refresh_token'];
$profileId = $result[0]['profileId'];


//$refreshToken = 'Atzr|IwEBID8Cr8D51I4XzWRU5wdohHUoGJRY1rempo6uwgk_niC5AgqZo_SVul0Nt8V5oU1j8P2T08oPjR8gLKSsWnJuAflfBzcMky0NzBoKcSIYH62WJ4I86G6t4jGxU7fitoLO79TJPFjCoHPXyjnvaNLxFaJPxOaW3t4fLBH9-1RGsAaEdrP0-r85iVNgG_pQE2HA7bl_ZMqWoJbXhww-YEsfMH6tBKXG0S0dMreLkEkdx75eABfzKwdDm9jokTL8YZjkqj1ELRFOwK6Pgv1PsYTvdI2Us1fTw-Bu1n_n4am_vlrK4ntseK_dqFHvrV4_h0aup1hoChA5KZD2ID3fG4e4be4iCRC66QdJxmjv_q_o8RxoZR_bG0vhlkU2rSYKnMnZOj7nkRS2Z6JoRPWRLw7nP8nEfHLRkCQnrOn2PHkrKX7MWTIWt1f-_rkr3ocfvgKfcixFvTc6XmNGg0IYbVidw0thS3-AgSpnGaG0O7Q-W9VZPFRFtas1PltUG69LL0ko2EOz6yW-RG9071MfpUMgre2_TUildA68rlcdikXtNfMtyYNwqvQhlSZ_eVXWGclIpk4XQ39a-5eJiB8HVfsAvgdF';
//$profileId = '1215041354659387';


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

/*==========================================================================


		██   ██ ███████ ██    ██ ██     ██  ██████  ██████  ██████  ███████
		██  ██  ██       ██  ██  ██     ██ ██    ██ ██   ██ ██   ██ ██
		█████   █████     ████   ██  █  ██ ██    ██ ██████  ██   ██ ███████
		██  ██  ██         ██    ██ ███ ██ ██    ██ ██   ██ ██   ██      ██
		██   ██ ███████    ██     ███ ███   ██████  ██   ██ ██████  ███████


============================================================================*/

try {
  importKeywords($pdo, $client, $user_id, 58);
} catch (\Exception $e) {
  echo "Error on file: " . $e->getFile() . "\n<br>";
  echo "Message: " . $e->getMessage() . "\n<br>";
  echo "On Line: " . $e->getLine() . "\n<br>";
  echo "Trace: " . $e->getTrace() . "\n<br>";
  echo "Code: " . $e->getCode() . "\n<br>";
}


/*==========================================================================


 █████  ██████       ██████  ██████   ██████  ██    ██ ██████  ███████
██   ██ ██   ██     ██       ██   ██ ██    ██ ██    ██ ██   ██ ██
███████ ██   ██     ██   ███ ██████  ██    ██ ██    ██ ██████  ███████
██   ██ ██   ██     ██    ██ ██   ██ ██    ██ ██    ██ ██           ██
██   ██ ██████       ██████  ██   ██  ██████   ██████  ██      ███████


============================================================================*/

// First, import all ad group names, campaign Id's, ad group Id's, default bids, and states

try {
  $result = $client->listAdGroups();
  $result = json_decode($result['response'], true);

// Iterate through all ad groups and insert them into database

  $sql = "INSERT INTO ad_groups (amz_adgroup_id, ad_group_name, amz_campaign_id, status, user_id, default_bid)
				VALUES (:amz_adgroup_id, :ad_group_name, :amz_campaign_id, :status, :user_id, :default_bid)";
  $stmt = $pdo->prepare($sql);

  for ($i = 0; $i < count($result); $i++) {
    $stmt->execute(array(
      ':amz_adgroup_id'		=> $result[$i]['adGroupId'],
      ':ad_group_name'		=> $result[$i]['name'],
      ':amz_campaign_id'	=> $result[$i]['campaignId'],
      ':status'						=> $result[$i]['state'],
      ':user_id'					=> $user_id,
      ':default_bid'      => $result[$i]['defaultBid']
    ));
  }


// Second, query database for list of all adgroup id's
  $sql = "SELECT amz_adgroup_id FROM ad_groups WHERE user_id=$user_id";
  $stmt = $pdo->query($sql);
  $result = $stmt->fetchAll(PDO::FETCH_COLUMN);

  for ($i = 0; $i < count($result); $i++) {
    importAdGroupMetrics($pdo, $result[$i], 59, $client);
  }
} catch (\Exception $e) {
  echo "Error on file: " . $e->getFile() . "\n<br>";
  echo "Message: " . $e->getMessage() . "\n<br>";
  echo "On Line: " . $e->getLine() . "\n<br>";
  echo "Trace: " . $e->getTrace() . "\n<br>";
  echo "Code: " . $e->getCode() . "\n<br>";
}



/*==========================================================================


 ██████  █████  ███    ███ ██████   █████  ██  ██████  ███    ██ ███████
██      ██   ██ ████  ████ ██   ██ ██   ██ ██ ██       ████   ██ ██
██      ███████ ██ ████ ██ ██████  ███████ ██ ██   ███ ██ ██  ██ ███████
██      ██   ██ ██  ██  ██ ██      ██   ██ ██ ██    ██ ██  ██ ██      ██
 ██████ ██   ██ ██      ██ ██      ██   ██ ██  ██████  ██   ████ ███████


============================================================================*/

try {
  $result = $client->listCampaigns();
  $result = json_decode($result['response'], true);

// Iterate through all ad groups and insert them into database

  $sql = "INSERT INTO campaigns (campaign_name, amz_campaign_id, user_id, campaign_type, targeting_type, status, daily_budget)
				VALUES (:camapign_name, :amz_campaign_id, :user_id, :campaign_type, :targeting_type, :status, :daily_budget)";
  $stmt = $pdo->prepare($sql);

  for ($i = 0; $i < count($result); $i++) {
    $targetingType = ($result[$i]['targetingType'] == 'manual') ? 'Manual' : 'Automatic';
    $stmt->execute(array(
      ':camapign_name'		=> $result[$i]['name'],
      ':amz_campaign_id'	=> $result[$i]['campaignId'],
      ':user_id'					=> $user_id,
      ':campaign_type'		=> $result[$i]['campaignType'],
      ':targeting_type'		=> $targetingType,
      ':status'						=> $result[$i]['state'],
      ':daily_budget'			=> $result[$i]['dailyBudget']
    ));
  }

// Second, query database for list of all campaign id's
  $sql = "SELECT amz_campaign_id FROM campaigns WHERE user_id=$user_id";
  $stmt = $pdo->query($sql);
  $result = $stmt->fetchAll(PDO::FETCH_COLUMN);

  for ($i = 0; $i < count($result); $i++) {
    importCampaignMetrics($pdo, $result[$i], 59);
  }
} catch (\Exception $e) {
  echo "Error on file: " . $e->getFile() . "\n<br>";
  echo "Message: " . $e->getMessage() . "\n<br>";
  echo "On Line: " . $e->getLine() . "\n<br>";
  echo "Trace: " . $e->getTrace() . "\n<br>";
  echo "Code: " . $e->getCode() . "\n<br>";
}


/*================================================================


		███    ██ ███████  ██████   █████  ████████ ██ ██    ██ ███████
		████   ██ ██      ██       ██   ██    ██    ██ ██    ██ ██
		██ ██  ██ █████   ██   ███ ███████    ██    ██ ██    ██ █████
		██  ██ ██ ██      ██    ██ ██   ██    ██    ██  ██  ██  ██
		██   ████ ███████  ██████  ██   ██    ██    ██   ████   ███████

		██   ██ ███████ ██    ██ ██     ██  ██████  ██████  ██████  ███████
		██  ██  ██       ██  ██  ██     ██ ██    ██ ██   ██ ██   ██ ██
		█████   █████     ████   ██  █  ██ ██    ██ ██████  ██   ██ ███████
		██  ██  ██         ██    ██ ███ ██ ██    ██ ██   ██ ██   ██      ██
		██   ██ ███████    ██     ███ ███   ██████  ██   ██ ██████  ███████


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


/*===============================================================

	███████ ██ ███    ██  █████  ██          ███████ ████████ ███████ ██████
	██      ██ ████   ██ ██   ██ ██          ██         ██    ██      ██   ██
	█████   ██ ██ ██  ██ ███████ ██          ███████    ██    █████   ██████
	██      ██ ██  ██ ██ ██   ██ ██               ██    ██    ██      ██
	██      ██ ██   ████ ██   ██ ███████     ███████    ██    ███████ ██

 *===============================================================*/


// Set user's active level to 4

$sql = "UPDATE users SET active=4 WHERE user_id=:user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(array(':user_id' => $user_id));
