<?php
namespace AmazonAdvertisingApi;
require '../../../database/pdo.inc.php';
require_once '../../AmazonAdvertisingApi/Client.php';

$refreshToken = $argv[1];
$user_id = $argv[2];

// Instantiate client for advertising API
$config = array(
  "clientId" => "amzn1.application-oa2-client.4246e0f086e441259742c758f63ca0bf",
  "clientSecret" => "9c9e07b214926479e14a0781051ecc3ad9b29686d3cef24e15eb130a47cabeb3",
  "refreshToken" => $refreshToken,
  "region" => "na",
  "sandbox" => false,
  );

// First, grab campaigns and store them in db
$campaigns = $client->listCampaigns(array("stateFilter" => "enabled"));

echo '<pre>';
var_dump($campaigns);
echo '</pre>';
