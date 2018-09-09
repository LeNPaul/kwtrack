<?php
/*
 *  User will see this after selecting their profile.
 *  MWS API Integration
 */

?>

<div class="container text-center">
	<h2>Integrate Your Amazon Account</h2>
	<p>In order for PPCOLOGY to optimize your campaigns at its full potential, we
		need to import a few important things from your Amazon account. </p>
	<p>
		Follow the steps below to import your products and sales data.
	</p>
	
</div>

<input type="hidden" id="user_id" value="<?= $_SESSION['user_id'] ?>" />

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script type="text/javascript">
var user_id = $("#user_id").val();
	$(document).ready(function(){
		$.ajax({
			type: 'POST',
			dataType: 'text',
			data: { "user_id": user_id },
			url: 'includes/dashpages/main/import_data.php',
			success: function() {
				alert('success');
			},
			error: function(msg, xhr) {
				alert(msg + ", " + xhr.responseText);
			}
		});
	});
</script>
