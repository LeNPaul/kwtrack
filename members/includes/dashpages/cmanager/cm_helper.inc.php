<?php
/*
 *  CAMPAIGN MANAGER HELPER FILE
 *    - All helper functions used for the campaign manager
 */

function calculateMetricAvg($arr) {
  $arr = array_filter($arr);
  $average = array_sum($arr)/count($arr);
  return $average;
}

/*
 *
 *
 *
 */
function cmGetCampaignData($pdo, $user_id) {
  $output = [];
  $sql = "SELECT * FROM campaigns WHERE amz_campaign_id={$user_id}";
  $stmt = $pdo->query($sql);
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  for ($i = 0; $i < count($result); $i++) {
    $ad_spend = array_sum($result[$i]['ad_spend']);
    $sales = array_sum($result[$i]['sales']);
    $acos = ($sales == 0) ? 0.0 : round($ad_spend / $sales, 2);
    $output[] = array(
      $result[$i]['campaign_name'],
      array_sum($result[$i]['impressions']),
      array_sum($result[$i]['clicks']),
      calculateMetricAvg($result[$i]['ctr']),
      $ad_spend,
      calculateMetricAvg($result[$i]['avg_cpc']),
      array_sum($result[$i]['units_sold']),
      $sales,
      $acos
    );
  }
  return $output;
}
