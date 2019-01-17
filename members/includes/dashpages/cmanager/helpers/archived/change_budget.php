<?php
namespace AmazonAdvertisingApi;
require_once '../../../AmazonAdvertisingApi/Client.php';
include '../../../../database/pdo.inc.php';
use PDO;

$user_id       = $_POST['user_id'];
$campaignName  = htmlspecialchars($_POST['campaignName']);
$cDataBack     = $_POST['cDataBack'];
$campaignId    = $cDataBack[$campaignName];
$refresh_token = $_POST['refresh_token'];
$profileId     = $_POST['profileId'];
$newBudget     = $_POST['newBudget'];
$newBudget     = round($newBudget, 2);

$config = array(
  "clientId" => "amzn1.application-oa2-client.4246e0f086e441259742c758f63ca0bf",
  "clientSecret" => "9c9e07b214926479e14a0781051ecc3ad9b29686d3cef24e15eb130a47cabeb3",
  "refreshToken" => $refresh_token,
  "region" => "na",
  "sandbox" => false,
);
$client            = new Client($config);
$client->profileId = $profileId;

$client->updateCampaigns(array(
  array(
    "campaignId"  => $campaignId,
    "dailyBudget" => $newBudget
  )
));

$sql  = "UPDATE campaigns SET daily_budget=:newBudget WHERE amz_campaign_id=:cid";
$stmt = $pdo->prepare($sql);
$stmt->execute(array(
  ":newBudget" => $newBudget,
  ":cid"       => $campaignId
));

echo htmlspecialchars_decode($campaignName) . ' budget changed to $' . $newBudget . ' per day.';
?>