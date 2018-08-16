<?php
/*
 * Helper functions for campaign data manipulation
 */
 
function getRefreshToken($pdo, $user_id) {
  $sql = 'SELECT refresh_token FROM users WHERE user_id=:user_id';
  $stmt = $pdo->query($sql);
  $result = $stmt->fetchAll(PDO::FETCH_COLUMN);
  var_dump($result);
}
?>