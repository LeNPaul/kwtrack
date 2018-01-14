<?php
include_once './includes/addkw.inc.php';
include_once './database/pdo.inc.php';

if (!empty($_POST['btnChangeProdName']) && !empty($_POST['newProdName']) && !empty($_POST['asin'])) {
  $sql = 'UPDATE asins SET prod_short_title = :newProdName WHERE asin = :asin';
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(
    ':newProdName' => $_POST['newProdName'],
    ':asin'        => $_POST['asin']
  ));
  $alertMsg = 'The product short name of <b>'. $_POST['asin'] .'</b> has been successfully updated to <b>'.$_POST['newProdName'].'</b>';
  $alert = createAlert('success', $alertMsg);
} elseif  (!empty($_POST['btnChangeProdName']) && sizeof($_POST['asin'] != 10)) {
  $alert = createAlert('danger', 'There was an error in your input.');
}