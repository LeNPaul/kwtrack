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
  $output = '';
  var_dump($profiles);
  // Iterate through profiles and append block level buttons to $output
  for ($i = 0; $i < count($profiles); $i++) {
    // Scrape Amazon to grab seller name
    $url = 'https://www.amazon.com/sp?seller=' . $profiles[$i]['accountInfo']['sellerStringId'];
    $doc = hQuery::fromUrl($url, ['Accept' => 'text/html,application/xhtml+xml;q=0.9,*/*;q=0.8']);
    $sellerName = $doc->find("h1#sellerName");

    // Find out which country the profile is from so we can put the country flag in the button
    if ($profiles[$i]['countryCode'] == 'US') {
      // Set USA flag
      $flag = '<span class="flag-icon flag-icon-us"></span> ';
      $output .= '<button type="button" class="btn btn-primary btn-lg btn-block" name="selectedProfile[]" value="' . $profiles[$i]['profileId'] . '">'
      . $flag . ' US - '
      . $sellerName . ' - '
      . $profiles[$i]['accountInfo']['sellerStringId']
      . '</button>';
    }
  }
  return $output;
}

?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>

<div class="container text-center">
	<h2>Select Your Profile</h2>
	<p>Here are the profiles that we detected on your Seller Central account. Please select the profile that you would like to integrate PPCOLOGY with.</p>

	<?php 
	echo displayProfiles($_SESSION['profiles']);
	?>
	
	<script type="text/javascript">
		$(document).ready(function(){
			$("button").click(function(){
				$.ajax({
					type: 'POST',
					dataType: 'text',
					data:{"selectedProfile":$(this).val()},
					url: 'includes/dashpages/main/getprofiles.php',
					success: function(data) {
						alert(data);
					},
					error: function(msg, xhr) {
						alert(msg + ", " + xhr.responseText);
					}
				});
			});
		});
	</script>
</div>
