<?php
namespace AmazonAdvertisingApi;

include dirname(__FILE__) . '/pdo.inc.php';
include dirname(__FILE__) . '/../includes/AmazonAdvertisingApi/Client.php';

use PDO;

/*

██   ██ ███████ ██      ██████  ███████ ██████  ███████
██   ██ ██      ██      ██   ██ ██      ██   ██ ██
███████ █████   ██      ██████  █████   ██████  ███████
██   ██ ██      ██      ██      ██      ██   ██      ██
██   ██ ███████ ███████ ██      ███████ ██   ██ ███████

*/

/*
 *  function getReport(Obj $client, Int $reportId) --> Array $report
 *    --> Gets report from Advertising API with $reportId.
 *
 *      --> Obj $client   - Advertising API client object
 *      --> Int $reportId - Report ID of the report we are retrieving
 *      --> Array $report - Completed keywords report generated from Amazon
 */

  function getReport($client, $reportId) {
    do {
      $report = $client->getReport($reportId);
      $result2 = json_decode($report['response'], true);
      if (array_key_exists('status', $result2)) {
        $status = $result2['status'];
      } else {
        $status = 'DONE';
        $report = $result2;
      }
    } while ($status == 'IN_PROGRESS');
    return $report;
  }

/*
 * function fast_array_diff(Array $b, Array $a) --> Array $d
 *   --> Returns the difference of elements in $a found in $b.
 */

function fast_array_diff($b, $a) {
  $at = array_flip($a);
  $d = array();
  foreach ($b as $i)
    if (!isset($at[$i]))
      $d[] = $i;
  return $d;
}

/*
 *  function array_search2D(Array $array, Mixed $key, Mixed $value) --> Array
 *    --> Returns index where $key => $value pair is found in a 2D array formatted like:
 *          [ i => [key1 => value1, key2 => value2, keyN => valueN] ]
 */

function array_search2D($array, $key, $value) {
  return (array_search($value, array_column($array, $key)));
}

/*
 * function cron_diffUpdateKeywords(PDO $pdo, Obj $client, Array $arrKWReport, Array $arrKWIDs) => void
 *   --> Finds the keyword ID's in $arrKWID's inside the keyword report ($arrKWReport) and updates
 *       those keywords.
 *
 *    --> PDO $pdo           - database handle
 *    --> Obj $client        - Advertising API client object
 *    --> Array $arrKWReport - keyword report generated from Amazon
 *    --> Array $arrKWIDs    - 1D array of the keyword ID's from the database
 */

function cron_diffUpdateKeywords($pdo, $client, $arrKWReport, $arrKWIDs) {
  $sql   = "UPDATE ppc_keywords
                SET status=:status,
                impressions=:impressions,
                bid=:bid,
                clicks=:clicks,
                ctr=:ctr,
                ad_spend=:ad_spend,
                avg_cpc=:avg_cpc,
                units_sold=:units_sold,
                sales=:sales
                WHERE kw_id=:kw_id";
  $stmt  = $pdo->prepare($sql);

  for ($b = 0; $b < count($arrKWIDs); $b++) {
    $kw_id = $arrKWIDs[$b];
    $index = array_search2D($arrKWReport, 'keywordId', $kw_id);

    $kw = $client->getBiddableKeyword($kw_id);
    $kw = json_decode($kw['response'], true);

    $status      = $kw['state'];
    $bid         = $kw['bid'];
    $impressions = $arrKWReport[$index]['impressions'];
    $clicks      = $arrKWReport[$index]['clicks'];
    $ctr         = ($impressions == 0) ? 0.0 : round($clicks / $impressions, 2);
    $ad_spend    = $arrKWReport[$index]['cost'];
    $avg_cpc     = ($clicks == 0) ? 0.0 : round($ad_spend / $clicks, 2);
    $units_sold  = $arrKWReport[$index]['attributedUnitsOrdered1d'];
    $sales       = $arrKWReport[$index]['attributedSales1d'];

    $sql2     = "SELECT * FROM ppc_keywords WHERE amz_kw_id={$kw_id}";
    $stmt2    = $pdo->query($sql2);
    $kwDbInfo = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    $impressionsDb = unserialize($kwDbInfo[0]['impressions']);
    $clicksDb      = unserialize($kwDbInfo[0]['clicks']);
    $ctrDb         = unserialize($kwDbInfo[0]['ctr']);
    $ad_spendDb    = unserialize($kwDbInfo[0]['ad_spend']);
    $avg_cpcDb     = unserialize($kwDbInfo[0]['avg_cpc']);
    $units_soldDb  = unserialize($kwDbInfo[0]['units_sold']);
    $salesDb       = unserialize($kwDbInfo[0]['sales']);

    array_unshift($impressionsDb, $impressions);
    array_unshift($clicksDb, $clicks);
    array_unshift($ctrDb, $ctr);
    array_unshift($ad_spendDb, $ad_spend);
    array_unshift($avg_cpcDb, $avg_cpc);
    array_unshift($units_soldDb, $units_sold);
    array_unshift($salesDb, $sales);
    array_pop($impressionsDb);
    array_pop($clicksDb);
    array_pop($ctrDb);
    array_pop($ad_spendDb);
    array_pop($avg_cpcDb);
    array_pop($units_soldDb);
    array_pop($salesDb);

    $stmt->execute(array(
      ":status"      => $status,
      ":impressions" => serialize($impressionsDb),
      ":bid"         => $bid,
      ":clicks"      => serialize($clicksDb),
      ":ctr"         => serialize($ctrDb),
      ":ad_spend"    => serialize($ad_spendDb),
      ":avg_cpc"     => serialize($avg_cpcDb),
      ":units_sold"  => serialize($units_soldDb),
      ":sales"       => serialize($salesDb)
    ));
  }
}

/*
 * function cron_diffUpdateKeywords(PDO $pdo, Obj $client, Array $arrKWReport) => void
 *   --> Updates all keywords inside of $arrKWReport
 *
 *    --> PDO $pdo           - database handle
 *    --> Obj $client        - Advertising API client object
 *    --> Array $arrKWReport - keyword report generated from Amazon
 */

function cron_updateKeywords($pdo, $client, $arrKWReport) {
  $sql   = "UPDATE ppc_keywords
                SET status=:status,
                impressions=:impressions,
                bid=:bid,
                clicks=:clicks,
                ctr=:ctr,
                ad_spend=:ad_spend,
                avg_cpc=:avg_cpc,
                units_sold=:units_sold,
                sales=:sales
                WHERE kw_id=:kw_id";
  $stmt  = $pdo->prepare($sql);

  for ($i = 0; $i < count($arrKWReport); $i++) {
    $kw_id = $arrKWReport[$i]['keywordId'];

    $kw = $client->getBiddableKeyword($kw_id);
    $kw = json_decode($kw['response'], true);

    $status      = $kw['state'];
    $bid         = $kw['bid'];
    $impressions = $arrKWReport[$i]['impressions'];
    $clicks      = $arrKWReport[$i]['clicks'];
    $ctr         = ($impressions == 0) ? 0.0 : round($clicks / $impressions, 2);
    $ad_spend    = $arrKWReport[$i]['cost'];
    $avg_cpc     = ($clicks == 0) ? 0.0 : round($ad_spend / $clicks, 2);
    $units_sold  = $arrKWReport[$i]['attributedUnitsOrdered1d'];
    $sales       = $arrKWReport[$i]['attributedSales1d'];

    $sql2     = "SELECT * FROM ppc_keywords WHERE amz_kw_id={$kw_id}";
    $stmt2    = $pdo->query($sql2);
    $kwDbInfo = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    $impressionsDb = unserialize($kwDbInfo[0]['impressions']);
    $clicksDb      = unserialize($kwDbInfo[0]['clicks']);
    $ctrDb         = unserialize($kwDbInfo[0]['ctr']);
    $ad_spendDb    = unserialize($kwDbInfo[0]['ad_spend']);
    $avg_cpcDb     = unserialize($kwDbInfo[0]['avg_cpc']);
    $units_soldDb  = unserialize($kwDbInfo[0]['units_sold']);
    $salesDb       = unserialize($kwDbInfo[0]['sales']);

    /*
    First check if length = 59. If yes, that means that $this
    update is the FIRST update. Only need to check 1 array since
    all of them will be the same length anyways.
     */

    if (count($impressionsDb) == 59) {
      array_unshift($impressionsDb, $impressions);
      array_unshift($clicksDb, $clicks);
      array_unshift($ctrDb, $ctr);
      array_unshift($ad_spendDb, $ad_spend);
      array_unshift($avg_cpcDb, $avg_cpc);
      array_unshift($units_soldDb, $units_sold);
      array_unshift($salesDb, $sales);
    } else {
      array_unshift($impressionsDb, $impressions);
      array_unshift($clicksDb, $clicks);
      array_unshift($ctrDb, $ctr);
      array_unshift($ad_spendDb, $ad_spend);
      array_unshift($avg_cpcDb, $avg_cpc);
      array_unshift($units_soldDb, $units_sold);
      array_unshift($salesDb, $sales);
      array_pop($impressionsDb);
      array_pop($clicksDb);
      array_pop($ctrDb);
      array_pop($ad_spendDb);
      array_pop($avg_cpcDb);
      array_pop($units_soldDb);
      array_pop($salesDb);
    }

    $stmt->execute(array(
      ":status"      => $status,
      ":impressions" => serialize($impressionsDb),
      ":bid"         => $bid,
      ":clicks"      => serialize($clicksDb),
      ":ctr"         => serialize($ctrDb),
      ":ad_spend"    => serialize($ad_spendDb),
      ":avg_cpc"     => serialize($avg_cpcDb),
      ":units_sold"  => serialize($units_soldDb),
      ":sales"       => serialize($salesDb),
      ":kw_id"       => $kw_id
    ));
  }
}
/*

██   ██ ███████ ██    ██ ██     ██  ██████  ██████  ██████  ███████
██  ██  ██       ██  ██  ██     ██ ██    ██ ██   ██ ██   ██ ██
█████   █████     ████   ██  █  ██ ██    ██ ██████  ██   ██ ███████
██  ██  ██         ██    ██ ███ ██ ██    ██ ██   ██ ██   ██      ██
██   ██ ███████    ██     ███ ███   ██████  ██   ██ ██████  ███████

*/

// query all userIDs from ppc_keywords where active = 5 and store in array (userIDs)
$userIDs   = [];

$sql     = "SELECT user_id, profileId, refresh_token FROM users WHERE active=5";
$stmt    = $pdo->query($sql);
$userIDs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// for each userID, request report for yesterday's keywords and from report, store all keyword IDs (reportKeywordIDs)

for ($i = 0; $i < count($userIDs); $i++) {
  $user_id = $userIDs[$i]['user_id'];
  // Instantiate client for advertising API
  $config = array(
  	"clientId" => "amzn1.application-oa2-client.4246e0f086e441259742c758f63ca0bf",
  	"clientSecret" => "9c9e07b214926479e14a0781051ecc3ad9b29686d3cef24e15eb130a47cabeb3",
  	"refreshToken" => $userIDs[$i]['refresh_token'],
  	"region" => "na",
  	"sandbox" => false,
  );
  $client = new Client($config);
  $client->profileId = $userIDs[$i]['profileId'];
  $date = date('Ymd', strtotime('-1 days'));
  $result = $client->requestReport(
    "keywords",
    array("reportDate"    => $date,
          "campaignType"  => "sponsoredProducts",
          "metrics"       => "adGroupId,campaignId,keywordId,keywordText,matchType,impressions,clicks,cost,campaignBudget,attributedUnitsOrdered1d,attributedSales1d"
    )
  );

  // Get the report id so we can use it to get the report
  $result2         = json_decode($result['response'], true);
  $reportId        = $result2['reportId'];
  $status          = $result2['status'];

  // Keep pinging the report until status !== IN_PROGRESS
  $result = getReport($client, $reportId);

  // Store all keyword IDs from the report in reportKeywordIDs
  $reportKeywordIDs = [];
  for ($j = 0; $j < count($result); $j++) {
    $reportKeywordIDs[] = $result[$j]['keywordId'];
  }

  // Fetch and store amz_kw_id for the current user in array (dbKeywordIDs)
  $sql          = "SELECT amz_kw_id FROM ppc_keywords WHERE user_id={$user_id}";
  $stmt         = $pdo->query($sql);
  $dbKeywordIDs = $stmt->fetchAll(PDO::FETCH_COLUMN);

  // First check if length of reportKeywordIDs > length of dbKeywordIDs.
  // If yes, new keywords were added and we need to insert them in the DB
  if (count($reportKeywordIDs) > count($dbKeywordIDs)) {
    $diff = fast_array_diff($reportKeywordIDs, $dbKeywordIDs);

    // foreach diff, find index of extra keywords in the report.
    for ($a = 0; $a < count($diff); $a++) {
      $kw_id = $diff[$a];
      $index = array_search2D($result, 'keywordId', $kw_id);
       if ($index) {
         // Prepare metric arrays to be inserted into db for keyword
         $impressions = array_fill(0, 59, 0);
         $clicks      = array_fill(0, 59, 0);
         $ctr         = array_fill(0, 59, 0);
         $ad_spend    = array_fill(0, 59, 0);
         $avg_cpc     = array_fill(0, 59, 0);
         $units_sold  = array_fill(0, 59, 0);
         $sales       = array_fill(0, 59, 0);

         array_unshift($impressions, $result[$index]['impressions']);
         array_unshift($clicks, $result[$index]['clicks']);
         array_unshift($ctr, ($result[$index]['clicks'] == 0) ? 0.0 : ($result[$index]['clicks'] / $result[$index]['impressions']));
         array_unshift($ad_spend, $result[$index]['cost']);
         array_unshift($avg_cpc, ($result[$index]['clicks'] == 0) ? 0.0 : ($result[$index]['cost'] / $result[$index]['clicks']));
         array_unshift($units_sold, $result[$index]['attributedUnitsOrdered1d']);
         array_unshift($sales, $result[$index]['attributedSales1d']);

         // Get bid and status of the current keyword
         $kw = $client->getBiddableKeyword($kw_id);
         $kw = json_decode($kw['response'], true);

         // Serialize and store all new keywords in db
         $sql = "INSERT INTO ppc_keywords (user_id, amz_campaign_id, amz_adgroup_id, amz_kw_id, keyword_text, match_type, status, impressions, bid, clicks, ctr, ad_spend, avg_cpc, units_sold, sales)
         VALUES (:user_id, :amz_campaign_id, :amz_adgroup_id, :amz_kw_id, :keyword_text, :match_type, :status, :impressions, :bid, :clicks, :ctr, :ad_spend, :avg_cpc, :units_sold, :sales)";
         $stmt = $pdo->prepare($sql);
         $stmt->execute(array(
           ":user_id"         => $user_id,
           ":amz_campaign_id" => $result[$index]['campaignId'],
           ":amz_adgroup_id"  => $result[$index]['adGroupId'],
           ":amz_kw_id"       => $kw_id,
           ":keyword_text"    => $result[$index]['keywordText'],
           ":match_type"      => $result[$index]['matchType'],
           ":status"          => $kw['state'],
           ":impressions"     => serialize($impressions),
           ":bid"             => $kw['bid'],
           ":clicks"          => serialize($clicks),
           ":ctr"             => serialize($ctr),
           ":ad_spend"        => serialize($ad_spend),
           ":avg_cpc"         => serialize($avg_cpc),
           ":units_sold"      => serialize($units_sold),
           ":sales"           => serialize($sales)
         ));

       } else {
         echo 'An error has occurred.';
         die;
       }
    }

    // After taking care of all new keywords in the diff array, we need to update the rest of the
    // keywords that are already in the db. Remainder keyword ID's will be stored in $diffRemainderOfKW
    $diffRemainderOfKW = fast_array_diff($reportKeywordIDs, $diff);

    if (!empty($diffRemainderOfKW)) {
      cron_diffUpdateKeywords($pdo, $client, $result, $diffRemainderOfKW);
    } else {
      echo "An error has occured: diffRemainderOfKW is empty.";
    }
  } else {
    // If length of reportKeywordIDs == length of dbKeywordIDs, then no new keywords have been added
    // Continue to update all keywords
    cron_updateKeywords($pdo, $client, $result);
  }
}

//
//    _|_|    _|_|_|      _|_|_|  _|_|_|      _|_|    _|    _|  _|_|_|      _|_|_|
//  _|    _|  _|    _|  _|        _|    _|  _|    _|  _|    _|  _|    _|  _|
//  _|_|_|_|  _|    _|  _|  _|_|  _|_|_|    _|    _|  _|    _|  _|_|_|      _|_|
//  _|    _|  _|    _|  _|    _|  _|    _|  _|    _|  _|    _|  _|              _|
//  _|    _|  _|_|_|      _|_|_|  _|    _|    _|_|      _|_|    _|        _|_|_|
//
//

// reuse userIDs array from keyword update for adgroups
// for each userID, request report for yesterday's adGroups and from report, store all adGroup IDs (reportAdGroupID)

for ($i = 0; $i < count($userIDs); $i++){
	$user_id = $userIDs[$i]['user_id'];
  // Instantiate client for advertising API
  $config = array(
  	"clientId" => "amzn1.application-oa2-client.4246e0f086e441259742c758f63ca0bf",
  	"clientSecret" => "9c9e07b214926479e14a0781051ecc3ad9b29686d3cef24e15eb130a47cabeb3",
  	"refreshToken" => $userIDs[$i]['refresh_token'],
  	"region" => "na",
  	"sandbox" => false,
  );
  $client = new Client($config);
  $client->profileId = $userIDs[$i]['profileId'];
  $date = date('Ymd', strtotime('-1 days'));
  $result = $client->requestReport(
    "adGroups",
    array("reportDate"    => $date,
          "campaignType"  => "sponsoredProducts",
          "metrics"       => "adGroupId,adGroupName,defaultBid,state"
    )
  );

  $sql     = "SELECT amz_adgroup_id,status,daily_budget,ad_group_name FROM ad_groups WHERE user_id={$user_id}";
  $stmt    = $pdo->query($sql);
  $dbAdGroup = $stmt->fetchAll(PDO::FETCH_ASSOC);
  $dbAdGroupID = [];

  // Get the report id so we can use it to get the report
  $result2         = json_decode($result['response'], true);
  $reportId        = $result2['reportId'];
  $status          = $result2['status'];

  // Keep pinging the report until status !== IN_PROGRESS
  $result = getReport($client, $reportId);

  $reportAdGroupID = [];

  for ($y = 0; $y < count($result); $y++) {
	  $reportAdGroupID[] = $result[$y]['adGroupId'];
  }

  for ($x = 0; $x < count($dbAdGroup); $x++) {
	  $dbAdGroupID[] = $dbAdGroup[$x]['amz_adgroup_id'];
  }

  if (count($reportAdGroupID) > count($dbAdGroupID)) {
	  $arrayDiff = fast_array_diff($reportAdGroupID, $dbAdGroupID);

    if (!empty($arrayDiff)) {
      $sql = "INSERT INTO ad_groups (amz_campaign_id,amz_adgroup_id,ad_group_name,default_bid,status) VALUES (:amz_campaign_id,:amz_adgroup_id,:ad_group_name,:default_bid,:status)";
      $stmt = $pdo->prepare($sql);

      for ($b = 0; $b < count($arrayDiff); $b++) {
        $ag_id = $arrayDiff[$b];
        $index = array_search2D($result, 'adGroupId', $ag_id);

        if ($index){
          $daddyFernandyayy = $client->getAdGroup($ag_id);
          $daddyFernandyayy = json_decode($daddyFernandyayy, true);

          $stmt->execute(array(
            ":amz_campaign_id" => $daddyFernandyayy['campaignId'],
            ":amz_adgroup_id"  => $daddyFernandyayy['adGroupId'],
            ":ad_group_name"   => $daddyFernandyayy['adGroupName'],
            ":default_bid"     => $daddyFernandyayy['defaultBid'],
            ":status"          => $daddyFernandyayy['state']
          ));
        } else {
          echo "an error has occurred";
        }
      }
    } else {
      for($daddy = 0; $daddy < count($reportAdGroupID); $daddy++) {
        importAdGroupMetrics($pdo, $reportAdGroupID[$daddy], 60, $client);
      }
    }
  }
}

/*

 ██████  █████  ███    ███ ██████   █████  ██  ██████  ███    ██ ███████
██      ██   ██ ████  ████ ██   ██ ██   ██ ██ ██       ████   ██ ██
██      ███████ ██ ████ ██ ██████  ███████ ██ ██   ███ ██ ██  ██ ███████
██      ██   ██ ██  ██  ██ ██      ██   ██ ██ ██    ██ ██  ██ ██      ██
 ██████ ██   ██ ██      ██ ██      ██   ██ ██  ██████  ██   ████ ███████

*/

for ($i = 0; $i < count($userIDs); $i++){
	$user_id = $userIDs[$i]['user_id'];

	// Instantiate client for advertising API
  $config = array(
  	"clientId" => "amzn1.application-oa2-client.4246e0f086e441259742c758f63ca0bf",
  	"clientSecret" => "9c9e07b214926479e14a0781051ecc3ad9b29686d3cef24e15eb130a47cabeb3",
  	"refreshToken" => $userIDs[$i]['refresh_token'],
  	"region" => "na",
  	"sandbox" => false,
  );
  $client = new Client($config);
  $client->profileId = $userIDs[$i]['profileId'];

  //for each userID, request report for yesterday's campaigns and from report, store all campaign IDs (reportCampaignID)
  $daddysCampaigns = $client->listCampaigns();
  $daddysCampaigns = json_decode($daddysCampaigns, true);

  $reportCampaignIDs = [];

  for ($d = 0; $d < count($daddysCampaigns); $d++) {
	  $reportCampaignIDs[] = $daddysCampaigns[$d]['campaignId'];
  }

  //create dbCampaignID array from dbCampaign
  $sql = "SELECT amz_campaign_id,status,daily_budget,campaign_name FROM campaigns WHERE user_id={$user_id}";
  $stmt = $pdo->query($sql);
  $dbCampaign = $stmt->fetchAll(PDO::FETCH_ASSOC);
  $dbCampaignIDs = [];

  for ($e = 0; $e < count($dbCampaign); $e++) {
	  $dbCampaignIDs[] = $dbCampaign[$e]['amz_campaign_id'];
  }

  //first check if length of reportCampaignID > dbCampaignID
  if (count($reportCampaignIDs) > count($dbCampaignIDs)) {
	  $campaignDiff = fast_array_diff($reportCampaignIDs, $dbCampaignIDs);

    if (!empty($campaignDiff)) {
      $sql = "INSERT INTO campaigns (campaign_name,amz_campaign_id,campaign_type,targeting_type,daily_budget,status) VALUES (:campaign_name,:amz_campaign_id,:campaign_type,:targeting_type,:daily_budget,:status";
      $stmt = $pdo->prepare($sql);

      //for each diff, find index of extra campaigns in the report. report[diff]
      for ($f = 0; $f < count($campaignDiff); $f++) {
        $camp_id = $campaignDiff[$f];
        $index = array_search2D($daddysCampaigns, 'campaignId', $camp_id);

        if ($index) {
          $daddyFernand = $client->getCampaign($camp_id);
          $daddyFernand = json_decode($daddyFernand, true);

          //insert campaign data into db with serialized metric arrays and append to dbCampaignID (IDs only)
          $stmt->execute(array(
            ":campaign_name"   => $daddyFernand['name'],
            ":amz_campaign_id" => $daddyFernand['campaignId'],
            ":campaign_type"   => "sponsoredProducts",
            ":targeting_type"  => $daddyFernand['targetingType'],
            ":daily_budget"    => $daddyFernand['dailyBudget'],
            ":status"          => $daddyFernand['state']
          ));
        } else {
          echo "an error has occurred";
        }
      }
    } else {
      for ($dad = 0; $dad < count($reportCampaignIDs); $dad++) {
        importCampaignMetrics($pdo, $reportCampaignIDs[$dad], 60);
      }
    }
  }
}
?>
