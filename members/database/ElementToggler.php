<?php
namespace AmazonAdvertisingApi;
require_once dirname(__FILE__) . "/../includes/AmazonAdvertisingApi/Client.php";
require_once dirname(__FILE__) . "/pdo.inc.php";

use PDO;
use PDOException;

ini_set('precision',30);

/*
 * $data_level glossary:
 *    0 = campaign, 1 = ad group, 2 = keyword
 *
 * if $config["toggle"] = true, then enable element
 * if $config["toggle"] = false, then pause element
 * if $config["toggle"] = archive then archive element
 */

class ElementToggler
{
  private $client;
  
  private $element_id;
  private $element_name;
  private $data_level;
  private $state;
  
  private $alert_text;
  
  public function __construct($config)
  {
    $this->element_id   = floatval($config["element_id"]);
    $this->element_name = $config["element_name"];
    $this->data_level   = $config["data_level"];
    $this->state        = ($config["toggle"] == "true") ? "enabled" : (($config["toggle"] == "false") ? "paused" : "archived");
    
    $this->client = $this->get_amz_client($config["refresh_token"], $config["profile_id"]);
  }
  
  private function get_amz_client($refresh_token, $profile_id, $region = "na")
  {
    $config = array(
      "clientId" => "amzn1.application-oa2-client.4246e0f086e441259742c758f63ca0bf",
      "clientSecret" => "9c9e07b214926479e14a0781051ecc3ad9b29686d3cef24e15eb130a47cabeb3",
      "refreshToken" => $refresh_token,
      "region" => $region,
      "sandbox" => false,
    );
    $client = new Client($config);
    $client->profileId = $profile_id;
    
    return $client;
  }
  
  public function get_alert_text() {
    return $this->alert_text;
  }
  
  public function single_toggle()
  {
    global $pdo;
  
    $flag  = false;
  
    // If data level is on campaign level
    if ($this->data_level == 0) {
      $result = json_decode($this->client->updateCampaigns(array(array(
        "campaignId" => $this->element_id,
        "state"      => $this->state
      )))['response'], true)[0]["code"];
    
      if ($result == "SUCCESS") {
        $flag = true;
      
        $sql = "UPDATE campaigns SET status=:state WHERE amz_campaign_id=:id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
          ":state"  => $this->state,
          ":id"     => $this->element_id
        ));
      } else {
        $flag = false;
      }
    
    }
    // If data level is on adgroup level
    elseif ($this->data_level == 1) {
      $result = json_decode($this->client->updateAdGroups(array(array(
        "adGroupId" => $this->element_id,
        "state"     => $this->state
      )))['response'], true)[0]["code"];
    
      if ($result == "SUCCESS") {
        $flag = true;
      
        $sql = "UPDATE ad_groups SET status=:state WHERE amz_adgroup_id=:id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
          ":state"  => $this->state,
          ":id"     => $this->element_id
        ));
      } else {
        $flag = false;
      }
    
    }
    // If data level is on keyword level
    elseif ($this->data_level == 2) {
      $result = json_decode($this->client->updateBiddableKeywords(array(array(
        "keywordId" => $this->element_id,
        "state"     => $this->state
      )))['response'], true)[0]["code"];
      
      // TODO: Automatic campaign targeting clauses CANNOT be paused
      
      if ($result == "SUCCESS") {
        $flag = true;
      
        $sql = "UPDATE ppc_keywords SET status=:state WHERE amz_kw_id=:id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
          ":state"  => $this->state,
          ":id"     => $this->element_id
        ));
      } else {
        $flag = false;
      }
    
    }
  
    $this->alert_text = ($flag) ? $this->element_name . " has successfully been " . $this->state : "An error has occurred...";
  }
  
}