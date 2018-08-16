<?php
// file to import data and store in db
session_start();
$curl = curl_init();

//grab profile for profile ID. Todo: store profileID in db
$accessToken = $_GET[$argv[1]];
$url = "https://advertising-api.amazon.com/v1/profiles";
$options = array(
  "Content-Type:application/json",
  "Authorization: Bearer {$accessToken}"
);

curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
curl_setopt($curl, CURLOPT_HTTPHEADER, $options);

$result = curl_exec($curl);
$jsonResult = json_decode(stripslashes($result), true);
$profileID = $jsonResult["properties"]["profileID"]["description"];

//use profileID to get all the campaigns and store them in db
$url = "https://advertising-api.amazon.com/v1/campaigns"


?>