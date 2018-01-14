<?php
require_once './database/pdo.inc.php';

$sql = 'SELECT keyword, asin_id FROM keywords';
$kwArr = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

for ($i = 0; $i < sizeof($kwArr); $i++) {
  $kw = $kwArr[$i]['keyword'];
  $asin_id = $kwArr[$i]['asin_id'];

  // Get ASIN associated with current $kw and set it to $asin
  $sql = 'SELECT asin FROM asins WHERE asin_id="'.$asin_id.'"';
  $asin = $pdo->query($sql)->fetch(PDO::FETCH_COLUMN);

  // Find out kw_id of $kw
  $sql = 'SELECT kw_id FROM keywords WHERE keyword="'.$kw.'"';
  $kw_id = $pdo->query($sql)->fetch(PDO::FETCH_COLUMN);

  // Find out if kw has more than 7 entries in oldranks
  $sql = 'SELECT * FROM oldranks WHERE kw_id=' . $kw_id;
  $ranksOfKw = $pdo->query($sql)->fetchAll(PDO::FETCH_COLUMN);

  // If it does, then delete the first entry (oldest entry)
  if (sizeof($ranksOfKw) > 6) {
    $sql = 'DELETE FROM oldranks WHERE kw_id=:kw_id LIMIT 1';
    echo 'deleting first oldrank from ' . $kw . '<br>';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
      'kw_id' => $kw_id
    ));
  }
}