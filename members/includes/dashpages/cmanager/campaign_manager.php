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
    <!-- <table id="campaign_manager" class="table table-hover table-responsive">
      <thead>
        <tr>
          <th scope="col">Campaigns</th>
          <th scope="col">Impressions</th>
          <th scope="col">Clicks</th>
          <th scope="col">CTR</th>
          <th scope="col">Spend</th>
          <th scope="col">CPC</th>
          <th scope="col">Units Sold</th>
          <th scope="col">Sales</th>
          <th scope="col">ACoS</th>
        </tr>
      </thead>

      <tbody>

      </tbody>

    </table> -->
    <table id="campaign_manager" class="display" width="100%"></table>
  </div>
</div>

<script>
$(document).ready( function () {
  console.log('asdf');
  var dataset = <?= json_encode($campaignData) ?>;
  

  $('#campaign_manager').DataTable(
    paging: true,
    data: dataset,
    columns: [
      { title: "Campaigns" },
      { title: "Impressions" },
      { title: "Clicks" },
      { title: "CTR" },
      { title: "Spend" },
      { title: "CPC" },
      { title: "Units Sold" },
      { title: "Sales" },
      { title: "ACoS" }
    ]
  );

} );
</script>
