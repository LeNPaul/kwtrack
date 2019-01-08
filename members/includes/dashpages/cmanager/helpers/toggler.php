<?php
namespace AmazonAdvertisingApi;
session_start();
require_once "../../../AmazonAdvertisingApi/Client.php";
require_once dirname(__FILE__) . '../../../../../database/pdo.inc.php';
require_once dirname(__FILE__) . '../../../../../database/ElementToggler.php';

use PDO;

$config = array(
	"element_id"     => $_POST["element_id"],
	"element_name"   => $_POST["element_name"],
	"data_level"     => $_POST["data_level"],
	"toggle"         => $_POST["toggle"],
	"refresh_token"  => $_SESSION["refresh_token"],
	"profile_id"     => $_SESSION["profileId"]
);

$toggler = new ElementToggler($config);
$toggler->single_toggle();

echo $toggler->get_alert_text();

?>
