<?php
$curl = curl_init();

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

var_dump($result);