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
 *    "ads"          => array(
 *                        array(
 *                              "campaignId"  => campaign ID (Float)
 *                              "adGroupId"   => adgroup ID  (Float)
 *                              "sku"         => ASIN        (String (len == 10))
 *                              )
 *
 *    "keywords"     => array(
 *                        array(
 *                [REQUIRED]    "campaignId"   => campaign ID  (Float)
 *                [REQUIRED]    "adGroupId"    => adgroup ID   (Float)
 *                              "keywordText"  => keyword text (String)
 *                              "matchType"    => match type   (String "exact" | "phrase" | "broad")
 *                              "state"        => state        (String "enabled" | "paused" | "archived"))
 *                              ),
 *
 *    "neg_keywords" => array(
 *                        array(
 *                [OPTIONAL]    "campaignId"   => campaign ID  (Float)
 *                [OPTIONAL]    "adGroupId"    => adgroup ID   (Float)
 *                              "keywordText"  => keyword text (String)
 *                              "matchType"    => match type   (String "negativeExact" | "negativePhrase")
 *                              "state"        => state        (String "enabled" | "paused" | "archived"))
 *                              )
 *  )
 */

class ElementAdder
{
  private $element_type;   // String determined based on $config["element_type"]
  private $kw_match_type;  // Only if $element_type = "keyword" or "neg_keyword"
  private $child_elements; // Array that holds the name of all elements to be added
  private $user_id;
  
  private $kw_list_with_error     = [];
  private $neg_kw_list_with_error = [];

  private $direct_parent = array(
    "campaign_id" => null,
    "adgroup_id"  => null
  );

  private $client;

  public function __construct($config)
  {
    $this->data_level      = $config["data_level"];
    $this->child_elements  = $config["child_elements"];
    $this->element_type    = $config["element_type"];
    $this->user_id         = $config["user_id;"];

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
      "clientId"     => "amzn1.application-oa2-client.4246e0f086e441259742c758f63ca0bf",
      "clientSecret" => "9c9e07b214926479e14a0781051ecc3ad9b29686d3cef24e15eb130a47cabeb3",
      "refreshToken" => $refresh_token,
      "region"       => $region,
      "sandbox"      => false,
    );
    $client            = new Client($config);
    $client->profileId = $profile_id;

    return $client;
  }
  
  private function safe_add_keywords_to_db($insert_stmt, $result_msg, $direct_parent)
  {
    global $pdo;
    
    for($i = 0; $i < count($result_msg); $i++) {
      // Check if there were any errors for keywords being created
      if ($result_msg[$i]["code"] != "SUCCESS") {
        $this->kw_list_with_error[] = $this->child_elements["keywords"]["keywordText"];
        
        
        //throw new Exception("ER-CK01: There was an error adding your keywords. Please contact support@ppcology.io for help.");
      } else {
        $insert_stmt->execute(array(
          ":amz_adgroup_id" => $direct_parent["adgroup_id"],
          ":amz_kw_id"      => (float)$result_msg[$i]["keywordId"],
          ":keyword_text"   => $this->child_elements["keywords"][$i]["keywordText"],
          ":match_type"     => $this->child_elements["keywords"][$i]["matchType"],
          ":status"         => $this->child_elements["keywords"][$i]["state"]
        ));
      }
    }
  }
  
  private function safe_add_negkeywords_to_db($result_msg, $direct_parent, $level)
  {
    global $pdo;
    
    if ($level == 'campaign') {
  
      $insert_stmt = $pdo
        ->prepare("INSERT INTO campaign_neg_kw
              (kw_id, amz_campaign_id, keyword_text, state, match_type, user_id)
            VALUES
              (:kw_id, :amz_campaign_id, :keyword_text, :state, :match_type, :user_id)
            ");
      
      for($i = 0; $i < count($result_msg); $i++) {
        // Check if there were any errors for keywords being created
        if ($result_msg[$i]["code"] != "SUCCESS") {
          $this->neg_kw_list_with_error[] = $this->child_elements["keywords"]["keywordText"];
      
      
          //throw new Exception("ER-CK01: There was an error adding your keywords. Please contact support@ppcology.io for help.");
        } else {
          $insert_stmt->execute(array(
            ":kw_id"            => $result_msg[$i]["keywordId"],
            ":amz_campaign_id"  => $direct_parent["campaign_id"],
            ":keyword_text"     => $this->child_elements["neg_keywords"][$i]["keywordText"],
            ":state"            => $this->child_elements["neg_keywords"][$i]["state"],
            ":match_type"       => $this->child_elements["neg_keywords"][$i]["matchType"],
            ":user_id"          => $this->user_id
          ));
        }
      }
      
    } else {
  
      $insert_stmt = $pdo
        ->prepare("INSERT INTO adgroup_neg_kw
              (kw_id, amz_adgroup_id, keyword_text, state, match_type, user_id)
            VALUES
              (:kw_id, :amz_adgroup_id, :keyword_text, :state, :match_type, :user_id)
            ");
  
      for($i = 0; $i < count($result_msg); $i++) {
        // Check if there were any errors for keywords being created
        if ($result_msg[$i]["code"] != "SUCCESS") {
          $this->neg_kw_list_with_error[] = $this->child_elements["keywords"]["keywordText"];
      
      
          //throw new Exception("ER-CK01: There was an error adding your keywords. Please contact support@ppcology.io for help.");
        } else {
          $insert_stmt->execute(array(
            ":kw_id"            => $result_msg[$i]["keywordId"],
            ":amz_adgroup_id"   => $direct_parent["adgroup_id"],
            ":keyword_text"     => $this->child_elements["neg_keywords"][$i]["keywordText"],
            ":state"            => $this->child_elements["neg_keywords"][$i]["state"],
            ":match_type"       => $this->child_elements["neg_keywords"][$i]["matchType"],
            ":user_id"          => $this->user_id
          ));
        }
      }
      
    }
    
  }

  private function add_to_parent()
  {
    // Determine what kind of element the parent element is based on config input

    // Based on the parent element type, determine the correct API function to call
    // in order to add the child elements

    if ($this->element_type == "campaign") {

      $this->add_elements("campaign");
      $this->add_elements("ad_group", $this->direct_parent);
      $this->add_elements("ad", $this->direct_parent);
      $this->add_elements("keyword", $this->direct_parent);
      $this->add_elements("neg_keyword", $this->direct_parent);

    } else if ($this->element_type == "ad_group") {

      $this->add_elements("ad_group");
      $this->add_elements("ad", $this->direct_parent);
      $this->add_elements("keyword", $this->direct_parent);
      $this->add_elements("neg_keyword", $this->direct_parent);

    } else if ($this->element_type == "keyword") {

      $this->add_elements("keyword");

    }

    // Code won't reach here if an error was thrown
    // TODO: echo success message after everything is complete
  }

  private function add_elements($api_call_type, $direct_parent = null, $campaign_type = "sponsoredProducts") {
    global $pdo;
    
    if ($api_call_type == "campaign") {
      
      $insert_stmt = $pdo->prepare(
        "INSERT INTO campaigns
          (campaign_name, amz_campaign_id, user_id, campaign_type, targeting_type, daily_budget, status)
         VALUES (:campaign_name, :amz_campaign_id, :user_id, :campaign_type, :targeting_type, :daily_budget, :status)"
      );

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

          $result_msg = json_decode($result["response"], true, 512, JSON_BIGINT_AS_STRING);

          if ($result_msg[0]["code"] != "SUCCESS") {
            throw new Exception("ER-CC01: There was an error creating campaign "
            . $campaign["name"] . ". Please contact support@ppcology.io for help.");
          } else {
            // If success, insert into db
            
            $insert_stmt->execute(array(
              ":campaign_name"   => $campaign["name"],
              ":amz_campaign_id" => (float)$result_msg[0]["campaignId"],
              ":user_id"         => $this->user_id,
              ":campaign_type"   => $campaign_type,
              ":targeting_type"  => $campaign["targeting_type"],
              ":daily_budget"    => (float)$campaign["budget"],
              ":status"          => "enabled"
            ));
            
          }

          $this->direct_parent["campaign_id"]   = floatval($result[0]["campaignId"]);

        } catch (\Exception $e) {
          echo $e;
          return;
        }

      }

    }
    else if ($api_call_type == "ad_group") {
  
      $insert_stmt = $pdo->prepare(
        "INSERT INTO ad_groups
          (ad_group_name, amz_campaign_id, amz_adgroup_id, default_bid, status)
         VALUES (:ad_group_name, :amz_campaign_id, :amz_adgroup_id, :default_bid, :status)"
      );

        foreach ($this->child_elements["ad_groups"] as $ad_group) {
          try {
            
            $campaign_id = ($ad_group["campaign_id"] == null) ? $direct_parent["campaign_id"] : (float)$ad_group["campaign_id"];
            
            $result = $this->client->createAdGroups(array(
              array(
                "campaignId" => (float)$campaign_id,
                "name"       => $ad_group["name"],
                "state"      => $ad_group["state"],
                "defaultBid" => (float)$ad_group["default_bid"])
              ));

            $result_msg = json_decode($result["response"], true, 512, JSON_BIGINT_AS_STRING);

            if ($result_msg[0]["code"] != "SUCCESS") {
              throw new Exception("ER-CA01: There was an error creating ad group "
              . $ad_group["name"] . ". Please contact support@ppcology.io for help.");
            } else {
              $insert_stmt->execute(array(
                ":ad_group_name"    => $ad_group["name"],
                ":amz_campaign_id"  => (float)$campaign_id,
                ":amz_adgroup_id"   => (float)$result_msg[0]["adGroupId"],
                ":default_bid"      => (float)$ad_group["default_bid"],
                ":status"           => $ad_group["state"]
              ));
            }
            
            $this->direct_parent["campaign_id"]  = $result_msg["campaignId"];
            $this->direct_parent["adgroup_id"]   = $result_msg["adGroupId"];

          } catch (\Exception $e) {
            echo $e;
            return;
          }
        }

    }
    else if ($api_call_type == "ad") {
      // Can only be called if a campaign or ad group is being created
      // By the time those are created, the $direct_parent variable
      // will already have been populated with the appropriate parent element ID's

      try {
        // Populate the ad array campaign and ad group ID's
        foreach ($this->child_elements["ads"] as &$ad) {
          $ad["campaignId"] = (float)$direct_parent["campaign_id"];
          $ad["adGroupId"]  = (float)$direct_parent["adgroup_id"];
        }

        $result = $this
          ->client
          ->createProductAds($this->child_elements["ads"]);

        $result_msg = json_decode($result["response"], true, 512, JSON_BIGINT_AS_STRING)[0]["code"];

        // Check for any errors on Amazon's end
        if ($result_msg != "SUCCESS") {
          throw new Exception("ER-CAD01: There was an error creating an ad for your ASIN. Please contact support@ppcology.io for help.");
        }
      } catch (\Exception $e) {
        echo $e;
        return;
      }

    }
    else if ($api_call_type == "keyword") {
      /*
       * The only time a keyword can be created is on the ad group level.
       * This means that whenever a keyword is being created by the user,
       * the adgroup ID and campaign ID should be passed in thru the AJAX
       * call already inside the keyword's data array.
       *
       * However, if the user is creating a new ad group OR campaign, then the
       * campaign ID and ad group ID should be filled in $this->direct_parent array.
       */
  
      $insert_stmt = $pdo->prepare(
        "INSERT INTO ppc_keywords
          (amz_adgroup_id, amz_kw_id, keyword_text, match_type, status)
         VALUES (:amz_adgroup_id, :amz_kw_id, :keyword_text, :match_type, :status)"
      );

      try {

        // If user is creating a campaign or ad group
        if ($direct_parent) {
          // $this->child_elements["keywords"] will not have campaign ID's or adgroup ID's
          // so we need to fill these in
          foreach ($this->child_elements["keywords"] as &$keyword_array) {
            $keyword_array["campaignId"] = (float)$direct_parent["campaign_id"];
            $keyword_array["adGroupId"]  = (float)$direct_parent["adgroup_id"];
          }

          $result = $this
            ->client
            ->createBiddableKeywords($this->child_elements["keywords"]);

          $result_msg = json_decode($result["response"], true, 512, JSON_BIGINT_AS_STRING);
  
          $this->safe_add_keywords_to_db($insert_stmt, $result_msg, $direct_parent);

        } else {
          // If user is creating keywords in an existing ad group and campaign
          $result     = $this->client->createBiddableKeywords($this->child_elements["keywords"]);
          $result_msg = json_decode($result["response"], true)[0]["code"];

          // Check if there were any errors for keywords being created
          $this->safe_add_keywords_to_db($insert_stmt, $result_msg, $direct_parent);
          
        }

      } catch (\Exception $e) {
        echo $e;
        return;
      }

    }
    else if ($api_call_type == "neg_keyword") {

      try {
        if ($direct_parent["adgroup_id"] != null) {
          
          // If there's no adgroup ID, then create campaign level neg keywords
          foreach ($this->child_elements["neg_keywords"] as &$neg_keyword) {
            $neg_keyword["campaignId"] = $direct_parent["campaign_id"];
          }

          $result = $this
            ->client
            ->createCampaignNegativeKeywords($this->child_elements["neg_keywords"]);

          $result_msg = json_decode($result["response"], true, 512, JSON_BIGINT_AS_STRING);
  
          $this->safe_add_negkeywords_to_db($result_msg, $direct_parent, 'campaign');

          /*if ($result_msg != "SUCCESS") {
            throw new Exception("ER-CNKW01: There was an error adding your negative keywords. Please contact support@ppcology.io for help.");
          }*/

        } else if ($direct_parent["campaign_id"] != null && $direct_parent["adgroup_id"] != null) {
          // If there's no campaign ID, then create adgroup level neg keywords
          foreach ($this->child_elements["neg_keywords"] as &$neg_keyword) {
            $neg_keyword["campaignId"] = $direct_parent["campaign_id"];
            $neg_keyword["adGroupId"]  = $direct_parent["adGroupId"];
          }

          $result = $this
            ->client
            ->createNegativeKeywords($this->child_elements["neg_keywords"]);

          $result_msg = json_decode($result["response"], true, 512, JSON_BIGINT_AS_STRING);

          if ($result_msg[0]["code"] != "SUCCESS") {
            throw new Exception("ER-CNKW02: There was an error adding your negative keywords. Please contact support@ppcology.io for help.");
          }
        }

      } catch (\Exception $e) {
        echo $e;
        return;
      }


    }
  }
  
}
?>
