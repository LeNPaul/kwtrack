<?php
include './amazon-search-trends/AmazonSearchTrends.php';

$keyword1 = $_POST["keyword1"];
$keyword2 = $_POST["keyword2"];
$keyword3 = $_POST["keyword3"];
$keyword4 = $_POST["keyword4"];

$amzModule = new AmazonSearchTrends();

try {
  $arr = [];

  if ($keyword1 != "") array_push($arr, $keyword1);
  if ($keyword2 != "") array_push($arr, $keyword2);
  if ($keyword3 != "") array_push($arr, $keyword3);
  if ($keyword4 != "") array_push($arr, $keyword4);

  $result = $amzModule->getTrends($arr);

  header('Content-type:application/json;charset=utf-8');
  echo $result;
} catch (Exception $ex) {
  $response = [
    "status" => "error",
    "message" => "An error occurred"
  ];
  header('Content-type:application/json;charset=utf-8');
  echo json_encode($result);
}
?>
