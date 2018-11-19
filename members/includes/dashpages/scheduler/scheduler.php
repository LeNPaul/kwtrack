<?php

if (!empty($_POST['campaignIdList'])) {
  $_SESSION['campaignIdList'] = $_POST['campaignIdList'];
  header("Location: dashboard?p=as&sp=e");
  exit();
}

?>
<input id="uid" type="hidden" value="<?= $_SESSION['user_id'] ?>" />
<h1>Ad Scheduling</h1>

<table id="campaign_list" class="table table-light table-hover table-bordered order-column" cellpadding="0" cellspacing="0" border="0" width="100%"></table>

<form id="goToEdit" method="POST">
  <button name="campaignIdList" id="campaignIdList" hidden type="submit"></button>
</form>

<script type="text/javascript" src="includes/dashpages/scheduler/assets/js/index.js"></script>
