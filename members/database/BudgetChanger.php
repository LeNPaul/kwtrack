<?php
namespace AmazonAdvertisingApi;
require_once dirname(__FILE__) . "/../includes/AmazonAdvertisingApi/Client.php";
require_once dirname(__FILE__) . "/pdo.inc.php";

use PDO;
use PDOException;

ini_set('precision', 30);

/*
 * $data_level glossary:
 *    0 = campaign, 1 = ad group, 2 = keyword
 */

class BudgetChanger {

  private $client;
  private $elementId;
  private $elementName;
  private $dataLevel;
  private $changeValue;
  private $alertText;
  private $failed;
  private $flag;
  private $today;

  public function __construct($new) {
    $this->elementId = floatval($new['element_id']);
    $this->elementName = $new['element_name'];
    $this->dataLevel = $new['data_level'];
    $this->changeValue = $new['budget_val'];
    $this->failed = [];
    $this->flag = false;
    $this->today = date("Y-m-d 00:00:00");
	
    $this->client = $this->getAmzClient($new['refresh_token'], $new['profile_id']);
  }

  private function getAmzClient($refreshToken, $profileId, $region = 'na') {
    $config = array(
      "clientId" => "amzn1.application-oa2-client.4246e0f086e441259742c758f63ca0bf",
      "clientSecret" => "9c9e07b214926479e14a0781051ecc3ad9b29686d3cef24e15eb130a47cabeb3",
      "refreshToken" => $refreshToken,
      "region" => $region,
      "sandbox" => false
    );

    $client = new Client($config);
    $client-> profileId = $profileId;

    return $client;
  }

  public function getSingleAlert() {
    $this->alertText = ($this->flag) ? $this->elementName . "'s budget has been successfully changed!" : "An error has occurred...";
    return $this->alertText;
  }

  public function getMultiAlert($success, $dataLevel) {
    if (count($this->failed == 0)) {
      $this->alertText = $success . " " . $datalevel . " successfully changed!";
    } else {
      $this->failed = implode("\n", $this->failed);
      $this->alertText = $success . " " . $datalevel . " successfully changed!" . "\nerror: \n" . $this->failed . " unsuccessfully changed";
    }
    return $this->alertText;
  }

  public function singleChange() {
    global $pdo;

    if($this->dataLevel == 0) {
      $result = json_decode($this->client->updateCampaigns(array(array(
        "campaignId" => $this->elementId,
        "dailyBudget" => $this->changeValue
      )))['response'], true)[0]['code'];

      if($result == "SUCCESS") {
        $this->flag = true;

        $sql = "UPDATE campaigns SET daily_budget=:value WHERE amz_campaign_id=:id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
          ":value" => $this->changeValue,
          ":id" => $this->elementId
        ));
        return 1;
      } else {
        $this->flag = false;
        array_push($this->failed, $this->elementName);
        return 0;
      }

    } elseif($this->dataLevel == 1) {
      $result = json_decode($this->client->updateAdGroups(array(array(
        "adGroupId" => $this->elementId,
        "defaultBid" => $this->changeValue
      )))['response'], true)[0]['code'];
      
      if($result == "SUCCESS") {
        $this->flag = true;

        $sql = "UPDATE ad_groups SET default_bid=:value WHERE amz_adgroup_id=:id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
          ":value" => $this->changeValue,
          ":id" => $this->elementId
        ));
        return 1;
      } else {
        $this->flag = false;
        array_push($this->failed, $this->elementName);
        return 0;
      }

    } else {
      $result = json_decode($this->client->updateBiddableKeywords(array(array(
        "keywordId" => $this->elementId,
        "bid" => $this->changeValue
      )))['response'], true)[0]['code'];

      if($result == "SUCCESS") {
        $this->flag = true;

        $sql = "UPDATE ppc_keyword_metrics SET bid=:value WHERE amz_kw_id=:id AND date=:date";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
          ":value" => $this->changeValue,
          ":id" => $this->elementId,
		  ":date" => $this->today
        ));
        return 1;
      } else {
        $this->flag = false;
        array_push($this->failed, $this->elementName);
        return 0;
      }
    }
  }
}
