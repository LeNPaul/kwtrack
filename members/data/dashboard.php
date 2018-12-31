<?php

session_start();

require_once '../database/MetricsQueryBuilder.php';
require_once '../database/pdo.inc.php';

global $pdo;

$user_id = $_SESSION['user_id'];
$refresh_token = $_SESSION['refresh_token'];


$builder = new MetricsQueryBuilder();
$builder->userId = $user_id;
$builder->includeCampaigns = true;
$builder->startDate = $_GET['start'];
$builder->endDate = $_GET['end'];
$result = $builder->execute($pdo);


header('Content-type: application/json');


function cmCheckboxState($status) {
  if ($status == 'enabled') {
    return '<input type="checkbox" class="toggle-campaign" checked data-toggle="toggle" data-size="mini" data-value="2" />';
  } elseif ($status == 'paused') {
    return '<input type="checkbox" class="toggle-campaign" data-toggle="toggle" data-size="mini" data-value="1" />';
  } else {
    return '<input type="checkbox" class="toggle-campaign-archive" data-toggle="toggle" data-size="mini" data-value="0" disabled />';
  }
}

$result = array_map(function($e){
  
  $campaignLink = '<a href="javascript:void(0)" class="name c_link" id="' . $e['amz_campaign_id'] . '">' . $e['campaign_name'] . '</a>';
  $budget =
    '<div class="input-group cm-input-group">
      <div class="input-group-prepend">
        <span class="input-group-text">$</span>
      </div>

      <input type="text" class="form-control edit-budget" data-color="danger" placeholder=" ' . $e['daily_budget'] . '" />

      <div class="input-group-append">
        <button class="btn btn-success btn-outline-secondary btn-edit-budget" type="button" style="display:none">Save</button>
      </div>
    </div>';
  
  
  return array(
    cmCheckboxState($e['status']),
    $campaignLink,
    $budget,
    $e['targeting_type'],
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
}, $result);

echo json_encode(array('data' => $result));

?>