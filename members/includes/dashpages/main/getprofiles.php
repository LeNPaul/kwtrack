<?php
session_start();
require '../../../database/pdo.inc.php';
require '../helper.inc.php';
error_reporting(E_ALL); ini_set("error_reporting", E_ALL);

/*
// Insert profileID in database for the user and set active level to 3
$profileId = $_POST['selectedProfile'];
$profileId = $profileId[0];
$sql = 'UPDATE users SET profileId=:profileId, active=:level WHERE user_id=:user_id';
$stmt = $pdo->prepare($sql);
$stmt->execute(array(
  ':profileId' => $profileId,
  ':level'     => 3,
  ':user_id'    => $_SESSION['user_id']
));

// Get refresh token to pass onto import_data.php
$sql = 'SELECT refresh_token FROM users WHERE user_id=' . $_SESSION['user_id'];
$stmt = $pdo->query($sql);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
$refreshToken = $result[0]['refresh_token'];
*/
// Run campaign importing in background [FIX WHEN YOU COME BACK]
//$user_id = $_SESSION['user_id'];
echo 'running exec <br /><br />';
//exec("php import_data.php > /dev/null 2>&1");
shell_exec("php -f import_data.php 2>&1 >> error_log");
echo 'finished running exec <br />';


//shell_exec("php import_data.php $refreshToken $user_id $profileId > /dev/null &");

// Redirect to dashboard
header('location: ../../../dashboard.php');
//exit();
