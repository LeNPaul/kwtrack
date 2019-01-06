<?php
namespace AmazonAdvertisingApi;
session_start();
require_once "../../../AmazonAdvertisingApi/Client.php";
require_once dirname(__FILE__) . '../../../../../database/pdo.inc.php';

use PDO;

/*
 * $data_level glossary:
 *    0 = campaign, 1 = ad group, 2 = keyword
 *
 * if $toggle = true, then enable element
 * if $toggle = false, then pause element
 */

$user_id       = $_SESSION['user_id'];
$refresh_token = $_SESSION['refresh_token'];
$profile_id    = $_SESSION['profileId'];
$id            = (float) floatval($_POST['id']);
$element_name  = $_POST['element_name'];
$data_level    = $_POST['data_level'];
$toggle        = $_POST['toggle'];

$config = array(
  "clientId" => "amzn1.application-oa2-client.4246e0f086e441259742c758f63ca0bf",
  "clientSecret" => "9c9e07b214926479e14a0781051ecc3ad9b29686d3cef24e15eb130a47cabeb3",
  "refreshToken" => $refresh_token,
  "region" => "na",
  "sandbox" => false,
);
$client = new Client($config);
$client->profileId = $profile_id;

$alert_text = null;

function toggler($client, $element_name, $toggle, $id) {
  global $pdo;
  global $data_level;
  global $alert_text;

  $flag  = false;
  $state = ($toggle == "true") ? "enabled" : "paused";

  // If data level is on campaign level
  if ($data_level == 0) {
    $result = json_decode($client->updateCampaigns(array(array(
      "campaignId" => $id,
      "state"      => $state
    )))['response'], true)[0]["code"];

    if ($result == "SUCCESS") {
      $flag = true;

      $sql = "UPDATE campaigns SET status=:state WHERE amz_campaign_id=:id";
      $stmt = $pdo->prepare($sql);
      $stmt->execute(array(
        ":state"  => $state,
        ":id"    => $id
      ));
    } else {
      $flag = false;
    }

  }
  // If data level is on adgroup level
  elseif ($data_level == 1) {
    $result = json_decode($client->updateAdGroups(array(array(
      "adGroupId" => $id,
      "state"     => $state
    )))['response'], true)[0]["code"];

    if ($result == "SUCCESS") {
      $flag = true;

      $sql = "UPDATE ad_groups SET status=:state WHERE amz_adgroup_id=:id";
      $stmt = $pdo->prepare($sql);
      $stmt->execute(array(
        ":state"  => $state,
        ":id"     => $id
      ));
    } else {
      $flag = false;
    }

  }
  // If data level is on keyword level
  elseif ($data_level == 2) {
    $result = json_decode($client->updateBiddableKeywords(array(array(
      "keywordId" => $id,
      "state"     => $state
    )))['response'], true)[0]["code"];

    if ($result == "SUCCESS") {
      $flag = true;

      $sql = "UPDATE ppc_keywords SET status=:state WHERE amz_kw_id=:id";
      $stmt = $pdo->prepare($sql);
      $stmt->execute(array(
        ":state"  => $state,
        ":id"     => $id
      ));
    } else {
      $flag = false;
    }

  }

  $alert_text = ($flag) ? $element_name . " has successfully been " . $state : "An error has occurred...";
}

try {
  toggler($client, $element_name, $toggle, $id);
  echo $alert_text;
} catch(Exception $er) {
  echo "An error has occurred.";
}

?>
