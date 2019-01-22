<?php
require '../../../database/pdo.inc.php';


$arr = array(
  "keywords" => array(
    array(
      "c" => 3,
      "d" => 4
    ),
    array(
      "c" => 3,
      "d" => 4
    )
));

echo '<pre>';
var_dump($arr);
echo '</pre>';

foreach($arr["keywords"] as &$kw_arr) {
  $kw_arr["e"] = 5;
  $kw_arr["f"] = 6;
}

echo '<pre>';
var_dump($arr);
echo '</pre>';
?>
