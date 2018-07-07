<?php

if (isset($_POST['id_token'])){
  include_once 'google-api-php-client/vendor/autoload.php';
  $client = new Google_Client([
    'client_id' => '446928133679-t5tnlmec6g3i9ogsfebamb005ka1vd45.apps.googleusercontent.com'
  ]);
  $payload = $client->verifyIdToken($_POST['id_token']);
  if ($payload) {
    print_r($payload);
    exit();
  }else{
    echo 'payload invalid';
    print_r($payload);
    exit();
  }
}

?>