<?php
/*
 *  User will see this after selecting their profile.
 *  MWS API Integration
 */

?>

<div class="container text-center">

</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script type="text/javascript">
var user_id = "<?= $_SESSION['user_id'] ?>";
	$(document).ready(function(){
		$.ajax({
			type: 'POST',
			dataType: 'text',
			url: 'includes/dashpages/main/import_data.php',
			success: function() {
				alert('user id: ' . user_id);
			},
			error: function(msg, xhr) {
				alert(msg + ", " + xhr.responseText);
			}
		});
	});
</script>
