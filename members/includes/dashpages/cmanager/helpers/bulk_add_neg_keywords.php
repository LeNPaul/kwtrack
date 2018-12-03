<?php
namespace AmazonAdvertisingApi;
use PDO;

require_once '../../../AmazonAdvertisingApi/Client.php';
include '../../../../database/pdo.inc.php';

$campaignName     = $_POST['campaignName'];
$campaignDataBack = $_POST['cDataBack'];
$user_id          = $_POST['user_id'];
$refresh_token    = $_POST['refresh_token'];
$profileId        = $_POST['profileId'];

$config = array(
  "clientId" => "amzn1.application-oa2-client.4246e0f086e441259742c758f63ca0bf",
  "clientSecret" => "9c9e07b214926479e14a0781051ecc3ad9b29686d3cef24e15eb130a47cabeb3",
  "refreshToken" => $refresh_token,
  "region" => "na",
  "sandbox" => false,
);
$client            = new Client($config);
$client->profileId = $profileId;

try {

} catch (\Exception $e) {

}
