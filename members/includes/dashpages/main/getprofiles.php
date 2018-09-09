<?php
namespace AmazonAdvertisingApi;
session_start();
require '../../../database/pdo.inc.php';
require '../helper.inc.php';
require_once '../../AmazonAdvertisingApi/Client.php';
error_reporting(E_ALL); ini_set("error_reporting", E_ALL);
use PDO;

// Insert profileID in database for the user and set active level to 3
$profileId = $_POST['selectedProfile'];
//$profileId = $profileId[0];
$sql = 'UPDATE users SET profileId=:profileId, active=:level WHERE user_id=:user_id';
$stmt = $pdo->prepare($sql);
$stmt->execute(array(
  ':profileId' => $profileId,
  ':level'     => 3,
  ':user_id'    => $_SESSION['user_id']
));

// Get refresh token
$sql = 'SELECT refresh_token FROM users WHERE user_id=' . $_SESSION['user_id'];
$stmt = $pdo->query($sql);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
$refreshToken = $result[0]['refresh_token'];
$user_id = $_SESSION['user_id'];

echo "finished";