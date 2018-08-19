<?php
// Insert profileID in database for the user and set active level to 3
$profileId = $_POST['selectedProfile[0]'];
$sql = 'UPDATE users SET profileId=:profileId, active=:level WHERE user_id=:user_id';
$stmt = $pdo->prepare($sql);
$stmt->execute(array(
  ':profileId' => $profileId,
  ':level'     => 3,
  'user_id'    => $_SESSION['user_id']
));



// Run campaign importing in background
exec("php import_data.php $refreshToken $user_id > /dev/null &");

// Redirect to dashboard
header('location: ../../../dashboard.php');
exit();
