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
$rawCampaignData = $result[2];

$dateArr = [];
$dateArr[] = date("M d");
for ($j = 1; $j < 60; $j++) {
	$dateArr[] = date("M d", strtotime("-".$j." days"));
}
$dateArr = array_reverse($dateArr);

?>

<div class="input-group">
  <div class="input-group-prepend">
    <span class="input-group-text">$</span>
  </div>
  
  <input type="text" class="form-control edit-budget" placeholder="0.20">
  
  <div class="input-group-append">
    <button class="btn btn-outline-secondary btn-edit-budget" type="button">Save</button>
  </div>
</div>

<h2 class="text-center">Campaign Manager</h2>
<div id="campaignRange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 33%">
  <i class="fa fa-calendar"></i>
  <span></span> <i class="fa fa-caret-down"></i>
</div><br>

<nav aria-label="breadcrumb" role="navigation">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><h6 id="bc">All Campaigns</h6></li>
  </ol>
</nav>


<div class="row">
  <div class="col-12 col-sm-12 col-md-12 col-lg-12">
    <button id="select_all" class="btn btn-primary">Select All</button>
    <table id="campaign_manager" class="table table-light table-hover row-border order-column" cellpadding="0" cellspacing="0" border="0" width="100%"></table>
    <table id="adgroup_manager" class="table table-light table-hover row-border order-column" cellpadding="0" cellspacing="0" border="0" width="100%"></table>
    <table id="keyword_manager" class="table table-light table-hover row-border order-column" cellpadding="0" cellspacing="0" border="0" width="100%"></table>
  </div>
</div>

<script>
$(document).ready( function () {
  var dataset  = <?= json_encode($campaignDataFront) ?>;
  var databack = <?= json_encode($campaignDataBack) ?>;
  var user_id  = <?= $user_id ?>;

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
      // Handle and style toggle buttons
      $('.toggle-campaign').bootstrapToggle({
        on: '<i class="fa fa-play"></i>',
        off: "Paused",
        size: "small",
        onstyle: "success",
        offstyle: "primary"
      });
      $(".toggle-campaign-archive").bootstrapToggle({
        off: "Archived",
        size: "mini"
      });
      
      rowClasses = $('#campaign_manager tbody tr').attr("class");

      if (rowClasses.includes("selected")) {
        $('#campaign_manager tbody tr').css('background-color', 'rgba(193, 235, 255, 0.4)');
      } else {
        $('#campaign_manager tbody tr').css('background-color', '#fdfdfe');
      }

      $(".toggle").on("click", function() {
        // FIND OUT HOW TO PASS CAMPAIGN ID TO THIS FUNCTION
        $(this).toggleClass('toggle-selected');
        var campaignName = $(this).parent().next().children(".c_link").text();
        
        console.log(dt.rows('.toggle-selected').data());

        if ($(this).hasClass("off")) {
          console.log('turning toggle on');
        } else {
          console.log('turning toggle off');
        }
        
        toggleActive = $(this).hasClass("off");

        $.ajax({
          type: "POST",
          url: "includes/dashpages/cmanager/helpers/toggle_campaigns.php",
          data: { toggle: toggleActive, campaignName: campaignName, cDataBack: databack, user_id: user_id },
          
          success: function(alertText) {
            swal({
              title: "Success!",
              text: alertText,
              type: "success",
              confirmButtonText: "Close"
            });
            $(this).toggleClass('toggle-selected');
          },
          error: function() {
          
          }
        });
      });

      $(".c_link").on("click", function() {
          var campaignName     = $(this).text();
          var campaignDataBack = <?= json_encode($campaignDataBack) ?>;
          console.log(campaignDataBack);
          dt.destroy();

          $("#bc").text(function(index, currentText) {
            return currentText + " / " + campaignName;
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
            }, // success (campaign manager)

            error: function(msg) {
              alert(msg);
            } //error (campaign manager)

          }); //ajax
        }); //on campaign name click
	  } //drawCallback
	}); //DataTable


  $('#campaign_manager tbody').on('click', 'tr', function() {
	  $(this).toggleClass('selected');

    rowClasses = $(this).attr("class");

    if (rowClasses.includes("selected")) {
      $(this).css('background-color', 'rgba(193, 235, 255, 0.4)');
    } else {
      $(this).css('background-color', '#fdfdfe');
    }

    //console.log( dt.rows('.selected').data() );
	  //alert("clicked");
  });

  $('button#select_all').click(function() {
    $('tr.odd, tr.even').toggleClass('selected');

    asdf = $('#campaign_manager tbody tr').attr("class");
    console.log(asdf);
    console.log(dt.rows('.selected').data());
    dt.draw();
  });



}); //document.ready

  $(function() {

    var start = moment().subtract(59, 'days');
    var end = moment();

    function cb(begin, finish) {
	  //cmUpdate(begin.format('MMM DD'), finish.format('MMM DD'));
      $('#campaignRange span').html(begin.format('MMMM D, YYYY') + ' - ' + finish.format('MMMM D, YYYY'));
    }

    $('#campaignRange').daterangepicker({
      maxDate: moment(),
      minDate: moment().subtract(59, 'days'),
      startDate: start,
      endDate: end,
      ranges: {
        'Today': [moment(), moment()],
        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        'Last 60 days': [moment().subtract(59, 'days'), moment()],
        'This Month': [moment().startOf('month'), moment().endOf('month')],
        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
      }
    }, cb);
	
	$('#campaignRange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
  });
  
  function cmUpdate(startIndex, endIndex) {
	  var dateArr = <?= json_encode($dateArr) ?>;  
	  var campaignData = <?= json_encode($rawCampaignData) ?>;
	  
	  startArr = dateArr.indexOf(startIndex);
	  endArr = dateArr.indexOf(endIndex);
	  
	  
  };
</script>
