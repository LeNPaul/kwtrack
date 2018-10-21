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
    return '<input type="checkbox" checked data-toggle="toggle" data-size="mini">';
  } elseif ($status == 'paused') {
    return '<input type="checkbox" data-toggle="toggle" data-size="mini">';
  } else {
    return '-';
  }
}

/*
 *  function cmGetCampaignData(PDO $pdo, Int $user_id) --> Array(Array $output, Array $campaigns)
 *    --> Gets campaign metrics from DB to output onto campaign manager.
 *
 *      --> PDO $pdo         - database handle
 *      --> Int $user_id     - user id of the user
 *      --> Array $output    - frontend data that user sees on Datatables
 *      --> Array $campaigns - server side associative array of "campaign name" => campaign ID
 *                             Use this to make AJAX request to pull ad group data
 */
function cmGetCampaignData($pdo, $user_id) {
  $output = [];
  $campaigns = [];
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

    $campaignLink = '<a href="javascript:void(0)" class="name c_link">' . $result[$i]['campaign_name'] . '</a>';

    $output[] = array(
	    cmCheckboxState($result[$i]['status']),
      $campaignLink,
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

    $campaigns[htmlspecialchars($result[$i]['campaign_name'])] = $result[$i]['amz_campaign_id'];
  }
  return [$output, $campaigns];
}

/*
 *  function cmGetAdGroupData($pdo, $campaignId) --> Array(Array $output, Array $adgroups)
 *    --> Get ad group metrics from DB to output to campaign manager
 *
 *      --> PDO $pdo        - database handle
 *      --> Int $campaignId - campaign ID to pull ad groups for
 *      --> Array $output   - frontend adgroup data to be shown
 *      --> Array $adgroups - backend adgroup data with ID's - "adgroup name" => adgroup ID
 */

function cmGetAdGroupData($pdo, $campaignId) {
  $output   = [];
  $adgroups = [];
  $sql = "SELECT * FROM ad_groups WHERE amz_campaign_id={$campaignId}";
  $stmt = $pdo->query($sql);
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  for ($i = 0; $i < count($result); $i++) {
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

    $adgroupLink = '<a href="javascript:void(0)" class="name ag_link">' . $result[$i]['ad_group_name'] . '</a>';

    $output[] = array(
	    cmCheckboxState($result[$i]['status']),
      $adgroupLink,
	    $result[$i]['status'],
      $result[$i]['default_bid'],
      $impressions,
      $clicks,
      $ctr,
      $ad_spend,
      $avg_cpc,
      $units_sold,
      $sales,
      $acos
    );

    $adgroups[htmlspecialchars($result[$i]['ad_group_name'])] = $result[$i]['amz_adgroup_id'];
  }
  return [$output, $adgroups];
}

/*
 *  function cmGetKeywordData($pdo, $adgroupId) --> Array $keywords ("keywordName" => keywordId)
 *    --> Get keyword metrics from DB to output to campaign manager
 *
 *      --> PDO $pdo        - database handle
 *      --> Int $adgroupId  - adgroup ID to pull keywords for
 *      --> Array $keywords - backend keyword data with ID's - "keyword name" => keyword ID
 */

function cmGetKeywordData($pdo, $adgroupId) {
  $output   = [];
  $keywords = [];
  $sql = "SELECT * FROM ppc_keywords WHERE amz_adgroup_id={$adgroupId}";
  $stmt = $pdo->query($sql);
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  for ($i = 0; $i < count($result); $i++) {
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
    $bid         = '$' . round($result[$i]['bid'], 2);
    $kwText      = '<b class="name">' . $result[$i]['keyword_text'] . "</b>";
    
    $output[] = array(
	    cmCheckboxState($result[$i]['status']),
      $kwText,
      $result[$i]['match_type'],
	    $result[$i]['status'],
      $bid,
      $impressions,
      $clicks,
      $ctr,
      $ad_spend,
      $avg_cpc,
      $units_sold,
      $sales,
      $acos
    );

    $keywords[htmlspecialchars($result[$i]['keyword_text'])] = $result[$i]['amz_kw_id'];
  }
  return [$output, $keywords];
}
