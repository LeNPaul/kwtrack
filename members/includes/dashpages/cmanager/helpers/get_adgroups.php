<?php
include '../cm_helper.inc.php';
include '../../../../database/pdo.inc.php';

$campaignName     = htmlspecialchars($_POST['campaignName']);
$campaignDataBack = $_POST['campaignDataBack'];
$campaignId       = $campaignDataBack[$campaignName];

echo cmGetAdGroupData($pdo, $campaignId);

//return cmGetAdGroupData($pdo, $campaignId);
?>
