<?php
include '../../../../../database/pdo.inc.php';

function array_flatten($array) {
  $return = array();
  foreach ($array as $key => $value) {
    if (is_array($value)){
      $return = array_merge($return, array_flatten($value));
    } else {
      $return[$key] = $value;
    }
  }
  return $return;
}

$sched   = $_POST['s']; // stringified JSON of schedule
$cidList = $_POST['c'];
$cidList = json_decode($cidList, false);

$sql  = "UPDATE campaigns SET schedule=:schedule WHERE amz_campaign_id=:cid";
$stmt = $pdo->prepare($sql);

$arr = array_flatten(json_decode($sched, false));
$sum = array_sum($arr);

if ($sum === 0) {
  for ($i = 0; $i < count($cidList); $i++) {
    $stmt->execute(array(
      'schedule' => '0',
      'cid'      => $cidList[$i]
    ));
  }
} else {
  for ($i = 0; $i < count($cidList); $i++) {
    $stmt->execute(array(
      'schedule' => $sched,
      'cid'      => $cidList[$i]
    ));
  }
}
?>