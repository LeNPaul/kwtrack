<?php

session_start();


try {
  $pdo = new PDO('mysql:host=localhost;port=3306;dbname=kwtracker', 'pojka', '123456');
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  
} catch (PDOException $er) {
  echo $e->getMessage();
}

// First, find kw_id of $_SESSION['currentKw']
$sql = 'SELECT kw_id FROM keywords WHERE keyword="'.$_SESSION[currentKw].'"';
$kw_id = $pdo->query($sql)->fetchColumn();

// Then, find all historical rank data of $_SESSION['currentKw']
$sql = 'SELECT * FROM oldranks WHERE kw_id="' . $kw_id . '"';
$result = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
$data = array();

foreach ($result as $value) {
  $data[] = $value;
}

print(json_encode($data));

