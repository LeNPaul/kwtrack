<?php
namespace AmazonAdvertisingApi;
require_once '../../../AmazonAdvertisingApi/Client.php';
include '../../../../database/pdo.inc.php';
use PDO;

$user_id          = $_POST['user_id'];
$toggle           = $_POST['toggle'];
$campaignName     = htmlspecialchars($_POST['campaignName']);
$campaignDataBack = $_POST['cDataBack'];
$campaignId       = $campaignDataBack[$campaignName];
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
  $client->updateCampaigns(array(
    array("campaignId" => $campaignId,
          "state"      => 'enabled')
  ));
  
  $sql = "UPDATE campaigns SET status=:state WHERE amz_campaign_id=:cid";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(
    ":state"  => 'enabled',
    ":cid"    => $campaignId
  ));
  $alertText = htmlspecialchars_decode($campaignName) . " has been enabled.";
} else if ($toggle == 'false') {
  $client->updateCampaigns(array(
    array("campaignId" => $campaignId,
          "state"      => 'paused')
  ));
  
  $sql = "UPDATE campaigns SET status=:state WHERE amz_campaign_id=:cid";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(
    ":state"  => 'paused',
    ":cid"    => $campaignId
  ));
  $alertText = htmlspecialchars_decode($campaignName) . " has been paused.";
} else {
  $client->updateCampaigns(array(
    array("campaignId" => $campaignId,
		  "state"	   => 'archived')
  ));
  
  $sql = "UPDATE campaigns SET status=:state WHERE amz_campaign_id=:cid";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(
    ":state"  => 'archived',
	":cid"	  => $campaignId
  ));
  $alertText = htmlspecialchars_decode($campaignName) . " has been archived.";
}

echo $alertText;