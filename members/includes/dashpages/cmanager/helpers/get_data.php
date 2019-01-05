<?php

session_start();

require_once dirname(__FILE__) . '../../../../../database/MetricsQueryBuilder.php';
require_once dirname(__FILE__) . '../../../../../database/pdo.inc.php';

global $pdo;

$user_id = $_SESSION['user_id'];
$refresh_token = $_SESSION['refresh_token'];


$builder = new MetricsQueryBuilder();
$builder->userId = $user_id;
$builder->includeCampaigns = true;
$builder->startDate = $_GET['start'];
$builder->endDate = $_GET['end'];

$dataLevel = 'campaign';
// Should we also filter by campaign id?
if (isset($_GET['campaignId'])) {
  $builder->campaignIds = [$_GET['campaignId']];
  $builder->includeAdGroups = true;
  $dataLevel = 'ad_group';
}
// Should we filter on ad group?
else if (isset($_GET['adGroupId'])) {
  $builder->adGroupIds = [$_GET['adGroupId']];
  $builder->includeKeywords = true;
  $dataLevel = 'keyword';
}

$result = $builder->execute($pdo);

header('Content-type: application/json');

function cmCheckboxState($status, $toggleClass) {
  if ($status == 'enabled') {
    return '<input type="checkbox" class="toggle-'. $toggleClass . '" checked data-toggle="toggle" data-size="mini" data-value="2" />';
  } elseif ($status == 'paused') {
    return '<input type="checkbox" class="toggle-'. $toggleClass . '" data-toggle="toggle" data-size="mini" data-value="1" />';
  } else {
    return '<input type="checkbox" class="toggle-' . $toggleClass . '-archive" data-toggle="toggle" data-size="mini" data-value="0" disabled />';
  }
}

function cmLink($className, $id, $name){
  return '<a href="javascript:void(0)" class="name '. $className. '" id="' . $id . '">' .  $name . '</a>';
}

function moneyInput($className, $value){
  return '<div class="input-group cm-input-group">
      <div class="input-group-prepend">
        <span class="input-group-text">$</span>
      </div>

      <input type="text" class="form-control edit-'. $className . '" data-color="danger" placeholder=" ' . $value . '" />

      <div class="input-group-append">
        <button class="btn btn-success btn-outline-secondary btn-edit-'. $className .'" type="button" style="display:none">Save</button>
      </div>
    </div>';
}

$result = array_map(function($e){
  global $dataLevel;
  
  $row = array(
    $e['impressions_formatted'],
    $e['clicks_formatted'],
    $e['ctr_formatted'],
    $e['ad_spend_formatted'],
    $e['avg_cpc_formatted'],
    $e['units_sold_formatted'],
    $e['sales_formatted'],
    $e['cvr_formatted'],
    $e['acos_formatted']
  );
  
  if ($dataLevel == 'campaign') {
    // Insert new values into array in reverse order
    array_unshift($row, $e['targeting_type']);
    array_unshift($row, moneyInput('budget', $e['daily_budget']));
    array_unshift($row, cmLink('c_link', $e['amz_campaign_id'], $e['campaign_name']));
    array_unshift($row, cmCheckboxState($e['status'], 'campaign'));
  }
  else if ($dataLevel == 'ad_group'){
    // Insert new values into array in reverse order
    array_unshift($row, moneyInput('default_bid', number_format($e['default_bid'], 2)));
    array_unshift($row, cmLink('ag_link', $e['amz_adgroup_id'], $e['ad_group_name']));
    array_unshift($row, cmCheckboxState($e['status'], 'campaign')); // TODO: Change class name for ad_group
  }
  else if ($dataLevel == 'keyword'){
    // Insert new values into array in reverse order
    
    // TODO: Replace the avg_bid with the real bid
    array_unshift($row, moneyInput('bid', number_format($e['avg_bid'], 2)));
    array_unshift($row, $e['match_type']);
    array_unshift($row, '<b class="name" id="' . $e['amz_kw_id'] . '">' . $e['keyword_text'] . "</b>");
    array_unshift($row, cmCheckboxState($e['status'], 'campaign')); // TODO: Change class name for keyword
  }
  
  return $row;
}, $result);

echo json_encode(array('data' => $result));

?>