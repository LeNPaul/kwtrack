<?php
namespace AmazonAdvertisingApi;
require_once '../../../AmazonAdvertisingApi/Client.php';
include '../../../../database/pdo.inc.php';
use PDO;

$user_id = $_POST['user_id'];
$campaignName = htmlspecialchars($_POST['campaignName']);
$cDataBack = $_POST['cDataBack'];
$campaignId = $cDataBack[$campaignName];

?>