<?php
include 'pdo.inc.php';

// query all userIDs from ppc_keywords where active = 5 and store in array (userIDs)
$userIDs = [];
$sql = "SELECT user_id FROM users WHERE active=5";
$stmt = $pdo->query($sql);
$userIDs = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo '<pre>';
var_dump($userIDs);
echo '</pre>';
?>
