<?php
/* Verifies registered user email, the link to this page is
 * included in the register.php email message
 */
require './members/database/pdo.inc.php';
session_start();

// Make sure email and hash vars aren't empty
if ((isset($_GET['email']) && !empty($_GET['email'])) && (isset($_GET['hash']) && !empty($_GET['hash']))) {
  $email = htmlentities($_GET['email']);
  $hash = htmlentities($_GET['hash']);
  
  // Select user with matching email and hash, who hasn't verified their account yet (active = 0)
  $sql = "SELECT * FROM users WHERE email='$email' AND hash='$hash' AND active='0'";
  $stmt = $pdo->query($sql);
  $results = $stmt->fetchAll();
  
  if (count($results) == 0) {
    $_SESSION['message'] = createAlert(danger, 'Account has already been activated or the URL is invalid!');
  } else {
    $_SESSION['message'] = createAlert(success, 'Your account has been activated!');
    
    // Set the user status to active (active = 1)
    
  }
}

?>