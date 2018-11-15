<?php
include '../../../../../database/pdo.inc.php';

$user_id = $_POST['user_id'];

$sql = 'SELECT campaign_name, amz_campaign_id, schedule FROM campaigns WHERE user_id = ?';
$stmt = $pdo->prepare($sql);
$stmt->bindParam(1, $user_id, PDO::PARAM_INT);
$stmt->execute();
$campaignList = $stmt->fetchAll(PDO::FETCH_ASSOC);

$outputList = [];

function drawCheckbox($campaignId) {
  return
  '<div class="form-check">
    <input class="form-check-input position-static" type="checkbox" id="blankCheckbox" value="' . $campaignId . '">
  </div>';
}


for ($i = 0; $i < count($campaignList); $i++) {
  if ($campaignList[$i]['schedule'] === 0) {
    $outputList[] = array(
      drawCheckbox($campaignList[$i]['amz_campaign_id']),
      $campaignList[$i]['campaign_name'],
      '<span class="circle_on"></span>');
  } else {
    $outputList[] = array(
      drawCheckbox($campaignList[$i]['amz_campaign_id']),
      $campaignList[$i]['campaign_name'],
      '<span class="circle_off"></span>');
  }
}

echo json_encode($outputList, true);
?>
