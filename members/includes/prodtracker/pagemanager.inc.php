<?php
if ( !isset($_SESSION) ) { session_start(); }
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/pdo.inc.php';

/* If user JUST clicked on the Product Tracker from main nav ($_GET will be empty) -- so show homepage*/
if (empty($_GET)) {
  include_once $_SERVER['DOCUMENT_ROOT'] . '/includes/prodtracker/pages/overview.inc.php';
}
/* If user clicks Brands -> Manage Brands */
else if ($_GET['manage'] == 'brands') {
  include_once $_SERVER['DOCUMENT_ROOT'] . '/includes/prodtracker/pages/managebrands.inc.php';
}
/* If user clicks Products -> Manage Products */
else if ($_GET['manage'] == 'products') {
  echo 'Hello products.';
}
?>