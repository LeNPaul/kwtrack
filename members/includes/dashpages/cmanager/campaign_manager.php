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

$result = cmGetCampaignData($pdo, $user_id);
$campaignDataFront = $result[0];
$campaignDataBack  = $result[1];

?>

<h2>Campaigns</h2>

<div class="row">
  <div class="col-12 col-sm-12 col-md-12 col-lg-12">
    <table id="campaign_manager" class="table table-light table-hover row-border order-column" cellpadding="0" cellspacing="0" border="0" width="100%"></table>
  </div>
</div>

<script>
$(document).ready( function () {

  var dataset = <?= json_encode($campaignDataFront) ?>;

  var dt = $('#campaign_manager').DataTable(
    {
      // buttons: ['copy'],
      // responsive: true,
      // autoWidth: true,
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
        { title: "Campaign Name"},
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
      ],

	  drawCallback: function(settings) {
		  $('.sorting_1 input').bootstrapToggle();

      $(".c_link").on("click", function() {
        var campaignName     = $(this).text();
        var campaignDataBack = <?= json_encode($campaignDataBack) ?>;

        $.ajax({
          type: "POST",

          data: {
            "campaignName"     : campaignName,
            "campaignDataBack" : campaignDataBack
          },
          dataType: "text",

          url: "includes/dashpages/cmanager/helpers/get_adgroups.php",

          success: function(data){
            console.log('running...');
            data = JSON.parse(data);
            console.log(data);

            var dataset         = data[0][0];
            var adgroupDataBack = data[1];

            console.log('DATASET: ');
            console.log(dataset);
            //console.log(adgroupDataBack);

            var adgrOptions = {
              columns: [
                { title: "Active" },
                { title: "Ad Group Name" },
                { title: "Status" },
                { title: "Default Bid" },
                { title: "Impressions" },
                { title: "Clicks" },
                { title: "CTR" },
                { title: "Ad Spend" },
                { title: "Avg. CPC" },
                { title: "Units Sold" },
                { title: "Sales" },
                { title: "ACoS" }
              ],
              data: dataset
            };

            dt.destroy();
            dt = $("#campaign_manager").DataTable(adgrOptions);
          },

          error: function(msg) {
            alert(msg);
          }

        });
      });

	  }
	}
  );

  $('#campaign_manager tbody').on('click', 'tr', function() {
	  $(this).toggleClass('selected');
	  //alert("clicked");
  });



} );


</script>
