<?php
namespace AmazonAdvertisingApi;
session_start();
require '../../../database/pdo.inc.php';
//require '../helper.inc.php';
require_once '../../AmazonAdvertisingApi/Client.php';
error_reporting(E_ALL); ini_set("error_reporting", E_ALL);

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
$user_id = $_SESSION['user_id'];

import_data();


function import_data(){
	// TODO: integrate campaign, adgroup, and keyword import code from test.php here
	// Instantiate client for advertising API
	$config = array(
		"clientId" => "amzn1.application-oa2-client.4246e0f086e441259742c758f63ca0bf",
		"clientSecret" => "9c9e07b214926479e14a0781051ecc3ad9b29686d3cef24e15eb130a47cabeb3",
		"refreshToken" => $refreshToken,
		"region" => "na",
		"sandbox" => false,
	);
	$client = new Client($config);
	$client->profileId = $profileId;

	// First, grab campaigns and store them in db
	

	// Second, grab ad groups and store them in db

}

// Redirect to dashboard
// header('location: ../../../dashboard.php');
// exit();

*/