<?php
/*
 *  CAMPAIGN MANAGER FOR USERS
 *    Allows users to edit and change all their campaigns in PPCOLOGY.
 */

 include './includes/dashpages/cmanager/cm_helper.inc.php';

$user_id = $_SESSION['user_id'];
// Check to see if user has any campaign groups
$sql = "SELECT * FROM cgroups WHERE user_id={$user_id}";
$stmt = $pdo->query($sql);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
$cGroupsExist = (count($result) == 0) ? 0 : 1;

$campaignData = cmGetCampaignData($pdo, $user_id);
?>

<h2>Campaigns</h2>

<div class="row">
  <div class="col-12 col-sm-12 col-md-12 col-lg-12">
    <table id="campaign_manager" class="table table-light table-hover row-border order-column nowrap" cellpadding="0" cellsacing="0" border="0" width="100%"></table>
  </div>
</div>

<script>
$(document).ready( function () {
  var dataset = <?= json_encode($campaignData) ?>;

  $('#campaign_manager').DataTable(
    {
      // buttons: ['copy'],
      // responsive: true,
      //autoWidth: true,
      scrollX: true,
      paging: true,
      pagingType: "full_numbers",
      lengthMenu: [
          [10, 25, 50, 100, -1],
          [10, 25, 50, 100, "All"]
        ],
      // fixedColumns: {
      //   leftColumns: 2
      // },
      data: dataset,
      columns: [
		    { title: "Active" },
        { title: "Campaign Name", width: "0"},
		    { title: "Status" },
        { title: "Budget" },
        { title: "Targeting Type" },
        { title: "Impressions" },
        { title: "Clicks" },
        { title: "CTR" },
        { title: "Spend" },
        { title: "CPC" },
        { title: "Units Sold" },
        { title: "Sales" },
        { title: "ACoS" }
      ]
    }
  );

  var cm = $('#campaign_manager').DataTable();
  cm.columns.adjust();

} );
</script>
