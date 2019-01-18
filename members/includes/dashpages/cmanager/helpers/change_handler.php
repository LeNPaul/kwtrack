<?php
namespace AmazonAdvertisingApi;
session_start();
require_once "../../../AmazonAdvertisingApi/Client.php";
require_once dirname(__FILE__) . '../../../../../database/pdo.inc.php';
require_once dirname(__FILE__) . '../../../../../database/BudgetChanger.php';

use PDO;

$num = count($_POST["element_id"]);
$success = 0;
$datalevel = ($_POST["data_level"] == 0) ? "campaign budgets" : (($_POST["data_level"] == 1) ? "ad group default bids" : "keyword bids");

for ($i = 0; $i < $num; $i++) {
  $config = array(
    "element_id"     => $_POST["element_id"][$i],
    "element_name"   => $_POST["element_name"][$i],
    "data_level"     => $_POST["data_level"],
    "budget_val"     => $_POST["change_value"],
    "refresh_token"  => $_SESSION["refresh_token"],
    "profile_id"     => $_SESSION["profileId"]
  );

  $changeHandler = new BudgetChanger($config);
  $success += $changeHandler->singleChange();
}

if ($num == 1) {
  echo $changeHandler->getSingleAlert();
} else {
  echo $changeHandler->getMultiAlert($success, $datalevel);
}