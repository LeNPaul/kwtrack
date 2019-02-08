<?php
namespace AmazonAdvertisingApi;
session_start();
require_once "../../../AmazonAdvertisingApi/Client.php";
require_once dirname(__FILE__) . '../../../../../database/pdo.inc.php';
require_once dirname(__FILE__) . '../../../../../database/ElementToggler.php';

use PDO;

$num = count($_POST["element_id"]);
$success = 0;
if ($_POST["data_level"] == 0) {
  $datalevel = "campaign";
} elseif ($_POST["data_level"] == 1) {
  $datalevel = "ad group";
} elseif ($_POST["data_level"] == 2) {
  $datalevel = "keyword";
} elseif ($_POST["data_level"] == 3) {
  $datalevel = "campaign negative keyword";
} elseif ($_POST["data_level"] == 4) {
  $datalevel = "ad group negative keyword";
}

for ($i = 0; $i < $num; $i++) {
  $config = array(
    "element_id"     => $_POST["element_id"][$i],
    "element_name"   => $_POST["element_name"][$i],
    "data_level"     => $_POST["data_level"],
    "toggle"         => $_POST["toggle"],
    "refresh_token"  => $_SESSION["refresh_token"],
    "profile_id"     => $_SESSION["profileId"]
  );

  $toggler = new ElementToggler($config);
  $success += $toggler->single_toggle();
}

if ($num == 1) {
  echo $toggler->get_alert_text();
} else {
  echo $toggler->get_multi_alert($success, $datalevel);
}
?>
