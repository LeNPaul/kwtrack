<?php
namespace AmazonAdvertisingApi;
require_once '../../../AmazonAdvertisingApi/Client.php';

$toggle = $_POST['toggle'];
$campaignName = htmlspecialchars($_POST['campaignName']);
$campaignDataBack = $_POST['cDataBack'];
$campaignId = $campaignDataBack[$campaignName];

if ($toggle === true) {

} else {

}
