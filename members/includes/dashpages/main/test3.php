#!/usr/bin/env php
<?php
require '../../../database/pdo.inc.php';

$sql = "INSERT INTO keywords (kw_id) VALUES (:kw_id)";
$stmt = $pdo->prepare($sql);
$stmt->execute(array(':kw_id' => 1234));

?>
