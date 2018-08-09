<?php
/* Password reset process, updates database with new user password */
require './members/database/pdo.inc.php';
session_start();

// Make sure the form is being submitted with method="post"
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  $new_password = password_hash($_POST['newpassword'], PASSWORD_BCRYPT);

  // We get $_POST['email'] and $_POST['hash'] from the hidden input field of reset.php form
  $email = htmlentities($_POST['email']);
  $hash = htmlentities($_POST['hash']);

  $sql = "UPDATE users SET password=:new_password, hash=:hash WHERE email=:email";
  $stmt = $pdo->prepare($sql);
  $results = $stmt->execute(array(
    'new_password' => $new_password,
    'hash'         => $hash,
    'email'        => $email
  ));

  if ( $results ) {

    $_SESSION['message'] = "Your password has been reset successfully!";
    header("location: login.php");

  }
}
?>
