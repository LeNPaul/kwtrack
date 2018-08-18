<?php
/*
 *  User will see this page if they have activated and authorized their profiles.
 *  Users will have a refresh token at this point and will have to choose which profile they
 */
require $_SERVER['DOCUMENT_ROOT'] . '/members/node_modules/hquery.php/hquery.php';

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

  $sellerName = $doc->find('h1#sellerName');
  return $sellerName;
}

?>

<div class="container text-center">
  <?php var_dump(displayProfiles($_SESSION['profiles'])) ?>
</div>
