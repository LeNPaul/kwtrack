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

function launchBackgroundProcess($command) {
  // Run command Asynchroniously (in a separate thread)
  if(PHP_OS=='WINNT' || PHP_OS=='WIN32' || PHP_OS=='Windows'){
    // Windows
    $command = 'start "" '. $command;
  } else {
    // Linux/UNIX
    $command = $command .' /dev/null &';
  }
  $handle = popen($command, 'r');
  if($handle!==false){
    pclose($handle);
    return true;
  } else {
    return false;
  }
}
?>
