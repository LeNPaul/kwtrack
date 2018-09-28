<?php
include '../cm_helper.inc.php';

$campaignName     = $_POST['campaignName'];
$campaignDataBack = $_POST['campaignDataBack'];
$campaignId       = $campaignDataBack[$campaignName];

return cmGetAdGroupData($pdo, $campaignId);
?>
