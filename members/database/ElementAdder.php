<?php
namespace AmazonAdvertisingApi;
require_once dirname(__FILE__) . "/../includes/AmazonAdvertisingApi/Client.php";
require_once dirname(__FILE__) . "/pdo.inc.php";

use PDO;
use PDOException;

ini_set('precision', 30);

/*
 * $data_level glossary:
 *  0 = campaigns, 1 = ad groups, 2 = keywords
 *
 *  $element_type can only be "campaign", "ad_group", "neg_keyword", or "keyword"
 *
 *  $child_elements accepts an associative array in the following format:
 *
 *  array(
 *    "campaigns"    => array(
 *                        array("campaign_name"  => name   (String)
 *                              "targeting_type" => type   (String "manual" | "auto")
 *                              "budget"         => budget (Float > 1))
 *                              ),
 *
 *    "ad_groups"    => array(
 *                        array(
 *                [OPTIONAL]    "campaign_id" => campaign ID (Float)
 *                              "name"        => name        (String)
 *                              "state"       => state       (String "enabled" | "paused" | "archived")
 *                              "default_bid" => default bid (Float > 0.02))
 *                              ),
 *
 *    "keywords"     => array(
 *                        array(
 *                [OPTIONAL]    "ad_group_id"    => adgroup ID   (Float)
 *                              "keyword_text"   => keyword text (String)
 *                              "match_type"     => match type   (String "exact" | "phrase" | "broad")
 *                              "state"          => state        (String "enabled" | "paused" | "archived"))
 *                              ),
 *
 *    "neg_keywords" => array(
 *                        array(
 *                [OPTIONAL]    "campaign_id"    => campaign ID  (Float)
 *                [OPTIONAL]    "ad_group_id"    => adgroup ID   (Float)
 *                              "keyword_text"   => keyword text (String)
 *                              "match_type"     => match type   (String "negativeExact" | "negativePhrase")
 *                              "state"          => state        (String "enabled" | "paused" | "archived"))
 *                              )
 *  )
 */

class ElementAdder
{
  private $parent_elements; // Array
  private $child_element_type; // String determined based on $config["child_element_type"]
  private $kw_match_type; // Only if $element_type = "keyword" or "neg_keyword"
  private $child_elements; // Array that holds the name of all elements to be added

  private $direct_parent = array("id" => null, "type" => null);

  private $client;

  public function __construct($config)
  {
    $this->data_level      = $config["data_level"];
    $this->parent_elements = $config["parent_elements"];
    $this->child_elements  = $config["child_elements"];
    $this->element_type    = $config["element_type"];

    $this->kw_match_type =
    ($this->element_type == "keyword" || $this->element_type == "neg_keyword")
    ? $config["kw_match_type"]
    : null;

    // Instantiate the client
    $this->client = get_amz_client($config["refresh_token"], $config["profile_id"]);
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

  private function add_to_parent()
  {
    // Determine what kind of element the parent element is based on config input

    // Based on the parent element type, determine the correct API function to call
    // in order to add the child elements

    if ($this->element_type == "campaign") {

      $this->add_elements("campaign");
      $this->add_elements("ad_group", $this->direct_parent["id"]);
      $this->add_elements("keyword", $this->direct_parent["id"]);

    } else if ($this->element_type == "ad_group") {

      $this->add_elements("ad_group");
      $this->add_elements("keyword", $this->direct_parent["id"]);

    } else if ($this->element_type == "keyword") {

      $this->add_elements("keyword");

    }

    // Go through list of parent element to add child elements to
    foreach ($this->parent_elements as $parent_element) {


    }
  }

  private function add_elements($api_call_type, $direct_parent_id = null, $campaign_type = "sponsoredProducts") {

    if ($api_call_type == "campaign") {

      foreach ($this->child_elements["campaign"] as $campaign) {

        try {
          $result = $this->client->createCampaigns(array(
            array(
              "name"          => $campaign["name"],
              "campaignType"  => $campaign_type,
              "targetingType" => $campaign["targeting_type"],
              "state"         => "enabled",
              "dailyBudget"   => $campaign["budget"],
              "startDate"     => date("Ymd")
            )
          ));

          $result_msg = json_decode($result["response"], true);

          if ($result_msg[0]["code"] != "SUCCESS") {
            throw new Exception("ER-CC01: There was an error creating campaign "
            . $campaign["name"] . ". Please contact support@ppcology.io for help.");
          }

          $this->direct_parent["id"]   = floatval($result[0]["campaignId"]);
          $this->direct_parent["type"] = "campaign";

        } catch (Exception $e) {
          echo $e;
        }

      }

    } else if ($api_call_type == "ad_group") {

        foreach ($this->child_elements["ad_groups"] as $ad_group) {

          try {
            $result = $this->client->createAdGroups(array(
              array(
                "campaignId" => ($ad_group["campaign_id"] == null) ? $direct_parent_id : $ad_group["campaign_id"],
                "name"       => $ad_group["name"],
                "state"      => $ad_group["state"],
                "defaultBid" => $ad_group["default_bid"])
              ));

            $result_msg = json_decode($result["response"], true);

            if ($result_msg["code"] != "SUCCESS") {
              throw new Exception("ER-CA01: There was an error creating ad group "
              . $ad_group["name"] . ". Please contact support@ppcology.io for help.");
            }

            $this->direct_parent["id"]   = $result_msg["keywordId"];
            $this->direct_parent["type"] = "ad_group";

          } catch (Exception $e) {
            echo $e;
          }
        }

    } else if ($api_call_type == "keyword") {

      if ($direct_parent_id) {
        $result = $this->client->createBiddableKeywords(array(

        ));
      }

    } else if ($api_call_type == "neg_keyword") {

      if ($this->direct_parent["type"] == "campaign") {

      } else if ($this->direct_parent["type"] == "ad_group") {

      }

    }
  }
}
?>
