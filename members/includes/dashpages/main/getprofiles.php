<?php
session_start();
require '../../../database/pdo.inc.php';
require '../helper.inc.php';
error_reporting(E_ALL); ini_set("error_reporting", E_ALL);

/*
// Insert profileID in database for the user and set active level to 3
$profileId = $_POST['selectedProfile'];
$profileId = $profileId[0];
$sql = 'UPDATE users SET profileId=:profileId, active=:level WHERE user_id=:user_id';
$stmt = $pdo->prepare($sql);
$stmt->execute(array(
  ':profileId' => $profileId,
  ':level'     => 3,
  ':user_id'    => $_SESSION['user_id']
));

// Get refresh token to pass onto import_data.php
$sql = 'SELECT refresh_token FROM users WHERE user_id=' . $_SESSION['user_id'];
$stmt = $pdo->query($sql);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
$refreshToken = $result[0]['refresh_token'];
*/
// Run campaign importing in background [FIX WHEN YOU COME BACK]
//$user_id = $_SESSION['user_id'];

/**
* Launch Background Process
*
* Launches a background process (note, provides no security itself, $call must be sanitized prior to use)
* @param string $call the system call to make
* @author raccettura
*/
function launchBackgroundProcess($call) {
    // Windows
    if(is_windows()){
        pclose(popen('start /b '.$call, 'r'));
    }
    // Some sort of UNIX
    else {
        pclose(popen($call.' /dev/null &', 'r'));
    }
    return true;
}


/**
* Is Windows
*
* Tells if we are running on Windows Platform
* @author raccettura
*/
function is_windows(){
    if(PHP_OS == 'WINNT' || PHP_OS == 'WIN32'){
        return true;
    }
    return false;
}

echo 'running exec <br /><br />';
//exec("php import_data.php > /dev/null 2>&1");
launchBackgroundProcess("php import_data.php");
echo 'finished running exec <br />';


//shell_exec("php import_data.php $refreshToken $user_id $profileId > /dev/null &");

// Redirect to dashboard
header('location: ../../../dashboard.php');
//exit();
