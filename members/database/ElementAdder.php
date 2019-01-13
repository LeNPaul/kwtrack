<?php
namespace AmazonAdvertisingApi;
require_once dirname(__FILE__) . "/../includes/AmazonAdvertisingApi/Client.php";
require_once dirname(__FILE__) . "/pdo.inc.php";

use PDO;
use PDOException;

ini_set('precision',30);

/*
 * $data_level glossary:
 *  0 = campaigns, 1 = ad groups, 2 = keywords
 *
 *  $element_type can only be "campaign", "ad_group", "neg_keyword", or "keyword"
 */
class ElementAdder
{
  private $data_level;
  private $parent_elements; // Array
  private $child_element_type; // String determined based on $config["child_element_type"]
  private $kw_match_type; // Only if $element_type = "keyword" or "neg_keyword"
  private $child_elements; // Array that holds the name of all elements to be added

  private $client;

  public function __construct($config)
  {
    $this->data_level      = $config["data_level"];
    $this->parent_elements = $config["parent_elements"];
    $this->child_elements  = $config["child_elements"];
    $this->element_type    = $config["child_element_type"];

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

    if ($this->data_level == 0) {

    } else if ($this->data_level == 1) {

    } else if ($this->data_level == 2) {

    }

    // Go through list of parent element to add child elements to
    foreach ($this->parent_elements as $parent_element) {


    }
  }

  private function add_elements($api_call_type) {
    if ($api_call_type == "campaign") {
      foreach ($this->parent_elements as $campaign) {
        
      }
    } else if ($api_call_type == "ad_group") {

    } else if ($api_call_type == "keyword") {

    } else if ($api_call_type == "neg_keyword") {

    }
  }
}
?>
