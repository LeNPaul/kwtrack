<?php
include '../cm_helper.inc.php';
include '../../../../database/pdo.inc.php';

$campaignName     = htmlspecialchars($_POST['campaignName']);
$campaignDataBack = $_POST['campaignDataBack'];
$campaignId       = $campaignDataBack[$campaignName];

var_dump(json_encode(cmGetAdGroupData($pdo, $campaignId), true));

//return cmGetAdGroupData($pdo, $campaignId);
?>
