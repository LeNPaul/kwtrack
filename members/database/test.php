<?php
require './pdo.inc.php';

$sql = "SELECT * FROM users WHERE email='fernandgee@gmail.com'";
$stmt = $pdo->query($sql);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo '<pre>' . var_dump($result) . '</pre>';
