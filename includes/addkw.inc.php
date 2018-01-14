<?php
require_once './database/pdo.inc.php';

/* 
 * checkAsinExists(PDO $pdo, String $asin) => Bool
 *  --> Check if ASIN exists in the database
 * 
 *    --> PDO $pdo - PDO database connection
 *    --> String $asin - ASIN to search for
 */
function checkAsinExists($pdo, $asin) {
  $stmt = $pdo->query('SELECT * FROM asins WHERE asin="'.$asin.'"');
  $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return ($results) ? [true, $results[0]] : false;
}

function generateDropdown($pdo) {
  // Query all ASINS from db
  $sql = 'SELECT asin, prod_short_title FROM asins';
  $stmt = $pdo->query($sql);
  $asinArr = $stmt->fetchAll(PDO::FETCH_ASSOC);

  echo('<select name="asin" class="custom-select">
          <option selected>Select ASIN</option>');
  for ($i = 0; $i < sizeof($asinArr); $i++) {
    echo('<option value="' . $asinArr[$i]['asin'] . '">'. $asinArr[$i]['asin'] . ' | '. $asinArr[$i]['prod_short_title'] .'</option>');
  }
  echo('</select>');
}

/* Run if 'Add Keyword' button is clicked */
if (!empty($_POST['addkw']) && !empty($_POST['keyword']) && strlen($_POST['asin']) == 10 && checkAsinExists($pdo, $_POST['asin'])) {
  $asinID = checkAsinExists($pdo, $_POST['asin']);
  $asinID = $asinID[1]['asin_id'];
  $sql = "INSERT INTO keywords (keyword, asin_id) VALUES (:keyword, :asin_id)";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(
    ':keyword' => $_POST['keyword'],
    ':asin_id' => $asinID
  ));
  updateRanks($pdo, $_POST['keyword'], $_POST['asin']);
  echo createAlert('success', '<b>'. $_POST['keyword']. '</b> has been added!');
} elseif (!empty($_POST['addkw']) && (!checkAsinExists($pdo, $_POST['asin']) || strlen($_POST['asin']) < 10 || empty($_POST['asin']))) {
  echo createAlert('danger', 'ASIN format is incorrect. Please check for typos');
} elseif (!empty($_POST['addkw']) && !checkAsinExists($pdo, $_POST['asin'])) {
  echo createAlert('danger', 'ASIN doesn\'t exist');
} elseif (!empty($_POST['addkw']) && empty($_POST['keyword'])) {
  echo createAlert('danger', 'Please enter a keyword to add.');
}