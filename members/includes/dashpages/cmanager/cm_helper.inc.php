<?php

require_once dirname(__FILE__) . '../../../../database/MetricsQueryBuilder.php';

/*
 *  CAMPAIGN MANAGER HELPER FILE
 *    - All helper functions used for the campaign manager
 */

function calculateMetricAvg($arr, $percent = false) {
  $arr = array_filter($arr);
  $average = (count($arr) == 0) ? 0 : array_sum($arr)/count($arr);
  return ($percent) ? $average : $average * 100;
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
    return '<input type="checkbox" class="toggle-campaign" checked data-toggle="toggle" data-size="mini" data-value="2" />';
  } elseif ($status == 'paused') {
    return '<input type="checkbox" class="toggle-campaign" data-toggle="toggle" data-size="mini" data-value="1" />';
  } else {
    return '<input type="checkbox" class="toggle-campaign-archive" data-toggle="toggle" data-size="mini" data-value="0" disabled />';
  }
}

/*
 *  function cmGetCampaignData(PDO $pdo, Int $user_id) --> Array(Array $output, Array $campaigns, Array $rawData)
 *    --> Gets campaign metrics from DB to output onto campaign manager.
 *
 *      --> PDO $pdo         - database handle
 *      --> Int $user_id     - user id of the user
 *      --> Array $output    - frontend data that user sees on Datatables
 *      --> Array $campaigns - server side associative array of "campaign name" => campaign ID
 *                             Use this to make AJAX request to pull ad group data
 *		--> Array $rawData	 - Unsummed campaign data to be used by date range picker for custom ranges
 */
function cmGetCampaignData($pdo, $user_id) {
  $output          = [];
  $campaigns       = [];
  $rawCampaignData = [];
  
  $builder = new MetricsQueryBuilder();
  $builder->userId = $user_id;
  $builder->includeCampaigns = true;
  $result = $builder->execute($pdo);
  
  
  for ($i = 0; $i < count($result); $i++) {

  	/*if ($result[$i]['status'] == 'enabled') {
  		$active = 0;
  	} else if ($result[$i]['status'] == 'paused') {
  		$active = 1;
  	} else {
  		$active = 2;
  	}*/
  	
    $ad_spend    = $result[$i]['ad_spend_formatted'];
    $sales       = $result[$i]['sales_formatted'];
    $impressions = $result[$i]['impressions_formatted'];
    $clicks      = $result[$i]['clicks_formatted'];
    $ctr         = $result[$i]['ctr_formatted'];
    $avg_cpc     = $result[$i]['avg_cpc_formatted'];
    $units_sold  = $result[$i]['units_sold_formatted'];
    $conversion  = $result[$i]['cvr_formatted'];
    $acos        = $result[$i]['acos_formatted'];
    
    $campaignLink = '<a href="javascript:void(0)" class="name c_link ' . $result[$i]['targeting_type'] . '" id="' . $result[$i]['amz_campaign_id'] . '">' . $result[$i]['campaign_name'] . '</a>';
    $budget =  '<div class="input-group cm-input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">$</span>
                  </div>

                  <input type="text" class="form-control edit-budget" data-color="danger" placeholder=" ' . $result[$i]['daily_budget'] . '" />

                  <div class="input-group-append">
                    <button class="btn btn-success btn-outline-secondary btn-edit-budget" type="button" style="display:none">Save</button>
                  </div>
                </div>';

    /*<div class="input-group-append">
                    <button class="btn btn-outline-secondary btn-edit-budget" type="button">Save</button>
                  </div>*/

    $rawCampaignData[] = array(
      cmCheckboxState($result[$i]['status']),
      $campaignLink,
      $result[$i]['status'],
      $result[$i]['budget'],
      $result[$i]['targeting_type'],
      $result
    );

    $output[] = array(
	    cmCheckboxState($result[$i]['status']),
      $campaignLink,
      $result[$i]['budget'],
      $result[$i]['targeting_type'],
      $impressions,
      $clicks,
      $ctr,
      $ad_spend,
      $avg_cpc,
      $units_sold,
      $sales,
	  $conversion,
      $acos
    );

    $campaigns[htmlspecialchars($result[$i]['campaign_name'])] = $result[$i]['amz_campaign_id'];
  }
  return [$output, $campaigns, $rawCampaignData];
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
  $output         = [];
  $adgroups       = [];
  $rawAdgroupData = [];
  $sql            = "SELECT * FROM ad_groups WHERE amz_campaign_id={$campaignId}";
  $stmt           = $pdo->query($sql);
  $result         = $stmt->fetchAll(PDO::FETCH_ASSOC);

  for ($i = 0; $i < count($result); $i++) {

    $adSpendArray     = unserialize($result[$i]['ad_spend']);
    $salesArray       = unserialize($result[$i]['sales']);
    $impressionsArray = unserialize($result[$i]['impressions']);
    $clicksArray      = unserialize($result[$i]['clicks']);
    $ctrArray         = unserialize($result[$i]['ctr']);
    $avgCpcArray      = unserialize($result[$i]['avg_cpc']);
    $unitsSoldArray   = unserialize($result[$i]['units_sold']);

    $ad_spend    = array_sum($adSpendArray);
    $sales       = array_sum($salesArray);
    $impressions = array_sum($impressionsArray);
    $clicks      = array_sum($clicksArray);
    $ctr         = round(calculateMetricAvg($ctrArray), 2);
    $avg_cpc     = ($clicks == 0) ? 0 : round($ad_spend / $clicks, 2);
    $units_sold  = array_sum($unitsSoldArray);

    // Replace any 0's with "-"
    $acos        = ($sales == 0) ? "-" : round(($ad_spend / $sales) * 100, 2) . '%';
    $ad_spend    = ($ad_spend == 0) ? '-' : '$' . round($ad_spend, 2);
    $sales       = ($sales == 0) ? '-' : '$' . round($sales, 2);
    $impressions = ($impressions == 0) ? '-' : $impressions;
    $clicks      = ($clicks == 0) ? '-' : $clicks;
    $ctr         = ($ctr == 0) ? '-' : $ctr . '%';
    $avg_cpc     = ($avg_cpc == 0) ? '-' : '$' . $avg_cpc;
    $units_sold  = ($units_sold == 0) ? '-' : $units_sold;
//<a href="javascript:void(0)" class="name c_link" id="' . $result[$i]['amz_campaign_id'] . '">' . $result[$i]['campaign_name'] . '</a>';
    $adgroupLink = '<a href="javascript:void(0)" class="name ag_link" id="' . $result[$i]['amz_adgroup_id'] . '">' . $result[$i]['ad_group_name'] . '</a>';
    $conversion  = ($clicks == 0) ? '-' : round(($units_sold / $clicks) * 100, 2) . '%';
    /*$default_bid = '<div class="input-group cm-input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">$</span>
                      </div>

                      <input type="text" class="form-control edit-budget" data-color="danger" placeholder=" ' . $result[$i]['default_bid'] . '" />

                      <div class="input-group-append">
                        <button class="btn btn-success btn-outline-secondary btn-edit-default-bid" type="button" style="display:none">Save</button>
                      </div>
                    </div>';*/
    
    $rawAdgroupData[] = array(
      cmCheckboxState($result[$i]['status']),
      $adgroupLink,
      $result[$i]['status'],
      $result[$i]['default_bid'],
      $impressionsArray,
      $clicksArray,
      $ctrArray,
      $adSpendArray,
      $avgCpcArray,
      $unitsSoldArray,
      $salesArray
    );

    $output[] = array(
        cmCheckboxState($result[$i]['status']),
      $adgroupLink,
      $result[$i]['default_bid'],
      $impressions,
      $clicks,
      $ctr,
      $ad_spend,
      $avg_cpc,
      $units_sold,
      $sales,
      $conversion,
      $acos
    );

    $adgroups[htmlspecialchars($result[$i]['ad_group_name'])] = $result[$i]['amz_adgroup_id'];
  }
  return [$output, $adgroups, $rawAdgroupData];
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
  $rawKwData = [];
  $sql = "SELECT * FROM ppc_keywords WHERE amz_adgroup_id={$adgroupId}";
  $stmt = $pdo->query($sql);
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  for ($i = 0; $i < count($result); $i++) {
    $adSpendArray     = unserialize($result[$i]['ad_spend']);
    $salesArray       = unserialize($result[$i]['sales']);
    $impressionsArray = unserialize($result[$i]['impressions']);
    $clicksArray      = unserialize($result[$i]['clicks']);
    $ctrArray         = unserialize($result[$i]['ctr']);
    $avgCpcArray      = unserialize($result[$i]['avg_cpc']);
    $unitsSoldArray   = unserialize($result[$i]['units_sold']);
    
    $ad_spend    = array_sum($adSpendArray);
    $sales       = array_sum($salesArray);
    $impressions = array_sum($impressionsArray);
    $clicks      = array_sum($clicksArray);
    $ctr         = round(calculateMetricAvg($ctrArray), 2);
    $avg_cpc     = ($clicks == 0) ? 0 : round($ad_spend / $clicks, 2);
    $units_sold  = array_sum($unitsSoldArray);

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
    $kwText      = '<b class="name" id="' . $result[$i]['amz_kw_id'] . '">' . $result[$i]['keyword_text'] . "</b>";
    $conversion  = ($clicks == 0) ? '-' : round(($units_sold / $clicks) * 100, 2) . '%';
    
    $rawKwData[] = array(
      cmCheckboxState($result[$i]['status']),
      $kwText,
      $result[$i]['status'],
      $result[$i]['match_type'],
      $bid,
      $impressionsArray,
      $clicksArray,
      $ctrArray,
      $adSpendArray,
      $avgCpcArray,
      $unitsSoldArray,
      $salesArray,
      $conversion,
      $acos
    );
    
    $output[] = array(
      cmCheckboxState($result[$i]['status']),
      $kwText,
      $result[$i]['match_type'],
      $bid,
      $impressions,
      $clicks,
      $ctr,
      $ad_spend,
      $avg_cpc,
      $units_sold,
      $sales,
      $conversion,
      $acos
    );

    $keywords[htmlspecialchars($result[$i]['keyword_text'])] = $result[$i]['amz_kw_id'];
  }
  return [$output, $keywords, $rawKwData];
}
