<?php
require_once dirname(__FILE__) . '/UserDataImporter.php';
require_once dirname(__FILE__) . '/pdo.inc.php';

$user_id_list = $pdo
  ->query("SELECT user_id FROM users WHERE active=5")
  ->fetchAll(PDO::FETCH_COLUMN);

$importer = new UserDataImporter();
foreach ($user_id_list as $user_id) {
  $importer->import($user_id, 1);
}
?>
