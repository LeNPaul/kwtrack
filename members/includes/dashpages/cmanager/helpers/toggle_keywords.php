<?php
namespace AmazonAdvertisingApi;
use PDO;

require_once '../../../AmazonAdvertisingApi/Client.php';
include '../../../../database/pdo.inc.php';

$user_id          = $_POST['user_id'];
$toggle           = $_POST['toggle'];
$keywordName      = htmlspecialchars($_POST['keywordName']);
$keywordDataBack  = $_POST['adgroupDataBack'];
$keywordId        = $keywordDataBack[$keywordName];
$refresh_token    = $_POST['refresh_token'];
$profileId        = $_POST['profileId'];

$config = array(
  "clientId" => "amzn1.application-oa2-client.4246e0f086e441259742c758f63ca0bf",
  "clientSecret" => "9c9e07b214926479e14a0781051ecc3ad9b29686d3cef24e15eb130a47cabeb3",
  "refreshToken" => $refresh_token,
  "region" => "na",
  "sandbox" => false,
);
$client = new Client($config);
$client->profileId = $profileId;

if ($toggle == 'true') {
  $client->updateBiddableKeywords(array(
    array("keywordId" => $keywordId,
      "state"      => 'enabled')
  ));
  
  $sql = "UPDATE ad_groups SET status=:state WHERE amz_campaign_id=:agid";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(
    ":state"  => 'enabled',
    ":agid"    => $keywordId
  ));
  $alertText = htmlspecialchars_decode($keywordName) . " has been enabled.";
} else {
  $client->updateBiddableKeywords(array(
    array("keywordId" => $keywordId,
      "state"      => 'paused')
  ));
  
  $sql = "UPDATE ad_groups SET status=:state WHERE amz_campaign_id=:agid";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(
    ":state"  => 'paused',
    ":agid"    => $keywordId
  ));
  $alertText = htmlspecialchars_decode($keywordName) . " has been paused.";
}

echo $alertText;