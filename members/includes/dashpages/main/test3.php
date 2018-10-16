#!/usr/bin/env php
<?php
require '../../../database/pdo.inc.php';

class Arrays {
  function diff($b, $a) {
    $at = array_flip($a);
    $d = array();
    foreach ($b as $i)
      if (!isset($at[$i]))
        $d[] = $i;
    return $d;
  }
}

$arr1 = [1,2,3,4,5,6,7,8,9,10];
$arr2 = [2,3,4,5,11];

$b = Arrays::diff($arr1, $arr2);

echo '<pre>';
var_dump($b);
echo '</pre>';

?>
