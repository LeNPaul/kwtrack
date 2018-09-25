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
 *
 *
 *
 */
function cmGetCampaignData($pdo, $user_id) {
  $output = [];
  $sql = "SELECT * FROM campaigns WHERE user_id={$user_id}";
  $stmt = $pdo->query($sql);
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  for ($i = 0; $i < count($result); $i++) {
	if ($result[$i]['status'] == 'enabled') {
		$active = 0;
	} elseif ($result[$i]['status'] == 'paused') {
		$active = 1;
	} else {
		$active = 2;
	}
    $ad_spend = array_sum(unserialize($result[$i]['ad_spend']));
    $sales = array_sum(unserialize($result[$i]['sales']));
    $acos = ($sales == 0) ? "-" : round(($ad_spend / $sales) * 100, 2) . '%';
    $output[] = array(
	  $active,
      $result[$i]['campaign_name'],
	  $result[$i]['status'],
      '$' . $result[$i]['daily_budget'],
      $result[$i]['targeting_type'],
      array_sum(unserialize($result[$i]['impressions'])),
      array_sum(unserialize($result[$i]['clicks'])),
      round(calculateMetricAvg(unserialize($result[$i]['ctr'])), 2) . '%',
      '$' . round($ad_spend, 2),
      round(calculateMetricAvg(unserialize($result[$i]['avg_cpc'])), 2) . '%',
      array_sum(unserialize($result[$i]['units_sold'])),
      '$' . round($sales, 2),
      $acos
    );
  }
  return $output;
}
