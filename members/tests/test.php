<?php

namespace AmazonAdvertisingApi;
require_once  "../AmazonAdvertisingApi/Client.php";

$config = array(
  "clientId" => "amzn1.application-oa2-client.4246e0f086e441259742c758f63ca0bf",
  "clientSecret" => "9c9e07b214926479e14a0781051ecc3ad9b29686d3cef24e15eb130a47cabeb3",
  "refreshToken" => "accessToken",
  "region" => "na",
  "sandbox" => true,
);

$client = new Client($config);

?>