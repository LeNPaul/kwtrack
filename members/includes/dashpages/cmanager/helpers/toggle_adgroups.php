<?php
namespace AmazonAdvertisingApi;
use PDO;
require_once '../../../AmazonAdvertisingApi/Client.php';
include '../../../../database/pdo.inc.php';

$user_id          = $_POST['user_id'];
$toggle           = $_POST['toggle'];
$adgroupName      = htmlspecialchars($_POST['adgroupName']);
$adgroupDataBack  = $_POST['adgroupDataBack'];
$adgroupId        = $adgroupDataBack[$adgroupName];
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
  $client->updateAdGroups(array(
    array("adGroupId" => $adgroupId,
      "state"      => 'enabled')
  ));
  
  $sql = "UPDATE ad_groups SET status=:state WHERE amz_adgroup_id=:agid";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(
    ":state"  => 'enabled',
    ":agid"    => $adgroupId
  ));
  $alertText = htmlspecialchars_decode($adgroupName) . " has been enabled.";
} else if ($toggle == 'false') {
  $client->updateAdGroups(array(
    array("adGroupId" => $adgroupId,
      "state"      => 'paused')
  ));
  
  $sql = "UPDATE ad_groups SET status=:state WHERE amz_adgroup_id=:agid";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(
    ":state"  => 'paused',
    ":agid"    => $adgroupId
  ));
  $alertText = htmlspecialchars_decode($adgroupName) . " has been paused.";
} else {
  $client->updateAdGroups(array(
    array("adGroupId" => $adGroupId,
		  "state"	   => 'archived')
  ));
  
  $sql = "UPDATE ad_groups SET status=:state WHERE amz_adgroup_id=:agid";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(
    ":state"  => 'archived',
	":agid"	  => $adgroupId
  ));
  $alertText = htmlspecialchars_decode($adgroupName) . " has been archived.";
}

echo $alertText;