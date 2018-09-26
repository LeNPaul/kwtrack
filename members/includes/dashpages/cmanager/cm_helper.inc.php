<?php
/*
 *  CAMPAIGN MANAGER HELPER FILE
 *    - All helper functions used for the campaign manager
 */

function calculateMetricAvg($arr) {
  $arr = array_filter($arr);
  $average = (count($arr) == 0) ? 0 : array_sum($arr)/count($arr);
  return $average;
}

/*
 *  function cmCheckboxState(String $status) --> String $output
 *    --> Outputs a checkbox (toggler) on the campaign manager based on the
 *        campaign status.
 *
 *      --> String $status - string representing the status of the campaign
 */
function cmCheckboxState($status) {
  if ($status == 'enabled') {
    //return '<input type="checkbox" checked data-toggle="toggle">';
    return '<button type="button" class="btn btn-secondary btn-xs btn-toggle focus" data-toggle="button" aria-pressed="true" autocomplete="off">
              <div class="handle"></div>
            </button>';
  } elseif ($status == 'paused') {
    //return '<input type="checkbox" data-toggle="toggle">';
    return '<button type="button" class="btn btn-secondary btn-xs btn-toggle focus" data-toggle="button" aria-pressed="false" autocomplete="off">
              <div class="handle"></div>
            </button>';
  } else {
    return '-';
  }
}

/*
 *  function cmGetCampaignData(PDO $pdo, Int $user_id) --> Array $output
 *    --> Gets campaign metrics from DB to output onto campaign manager.
 *
 *      --> PDO $pdo     - database handle
 *      --> Int $user_id - user id of the user
 */
function cmGetCampaignData($pdo, $user_id) {
  $output = [];
  $sql = "SELECT * FROM campaigns WHERE user_id={$user_id}";
  $stmt = $pdo->query($sql);
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  for ($i = 0; $i < count($result); $i++) {

  	if ($result[$i]['status'] == 'enabled') {
  		$active = 0;
  	} else if ($result[$i]['status'] == 'paused') {
  		$active = 1;
  	} else {
  		$active = 2;
  	}

    $ad_spend    = array_sum(unserialize($result[$i]['ad_spend']));
    $sales       = array_sum(unserialize($result[$i]['sales']));
    $impressions = array_sum(unserialize($result[$i]['impressions']));
    $clicks      = array_sum(unserialize($result[$i]['clicks']));
    $ctr         = round(calculateMetricAvg(unserialize($result[$i]['ctr'])), 2);
    $avg_cpc     = round(calculateMetricAvg(unserialize($result[$i]['avg_cpc'])), 2);
    $units_sold  = array_sum(unserialize($result[$i]['units_sold']));

    // Replace any 0's with "-"
    $acos        = ($sales == 0) ? "-" : round(($ad_spend / $sales) * 100, 2) . '%';
    $ad_spend    = ($ad_spend == 0) ? '-' : '$' . round($ad_spend, 2);
    $sales       = ($sales == 0) ? '-' : '$' . round($sales, 2);
    $impressions = ($impressions == 0) ? '-' : $impressions;
    $clicks      = ($clicks == 0) ? '-' : $clicks;
    $ctr         = ($ctr == 0) ? '-' : $ctr . '%';
    $avg_cpc     = ($avg_cpc == 0) ? '-' : '$' . $avg_cpc;
    $units_sold  = ($units_sold == 0) ? '-' : $units_sold;


    $output[] = array(
	    cmCheckboxState($result[$i]['status']),
      $result[$i]['campaign_name'],
	    $result[$i]['status'],
      '$' . $result[$i]['daily_budget'],
      $result[$i]['targeting_type'],
      $impressions,
      $clicks,
      $ctr,
      $ad_spend,
      $avg_cpc,
      $units_sold,
      $sales,
      $acos
    );
  }
  return $output;
}
