<?php
namespace AmazonAdvertisingApi;
require_once '../../AmazonAdvertisingApi/Client.php';

$config = array(
  "clientId" => "amzn1.application-oa2-client.4246e0f086e441259742c758f63ca0bf",
  "clientSecret" => "9c9e07b214926479e14a0781051ecc3ad9b29686d3cef24e15eb130a47cabeb3",
  "refreshToken" => $refreshToken,
  "region" => "na",
  "sandbox" => false,
  );
$client = new Client($config);
$client->profileId = $profileId;

$result = $client->requestReport(
  "campaigns",
  array("reportDate"    => "20180810",
        "campaignType"  => "sponsoredProducts",
        "metrics"       => "campaignName, campaignId, campaignStatus,
                            campaignBudget, impressions, clicks, cost,
                            attributedConversions30d, attributedUnitsOrdered30d,
                            attributedSales30d"
  )
);

echo '<pre>';
var_dump($result);
echo '</pre>';

?>
