<?php
namespace AmazonAdvertisingApi;
require_once '../../../AmazonAdvertisingApi/Client.php';
include '../../../../database/pdo.inc.php';

$user_id          = $_POST['user_id'];
$toggle           = $_POST['toggle'];
$campaignName     = htmlspecialchars($_POST['campaignName']);
$campaignDataBack = $_POST['cDataBack'];
$campaignId       = $campaignDataBack[$campaignName];

$sql = "SELECT refresh_token, profileId FROM users WHERE user_id=:user_id";
z
if ($toggle === true) {

} else {

}
