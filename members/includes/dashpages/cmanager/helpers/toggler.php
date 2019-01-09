<?php
namespace AmazonAdvertisingApi;
session_start();
require_once "../../../AmazonAdvertisingApi/Client.php";
require_once dirname(__FILE__) . '../../../../../database/pdo.inc.php';
require_once dirname(__FILE__) . '../../../../../database/ElementToggler.php';

use PDO;

$num = count($_POST["element_id"]);
$success = 0;

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
	echo $toggler->get_multi_alert($success);
}
?>
