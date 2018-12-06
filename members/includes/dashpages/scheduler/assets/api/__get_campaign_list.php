<?php
include '../../../../../database/pdo.inc.php';

$user_id = $_POST['user_id'];

$sql = 'SELECT campaign_name, amz_campaign_id, schedule, status FROM campaigns WHERE user_id = ? AND status<>"archived"';
$stmt = $pdo->prepare($sql);
$stmt->bindParam(1, $user_id, PDO::PARAM_INT);
$stmt->execute();
$campaignList = $stmt->fetchAll(PDO::FETCH_ASSOC);

$outputList = [];

for ($i = 0; $i < count($campaignList); $i++) {
  if ($campaignList[$i]['schedule'] == '0') {
    $outputList[] = array(
      $campaignList[$i]['campaign_name'],
      $campaignList[$i]['status'],
      '<span class="circle_off" id="' .  $campaignList[$i]['amz_campaign_id'] . '"></span>');
  } else {
    $outputList[] = array(
      $campaignList[$i]['campaign_name'],
      $campaignList[$i]['status'],
      '<span class="circle_on" id="' .  $campaignList[$i]['amz_campaign_id'] . '"></span>');
  }
}

echo json_encode($outputList, true);
?>
