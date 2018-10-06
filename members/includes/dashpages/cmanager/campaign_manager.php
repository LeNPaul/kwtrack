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

<h2 class="text-center">Campaign Manager</h2>
<h6 id="bc">All Campaigns</h6>

<div class="row">
  <div class="col-12 col-sm-12 col-md-12 col-lg-12">
    <table id="campaign_manager" class="table table-light table-hover row-border order-column" cellpadding="0" cellspacing="0" border="0" width="100%"></table>
    <table id="adgroup_manager" class="table table-light table-hover row-border order-column" cellpadding="0" cellspacing="0" border="0" width="100%"></table>
    <table id="keyword_manager" class="table table-light table-hover row-border order-column" cellpadding="0" cellspacing="0" border="0" width="100%"></table>
  </div>
</div>

<script>
$(document).ready( function () {

  var dataset = <?= json_encode($campaignDataFront) ?>;
  console.log(dataset);
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
		    $('td input').bootstrapToggle();

        $(".c_link").on("click", function() {
          var campaignName     = $(this).text();
          var campaignDataBack = <?= json_encode($campaignDataBack) ?>;
          dt.destroy();

          $("#bc").text(function(index, currentText){
            return currentText + " > " + campaignName;
          });

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
              console.log(data);
              data = JSON.parse(data);
              console.log(data);

              var dataset         = data[0];
              var adgroupDataBack = data[1];

              console.log('DATASET: ');
              console.log(dataset);
              //console.log(adgroupDataBack);

              var adgrOptions = {
                scrollX: true,
                paging: true,
                pagingType: "full_numbers",
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                  ],
                data: dataset,
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

                drawCallback: function(settings) {
                  $('td input').bootstrapToggle();

                  $(".ag_link").on("click", function() {

                    var adgroupName     = $(this).text();
                    // backend adgroup data already stored in adgroupDataBack
                    dt_adgroups.destroy();

                    // Breadcrumb text. Edit later to include links that go back.
                    $("#bc").text(function(index, currentText){
                      return currentText + " > " + adgroupName;
                    });

                    $.ajax({
                      type: "POST",

                      data: {
                        "adgroupName"     : adgroupName,
                        "adgroupDataBack" : adgroupDataBack
                      },

                      dataType: "text",

                      url: "includes/dashpages/cmanager/helpers/get_keywords.php",

                      success: function(data) {
                        console.log("running keyword campaign manager section");
                        console.log(data);

                        data            = JSON.parse(data);

                        console.log(data);

                        dataset         = data[0];
                        keywordDataBack = data[1];

                        console.log('DATASET: ');
                        console.log(dataset);
                        console.log(keywordDataBack);

                        var kwOptions = {
                          scrollX: true,
                          paging: true,
                          pagingType: "full_numbers",
                          lengthMenu: [
                              [10, 25, 50, 100, -1],
                              [10, 25, 50, 100, "All"]
                            ],
                          data: dataset,
                          columns: [
                            { title: "Active" },
                            { title: "Keyword" },
                            { title: "Match Type" },
                            { title: "Status" },
                            { title: "Bid" },
                            { title: "Impressions" },
                            { title: "Clicks" },
                            { title: "CTR" },
                            { title: "Ad Spend" },
                            { title: "Avg. CPC" },
                            { title: "Units Sold" },
                            { title: "Sales" },
                            { title: "ACoS" }
                          ],

                          drawCallback: function(settings) {
                            $('td input').bootstrapToggle();

                          } // drawCallback (keyword manager)
                        }; // kwOptions

                        dt_adgroups.destroy();
                        dt_keywords = $("#keyword_manager").DataTable(kwOptions);
                      }, // success (keyword manager)

                      error: function(msg) {
                        alert(msg);
                      } // error (keyword manager)

                    });

                  }); // .ag_link on click

                } // drawCallback

              }; // adgrOptions

              dt.destroy();
              dt_adgroups = $("#adgroup_manager").DataTable(adgrOptions);
            },

            error: function(msg) {
              alert(msg);
            } //error

          }); //ajax
        }); //on Click
	  } //drawCallback
	}); //DataTable


  $('#campaign_manager tbody').on('click', 'tr', function() {
	  $(this).toggleClass('selected');
	  //alert("clicked");
  });



}); //document.ready


</script>
