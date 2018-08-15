<?php
session_start();
require '../../../database/pdo.inc.php';
$curl = curl_init();

// Redirect if someone goes to this link directly
if (empty($_GET['code']) || !isset($_SESSION['logged_in'])) {
  $_SESSION['message'] = createAlert('danger', 'An error has occurred. Please try again.');
  header('location: ../../../dashboard.php');
  exit();
}

// Grab auth code from the URL after the user goes thru consent screens
$authCode = $_GET['code'];
$url = 'https://api.amazon.com/auth/o2/token';
$data = 'grant_type=authorization_code&code=' . $authCode . '&redirect_uri=https://ppcology.io/&client_id=amzn1.application-oa2-client.4246e0f086e441259742c758f63ca0bf&client_secret=9c9e07b214926479e14a0781051ecc3ad9b29686d3cef24e15eb130a47cabeb3';

$options = array(
  'Content-Type:application/x-www-form-urlencoded;charset=UTF-8'
);

// Set cURL options to POST and receive JSON as a response
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, $options);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

// Store JSON in $result and decode it so it's an array
$result = curl_exec($curl);
curl_close($curl);
$jsonResult = json_decode(stripslashes($result),true);

// Get refresh token and store it in db for the user
$refreshToken = $jsonResult['refresh_token'];
$sql = 'UPDATE users SET refresh_token=:refresh_token, active=:active WHERE user_id=:user_id';
$stmt = $pdo->prepare($sql);
$stmt->execute(array(
  ':refresh_token' => $refreshToken,
  ':active'        => 2,
  ':user_id'       => $_SESSION['user_id']
));

// Set success message and redirect them back to dashboard
$_SESSION['message'] = createAlert('success', 'Your advertising data has been put in queue to be imported. Campaign data importing may take up to 48 hours.');
header('location: ../../../dashboard.php');
exit();
