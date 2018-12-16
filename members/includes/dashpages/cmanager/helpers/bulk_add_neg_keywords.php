<?php
namespace AmazonAdvertisingApi;
use PDO;

require_once '../../../AmazonAdvertisingApi/Client.php';
include '../../../../database/pdo.inc.php';


function fillCampaignID($inputArr, $campaignID) {
  for ($i = 0; $i < count($inputArr); $i++) {
    $inputArr[$i]["campaignId"] = $campaignID;
  }
}

/**
 *
 * $negKeywordList
 *  [
 *    [
 *      "campaignId"  => null
 *      "keywordText" => keyword text,
 *      "matchType"   => "negativeExact"/"negativePhrase"
 *      "state"       => "enabled"
 *    ]
 *        ...
 *  ]
 *
 */

$campaignList     = $_POST['campaignList'];
$campaignDataBack = $_POST['cDataBack'];
$user_id          = $_POST['user_id'];
$refresh_token    = $_POST['refresh_token'];
$profileId        = $_POST['profileId'];
$negKeywordList   = $_POST['negKeywordList'];

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
  for ($i = 0; $i < count($campaignList); $i++) {
    $currentCampaignID    = $campaignDataBack[$campaignList[$i]];
    $modifiedCampaignList = fillCampaignID($campaignList, $currentCampaignID);
    /* $modifiedCampaignList is a campaign list with the proper campaign ID */

    try {
      $result = $client->createNegativeKeywords($modifiedCampaignList);
    } catch (\Exception $e) {
      echo $e->getMessage();
    }
  }


} catch (\Exception $e) {
  echo "Error:" . $e->getMessage() . "\n"
      ."Trace: " . $e->getTrace() . "\n"
      ."On Line: " . $e->getLine() . "\n";
}
