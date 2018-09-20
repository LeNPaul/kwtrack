<?php
/* Verifies registered user email, the link to this page is
 * included in the register.php email message
 */
session_start();
require './members/database/pdo.inc.php';


// Make sure email and hash vars aren't empty
if ((isset($_GET['email']) && !empty($_GET['email'])) && (isset($_GET['hash']) && !empty($_GET['hash']))) {
  $email = htmlentities($_GET['email']);
  $hash = htmlentities($_GET['hash']);

  // Select user with matching email and hash, who hasn't verified their account yet (active = 0)
  $sql = "SELECT * FROM users WHERE email='$email' AND hash='$hash' AND active='0'";
  $stmt = $pdo->query($sql);
  $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

  if (count($results) == 0) {
    $_SESSION['message'] = createAlert('danger', 'Account has already been activated or the URL is invalid!');
    header("location: ../login.php");
    exit();
  } else {
    $_SESSION['message'] = createAlert('success', 'Your account has been activated!');

    // Set the user status to active (active = 1)
    $sql = "UPDATE users SET active='1' WHERE email=:email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array("email" => $email));
    $_SESSION['active'] = 1;

    header("location: https://ppcology.io/members/login.php");
    exit();
  }
} else {
  $_SESSION['message'] = createAlert('danger', 'Invalid parameters provided for account verification!');
  header("location: ../login.php");
  exit();
}

?>
