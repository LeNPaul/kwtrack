<?php
/*
 *  User will see this page if they have activated and authorized their profiles.
 *  Users will have a refresh token at this point and will have to choose which profile they
 */
require_once $_SERVER['DOCUMENT_ROOT'] . '/members/node_modules/hquery.php/hquery.php';

/*
 * function displayProfiles(Array $profiles) => html shit
 *    --> Takes array of $profiles and outputs buttons for the user to select
 *
 *      --> Array $profiles - array of profiles for the user
 */

function displayProfiles($profiles) {
  // Scrape Amazon to grab seller name
  $url = 'https://www.amazon.com/sp?seller=' . $profiles[0]['accountInfo']['sellerStringId'];
  $doc = hQuery::fromUrl($url, ['Accept' => 'text/html,application/xhtml+xml;q=0.9,*/*;q=0.8']);
  $sellerName = $doc->find("h1#sellerName");
  $output = '';
  var_dump($profiles);
  // Iterate through profiles and append block level buttons to $output
  for ($i = 0; $i < count($profiles); $i++) {
    // Find out which country the profile is from so we can put the country flag in the button
    if ($profiles[$i]['countryCode'] == 'US') {
      // Set USA flag
      $output .= '<button type="button" class="btn btn-primary btn-lg btn-block" name="selectedProfile[]" value="' . $profiles[$i]['profileID'] . '">US - ' . $sellerName . ' - ' . $profiles[$i]['accountInfo']['sellerStringId'] . '</button>';

    }
  }
}

// Check if profile was selected
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Insert profileID in database for the user
  $profileID = $_POST['selectedProfile'];
  // Update active level to 3

}

?>

<div class="container text-center">
  <?php var_dump($_POST); echo displayProfiles($_SESSION['profiles']); ?>
</div>
