<?php
namespace AmazonAdvertisingApi;
require_once "AmazonAdvertisingApi/Client.php";

$config = array(
  "clientId" => "amzn1.application-oa2-client.4246e0f086e441259742c758f63ca0bf",
  "clientSecret" => "9c9e07b214926479e14a0781051ecc3ad9b29686d3cef24e15eb130a47cabeb3",
  "region" => "na",
  "refreshToken" => "Atzr|IwEBIP9tInWp1t__Vk1JZDUHyuWUu2rbgbkL339xqXIJ9rp_x4aVgx-vyHQpi7VxwnOoTRCP-3X6dEHlyfniD5Fs9oWfeiiLvfyIhTOkx9-blulp3U2EgUNx7Z0EuiQxgk9fS1n5t0ELLaq1kSZ9Ja_1xl1Ec7r_XwxO40IB8ipt5RdE-fAnu6PKbwj6eR4XLdk1t2DqxSWGRhoVEG715WX2Y1Hzsi8LgdloR47APPAwg_BO1qdikz2M_zkgXQtmbBV46v55u8vzflAq-DYdGgOxwKtmU6U2cJKSwZqwmI2uwebT_2JCWH-xHjCxAlLzloNBqt3CoknPsFVmuQAtE1ElzUPqUXUkd3n0XPpIsbWtI6plKSQq2steikD7CwUMfP0WLAhA2vIfQF1cbrBQ6iWSd6pO8LZyChfB4FyBGwzHGr11wuTMAvZ7KhKNVxoD6VaTi9uADzLur8QoTt7C0ekZzGnW2N8G2p4nA8g0izvBTK6EBnHuCozD5YJXy965-4h3rZcmTqXu8Lv0Q5OzY3xHRfwQN-xVTgVUUFLPGZc4ojC_uZupXTQsi1ZmIyp83nvNwT4",
  "sandbox" => true);

$client = new Client($config);

$request = $client->listProfiles();
$profileJSON = json_decode($request['response'], true, 512, JSON_BIGINT_AS_STRING);
$profileID = $profileJSON[0]['profileId'];

$client->profileId = $profileID;

var_dump($client->listCampaigns(array("stateFilter" => "enabled")));

if (!empty($_GET['createCampaign']) && !empty($_GET["campaignName"]))
{
  echo 'wagwan';
} else {
  echo "error";
}

?>

<html>
<head>

</head>

<body>
<form method="GET">
  <input type="text" name="campaignName" />
  <button type="submit" name="createCampaign" value="y">Create Campaign</button>
</form>
</body>

</html>