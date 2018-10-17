#!/usr/bin/env php
<?php
require '../../../database/pdo.inc.php';

$sql = "SELECT impressions FROM campaigns WHERE user_id=2";
$stmt = $pdo->query($sql);
$result = $stmt->fetch(PDO::FETCH_COLUMN);

var_dump(unserialize($result));

?>
