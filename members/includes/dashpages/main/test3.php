#!/usr/bin/env php
<?php
require '../../../database/pdo.inc.php';

$user_id = 2;
$stmt = $pdo->prepare("SELECT refresh_token, profileId FROM users WHERE user_id=?");
$stmt->bindParam(1, $user_id);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo '<pre>';
var_dump($result);
echo '</pre>';

?>
