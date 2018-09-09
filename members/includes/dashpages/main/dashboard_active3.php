<?php
/*
 *  User will see this after selecting their profile.
 *  MWS API Integration
 */

?>

<div class="container text-center">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script type="text/javascript">
		$(document).ready(function(){
			$.ajax({
				type: 'POST',
				dataType: 'text',
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
</div>
