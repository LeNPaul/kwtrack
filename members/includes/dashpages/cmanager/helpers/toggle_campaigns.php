<?php
namespace AmazonAdvertisingApi;
require_once '../../../AmazonAdvertisingApi/Client.php';
include '../../../../database/pdo.inc.php';
use PDO;

$user_id          = $_POST['user_id'];
$toggle           = $_POST['toggle'];
$campaignName     = htmlspecialchars($_POST['campaignName']);
$campaignDataBack = $_POST['cDataBack'];
$campaignId       = $campaignDataBack[$campaignName];

$stmt = $pdo->prepare("SELECT refresh_token, profileId FROM users WHERE user_id=?");
$stmt->bindParam(1, $user_id);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOCC);
echo '<pre>';
var_dump($result);
echo '</pre>';


if ($toggle === true) {

} else {

}
