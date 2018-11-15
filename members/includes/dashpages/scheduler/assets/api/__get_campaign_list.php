<?php
include '../../../../../database/pdo.inc.php';

$user_id = 2/*$_POST['user_id']*/;

$sql = 'SELECT campaign_name, amz_campaign_id FROM campaigns WHERE user_id = ?';
$stmt = $pdo->prepare($sql);
$stmt->bindParam(1, $user_id, PDO::PARAM_INT);
$stmt->execute();
$campaignNameList = $stmt->fetchAll(PDO::FETCH_ASSOC);

$dummy = array_fill(0, 7, array_fill(0, 24, 24));

echo serialize($dummy);

echo json_encode($campaignNameList, true);

?>
