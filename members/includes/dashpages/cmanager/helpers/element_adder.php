<?php
namespace AmazonAdvertisingApi;
session_start();
require_once "../../../AmazonAdvertisingApi/Client.php";
require_once dirname(__FILE__) . '../../../../../database/pdo.inc.php';
require_once dirname(__FILE__) . '../../../../../database/ElementAdder.php';
ini_set('precision', 30);

function format_child_elements_arr(
  $campaign_data, $adgroup_data,
  $kw_data, $negkw_data,
  $asin_data
)
{
  return array(
    "campaigns"    => $campaign_data,
    "ad_groups"    => $adgroup_data,
    "ads"          => $asin_data,
    "keywords"     => $kw_data,
    "neg_keywords" => $negkw_data
  );
}

$user_id       = $_SESSION["user_id"];
$campaign_data = json_decode($_POST["campaign_data"], true, 512, JSON_BIGINT_AS_STRING);
$adgroup_data  = json_decode($_POST["adgroup_data"], true, 512, JSON_BIGINT_AS_STRING);
$kw_data       = json_decode($_POST["kw_data"], true, 512, JSON_BIGINT_AS_STRING);
$negkw_data    = json_decode($_POST["negkw_data"], true, 512, JSON_BIGINT_AS_STRING);
$asin_data     = json_decode($_POST["asin_data"], true, 512, JSON_BIGINT_AS_STRING);
$element_type  = $_POST["element_type"];

$config = array(
  "element_type"   => $element_type,
  "user_id"        => $user_id,
  "child_elements" => format_child_elements_arr(
    $campaign_data, $adgroup_data,
    $kw_data, $negkw_data,
    $asin_data)
);

$adder = new ElementAdder($config);


?>