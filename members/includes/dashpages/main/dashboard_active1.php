<?php
/*
 *  User will see this page if they have activated their account, but have not imported their data yet
 */



?>

<?php
if (isset($_SESSION['message'])) {
  echo $_SESSION['message'];
  unset($_SESSION['message']);
}
?>
<h2>Thank you for activating your account, <?= $_SESSION['first_name']?></h2>
<p>We will now need to import your advertising data from Amazon. Please note that this process may take up to 48 hours to complete.</p>
<p>Please click the Login with Amazon button below.</p>

<a href="https://www.amazon.com/ap/oa?client_id=amzn1.application-oa2-client.4246e0f086e441259742c758f63ca0bf&scope=cpc_advertising:campaign_management&response_type=code&state=208257577110975193121591895857093449424&redirect_uri=https://ppcology.io/members/includes/dashpages/profile/refresh.php" id="lwaBtn">
  <img border="0" alt="Login with Amazon" src="https://images-na.ssl-images-amazon.com/images/G/01/lwa/btnLWA_gold_312x64.png" width="312" height="64" />
</a>

