<?php
include '../cm_helper.inc.php';
include '../../../../database/pdo.inc.php';

$adgroupName     = htmlspecialchars($_POST['adgroupName']);
$adgroupDataBack = $_POST['adgroupDataBack'];

// Get the ad group ID of the adgroup we want to pull keywords for
$adgroupId = $adgroupDataBack[$adgroupName];

echo json_encode(cmGetKeywordData($pdo, $adgroupId), true);

?>
