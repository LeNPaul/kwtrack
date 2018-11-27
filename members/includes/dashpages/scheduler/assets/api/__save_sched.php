<?php
include '../../../../../database/pdo.inc.php';

$sched   = $_POST['s']; // stringified JSON of schedule
$cidList = $_POST['c'];

print_r($cidList); die;

$sql  = "UPDATE campaigns SET schedule=:schedule WHERE amz_campaign_id=:cid";
$stmt = $pdo->prepare($sql);
for ($i = 0; $i < count($cidList); $i++) {
  $stmt->execute(array(
    'schedule' => $sched,
    'cid'      => $cidList[$i]
  ));
}
?>