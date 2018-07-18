<?php
include_once './database/pdo.inc.php';
ini_set('max_execution_time', 1000); //set timeout time to 5 mins

/*
 *  updateListingTitle(PDO $pdo, String $asin) => null
 *    --> Updates the prod_title for $asin to check if the title was changed recently.
 */
function updateListingTitle($pdo, $asin) {
  $title = getTitleFromASIN($asin);
  $sql = 'UPDATE asins SET prod_title=:title WHERE asin=:asin';
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(
    ':title' => $title,
    ':asin'  => $asin
  ));
}


if (!empty($_POST['btnUpdateRanks'])) {
  // Get all keywords and set it to $kwArr
  $sql = 'SELECT keyword, asin_id FROM keywords';
  $kwArr = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

  // Loop through array of all keywords
  for ($i = 0; $i < sizeof($kwArr); $i++) {
    $kw = $kwArr[$i]['keyword'];
    $asin_id = $kwArr[$i]['asin_id'];

    // Get ASIN associated with current $kw and set it to $asin
    $sql = 'SELECT asin FROM asins WHERE asin_id="'.$asin_id.'"';
    $asin = $pdo->query($sql)->fetch(PDO::FETCH_COLUMN);
    
    // Change product title of $asin to its most recent version
    updateListingTitle($pdo, $asin);

    // Find out kw_id of $kw
    $sql = 'SELECT kw_id FROM keywords WHERE keyword="'.$kw.'"';
    $kw_id = $pdo->query($sql)->fetch(PDO::FETCH_COLUMN);

    // Find out if kw has more than 7 entries in oldranks
    $sql = 'SELECT * FROM oldranks WHERE kw_id=' . $kw_id;
    $ranksOfKw = $pdo->query($sql)->fetchAll(PDO::FETCH_COLUMN);

    // If it does, then delete the first entry (oldest entry)
    if (sizeof($ranksOfKw) > 6) {
      $sql = 'DELETE FROM oldranks WHERE kw_id=:kw_id LIMIT 1';
      $stmt = $pdo->prepare($sql);
      $stmt->execute(array(
        'kw_id' => $kw_id
      ));
    }

    // Use updateRanks() to update all kw ranks AND update oldranks table w/ historical data
    updateRanks($pdo, $kw, $asin);
  }
  echo createAlert('success', 'All ranks have been updated');
}


?>