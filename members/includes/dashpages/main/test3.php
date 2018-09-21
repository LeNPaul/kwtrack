<?php
namespace AmazonAdvertisingApi;
require_once '../../AmazonAdvertisingApi/Client.php';
require '../../../database/pdo.inc.php';
require '../helper.inc.php';
use PDO;


$sql = "INSERT INTO keywords (kw_id) VALUES (:kw_id)";
$stmt = $pdo->prepare($sql);
$stmt->execute(array(':kw_id' => 1234));

?>
