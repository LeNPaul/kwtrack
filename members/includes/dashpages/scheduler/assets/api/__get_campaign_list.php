<?php
include '../../../../../database/pdo.inc.php';

$user_id = $_POST['user_id'];

$sql = 'SELECT campaign_name FROM campaigns WHERE user_id = ?';
$stmt = $pdo->prepare($sql);
$stmt->bindParam(1, $user_id, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo '<pre>';
var_dump($result);
echo '</pre>';

?>
