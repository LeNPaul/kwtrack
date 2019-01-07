<?php
require_once dirname(__FILE__) . "/../../../../database/pdo.inc.php";
ini_set('precision',30);

global $pdo;

$data_level = $_POST["data_level"];
$element_id = $_POST["element_id"];

$output = [];

// Campaign data level
if ($data_level == 0) {
  $sql    = "SELECT * FROM campaign_neg_kw WHERE amz_campaign_id = " . $element_id . " AND state='enabled'";
  $result = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

  foreach ($result as $negKw) {
    $match_type = ($negKw['match_type'] == 'negativePhrase') ? 'Negative Phrase' : 'Negative Exact';
    $output[] = ["<p id='{$negKw['kw_id']}'>{$negKw['keyword_text']}</p>", $match_type];
  }
}
// Adgroup data level
else if ($data_level == 1) {
  $sql    = "SELECT * FROM adgroup_neg_kw WHERE amz_adgroup_id = " . $element_id . " AND state='enabled'";
  $result = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
  
  foreach ($result as $negKw) {
    $match_type = ($negKw['match_type'] == 'negativePhrase') ? 'Negative Phrase' : 'Negative Exact';
    $output[] = ["<p id='{$negKw['kw_id']}'>{$negKw['keyword_text']}</p>", $match_type];
  }
}

echo json_encode(array('data' => $output));
?>