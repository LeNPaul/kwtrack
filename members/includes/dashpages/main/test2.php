<?php
$arr = [1,1,1,1,1];
$arr = array_reduce($arr, function($carry, $element) { $carry += $element });

echo $arr;
?>
