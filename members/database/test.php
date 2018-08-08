<?php
require './pdo.inc.php';

$sql = 'SELECT * FROM users';
$stmt = $pdo->query($sql);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

var_dump($result);
