<?php
/*
 *  CAMPAIGN MANAGER FOR USERS
 *    Allows users to edit and change all their campaigns in PPCOLOGY.
 */

include './includes/dashpages/cmanager/cm_helper.inc.php';

$user_id = $_SESSION['user_id'];
$refresh_token = $_SESSION['refresh_token'];

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
// $dateArr = array_reverse($dateArr);

?>

<h2 class="text-center">Campaign Manager</h2>
<div id="campaignRange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 33%">
  <i class="fa fa-calendar"></i>
  <span></span> <i class="fa fa-caret-down"></i>
</div><br>

<nav aria-label="breadcrumb" role="navigation">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><h6 id="bc"><a href="javascript:void(0)" class="all_link">All Campaigns</a></h6></li>
  </ol>
</nav>


<div class="row">
  <div class="col-12 col-sm-12 col-md-12 col-lg-12">
    <button id="select_all" class="btn btn-primary">Select All</button>
    <table id="campaign_manager" class="table table-light table-hover row-border order-column" cellpadding="0" cellspacing="0" border="0" width="100%"></table>
  </div>
</div>

<script>
// 1 for campaign, 2 for adgroup(need campaign), 3 for keyword(need adgroup maybe)
var dataTableFlag = 1;
var currentCampaign = "";
var adGroupName = "";
var allCampaigns = "<a href=\"javascript:void(0)\" class=\"all_link\">All Campaigns</a>";

$(document).ready( function () {
  var dataset       = <?= json_encode($campaignDataFront) ?>;
  var databack      = <?= json_encode($campaignDataBack) ?>;
  var user_id       = <?= $user_id ?>;
  var refresh_token = "<?= $refresh_token ?>";
  var profileId     = <?= $_SESSION['profileId'] ?>;
  var adgroupDataBack = null;
  var rawAdgroupData  = null;
  var adGroupDataset = null;
  var keywordDataset = null;
  var keywordDataBack = null;
  var adgrOptions = null;

  //var dt  = $('#campaign_manager').DataTable(
  var campaignOptions =
  {
      // buttons: ['copy'],
      // responsive: true,
      // autoWidth: true,
	  dom: 'l<>ftip',
	  order: [[ 1, "asc" ]],
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
		    { title: "Status", "orderDataType": "dom-text-toggle"},
        { title: "Campaign Name"},
		    { title: "Status" },
        { title: "Budget", "orderDataType": "dom-text"},
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
      // Set dataTableFlag to 1 whenever campaign manager is drawn
        dataTableFlag = 1;

				// Handle and style toggle buttons
			  $('.toggle-campaign').bootstrapToggle({
			    on: '<i class="fa fa-play"></i>',
			    off: '<i class="fa fa-pause"></i>',
			    size: "small",
			    onstyle: "success",
			    offstyle: "primary"
			  });
			  $(".toggle-campaign-archive").bootstrapToggle({
			    off: '',
			    size: "small"
			  });

				// Handle budget changes when textbox is clicked
				$(".input-group input.form-control").on("focus", function() {
					$(this).next().children().show();
				});
				$(".input-group input.form-control").on("blur", function() {
					$(this).next().children().hide(200);
				});
				$('.input-group input.form-control').keypress(function (e) {
					var key = e.which;

					if (key == 13) {
						$(this).next().children("button").click();
						return false;
					}
				});

	  } //drawCallback

  }; //campaignOptions

	var dt  = $('#campaign_manager').DataTable(campaignOptions);

  // Status toggles
  $("#campaign_manager").on("click", ".toggle", function() {
    $(this).toggleClass('toggle-selected');
    var campaignName = $(this).parent().next().children(".c_link").text();

    console.log(dt.rows('.toggle-selected').data());

    if ($(this).hasClass("off")) {
      console.log('turning toggle on');
    } else {
      console.log('turning toggle off');
    }

    toggleActive = $(this).hasClass("off");

    // Toggle campaign w/ AJAX
    $.ajax({
      type: "POST",
      url: "includes/dashpages/cmanager/helpers/toggle_campaigns.php",
      data: {
        toggle: toggleActive,
        campaignName: campaignName,
        cDataBack: databack,
        user_id: user_id,
        refresh_token: refresh_token,
        profileId: profileId
      },

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
        swal({
          title: "Error",
          text: "An error has occurred. Please try again in a few moments.",
          type: "error",
          confirmButtonText: "Close"
        });
      }
    });
  });



	//Handle budget changes when save button is clicked
  $(".btn-edit-budget").on("click", function() {
    var budgetVal = $(this).parent().prev().val();
    // Verify input to check if numeric
    if (!$.isNumeric(budgetVal) || budgetVal < 1) {
      // Error and clear textbox if not numeric
      showNotification('bottom', 'left', 'danger', "Please enter a valid budget value.");
      $(this).parent().prev().val('');
    } else {
      campaignName = $(this).parent().parent().parent().prev().prev().children(".c_link").text();
      $.ajax({
        type: "POST",
        url: "includes/dashpages/cmanager/helpers/change_budget.php",
        data: {
          user_id: user_id,
          campaignName: campaignName,
          cDataBack: databack,
          refresh_token: refresh_token,
          profileId: profileId,
          newBudget: budgetVal
        },

        success: function(alertText) {
          swal({
            title: "Success!",
            text: alertText,
            type: "success",
            confirmButtonText: "Close"
          });
        }
      });
    }
  });

  // Handle selections from the user
  rowClasses = $('#campaign_manager tbody tr').attr("class");

  if (rowClasses.includes("selected")) {
    $('#campaign_manager tbody tr').css('background-color', 'rgba(193, 235, 255, 0.4)');
  } else {
    $('#campaign_manager tbody tr').css('background-color', '#fdfdfe');
  }



  //breadcrumbs ALL CAMPAIGNS click
  $(".breadcrumb").on("click", ".all_link", function() {
    dt.destroy();
	$("#campaign_manager").empty();

	dt = $("#campaign_manager").DataTable(campaignOptions);
	$("#bc").html(allCampaigns);

  });

  $(".breadcrumb").on("click", ".c_link", function() {
	dt.destroy();
	$("#campaign_manager").empty();

	dt = $("#campaign_manager").DataTable(adgrOptions);
	$("#bc").html(allCampaigns + " <b>></b> " + currentCampaign);
  });

  //when user clicks on a campaign link
  $("#campaign_manager").on("click", ".c_link", function() {
	  currentCampaign     = $(this).html();
	  var campaignDataBack = <?= json_encode($campaignDataBack) ?>;
	  console.log(campaignDataBack);
	  dt.destroy();
	  $('#campaign_manager').empty();

	  // Handle breadcrumbs
	  $("#bc").html(function(index, currentText) {
	    return currentText + " <b>></b> " + currentCampaign;
	  });

	  $.ajax({
	    type: "POST",

	    data: {
	      "campaignName"     : currentCampaign,
	      "campaignDataBack" : campaignDataBack
	    },
	    dataType: "text",

	    url: "includes/dashpages/cmanager/helpers/get_adgroups.php",

	    success: function(data){
	      console.log('running...');
	      data = JSON.parse(data);
	      console.log(data);

	      adGroupDataset         = data[0];
	      adgroupDataBack = data[1];
	      rawAdgroupData  = data[2];
	      window.rawAdgroupData = rawAdgroupData;

	      console.log('DATASET: ');
	      console.log(adGroupDataset);
	      //console.log(adgroupDataBack);

	      adgrOptions = {
	        scrollX: true,
	        paging: true,
	        pagingType: "full_numbers",
	        lengthMenu: [
	          [10, 25, 50, 100, -1],
	          [10, 25, 50, 100, "All"]
	        ],
	        data: adGroupDataset,
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
	          // Set dataTableFlag to 2 whenever campaign manager is drawn
	          dataTableFlag = 2;

	          $('td input').bootstrapToggle();



	        } // drawCallback

	      }; // adgrOptions

	      dt = $('#campaign_manager').DataTable(adgrOptions);

	    }, // success (campaign manager)

	    error: function(msg) {
	      alert(msg);
	    } //error (campaign manager)

	  }); //ajax
	}); //on campaign name click

	$("#campaign_manager").on("click", ".ag_link",  function() {
    adgroupName     = $(this).text();

    // backend adgroup data already stored in adgroupDataBack
    dt.destroy();
    $("#campaign_manager").empty();

    // Breadcrumb text. Edit later to include links that go back.
    $("#bc").html(function(index, currentText){
      console.log(currentText);
      return allCampaigns + " <b>></b> <a href=\"javascript:void(0)\" class=\"name c_link\">" + currentCampaign + "</a>" + " <b>></b> " + adgroupName;
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

        keywordDataset  = data[0];
        keywordDataBack = data[1];

        console.log('DATASET: ');
        console.log(keywordDataset);
        console.log(keywordDataBack);

        var kwOptions = {
          scrollX: true,
          paging: true,
          pagingType: "full_numbers",
          lengthMenu: [
              [10, 25, 50, 100, -1],
              [10, 25, 50, 100, "All"]
            ],
          data: keywordDataset,
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
            // Set dataTableFlag to 1 whenever campaign manager is drawn
            dataTableFlag = 3;

            $('td input').bootstrapToggle();

          } // drawCallback (keyword manager)
        }; // kwOptions

        dt = $("#campaign_manager").DataTable(kwOptions);
      }, // success (keyword manager)

      error: function(msg) {
        alert(msg);
      } // error (keyword manager)

    });

  }); // .ag_link on click

  $('#campaign_manager').on('click', 'tr', function() {
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

  $('#select_all').click(function() {
    $('tr.odd, tr.even').toggleClass('selected');

    asdf = $('#campaign_manager tbody tr').attr("class");
    console.log(asdf);
    console.log(dt.rows('.selected').data());
	    if (asdf.includes("selected")) {
      $('#campaign_manager tbody tr').css('background-color', 'rgba(193, 235, 255, 0.4)');
    } else {
      $('#campaign_manager tbody tr').css('background-color', '#fdfdfe');
    }
    dt.draw('page');
	
	
  });

  /* Create an array with the values of all the input boxes in a column. Used for sorting. */
  $.fn.dataTable.ext.order['dom-text'] = function  ( settings, col ) {
    return this.api().column( col, {order:'index'} ).nodes().map( function ( td, i ) {
      return $('input', td).attr('placeholder') * 1;
    } );
  }

  /* Create an array with the values of all the input boxes in a column. Used for sorting. */
  $.fn.dataTable.ext.order['dom-text-toggle'] = function  ( settings, col ) {
    return this.api().column( col, {order:'index'} ).nodes().map( function ( td, i ) {
      return $('input', td).attr('data-value') * 1;
    } );
  }

  /* NOTIFICATIONS */
  function showNotification(from, align, bootstrapColor, message) {
    $.notify({
      icon: "nc-icon nc-bell-55",
      message: message
    }, {
      type: bootstrapColor,
      timer: 4000,
      placement: {
        from: from,
        align: align
      }
    });
  }

  /* DATA RANGE PICKER */

  var start = moment().subtract(59, 'days');
  var end = moment();

  function cb(begin, finish) {
    cmUpdate(begin.format('MMM DD'), finish.format('MMM DD'));
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


  //TODO: figure out how to import adgroup data from datatable when dataTableFlag == 2
  function cmUpdate(startIndex, endIndex) {

    var round   = function (value, decimals) {
      return Number(Math.round(value + 'e' + decimals) + 'e-' + decimals);
    };

    var dateArr = <?= json_encode($dateArr) ?>;

    // If the campaign table is currently drawn
    if (dataTableFlag === 1) {
      var newDtData = [];
      var dtData    = <?= json_encode($rawCampaignData) ?>;

      var startArr   = dateArr.indexOf(startIndex);
      var endArr     = dateArr.indexOf(endIndex);
      var diffOfDays = startArr - endArr + 1;
      console.log('start Arr: ' + startArr, 'end Arr: ' + endArr);

      var impressionsSum = 0;
      var clicksSum      = 0;
      var ctrAvg         = 0;
      var adSpendSum     = 0;
      var avgCpcSumAvg   = 0;
      var unitsSoldSum   = 0;
      var salesSum       = 0;

      for (j = 0; j < dtData.length; j++) {

        for (i = endArr; i <= startArr; i++) {
          impressionsSum += dtData[j][5][i];
          clicksSum      += dtData[j][6][i];
          ctrAvg         += dtData[j][7][i];
          adSpendSum     += dtData[j][8][i];
          avgCpcSumAvg   += dtData[j][9][i];
          unitsSoldSum   += dtData[j][10][i];
          salesSum       += dtData[j][11][i];
          console.log('campaign #' + j + ' - sales: ' + dtData[j][11][i] + ' for day #' + i);

				}

        console.log("ad spend sum: " + adSpendSum, "sales sum: " + salesSum);
        var acos     = (salesSum === 0) ? '-' : round((adSpendSum / salesSum) * 100, 2);
        adSpendSum   = round(adSpendSum, 2);
        salesSum     = round(salesSum, 2);
        ctrAvg       = round(ctrAvg / diffOfDays, 2);
        avgCpcSumAvg = round(avgCpcSumAvg / diffOfDays, 2);

        impressionsSum = (impressionsSum === 0) ? '-' : impressionsSum;
        clicksSum      = (clicksSum === 0) ? '-' : clicksSum;
        ctrAvg         = (ctrAvg === 0) ? '-' : ctrAvg + '%';
        adSpendSum     = (adSpendSum === 0) ? '-' : '$' + adSpendSum;
        avgCpcSumAvg   = (avgCpcSumAvg === 0) ? '-' : '$' + avgCpcSumAvg;
        unitsSoldSum   = (unitsSoldSum === 0) ? '-' : unitsSoldSum;
        salesSum       = (salesSum === 0) ? '-' : '$' + salesSum;

        newDtData.push([
          dtData[j][0],
          dtData[j][1],
          dtData[j][2],
          dtData[j][3],
          dtData[j][4],
          impressionsSum,
          clicksSum,
          ctrAvg,
          adSpendSum,
          avgCpcSumAvg,
          unitsSoldSum,
          salesSum,
          acos]);

        impressionsSum = 0;
        clicksSum      = 0;
        ctrAvg         = 0;
        adSpendSum     = 0;
        avgCpcSumAvg   = 0;
        unitsSoldSum   = 0;
        salesSum       = 0;
      }
      console.log(newDtData);
      dt.clear().rows.add(newDtData).draw();

    }
    // If the adgroup table is being drawn
    else if (dataTableFlag === 2) {

      var newAdgroupData = [];
      var adgroupData    = window.rawAdgroupData;

      console.log(adgroupData);

      var startArr   = dateArr.indexOf(startIndex);
      var endArr     = dateArr.indexOf(endIndex);
      var diffOfDays = startArr - endArr + 1;
      console.log('start Arr: ' + startArr, 'end Arr: ' + endArr);

      var impressionsSum = 0;
      var clicksSum      = 0;
      var ctrAvg         = 0;
      var adSpendSum     = 0;
      var avgCpcSumAvg   = 0;
      var unitsSoldSum   = 0;
      var salesSum       = 0;

      for (j = 0; j < adgroupData.length; j++) {

        for (i = endArr; i <= startArr; i++) {
          impressionsSum += adgroupData[j][4][i];
          clicksSum      += adgroupData[j][5][i];
          ctrAvg         += adgroupData[j][6][i];
          adSpendSum     += adgroupData[j][7][i];
          avgCpcSumAvg   += adgroupData[j][8][i];
          unitsSoldSum   += adgroupData[j][9][i];
          salesSum       += adgroupData[j][10][i];
          console.log('adgroup #' + j + ' - sales: ' + adgroupData[j][10][i] + ' for day #' + i);
        }

        console.log("ad spend sum: " + adSpendSum, "sales sum: " + salesSum);

        var acos     = (salesSum === 0) ? '-' : round((adSpendSum / salesSum) * 100, 2);
        adSpendSum   = round(adSpendSum, 2);
        salesSum     = round(salesSum, 2);
        ctrAvg       = round(ctrAvg / diffOfDays, 2);
        avgCpcSumAvg = round(avgCpcSumAvg / diffOfDays, 2);
        impressionsSum = (impressionsSum === 0) ? '-' : impressionsSum;
        clicksSum      = (clicksSum === 0) ? '-' : clicksSum;
        ctrAvg         = (ctrAvg === 0) ? '-' : ctrAvg + '%';
        adSpendSum     = (adSpendSum === 0) ? '-' : '$' + adSpendSum;
        avgCpcSumAvg   = (avgCpcSumAvg === 0) ? '-' : '$' + avgCpcSumAvg;
        unitsSoldSum   = (unitsSoldSum === 0) ? '-' : unitsSoldSum;
        salesSum       = (salesSum === 0) ? '-' : '$' + salesSum;

        newAdgroupData.push([
          adgroupData[j][0],
          adgroupData[j][1],
          adgroupData[j][2],
          adgroupData[j][3],
          impressionsSum,
          clicksSum,
          ctrAvg,
          adSpendSum,
          avgCpcSumAvg,
          unitsSoldSum,
          salesSum,
          acos]);

        impressionsSum = 0;
        clicksSum      = 0;
        ctrAvg         = 0;
        adSpendSum     = 0;
        avgCpcSumAvg   = 0;
        unitsSoldSum   = 0;
        salesSum       = 0;
      }

      console.log(newAdgroupData);
      dt_adgroups.clear().rows.add(newAdgroupData).draw();

    }
    // If the keyword table is currently drawn
    else if (dataTableFlag === 3) {

      var newKeywordData = [];
      var keywordData    = '';

    }



  }

}); //document.ready

</script>
