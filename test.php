<?php

$curl = curl_init();

//curl_setopt($curl, CURLOPT_URL, 'https://ppcology.io/?code=ANJnRSvqXVROYHyktFmE&scope=cpc_advertising%3Acampaign_management&state=208257577110975193121591895857093449424');
$authCode = 'ANPLaaBdeysFprQmIszW';
$url = 'https://api.amazon.com/auth/o2/token?grant_type=authorization_code&code=' . $authCode . '&redirect_uri=https://ppcology.io&client_id=amzn1.application-oa2-client.4246e0f086e441259742c758f63ca0bf&client_secret=9c9e07b214926479e14a0781051ecc3ad9b29686d3cef24e15eb130a47cabeb3';

$options = array(
  'Content-Type:application/x-www-form-urlencoded;charset=UTF-8'
);

curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, $options);

$result = curl_exec($curl);

curl_close($curl);

var_dump($result);