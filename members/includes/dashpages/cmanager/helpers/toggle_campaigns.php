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

$stmt = $pdo->prepare("SELECT refresh_token, profileId FROM users WHERE user_id=?");
$stmt->bindParam(1, $user_id);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$refresh_token = $result[0]['refresh_token'];
$profileId     = $result[0]['profileId'];

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
    ":amz_campaign_id"  => $campaignId
  ));
  $alertText = htmlspecialchars_decode($campaignName) . " has been enabled.";
} else {
  $client->updateCampaigns(array(
    array("campaignId" => $campaignId,
          "state"      => 'paused')
  ));
  $sql = "UPDATE campaigns SET status=:state WHERE amz_campaign_id=:cid";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(
    ":state"  => 'paused',
    ":amz_campaign_id"  => $campaignId
  ));
  $alertText = htmlspecialchars_decode($campaignName) . " has been paused.";
}

echo $alertText;