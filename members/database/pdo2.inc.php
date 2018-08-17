<?php
error_reporting(E_ALL); ini_set('display_errors', TRUE); 
try {
  $pdo = new PDO('mysql:host=localhost;port=3306;dbname=ppcology_ppcology', 'ppcology_root', 'kA#3q5T}(|X70IcUl');
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $er) {
  echo $er->getMessage();
}
