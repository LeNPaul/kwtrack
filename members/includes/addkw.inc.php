<?php
require_once './database/pdo.inc.php';
require_once './includes/insertasin.inc.php';

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
  // Query all ASINs from db
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

// If an ASIN was added, insert into db
if (!empty($_POST['addAsin']) && !empty($_POST['newAsin']) && !empty($_POST['prodShortTitle']) && strlen($_POST['newAsin']) == 10) {
  insertASIN($pdo, $_POST['newAsin'], $_POST['prodShortTitle']);
  $_SESSION['successAsin'] = createAlert('success', '<strong>'. $_POST['newAsin'] . '</strong> has been added!');
  header("Location: index.php");
  exit();
} elseif (strlen($_POST['newAsin'] < 10) && !empty($_POST['addAsin'])) {
  $_SESSION['errorAsin'] = createAlert('danger', 'Please enter ASIN correctly');
  header("Location: index.php");
  exit();
} elseif (!empty($_POST['addAsin']) && empty($_POST['prodShortTitle'])) {
  $_SESSION['errorAsin'] = createAlert('danger', 'Please enter a short title for your product');
  header("Location: index.php");
  exit();
}

// If delete asin button is pressed, then run this shit
if (!empty($_POST['btnDelAsin'])) {
  $_SESSION['deletedAsin'] = $_POST['btnDelAsin'];
  deleteAsin($pdo, $_POST['btnDelAsin']);
  $_SESSION['successDelAsin'] = createAlert('success', '<b>'. $_SESSION['deletedAsin'] . '</b> and all of its keywords have been deleted.');
  header("Location: index.php");
  exit();
}

// If delete keyword button is pressed, then run this shit
if (!empty($_POST['btnDeleteKw'])) {
  deleteKw($pdo, $_POST['btnDeleteKw']);
  $_SESSION['successDel'] = createAlert('success', '<b>'. $_POST['btnDeleteKw'] . '</b> has been deleted.');
  header("Location: index.php");
  exit();
}

// Run if 'Add Keyword' button is clicked
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

  $_SESSION['success'] = createAlert('success', '<b>'. $_POST['keyword']. '</b> has been added!');
  header("Location: index.php");
  exit();
} elseif (!empty($_POST['addkw']) && (!checkAsinExists($pdo, $_POST['asin']) || strlen($_POST['asin']) < 10 || empty($_POST['asin']))) {
  $_SESSION['error'] = createAlert('danger', 'ASIN format is incorrect. Please check for typos');
  header("Location: index.php");
  exit();
} elseif (!empty($_POST['addkw']) && !checkAsinExists($pdo, $_POST['asin'])) {
  $_SESSION['error'] = createAlert('danger', 'ASIN doesn\'t exist');
  header("Location: index.php");
  exit();
} elseif (!empty($_POST['addkw']) && empty($_POST['keyword'])) {
  $_SESSION['error'] = createAlert('danger', 'Please enter a keyword to add.');
  header("Location: index.php");
  exit();
}