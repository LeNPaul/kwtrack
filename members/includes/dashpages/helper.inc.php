<?php
/*
 * Helper functions for campaign data manipulation
 */

 /*----------------------------------------------------------
  *
  *     DATA IMPORT HELPER FUNCTIONS
  *
  *----------------------------------------------------------*/

 /*
  *  function prepareDbArrays(Array $dataset) --> Array $dbVar
  *    --> Takes $dataset and prepares it for insertion in database
  *
  *      --> Array $dataset - unprepared array for specific metric
  *      --> Array $dbVar   - prepared array for specific metric
  */
 function prepareDbArrays($dataset, $dbVar) {
   for ($i = 0; $i < 60; $i++) {
     /* TESTING PURPOSES ONLY. REMOVE BREAK WHEN READY FOR FINAL TESTING PHASE test*/
     // if ($i === 2) { break; }

     $secondLoopLimit = count($dataset[$i]);
     for ($j = 0; $j < $secondLoopLimit; $j++) {
       $dbVar[$j][] = array_shift($dataset[$i]);
     }
   }
   return $dbVar;
 }

 /*
  *  function storeCampaignArrays(PDO $pdo, Array $dbVar, Array $arrCampaignIds, String $dbColName) --> void
  *    --> Updates campaigns in the database for the metric array $dbVar under column $dbColName
  *
  *      --> PDO $pdo              - database handler
  *      --> Array $dbVar          - Prepared array for specific metric
  *      --> Array $arrCampaignIds - Array of campaign ID's for a specific user
  *      --> String $dbColName     - column name for the table you want to update
  */
 function storeCampaignArrays($pdo, $dbVar, $arrCampaignIds, $dbColName) {
   for ($i = 0; $i < count($arrCampaignIds); $i++) {
     $sql = "UPDATE campaigns SET {$dbColName}=:value WHERE amz_campaign_id=:amz_campaign_id";
     $stmt = $pdo->prepare($sql);
     $stmt->execute(array(
       ':value'            => serialize($dbVar[$i]),
       ':amz_campaign_id'  => $arrCampaignIds[$i]
     ));
   }
 }

 /*
  *  function storeAdGroupArrays(PDO $pdo, Array $dbVar, Array $arrAdGroupIds, String $dbColName) --> void
  *    --> Updates ad groups in the database for the metric array $dbVar under column $dbColName
  *
  *      --> PDO $pdo              - database handler
  *      --> Array $dbVar          - Prepared array for specific metric
  *      --> Array $arrAdGroupIds  - Array of ad group ID's for a specific user
  *      --> String $dbColName     - column name for the table you want to update
  */
 function storeAdGroupArrays($pdo, $dbVar, $arrAdGroupIds, $dbColName) {
    for ($i = 0; $i < count($arrAdGroupIds); $i++) {
      $sql = "UPDATE ad_groups SET {$dbColName}=:value WHERE amz_adgroup_id=:amz_adgroup_id";
      $stmt = $pdo->prepare($sql);
      $stmt->execute(array(
        ':value'            => serialize($dbVar[$i]),
        ':amz_adgroup_id'  => $arrAdGroupIds[$i]
      ));
    }
 }

 /*
  *  function storeKeywordArrays(PDO $pdo, Array $dbVar, Array $arrKeywordIds, String $dbColName) --> void
  *    --> Updates ad groups in the database for the metric array $dbVar under column $dbColName
  *
  *      --> PDO $pdo              - database handler
  *      --> Array $dbVar          - Prepared array for specific metric
  *      --> Array $arrKeywordIds  - Array of keyword ID's for a specific user
  *      --> String $dbColName     - column name for the table you want to update
  */
 function storeKeywordArrays($pdo, $dbVar, $arrKeywordIds, $dbColName) {
   for ($i = 0; $i < count($arrKeywordIds); $i++) {
     $sql = "UPDATE ppc_keywords SET {$dbColName}=:value WHERE amz_kw_id=:amz_kw_id";
     $stmt = $pdo->prepare($sql);
     $stmt->execute(array(
         ':value'            => serialize($dbVar[$i]),
         ':amz_kw_id'        => $arrKeywordIds[$i]
     ));
   }
 }

 /*
  *  function importKeywords(PDO $pdo, Obj $client, Int $user_id) --> void
  *    --> Imports all keywords for the user into database. Imports all data from past 60 days.
  *        ONLY TO BE USED FOR NEW USERS.
  */

  function importKeywords($pdo, $client, $user_id) {
    $impressions = [];
    $clicks = [];
    $ctr = [];
    $adSpend = [];
    $avgCpc = [];
    $unitsSold = [];
    $sales = [];

    for ($i = 0; $i < 60; $i++) {

      // Each metric array will be storing campaign data like the following in a 2D array:
      //    METRIC ARRAY => [ARRAY1( * all data for metric for each keyword * ), ARRAY2(...), ..., ARRAY60(...)]
      //    METRIC ARRAY INDEX REPRESENTS 1 DAY OF DATA FOR THAT METRIC FOR ALL CAMPAIGNS

      $impressions[$i] = [];
      $clicks[$i] = [];
      $ctr[$i] = [];
      $adSpend[$i] = [];
      $avgCpc[$i] = [];
      $unitsSold[$i] = [];
      $sales[$i] = [];

      // Get date from $i days before today and format it as YYYYMMDD
      $date = date('Ymd', strtotime('-' . $i . ' days'));

      // Only on the very first iteration of this loop, we will iterate through the array
      // and store campaign name and campaign ID in the database
      if ($i === 0) {
        // Request the report from API with campaign name, campaignId, and campaign budget only
        // for the first iteration
        $result = $client->requestReport(
          "keywords",
          array("reportDate"    => $date,
                "campaignType"  => "sponsoredProducts",
                "metrics"       => "adGroupId,campaignId,keywordId,keywordText,matchType,impressions,clicks,cost,campaignBudget,attributedUnitsOrdered1d,attributedSales1d"
          )
        );

        // Get the report id so we can use it to get the report
        $result = json_decode($result['response'], true);
        $reportId = $result['reportId'];

        sleep(40);

        // Get the report using the report id
        $result = $client->getReport($reportId);
        $result = json_decode($result['response'], true);

        // Insert keywords into database
        for ($x = 0; $x < count($result); $x++) {

          // Get status and bid for each keyword
          $kw_id = $result[$x]['keywordId'];
          $status = $client->getBiddableKeyword($kw_id);
          $status = json_decode($status['response'], true);
          echo '<pre>';
          var_dump($status);
          echo '</pre>';

          $bid = $status['bid'];
          $status = $status['state'];

          $sql = 'INSERT INTO ppc_keywords (user_id, status, bid, keyword_text, amz_campaign_id, amz_adgroup_id, amz_kw_id, match_type)
                  VALUES (:user_id, :status, :bid, :keyword_text, :amz_campaign_id, :amz_adgroup_id, :amz_kw_id, :match_type)';
          $stmt = $pdo->prepare($sql);
          $stmt->execute(array(
            ':user_id'          => $user_id,
            ':status'           => $status,
            ':bid'              => $bid,
            ':keyword_text'     => $result[$x]['keywordText'],
            ':amz_campaign_id'  => $result[$x]['campaignId'],
            ':amz_adgroup_id'   => $result[$x]['adGroupId'],
            ':amz_kw_id'        => $result[$x]['keywordId'],
            ':match_type'       => $result[$x]['matchType']
          ));
        }
      } else {
        // All other iterations, we request this report to optimize time
        $result = $client->requestReport(
          "keywords",
          array("reportDate"    => $date,
                "campaignType"  => "sponsoredProducts",
                "metrics"       => "impressions,clicks,cost,attributedUnitsOrdered1d,attributedSales1d"
          )
        );

        // Get the report id so we can use it to get the report
        $result = json_decode($result['response'], true);
        $reportId = $result['reportId'];

        sleep(10);

        // Get the report using the report id
        $result = $client->getReport($reportId);
        $result = json_decode($result['response'], true);
      }

      // Loop to iterate through the report response
      for ($j = 0; $j < count($result); $j++) {

        // Removed the 'archived/paused' check for keywords since their states/status
    		// are not provided in the reports. You can only get their CURRENT states and not
    		// their past states.

        $impressions[$i][] = $result[$j]['impressions'];
        $clicks[$i][] = $result[$j]['clicks'];

        // Check if impressions are 0. If impressions are 0, then we know that CTR will also be 0.
        if ($result[$j]['impressions'] == 0) {
          $ctr[$i][] = 0.0;
        } else {
          $ctr[$i][] = round(($result[$j]['clicks'] / $result[$j]['impressions']), 2);
        }

        // Check if clicks are 0. If clicks are 0, then we know that CPC will also be 0.
        if ($result[$j]['clicks'] == 0) {
          $avgCpc[$i][] = 0.0;
        } else {
          $avgCpc[$i][] = round(($result[$j]['cost'] / $result[$j]['clicks']), 2);
        }

        // Push ad spend, units sold, and $ sales for the day to our arrays.
        $adSpend[$i][] = round($result[$j]['cost'], 2);
        $unitsSold[$i][] = $result[$j]['attributedUnitsOrdered1d'];
        $sales[$i][] = $result[$j]['attributedSales1d'];

      }
    }

    // Grab array of keywords by their keyword ID
    $sql = 'SELECT amz_kw_id FROM ppc_keywords WHERE user_id=' . htmlspecialchars($user_id);
    $stmt = $pdo->query($sql);
    $result = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo '<pre>';
    var_dump($result);
    echo '</pre>';


    // Declare arrays that we will serialize and store in the database
    $dbImpressions = [];
    $dbClicks = [];
    $dbCtr = [];
    $dbAdSpend = [];
    $dbAvgCpc = [];
    $dbUnitsSold = [];
    $dbSales = [];


    // Grab impression data from array and store in their respective campaigns
    $dbImpressions = prepareDbArrays($impressions, $dbImpressions);
    storeKeywordArrays($pdo, $dbImpressions, $result, 'impressions');
    // Grab clicks data from array and store in their respective campaigns
    $dbClicks = prepareDbArrays($clicks, $dbClicks);
    storeKeywordArrays($pdo, $dbClicks, $result, 'clicks');
    // Grab ctr data from array and store in their respective campaigns
    $dbCtr = prepareDbArrays($ctr, $dbCtr);
    storeKeywordArrays($pdo, $dbCtr, $result, 'ctr');
    // Grab ad spend data from array and store in their respective campaigns
    $dbAdSpend = prepareDbArrays($adSpend, $dbAdSpend);
    storeKeywordArrays($pdo, $dbAdSpend, $result, 'ad_spend');
    // Grab average cpc data from array and store in their respective campaigns
    $dbAvgCpc = prepareDbArrays($avgCpc, $dbAvgCpc);
    storeKeywordArrays($pdo, $dbAvgCpc, $result, 'avg_cpc');
    // Grab units sold data from array and store in their respective campaigns
    $dbUnitsSold = prepareDbArrays($unitsSold, $dbUnitsSold);
    storeKeywordArrays($pdo, $dbUnitsSold, $result, 'units_sold');
    // Grab sales data from array and store in their respective campaigns
    $dbSales = prepareDbArrays($sales, $dbSales);
    storeKeywordArrays($pdo, $dbSales, $result, 'sales');
  }

  /*
   *  function importAdGroupOrCampaignMetrics(PDO $pdo, String $dbName, String $dbColName, String $id) --> void
   *    --> Imports ad group or campaign metrics derived from their respective keywords.
   *
   *      --> PDO $pdo          - database handler
   *      --> String $dbName    - name of the table to insert all metrics into
   *      --> String $dbColName - name of the column in the table
   *      --> String $id        - id of the ad group or campaign
   */
  function importAdGroupOrCampaignMetrics($pdo, $dbName, $dbColName, $id) {
    // Query the database for all keywords under the specific ad group and store in $result
    $sql = "SELECT impressions, clicks, ctr, ad_spend, avg_cpc, units_sold, sales
            FROM ppc_keywords WHERE {$dbColName}={$id}";
    $stmt = $pdo->query($sql);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // For each keyword:
    // 1) unserialize arrays
    // 2) pull metrics then sum to 1 value
    // 3) append the metrics to their respective db prepared array
    // 3) store db prepared array in db for that ad group

    // Initialize database-ready arrays
    $impressionsDb = [];
    $clicksDb      = [];
    $ctrDb         = [];
    $ad_spendDb    = [];
    $avg_cpcDb     = [];
    $units_soldDb  = [];
    $salsDb        = [];

    for ($i = 0; $i < count($result); $i++) {
      // Unserialize the keyword's metric arrays
      $impressions = unserialize($result[$i]['impressions']);
      $clicks = unserialize($result[$i]['clicks']);
      $ad_spend = unserialize($result[$i]['ad_spend']);
      $avg_cpc = unserialize($result[$i]['avg_cpc']);
      $units_sold = unserialize($result[$i]['units_sold']);
      $sales = unserialize($result[$i]['sales']);

      // Reduce all metric arrays to 1 value
      $impressions = round(array_reduce($impressions, function($carry, $element) { return $carry += $element; }), 2);
      $clicks = round(array_reduce($clicks, function($carry, $element) { return $carry += $element; }), 2);
      $ctr = round($impressions / $clicks, 2);
      $ad_spend = round(array_reduce($ad_spend, function($carry, $element) { return $carry += $element; }), 2);

      // For average CPC, we need to filter the array to remove 0's
      // because 0's will skew the average calculation
      $avg_cpc = array_filter($avg_cpc, function($a) { return ($a != 0); });
      // Now that 0's are removed, we need to find the average
      $avg_cpc = round(array_sum($avg_cpc) / count($avg_cpc), 2);

      $units_sold = array_reduce($units_sold, function($carry, $element) { return $carry += $element; });
      $sales = round(array_reduce($sales, function($carry, $element) { return $carry += $element; }), 2);

      // Append all values to db prepared arrays
      $impressionsDb[] = $impressions;
      $clicksDb[]      = $clicks;
      $ctrDb[]         = $ctr;
      $ad_spendDb[]    = $ad_spend;
      $avg_cpcDb[]     = $avg_cpc;
      $units_soldDb[]  = $units_sold;
    }

    // After db prepared arrays are full, insert into the db
    $sql = "UPDATE {$dbName} SET impressions=:impressionsDb WHERE {$dbColName}=:id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
      ':impressionsDb'  => serialize($impressionsDb),
      ':id'             => $id
    ));

    $sql = "UPDATE {$dbName} SET clicks=:clicksDb WHERE {$dbColName}=:id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
      ':clicksDb'  => serialize($clicksDb),
      ':id'             => $id
    ));

    $sql = "UPDATE {$dbName} SET ctr=:ctrDb WHERE {$dbColName}=:id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
      ':ctrDb'  => serialize($ctrDb),
      ':id'             => $id
    ));

    $sql = "UPDATE {$dbName} SET ad_spend=:ad_spendDb WHERE {$dbColName}=:id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
      ':ad_spendDb'  => serialize($ad_spendDb),
      ':id'             => $id
    ));

    $sql = "UPDATE {$dbName} SET avg_cpc=:avg_cpcDb WHERE {$dbColName}=:id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
      ':avg_cpcDb'  => serialize($avg_cpcDb),
      ':id'             => $id
    ));

    $sql = "UPDATE {$dbName} SET units_sold=:units_soldDb WHERE {$dbColName}=:id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
      ':units_soldDb'  => serialize($units_soldDb),
      ':id'             => $id
    ));
  }



 /*----------------------------------------------------------
  *
  *     DASHBOARD DISPLAY HELPER FUNCTIONS
  *
  *----------------------------------------------------------*/

/*
 *  function multiUnserialize(Array $arr) --> Array $output
 *    --> Takes array of serialized arrays and returns array of unserialized arrays
 *
 *      --> Array $arr - array of unserialized arrays
 */
function multiUnserialize($arr) {
  for ($i = 0; $i < count($arr); $i++) {
    unserialize($arr[$i]);
  }
  return $arr;
}

/*
 *  function calculateMetrics(Array $metricArr[Array, Array, ..., Array], Int $numDays, String $metric) --> Int $output
 *    --> Outputs a Bootstrap card that displays PPC metrics for a variable number of days
 *
 *      --> Array $metricArr    - Array of arrays pulled from the database.
 *                                - Length will be equal to # of campaigns for the user
 *      --> Int $numDays        - number of days to calculate data for
 *      --> Int $output         - summed up total for the metric
 *      --> String $metric      - String that represents which metric we are calculating
 */
function calculateMetrics($metricArr, $numDays, $metric) {
  // Algorithm will pop the end of each array $numDays times and append it to the output array
  // After appending to output array, we use array_reduce to calculate the metric needed

  $output = [];

  for ($i = 0; $i < count($metricArr); $i++) {
    // If the output array has the required length, then break the loop
    if (count($output) == $numDays) { break; }

    $output[] = array_pop($metricArr[$i]);
  }

  if ($metric == 'adSpend' || $metric == 'ppcSales') {
    // If the metric being calculated is ad spend or PPC sales, then all we need to do is
    // get the sum of the array
    $output = array_reduce($output, function($carry, $element) { return $carry += $element; });
  }

  return $output;
}
?>
