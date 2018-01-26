<?php
require_once './database/pdo.inc.php';

/* If user JUST clicked on the Product Tracker from main nav ($_GET will be empty) -- so show homepage*/
if (empty($_GET)) {
  include_once './includes/prodtracker/pages/overview.inc.php';
}
/* If user clicks Brands -> Manage Brands */
else if ($_GET['manage'] == 'brands') {
  echo 'Hello brands.';
}
/* If user clicks Products -> Manage Products */
else if ($_GET['manage'] == 'products') {
  echo 'Hello products.';
}
?>