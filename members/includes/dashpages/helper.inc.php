<?php
/*
 * Helper functions for campaign data manipulation
 */

 /*----------------------------------------------------------
  *
  *
      ██ ███    ███ ██████   ██████  ██████  ████████ ██ ███    ██  ██████
      ██ ████  ████ ██   ██ ██    ██ ██   ██    ██    ██ ████   ██ ██
      ██ ██ ████ ██ ██████  ██    ██ ██████     ██    ██ ██ ██  ██ ██   ███
      ██ ██  ██  ██ ██      ██    ██ ██   ██    ██    ██ ██  ██ ██ ██    ██
      ██ ██      ██ ██       ██████  ██   ██    ██    ██ ██   ████  ██████
  *
  *----------------------------------------------------------*/

 /*
  *  function adjustDayOffset(Array $metricArr, Int $numDays) --> Array $output
  *    --> Takes $metricArr and prepends 0's $numDays-1 times for each old keyword that we encounter.
  *
  *      --> Array $metricArr - Associative array of metric data
  *      --> Int $numDays     - number of days we've gone through in importKeywords()
  *
  */
  function adjustDayOffset($metricArr, $numDays) {
    foreach ($metricArr as $key => $value) {
      if (count($value) < $numDays) {
        // Append a 0 to $value
        $value[] = 0;
      }
    }

    return $metricArr;
  }

 /*
  *  function prepareDbArrays(Array $dataset) --> Array $dbVar
  *    --> Takes $dataset and prepares it for insertion in database. Index 0 = TODAY = MOST RECENT.
  *    --> Preparing process:
  *
  *       CONVERTS UNPREPARED ARRAY - [d1:[a1, b1, ..., z1], d2:[a2, b2, ..., z2], ..., d60:[a60, b60, ..., z60]]
  *                                           (where d = day, a-z = unique keyword data)
  *       TO PREPARED ARRAY - [ Ka:{KWIDa, [a1, a2, ..., a60]}, ..., Kz:{KWIDz, [z1, z2, ..., z60]} ]
  *                                           (where K = unique keyword, a-z = unique keyword data)
  *
  *       --> Array $dataset - unprepared array for specific metric
  *       --> Array $dbVar   - prepared array for specific metric
  *
  */
 function prepareDbArrays($dataset, $dbVar) {
   /*for ($i = 0; $i < 60; $i++) {
     $secondLoopLimit = count($dataset[$i]);
     for ($j = 0; $j < $secondLoopLimit; $j++) {
       $dbVar[$j][] = $dataset[$i][$j];
     }
   }*/

   // Get keyword ID's and insert them into first index of each inner array
   // representing 60 days of data for 1 keyword.

   /* All keyword ID's will be in the first index of $dataset
      So we can limit the first loop to count($dataset[0]) */
   for ($i = 0; $i < count($dataset[0]); $i++) {
     // Create array inside Array$dbVar.
     // First index = keyword ID, second index = Array(keyword data)
     $dbVar[] = array($dataset[0][$i][0], array());
   }

   // Get keyword data associated with the KWID and insert it into $dbVar
   for ($i = 0; $i < count($dataset); $i++) {

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
        ':amz_adgroup_id'   => $arrAdGroupIds[$i]
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
  * function insertKeywords(PDO $pdo, Array $dataset, String $metric) --> void
  *   --> Updates the database for all keywords in $dataset. $dataset is an associative
  *       array with the keyword ID as the key and its historical data (as an array) as the value.
  *
  *     --> PDO $pdo        - database handl
  *     --> Array $dataset  - Associative array with all keyword data
  *     --> String $metric  - metric to update in the database
  */
  function insertKeywords($pdo, $dataset, $metric) {
    $sql = "UPDATE ppc_keywords SET {$metric}=:value WHERE amz_kw_id=:kw_id";
    $stmt = $pdo->prepare($sql);
    foreach ($dataset as $key => $value) {
      $stmt->execute(array(
        ':value' => serialize($value),
        ':kw_id' => $key
      ));
    }
  }

 /*

 /*

      ██   ██ ███████ ██    ██ ██     ██  ██████  ██████  ██████  ███████
      ██  ██  ██       ██  ██  ██     ██ ██    ██ ██   ██ ██   ██ ██
      █████   █████     ████   ██  █  ██ ██    ██ ██████  ██   ██ ███████
      ██  ██  ██         ██    ██ ███ ██ ██    ██ ██   ██ ██   ██      ██
      ██   ██ ███████    ██     ███ ███   ██████  ██   ██ ██████  ███████

  *  function importKeywords(PDO $pdo, Obj $client, Int $user_id, Int $days) --> void
  *    --> Imports all keywords for the user into database. Imports all data from past 60 days.
  *        ONLY TO BE USED FOR NEW USERS.
  *
  *       --> PDO $pdo        - database handle
  *       --> Obj $client     - client object from Advertising API
  *       --> Int $user_id    - user id of the user
  *       --> int $days       - number of days to import for
  */

  function importKeywords($pdo, $client, $user_id, $days) {
    echo '======= START IMPORT ======== <br />';
    $impressions = [];
    $clicks = [];
    $ctr = [];
    $adSpend = [];
    $avgCpc = [];
    $unitsSold = [];
    $sales = [];

    // Keep count of days of data gone through. For each iteration of $i,
    // the max number of days of data will always equal $numDays
    $numDays = 1;

    for ($i = 1; $i < $days; $i++) {
      echo '---- STARTING day #' . $i . '<br />';
      // Each metric array will be storing campaign data like the following in a 2D array:
      //    METRIC ARRAY => [ARRAY1( * all data for metric for each keyword * ), ARRAY2(...), ..., ARRAY60(...)]
      //    METRIC ARRAY INDEX REPRESENTS 1 DAY OF DATA FOR THAT METRIC FOR ALL CAMPAIGNS

      /*$impressions[$i] = [];
      $clicks[$i] = [];
      $ctr[$i] = [];
      $adSpend[$i] = [];
      $avgCpc[$i] = [];
      $unitsSold[$i] = [];
      $sales[$i] = [];*/

      // Get date from $i days before today and format it as YYYYMMDD
      $date = date('Ymd', strtotime('-' . $i . ' days'));

      // Only on the very first iteration of this loop, we will iterate through the array
      // and store campaign name and campaign ID in the database
      if ($i === 1) {
        echo '------ INITIATING 1st iteration import<br />';

        $result = $client->requestReport(
          "keywords",
          array("reportDate"    => $date,
                "campaignType"  => "sponsoredProducts",
                "metrics"       => "adGroupId,campaignId,keywordId,keywordText,matchType,impressions,clicks,cost,campaignBudget,attributedUnitsOrdered1d,attributedSales1d"
          )
        );

        // Get the report id so we can use it to get the report
        $result   = json_decode($result['response'], true);
        $reportId = $result['reportId'];
        $status   = $result['status'];

        // Keep pinging the report until status !== IN_PROGRESS
        while ($status == 'IN_PROGRESS') {
        	$result = $client->getReport($reportId);
        	$result = json_decode($result['response'], true);
          $status = (array_key_exists('status', $result)) ? $result['status'] : false;
        }
        $result = $client->getReport($reportId);
        $result = json_decode($result['response'], true);

        // Save count of the number of keywords today (will always be max keywords)
        // so we can use it in the future
        $numMaxKeywords = count($result);

        // Insert keywords into database

        $sql = 'INSERT INTO ppc_keywords (user_id, status, bid, keyword_text, amz_campaign_id, amz_adgroup_id, amz_kw_id, match_type)
                VALUES (:user_id, :status, :bid, :keyword_text, :amz_campaign_id, :amz_adgroup_id, :amz_kw_id, :match_type)';
        $stmt = $pdo->prepare($sql);

        for ($x = 0; $x < 10/*$numMaxKeywords*/; $x++) {

          // Get status and bid for each keyword
          $kw_id = $result[$x]['keywordId'];
          $status = $client->getBiddableKeyword($kw_id);
          $status = json_decode($status['response'], true);

          // Check if bid index exists in the report
          // If it does, set bid to what it is
          // If not, then set it to the ad group's default bid
          if (array_key_exists('bid', $status)) {
            $bid = $status['bid'];
          } else {
            // Get default bid from adgroup
            $adgBid = $client->getAdGroup($status['adGroupId']);
            $adgBid = json_decode($adgBid['response'], true);
            $adgBid = $adgBid['defaultBid'];
          }

          $status = $status['state'];

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

        echo '------ COMPLETING 1st iteration import<br />';

      } else {

        // All other iterations, we request this report to optimize time
        $result = $client->requestReport(
          "keywords",
          array("reportDate"    => $date,
                "campaignType"  => "sponsoredProducts",
                "metrics"       => "adGroupId,campaignId,keywordId,keywordText,matchType,impressions,clicks,cost,campaignBudget,attributedUnitsOrdered1d,attributedSales1d"
          )
        );

        // Get the report id so we can use it to get the report
        $result   = json_decode($result['response'], true);
        $reportId = $result['reportId'];
        $status   = $result['status'];

        // Keep pinging the report until status !== IN_PROGRESS
        while ($status == 'IN_PROGRESS') {
        	$result = $client->getReport($reportId);
        	$result = json_decode($result['response'], true);
          $status = (array_key_exists('status', $result)) ? $result['status'] : false;
        }

        $result = $client->getReport($reportId);
        $result = json_decode($result['response'], true);
      }

      // Save count of keywords for $date (only starts for "yesterday")
      $numCurrentKeywords = count($result);

      // Loop to iterate through the report response
      for ($j = 0; $j < count($result); $j++) {

        // Get keyword ID
        $kw_id = $result[$j]['keywordId'];

        // Append day's data of the keyword to the
        // key's (keyword ID) value (array of metric data) in the metric array
        $impressions[$kw_id][] = $result[$j]['impressions'];
        $clicks[$kw_id][]      = $result[$j]['clicks'];
        $ctr[$kw_id][]         = ($result[$j]['impressions'] == 0) ? 0.0 : round(($result[$j]['clicks'] / $result[$j]['impressions']), 2);
        $avgCpc[$kw_id][]      = ($result[$j]['clicks'] == 0) ? 0.0 : round(($result[$j]['clicks'] / $result[$j]['impressions']), 2);
        $adSpend[$kw_id][]     = round($result[$j]['cost'], 2);
        $unitsSold[$kw_id][]   = $result[$j]['attributedUnitsOrdered1d'];
        $sales[$kw_id][]       = $result[$j]['attributedSales1d'];
        /*
        // Removed the 'archived/paused' check for keywords since their states/status
    		// are not provided in the reports. You can only get their CURRENT states and not
    		// their past states.

        $impressions[$i][] = array($result[$j]['keywordId'], $result[$j]['impressions']);
        $clicks[$i][] = array($result[$j]['keywordId'], $result[$j]['clicks']);

        // Check if impressions are 0. If impressions are 0, then we know that CTR will also be 0.
        if ($result[$j]['impressions'] == 0) {
          $ctr[$i][] = array($result[$j]['keywordId'], 0.0);
        } else {
          $ctr[$i][] = array($result[$j]['keywordId'], round(($result[$j]['clicks'] / $result[$j]['impressions']), 2));
        }

        // Check if clicks are 0. If clicks are 0, then we know that CPC will also be 0.
        if ($result[$j]['clicks'] == 0) {
          $avgCpc[$i][] = array($result[$j]['keywordId'], 0.0);
        } else {
          $avgCpc[$i][] = array($result[$j]['keywordId'], round(($result[$j]['cost'] / $result[$j]['clicks']), 2));
        }

        // Push ad spend, units sold, and $ sales for the day to our arrays.
        $adSpend[$i][] = array($result[$j]['keywordId'], round($result[$j]['cost'], 2));
        $unitsSold[$i][] = array($result[$j]['keywordId'], $result[$j]['attributedUnitsOrdered1d']);
        $sales[$i][] = array($result[$j]['keywordId'], $result[$j]['attributedSales1d']);

        // Get how many 0.0's we need to append to the end of the metric arrays if $numCurrentKeywords < $numMaxKeywords
        if ($numCurrentKeywords < $numMaxKeywords) {
          $count = $numMaxKeywords - $numMaxKeywords;
          while ($count != 0) {
            $impressions[$i][] = array($result[$j]['keywordId'], 0.0);
            $clicks[$i][]      = array($result[$j]['keywordId'], 0.0);
            $ctr[$i][]         = array($result[$j]['keywordId'], 0.0);
            $avgCpc[$i][]      = array($result[$j]['keywordId'], 0.0);
            $adSpend[$i][]     = array($result[$j]['keywordId'], 0.0);
            $unitsSold[$i][]   = array($result[$j]['keywordId'], 0.0);
            $sales[$i][]       = array($result[$j]['keywordId'], 0.0);
            $count--;
          }
        }
        */
      }

      /* Now we have to do a check if there are any less keywords as we progress
         through the dates. If there are, then we need to append 1 0 to each metric array */

      $impressions = adjustDayOffset($impressions, $numDays);
      $clicks      = adjustDayOffset($clicks, $numDays);
      $ctr         = adjustDayOffset($ctr, $numDays);
      $avgCpc      = adjustDayOffset($avgCpc, $numDays);
      $adSpend     = adjustDayOffset($adSpend, $numDays);
      $unitsSold   = adjustDayOffset($unitsSold, $numDays);
      $sales       = adjustDayOffset($sales, $numDays);

      $numDays++;
      echo '-------- FINISH day #'.$i.'<br />';
    }

    // Insert all this shit into the database
    insertKeywords($pdo, $impressions, 'impressions');
    insertKeywords($pdo, $clicks, 'clicks');
    insertKeywords($pdo, $ctr, 'ctr');
    insertKeywords($pdo, $adSpend, 'ad_spend');
    insertKeywords($pdo, $avgCpc, 'avg_cpc');
    insertKeywords($pdo, $unitsSold, 'units_sold');
    insertKeywords($pdo, $sales, 'sales');

    /*
    // Grab array of keywords by their keyword ID
    $sql = 'SELECT amz_kw_id FROM ppc_keywords WHERE user_id=' . htmlspecialchars($user_id);
    $stmt = $pdo->query($sql);
    $result = $stmt->fetchAll(PDO::FETCH_COLUMN);

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
    */
  }

  /*
       █████  ██████       ██████  ██████   ██████  ██    ██ ██████  ███████
      ██   ██ ██   ██     ██       ██   ██ ██    ██ ██    ██ ██   ██ ██
      ███████ ██   ██     ██   ███ ██████  ██    ██ ██    ██ ██████  ███████
      ██   ██ ██   ██     ██    ██ ██   ██ ██    ██ ██    ██ ██           ██
      ██   ██ ██████       ██████  ██   ██  ██████   ██████  ██      ███████

   *  function importAdGroupMetrics(PDO $pdo, String $adGroupId, Int $days) --> void
   *    --> Imports ad group or campaign metrics derived from their respective keywords.
   *
   *      --> PDO $pdo          - database handler
   *      --> String $adGroupId - id of the ad group or campaign
   *      --> Int $days         - number of days to import
   */

  function importAdGroupMetrics($pdo, $adGroupId, $days) {
    // Query the database for all keywords under the specific ad group and store in $result
    $sql = "SELECT impressions, clicks, ctr, ad_spend, avg_cpc, units_sold, sales
            FROM ppc_keywords WHERE amz_adgroup_id={$adGroupId}";
    $stmt = $pdo->query($sql);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // For each keyword:
    // 1) unserialize arrays
    // 2) pull metrics then sum to 1 value
    // 3) append the metrics to their respective db prepared array
    // 3) store db prepared array in db for that ad group

    // Initialize database-ready arrays
    $impressionsDb = array_fill(0, $days, 0.0);
    $clicksDb      = array_fill(0, $days, 0.0);
    $ctrDb         = array_fill(0, $days, 0.0);
    $ad_spendDb    = array_fill(0, $days, 0.0);
    $avg_cpcDb     = array_fill(0, $days, 0.0);
    $units_soldDb  = array_fill(0, $days, 0.0);
    $salesDb       = array_fill(0, $days, 0.0);

    for ($i = 0; $i < count($result); $i++) {
      // Unserialize the keyword's metric arrays
      $impressions = unserialize($result[$i]['impressions']);
      $clicks = unserialize($result[$i]['clicks']);
      $ad_spend = unserialize($result[$i]['ad_spend']);
      $avg_cpc = unserialize($result[$i]['avg_cpc']);
      $units_sold = unserialize($result[$i]['units_sold']);
      $sales = unserialize($result[$i]['sales']);

      for ($j = 0; $j < $days; $j++) {
        $impressionsDb[$j] += $impressions[$j];
        $clicksDb[$j]      += $clicks[$j];
        $ad_spendDb[$j]    += $ad_spend[$j];
        $avg_cpcDb[$j]     += $avg_cpc[$j];
        $units_soldDb[$j]  += $units_sold[$j];
        $salesDb[$j]       += $sales[$j];
      }

      // Create and calculate CTR array
      for ($x = 0; $x < $days; $x++) {
        // If impressionsDb[$x] = 0, then ctrDb[$x] = 0
        $ctrDb[$x] = ($impressionsDb[$x] == 0) ? 0.0 : ($clicksDb[$x] / $impressionsDb[$x]);
      }

      /*
      // Reduce all metric arrays to 1 value
      $impressions = round(array_reduce($impressions, function($carry, $element) { return $carry += $element; }), 2);
      $clicks = round(array_reduce($clicks, function($carry, $element) { return $carry += $element; }), 2);

      // Check if clicks = 0. If no clicks, then CTR = 0
      if ($clicks == 0) {
        $ctr = 0;
      } else {
        $ctr = round($impressions / $clicks, 2);
      }

      $ad_spend = round(array_reduce($ad_spend, function($carry, $element) { return $carry += $element; }), 2);

      // For average CPC, we need to filter the array to remove 0's
      // because 0's will skew the average calculation
      $avg_cpc = array_filter($avg_cpc, function($a) { return ($a != 0); });

      /* Now that 0's are removed, we need to find the average. But first, check
         if the newly filtered $avg_cpc array is empty. If it is empty, then ctr
         will be 0
      */
      /*
      $avg_cpc = (count($avg_cpc) == 0) ? 0 : round(array_sum($avg_cpc) / count($avg_cpc), 2);

      $units_sold = array_reduce($units_sold, function($carry, $element) { return $carry += $element; });
      $sales = round(array_reduce($sales, function($carry, $element) { return $carry += $element; }), 2);

      // Append all values to db prepared arrays
      $impressionsDb[] = $impressions;
      $clicksDb[]      = $clicks;
      $ctrDb[]         = $ctr;
      $ad_spendDb[]    = $ad_spend;
      $avg_cpcDb[]     = $avg_cpc;
      $units_soldDb[]  = $units_sold;
      */
    }

    // Get default bid
    $adgBid = $client->getAdGroup(104829275664043);
    $adgBid = json_decode($adgBid['response'], true);
    $adgBid = $adgBid['defaultBid'];

    // After db prepared arrays are full, insert into the db
    $sql = "UPDATE ad_groups SET default_bid=:adgBid WHERE amz_adgroup_id=:adGroupId";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
      ':adgBid'    => $adgBid,
      ':adGroupId' => $adGroupId
    ));

    $sql = "UPDATE ad_groups SET impressions=:impressionsDb WHERE amz_adgroup_id=:adGroupId";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
      ':impressionsDb'  => serialize($impressionsDb),
      ':adGroupId'      => $adGroupId
    ));

    $sql = "UPDATE ad_groups SET clicks=:clicksDb WHERE amz_adgroup_id=:adGroupId";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
      ':clicksDb'  => serialize($clicksDb),
      ':adGroupId' => $adGroupId
    ));

    $sql = "UPDATE ad_groups SET ctr=:ctrDb WHERE amz_adgroup_id=:adGroupId";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
      ':ctrDb'      => serialize($ctrDb),
      ':adGroupId'  => $adGroupId
    ));

    $sql = "UPDATE ad_groups SET ad_spend=:ad_spendDb WHERE amz_adgroup_id=:adGroupId";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
      ':ad_spendDb'  => serialize($ad_spendDb),
      ':adGroupId'   => $adGroupId
    ));

    $sql = "UPDATE ad_groups SET avg_cpc=:avg_cpcDb WHERE amz_adgroup_id=:adGroupId";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
      ':avg_cpcDb'  => serialize($avg_cpcDb),
      ':adGroupId'  => $adGroupId
    ));

    $sql = "UPDATE ad_groups SET units_sold=:units_soldDb WHERE amz_adgroup_id=:adGroupId";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
      ':units_soldDb'    => serialize($units_soldDb),
      ':adGroupId'       => $adGroupId
    ));

    $sql = "UPDATE ad_groups SET sales=:salesDb WHERE amz_adgroup_id=:adGroupId";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
      ':salesDb'    => serialize($salesDb),
      ':adGroupId'  => $adGroupId
    ));
  }

/*
     ██████  █████  ███    ███ ██████   █████  ██  ██████  ███    ██ ███████
    ██      ██   ██ ████  ████ ██   ██ ██   ██ ██ ██       ████   ██ ██
    ██      ███████ ██ ████ ██ ██████  ███████ ██ ██   ███ ██ ██  ██ ███████
    ██      ██   ██ ██  ██  ██ ██      ██   ██ ██ ██    ██ ██  ██ ██      ██
     ██████ ██   ██ ██      ██ ██      ██   ██ ██  ██████  ██   ████ ███████

 *  function importCampaignMetrics(PDO $pdo, Int $campaignId, Int $days) --> void
 *    --> Sums up metrics from ad groups and imports to campaigns.
 *
 *      --> PDO $pdo        - database handle
 *      --> Int $campaignId - campaign id
 *      --> Int $days       - number of days to import data for
 */
 function importCampaignMetrics($pdo, $campaignId, $days) {
   // Query the database for all keywords under the specific ad group and store in $result
   $sql = "SELECT impressions, clicks, ctr, ad_spend, avg_cpc, units_sold, sales
           FROM ad_groups WHERE amz_campaign_id={$campaignId}";
   $stmt = $pdo->query($sql);
   $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

   // For each ad group:
   // 1) unserialize arrays
   // 2) pull metrics then sum to 1 value
   // 3) append the metrics to their respective db prepared array
   // 3) store db prepared array in db for that ad group

   // Initialize database-ready arrays
   $impressionsDb = array_fill(0, $days, 0.0);
   $clicksDb      = array_fill(0, $days, 0.0);
   $ctrDb         = array_fill(0, $days, 0.0);
   $ad_spendDb    = array_fill(0, $days, 0.0);
   $avg_cpcDb     = array_fill(0, $days, 0.0);
   $units_soldDb  = array_fill(0, $days, 0.0);
   $salesDb       = array_fill(0, $days, 0.0);

   for ($i = 0; $i < count($result); $i++) {
     // Unserialize the keyword's metric arrays
     $impressions = unserialize($result[$i]['impressions']);
     $clicks = unserialize($result[$i]['clicks']);
     $ad_spend = unserialize($result[$i]['ad_spend']);
     $avg_cpc = unserialize($result[$i]['avg_cpc']);
     $units_sold = unserialize($result[$i]['units_sold']);
     $sales = unserialize($result[$i]['sales']);

     for ($j = 0; $j < $days; $j++) {
       $impressionsDb[$j] += $impressions[$j];
       $clicksDb[$j]      += $clicks[$j];
       $ad_spendDb[$j]    += $ad_spend[$j];
       $avg_cpcDb[$j]     += $avg_cpc[$j];
       $units_soldDb[$j]  += $units_sold[$j];
       $salesDb[$j]       += $sales[$j];
     }

     // Create and calculate CTR array
     for ($x = 0; $x < $days; $x++) {
       // If impressionsDb[$x] = 0, then ctrDb[$x] = 0
       $ctrDb[$x] = ($impressionsDb[$x] == 0) ? 0.0 : ($clicksDb[$x] / $impressionsDb[$x]);
     }
   }

   // After db prepared arrays are full, insert into the db
   $sql = "UPDATE campaigns SET impressions=:impressionsDb WHERE amz_campaign_id=:campaignId";
   $stmt = $pdo->prepare($sql);
   $stmt->execute(array(
     ':impressionsDb'  => serialize($impressionsDb),
     ':campaignId'      => $campaignId
   ));

   $sql = "UPDATE campaigns SET clicks=:clicksDb WHERE amz_campaign_id=:campaignId";
   $stmt = $pdo->prepare($sql);
   $stmt->execute(array(
     ':clicksDb'  => serialize($clicksDb),
     ':campaignId' => $campaignId
   ));

   $sql = "UPDATE campaigns SET ctr=:ctrDb WHERE amz_campaign_id=:campaignId";
   $stmt = $pdo->prepare($sql);
   $stmt->execute(array(
     ':ctrDb'      => serialize($ctrDb),
     ':campaignId'  => $campaignId
   ));

   $sql = "UPDATE campaigns SET ad_spend=:ad_spendDb WHERE amz_campaign_id=:campaignId";
   $stmt = $pdo->prepare($sql);
   $stmt->execute(array(
     ':ad_spendDb'  => serialize($ad_spendDb),
     ':campaignId'   => $campaignId
   ));

   $sql = "UPDATE campaigns SET avg_cpc=:avg_cpcDb WHERE amz_campaign_id=:campaignId";
   $stmt = $pdo->prepare($sql);
   $stmt->execute(array(
     ':avg_cpcDb'  => serialize($avg_cpcDb),
     ':campaignId'  => $campaignId
   ));

   $sql = "UPDATE campaigns SET units_sold=:units_soldDb WHERE amz_campaign_id=:campaignId";
   $stmt = $pdo->prepare($sql);
   $stmt->execute(array(
     ':units_soldDb'    => serialize($units_soldDb),
     ':campaignId'       => $campaignId
   ));

   $sql = "UPDATE campaigns SET sales=:salesDb WHERE amz_campaign_id=:campaignId";
   $stmt = $pdo->prepare($sql);
   $stmt->execute(array(
     ':salesDb'    => serialize($salesDb),
     ':campaignId'  => $campaignId
   ));
 }



 /*----------------------------------------------------------

    ██████   █████  ███████ ██   ██ ██████   ██████   █████  ██████  ██████
    ██   ██ ██   ██ ██      ██   ██ ██   ██ ██    ██ ██   ██ ██   ██ ██   ██
    ██   ██ ███████ ███████ ███████ ██████  ██    ██ ███████ ██████  ██   ██
    ██   ██ ██   ██      ██ ██   ██ ██   ██ ██    ██ ██   ██ ██   ██ ██   ██
    ██████  ██   ██ ███████ ██   ██ ██████   ██████  ██   ██ ██   ██ ██████

                                ██   ██ ███████ ██      ██████  ███████ ██████  ███████
                                ██   ██ ██      ██      ██   ██ ██      ██   ██ ██
                                ███████ █████   ██      ██████  █████   ██████  ███████
                                ██   ██ ██      ██      ██      ██      ██   ██      ██
                                ██   ██ ███████ ███████ ██      ███████ ██   ██ ███████

  *----------------------------------------------------------*/

/*
 *  function multiUnserialize(Array $arr) --> Array $output
 *    --> Takes array of serialized arrays and returns array of unserialized arrays
 *
 *      --> Array $arr - array of unserialized arrays
 */

function multiUnserialize($arr) {
  for ($i = 0; $i < count($arr); $i++) {
    $arr[$i] = unserialize($arr[$i]);
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

  // NOTE: Change 20's to 60's after testing is completed
  $output = array_fill(0, $numDays, 0);

  for ($j = 0; $j < count($metricArr); $j++) {
		for ($i = 0; $i < $numDays; $i++) {
			$output[$i] += $metricArr[$j][$i];
		}
  }

  /*
  for ($i = 0; $i < count($metricArr); $i++) {
    // If the output array has the required length (number of days x number of campaigns), then break the loop
    if (count($output) == ($numDays * count($metricArr))) { break; }
    $output[] = array_pop($metricArr[$i]);
  }

  if ($metric == 'adSpend' || $metric == 'ppcSales') {
    // If the metric being calculated is ad spend or PPC sales, then all we need to do is
    // get the sum of the array
    $output = array_reduce($output, function($carry, $element) { return $carry += $element; });
  }
  */

  return $output;
}

function getMetricData($pdo, $dbColName, $user_id) {
  $sql = "SELECT {$dbColName} FROM campaigns WHERE user_id={$user_id}";
  $stmt = $pdo->query($sql);
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  $arr = [];

  for ($i = 0; $i < count($result); $i++) {
    $arr[] = $result[$i][$dbColName];
  }

  return $arr;
}
?>
