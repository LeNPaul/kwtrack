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

<!--

  Radio version of tabs.

  Requirements:
  - not rely on specific IDs for CSS (the CSS shouldn't need to know specific IDs)
  - flexible for any number of unkown tabs [2-6]
  - accessible

  Caveats:
  - since these are checkboxes the tabs not tab-able, need to use arrow keys

  Also worth reading:
  http://simplyaccessible.com/article/danger-aria-tabs/
-->

<div class="tabset">
  <!-- Tab 1 -->
  <input type="radio" name="tabset" id="tab1" aria-controls="marzen" checked>
  <label for="tab1">Märzen</label>
  <!-- Tab 2 -->
  <input type="radio" name="tabset" id="tab2" aria-controls="rauchbier">
  <label for="tab2">Rauchbier</label>
  <!-- Tab 3 -->
  <input type="radio" name="tabset" id="tab3" aria-controls="dunkles">
  <label for="tab3">Dunkles Bock</label>

  <div class="tab-panels">
    <section id="marzen" class="tab-panel">
      <h2>6A. Märzen</h2>
      <p><strong>Overall Impression:</strong> An elegant, malty German amber lager with a clean, rich, toasty and bready malt flavor, restrained bitterness, and a dry finish that encourages another drink. The overall malt impression is soft, elegant, and complex, with a rich aftertaste that is never cloying or heavy.</p>
      <p><strong>History:</strong> As the name suggests, brewed as a stronger “March beer” in March and lagered in cold caves over the summer. Modern versions trace back to the lager developed by Spaten in 1841, contemporaneous to the development of Vienna lager. However, the Märzen name is much older than 1841; the early ones were dark brown, and in Austria the name implied a strength band (14 °P) rather than a style. The German amber lager version (in the Viennese style of the time) was first served at Oktoberfest in 1872, a tradition that lasted until 1990 when the golden Festbier was adopted as the standard festival beer.</p>
  </section>
    <section id="rauchbier" class="tab-panel">
      <h2>6B. Rauchbier</h2>
      <p><strong>Overall Impression:</strong>  An elegant, malty German amber lager with a balanced, complementary beechwood smoke character. Toasty-rich malt in aroma and flavor, restrained bitterness, low to high smoke flavor, clean fermentation profile, and an attenuated finish are characteristic.</p>
      <p><strong>History:</strong> A historical specialty of the city of Bamberg, in the Franconian region of Bavaria in Germany. Beechwood-smoked malt is used to make a Märzen-style amber lager. The smoke character of the malt varies by maltster; some breweries produce their own smoked malt (rauchmalz).</p>
    </section>
    <section id="dunkles" class="tab-panel">
      <h2>6C. Dunkles Bock</h2>
      <p><strong>Overall Impression:</strong> A dark, strong, malty German lager beer that emphasizes the malty-rich and somewhat toasty qualities of continental malts without being sweet in the finish.</p>
      <p><strong>History:</strong> Originated in the Northern German city of Einbeck, which was a brewing center and popular exporter in the days of the Hanseatic League (14th to 17th century). Recreated in Munich starting in the 17th century. The name “bock” is based on a corruption of the name “Einbeck” in the Bavarian dialect, and was thus only used after the beer came to Munich. “Bock” also means “Ram” in German, and is often used in logos and advertisements.</p>
    </section>
  </div>

</div>

<h2 class="text-center">Campaign Manager</h2>

<!-- Top nav bar for adgroups + keywords -->
<ul class="nav nav-pills nav-pills-primary nav-pills-icons justify-content-center" id="cmanager_top_nav" role="tablist">
  <li class="nav-item">
    <a class="nav-link active show" data-toggle="tab" href="#adgroups" role="tablist">
      <i class="now-ui-icons objects_umbrella-13"></i>Ad Groups
    </a>
  </li>

  <li class="nav-item">
    <a class="nav-link" data-toggle="tab" href="#negKeywords" role="tablist">
      <i class="now-ui-icons shopping_shop"></i> Negative Keywords
    </a>
  </li>
</ul>

<!--  Top nav bar contents for each tab -->
<div class="tab-content tab-space tab-subcategories">
  <div class="tab-pane active show" id="adgroups">

		<div>
			<div id="campaignRange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 33%">
				<i class="fa fa-calendar"></i>
				<span></span> <i class="fa fa-caret-down"></i>
			</div>
		</div>
		<br />
		<div>
			<nav aria-label="breadcrumb" id="cmanager_breadcrumbs" role="navigation">
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><h6 id="bc"><a href="javascript:void(0)" class="all_link">All Campaigns</a></h6></li>
				</ol>
			</nav>
		</div>

		<div class="row">
		  <div class="col-12 col-sm-12 col-md-12 col-lg-12">
		    <table id="campaign_manager" class="table table-light table-hover row-border order-column" cellpadding="0" cellspacing="0" border="0" width="100%"></table>
		  </div>
		</div>

  </div>

  <div class="tab-pane" id="negKeywords">
    Efficiently unleash cross-media information without cross-media value. Quickly maximize timely deliverables for real-time schemas.
    <br>
    <br>Dramatically maintain clicks-and-mortar solutions without functional solutions.
  </div>


</div>






<script>
// 1 for campaign, 2 for adgroup(need campaign), 3 for keyword(need adgroup maybe)
var dataTableFlag 	= 1;
var currentCampaign = "";
var adGroupName 		= "";
var allCampaigns 		= "<a href=\"javascript:void(0)\" class=\"all_link\">All Campaigns</a>";


var sleep = function (time) {
  return new Promise( function(resolve){ return setTimeout(resolve, time); } );
};

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
  var rawKeywordData = null;
  var adgrOptions = null;

  //var dt  = $('#campaign_manager').DataTable(
  var campaignOptions =
  {
      // buttons: ['copy'],
      // responsive: true,
      // autoWidth: true,
	  dom: '<"#dt_topBar.row"<"col-md-5" B><"col-md-2"<"#info_selected">><"col-md-2" l><"col-md-3" f>> rt <"row"<"col-md-3"i><"col-md-9"p>>',
	  buttons: [
		{
			extend: 'selectAll',
			className: 'btn-primary'
		},
		{
      extend: 'selectNone',
      text: 'Deselect All',
      className: 'btn-deselect'
    },
		{
			text: 'Bulk Actions',
			className: 'btn-bulk-action',

			action: function (e, dt, node, config) {
				//1 = paused, 2 = enable
        var selectedCampaigns = dt.rows('.selected').data();
        var campaignIndexes   = dt.rows('.selected').indexes();
        var campaignIdArr     = [];
				var c_list 						= [];

				// Populate list of campaign names
				for (i = 0; i < selectedCampaigns.length; i++) {
					c_list.push(selectedCampaigns[i][1].match(/(?<=\>)(.*)(?=\<)/)[0]);
				}
				console.log(c_list);

        console.log("SELECTED CAMPAIGNS:", selectedCampaigns);

			  console.log(selectedCampaigns[0][0]);
			  console.log(selectedCampaigns[0][0].includes('data-value="2"'));
              // Populate list of campaign ID's
              for (i = 0; i < selectedCampaigns.length; i++) {
                var rx         = selectedCampaigns[i][1].match(/id="\d+/g);
                var campaignId = rx[0].replace("id=\"", "");
                campaignIdArr.push(campaignId);
              }

			  const {value : bulkAction} = swal({
				  title: 'Bulk Actions',
				  input: 'select',
				  inputOptions: {
					'addCampaigns' : 'Add To Campaign Group',
					'addKw' : 'Add Keywords',
					'addNegKw' : 'Add Negative Keywords',
					'pauseCampaign' : 'Pause Campaign(s)',
					'enableCampaign' : 'Enable Campaign(s)',
					'archiveCampaign' : 'Archive Campaign(s)',
					'changeBudget' : 'Change Budget'
				  },
				  inputPlaceholder: 'Select a bulk action',
				  confirmButtonClass: "btn-primary",
          cancelButtonClass: "btn-default",
				  confirmButtonColor: '#51cbce',
				  //cancelButtonColor: '#d33',
				  showCancelButton: true,
				  allowOutsideClick: false,
				  allowEnterKey: false,
				  allowEscapeKey: false,
				})
				.then(function(result) {
					if (result.value == 'pauseCampaign') {
						for (j = 0; j < selectedCampaigns.length; j++) {
							if (selectedCampaigns[j][0].includes('data-value="2"')) {
								var campaignName = selectedCampaigns[j][1].match(/(?<=\>)(.*)(?=\<)/)[0];

								$.ajax({
									type: "POST",
									url: "includes/dashpages/cmanager/helpers/toggle_campaigns.php",
									data: {
										toggle: false,
										campaignName: campaignName,
										cDataBack: databack,
										user_id: user_id,
										refresh_token: refresh_token,
										profileId: profileId
									}
								});

								console.log(selectedCampaigns[j]);


								//$($(dt.row(campaignIndexes[j]).node()).find("div")[0]).toggleClass('off');
                console.log($($(dt.row(campaignIndexes[j]).node()).find("div")[0]));
								$($(dt.row(campaignIndexes[j]).node()).find("div")[0]).click();
								selectedCampaigns[j][0] = selectedCampaigns[j][0].replace('data-value="2"', 'data-value="1"');
							}
						}
					}

					else if (result.value == 'enableCampaign') {
						for (j = 0; j < selectedCampaigns.length; j++) {

						  if (selectedCampaigns[j][0].includes('data-value="1"')) {
								var campaignName = selectedCampaigns[j][1].match(/(?<=\>)(.*)(?=\<)/)[0];
								$.ajax({
									type: "POST",
									url: "includes/dashpages/cmanager/helpers/toggle_campaigns.php",
									data: {
										toggle: true,
										campaignName: campaignName,
										cDataBack: databack,
										user_id: user_id,
										refresh_token: refresh_token,
										profileId: profileId
									}
								});

								$($(dt.row(campaignIndexes[j]).node()).find("div")[0]).click();
								selectedCampaigns[j][0] = selectedCampaigns[j][0].replace('data-value="1"', 'data-value="2"');
							}

						}
					}

					else if (result.value == 'addNegKw') {
            $('#c_addNegKw').modal({
							backdrop: 'static',
							keyboard: false
						});

						$("#c_addNegKw_submit").on("click", function() {
							// TODO: Format negKeywordList as specified in bulk_add_neg_keywords.php comments
							//    	 from textarea input.
							var isText				 = ($("#c_addnegKw_text").val()) ? true : false;

							if (isText) {
								var negKeywordList = [];
								var lines					 = $("#c_addnegKw_text").val().split('\n');
								var matchType		 	 = $("#c_addNegKw_matchType").val();

								for (i = 0; i < lines.length; i++) {
									// TODO: Sanitate input with RegEx
									var arr = {
										"campaignId"  : null,
										"keywordText" : lines[i],
										"matchType"   : matchType,
										"state"       : "enabled"
									};
									negKeywordList.push(arr);
								}

								$.ajax({
								  type: "POST",
								  url: "includes/dashpages/cmanager/helpers/bulk_add_neg_keywords.php",

								  data: {
								    campaignList: c_list,
								    cDataBack: databack,
								    user_id: user_id,
								    refresh_token: refresh_token,
								    profileId: profileId,
								    negKeywordList: negKeywordList
								  },

								  success: function(data) {
										console.log(data);
										$.notify({
						          icon: "nc-icon nc-bell-55",
						          message: "Negative keywords have successfully been added to all selected campaigns. Your negative keyword list on PPCOLOGY will update at 1AM tomorrow."
						        },{
						          type: 'success',
						          timer: 2000,
						          placement: {
						            from: 'bottom',
						            align: 'right'
						          }
						        });
								  },

								  error: function(data) {

								  }
								});
							} else {
								$.notify({
				          icon: "nc-icon nc-bell-55",
				          message: "Please enter your new negative keywords."
				        },{
				          type: 'danger',
				          timer: 2000,
				          placement: {
				            from: 'top',
				            align: 'right'
				          }
				        });
							}

						});
          }

		  else if (result.value == 'archiveCampaign') {
			  swal({
				  title: 'Are you sure you want to <b style="color:red;">ARCHIVE</b>?',
				  type: 'warning',
				  confirmButtonText: 'Yes!',
				  confirmButtonColor: '#009925',
				  cancelButtonColor: '#d33',
				  showCancelButton: true,
				  allowOutsideClick: false,
				  allowEnterKey: false,
				  allowEscapeKey: false,
			  })
			  .then(function(result) {
				  if (result) {
					  for (x = 0; x < selectedCampaigns.length; x++) {
						  var campaignName = selectedCampaigns[x][1].match(/(?<=\>)(.*)(?=\<)/)[0];

						  $.ajax({
								type: "POST",
								url: "includes/dashpages/cmanager/helpers/toggle_campaigns.php",
								data: {
									toggle: 'archive',
									campaignName: campaignName,
									cDataBack: databack,
									user_id: user_id,
									refresh_token: refresh_token,
									profileId: profileId
								}
						  });

						  //code to make the status toggle button greyed out
						  //add notif to notify user it went through
					  }
				  }
			  })
		  }
				}) //.then
			} //action
		}
	  ],
	  //TODO: dont make this multi, use row().select() and trigger when row clicked
	  select: {
          style: 'multi'
        },
        language: {
          select: {
            rows: ""
          }
        },
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
        { title: "Budget", "orderDataType": "dom-text"},
        { title: "Targeting Type" },
        { title: "Impressions" },
        { title: "Clicks" },
        { title: "CTR" },
        { title: "Spend" },
        { title: "CPC" },
        { title: "Units Sold" },
        { title: "Sales" },
		{ title: "CR" },
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
	$(".btn-deselect").css("visibility", "hidden");
	$(".btn-bulk-action").css("visibility", "hidden");

	//handle select all and deselect all
	$("body").on("mouseup", function() {

    sleep(50).then(function() {
      var campaignsSelected = dt.rows( '.selected' );
      if (dt.rows( '.selected' ).any()) {
        //$(".btn-scheduler").css("visibility", "visible");
        $(".btn-deselect").css("visibility", "visible");
		    $(".btn-bulk-action").css("visibility", "visible");

        if (campaignsSelected[0].length === 1) {
          $("#info_selected").text(campaignsSelected[0].length + " campaign selected");
        } else {
          $("#info_selected").text(campaignsSelected[0].length + " campaigns selected");
        }
      } else {
        //$(".btn-scheduler").css("visibility", "hidden");
        $(".btn-deselect").css("visibility", "hidden");
		    $(".btn-bulk-action").css("visibility", "hidden");

        $("#info_selected").text("");
      }
    });

  });

  // Status toggles
  $("#campaign_manager").on("click", ".toggle", function() {
    var campaignName = $(this).parent().next().children(".c_link").text();

    toggleActive = $(this).hasClass("off");
    (toggleActive) ? $(this).children("input").attr("data-value", 2) : $(this).children("input").attr("data-value", 1);

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
        $.notify({
          icon: "nc-icon nc-bell-55",
          message: alertText
        },{
          type: 'success',
          timer: 2000,
          placement: {
            from: 'bottom',
            align: 'right'
          }
        });
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
	    return currentText + " <b>/</b> " + currentCampaign;
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
			dom: '<"#dt_topBar.row"<"col-md-5" B><"col-md-2"<"#info_selected">><"col-md-2" l><"col-md-3" f>>rt<"row"<"col-md-3"i><"col-md-9"p>>',
			buttons: [
				{
					extend: 'selectAll',
					className: 'btn-primary'
				},
				{
					extend: 'selectNone',
					text: 'Deselect All',
					className: 'btn-deselect'
				},
				{
					text: 'Bulk Actions',
					className: 'btn-bulk-action',

					action: function (e, dt, node, config) {
						var selectedAdgroups = dt.rows ( '.selected' ).data();
						var adgroupIndexes = dt.rows('.selected').indexes();
						var adgroupIdArr = [];
						// Populate list of campaign ID's
						for (i = 0; i < selectedAdgroups.length; i++) {
							console.log(selectedAdgroups[i][1]);
							var rx         = selectedAdgroups[i][1].match(/id="\d+/g);
							var adgroupId = rx[0].replace("id=\"", "");
							adgroupIdArr.push(adgroupId);
						}

						const {value : bulkAction} = swal({
							title: 'Bulk Actions',
							input: 'select',
							inputOptions: {
								'changeDefaultBid' : 'Change Default Bid',
								'addKw' : 'Add Keywords',
								'addNegKw' : 'Add Negative Keywords',
								'pauseAdgroup' : 'Pause Adgroup(s)',
								'enableAdgroup' : 'Enable Adgroups(s)',
								'archiveAdgroup' : 'Archive Adgroups(s)'
							},
							inputPlaceholder: 'Select a bulk action',
							confirmButtonClass: "btn-success",
							cancelButtonClass: "btn-secondary",
							confirmButtonColor: '#009925',
							cancelButtonColor: '#d33',
							showCancelButton: true,
							allowOutsideClick: false,
							allowEnterKey: false,
							allowEscapeKey: false,
						})
						.then(function(result) {
							if (result.value == 'pauseAdgroup') {
								for (j = 0; j < selectedAdgroups.length; j++) {
									if (selectedAdgroups[j][0].includes('data-value="2"')) {
										var adgroupName = selectedAdgroups[j][1].match(/(?<=\>)(.*)(?=\<)/)[0];
										$.ajax({
											type: "POST",
											url: "includes/dashpages/cmanager/helpers/toggle_adgroups.php",
											data: {
												toggle: false,
												adgroupName: adgroupName,
												adgroupDataBack: databack,
												user_id: user_id,
												refresh_token: refresh_token,
												profileId: profileId
											},
										});

										$($(dt.row(adgroupIndexes[j]).node()).find("div")[0]).click();
										selectedAdgroups[j][0] = selectedAdgroups[j][0].replace('data-value="2"', 'data-value="1"');
									}
								}
							}
							else if (result.value == 'enableAdgroup') {
								for (j = 0; j < selectedAdgroups.length; j++) {
									if (selectedAdgroups[j][0].includes('data-value="1"')) {
										var adgroupName = selectedAdgroups[j][1].match(/(?<=\>)(.*)(?=\<)/)[0];
										$.ajax({
											type: "POST",
											url: "includes/dashpages/cmanager/helpers/toggle_adgroups.php",
											data: {
												toggle: true,
												adgroupName: adgroupName,
												adgroupDataBack: databack,
												user_id: user_id,
												refresh_token: refresh_token,
												profileId: profileId
											},
										});

										$($(dt.row(adgroupIndexes[j]).node()).find("div")[0]).click();
										selectedAdgroups[j][0] = selectedAdgroups[j][0].replace('data-value="1"', 'data-value="2"');
									}
								}
							}
							else if (result.value == 'archiveAdgroup') {
								swal({
									title: 'Are you sure you want to <b style="color:red;">ARCHIVE</b>?',
									type: 'warning',
									confirmButtonText: 'Yes!',
									confirmButtonColor: '#009925',
									cancelButtonColor: '#d33',
									showCancelButton: true,
									allowOutsideClick: false,
									allowEnterKey: false,
									allowEscapeKey: false,
								})
								.then(function(result) {
									if (result) {
										for (x = 0; x < selectedAdgroups.length; x++) {
											var adgroupName = selectedAdgroups[x][1].match(/(?<=\>)(.*)(?=\<)/)[0];
											$.ajax({
												type: "POST",
												url: "includes/dashpages/cmanager/helpers/toggle_adgroups.php",
												data: {
													toggle: 'archive',
													adgroupName: adgroupName,
													adgroupDataBack: databack,
													user_id: user_id,
													refresh_token: refresh_token,
													profileId: profileId
												},
											});

											//code to make status toggle button greyed out
											//add notif to notify user it went through
										}
									}
								})
							}
						})
					}
				}
			],
			select: {
				style: 'multi'
			},
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
	          { title: "Default Bid" },
	          { title: "Impressions" },
	          { title: "Clicks" },
	          { title: "CTR" },
	          { title: "Ad Spend" },
	          { title: "Avg. CPC" },
	          { title: "Units Sold" },
	          { title: "Sales" },
			  { title: "CR" },
	          { title: "ACoS" }
	        ],

	        drawCallback: function(settings) {
	          // Set dataTableFlag to 2 whenever campaign manager is drawn
	          dataTableFlag = 2;

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


	        } // drawCallback

	      }; // adgrOptions

	      dt = $('#campaign_manager').DataTable(adgrOptions);
		  $(".btn-deselect").css("visibility", "hidden");
		  $(".btn-bulk-action").css("visibility", "hidden");

	    }, // success (campaign manager)

	    error: function(msg) {
	      alert(msg);
	    } //error (campaign manager)

	  }); //ajax
	}); //on campaign name click

  //when user clicks on an adgroup link
  $("#campaign_manager").on("click", ".ag_link",  function() {
    adgroupName     = $(this).text();

    // backend adgroup data already stored in adgroupDataBack
    dt.destroy();
    $("#campaign_manager").empty();

    // Breadcrumb text. Edit later to include links that go back.
    $("#bc").html(function(index, currentText) {
      console.log(currentText);
      return allCampaigns + " <b>/</b> <a href=\"javascript:void(0)\" class=\"name c_link\">" + currentCampaign + "</a>" + " <b>/</b> " + adgroupName;
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

        data            = JSON.parse(data);

        keywordDataset  = data[0];
        keywordDataBack = data[1];
		rawKeywordData  = data[2];
		window.rawKeywordData = rawKeywordData;

        console.log('DATASET: ');
        console.log(keywordDataset);
        console.log(keywordDataBack);
		console.log(rawKeywordData);

        var kwOptions = {
		  dom: '<"#dt_topBar.row"<"col-md-5" B><"col-md-2"<"#info_selected">><"col-md-2" l><"col-md-3" f>>rt<"row"<"col-md-3"i><"col-md-9"p>>',
		  buttons: [
			{
				extend: 'selectAll',
				className: 'btn-primary'
			},
			{
				extend: 'selectNone',
				text: 'Deselect All',
				className: 'btn-deselect'
			},
			{
				text: 'Bulk Actions',
				className: 'btn-bulk-action',

				action: function (e, dt, node, config) {
					var selectedKeywords = dt.rows ( '.selected' ).data();
					var keywordIndexes = dt.rows('.selected').indexes();
					var keywordIdArr = [];
					// Populate list of campaign ID's
					for (i = 0; i < selectedKeywords.length; i++) {
						console.log(selectedKeywords[i][1]);
						var rx         = selectedKeywords[i][1].match(/id="\d+/g);
						var keywordId = rx[0].replace("id=\"", "");
						keywordIdArr.push(keywordId);
					}

					const {value : bulkAction} = swal({
						title: 'Bulk Actions',
						input: 'select',
						inputOptions: {
							'changeBid' : 'Change Bid',
							'pauseKeyword' : 'Pause Keyword(s)',
							'enableKeyword' : 'Enable Keyword(s)',
							'archiveKeyword' : 'Archive Keyword(s)'
						},
						inputPlaceholder: 'Select a bulk action',
						confirmButtonClass: "btn-success",
						cancelButtonClass: "btn-secondary",
						confirmButtonColor: '#009925',
						cancelButtonColor: '#d33',
						showCancelButton: true,
						allowOutsideClick: false,
						allowEnterKey: false,
						allowEscapeKey: false,
					})
					.then(function(result) {
						if (result.value == 'pauseKeyword') {
							for (j = 0; j < selectedKeywords.length; j++) {
								if (selectedKeywords[j][0].includes('data-value="2"')) {
									var keywordName = selectedKeywords[j][1].match(/(?<=\>)(.*)(?=\<)/)[0];
									$.ajax({
										type: "POST",
										url: "includes/dashpages/cmanager/helpers/toggle_keywords.php",
										data: {
											toggle: false,
											keywordName: keywordName,
											keywordDataBack: keywordDataBack,
											user_id: user_id,
											refresh_token: refresh_token,
											profileId: profileId
										},
									});

									$($(dt.row(keywordIndexes[j]).node()).find("div")[0]).click();
									selectedKeywords[j][0] = selectedKeywords[j][0].replace('data-value="2"', 'data-value="1"');
								}
							}
						}
						else if (result.value == 'enableKeyword') {
							for (j = 0; j < selectedKeywords.length; j++) {
								if (selectedKeywords[j][0].includes('data-value="1"')) {
									var keywordName = selectedKeywords[j][1].match(/(?<=\>)(.*)(?=\<)/)[0];
									$.ajax({
										type: "POST",
										url: "includes/dashpages/cmanager/helpers/toggle_keywords.php",
										data: {
											toggle: true,
											keywordName: keywordName,
											keywordDataBack: keywordDataBack,
											user_id: user_id,
											refresh_token: refresh_token,
											profileId: profileId
										},
									});

									$($(dt.row(keywordIndexes[j]).node()).find("div")[0]).click();
									selectedKeywords[j][0] = selectedKeywords[j][0].replace('data-value="1"', 'data-value="2"');
								}
							}
						}
						else if (result.value == 'archiveKeyword') {
						  swal({
							  title: 'Are you sure you want to <b style="color:red;">ARCHIVE</b>?',
							  type: 'warning',
							  confirmButtonText: 'Yes!',
							  confirmButtonColor: '#009925',
							  cancelButtonColor: '#d33',
							  showCancelButton: true,
							  allowOutsideClick: false,
							  allowEnterKey: false,
							  allowEscapeKey: false,
						  })
						  .then(function(result) {
							  if (result) {
								  for (x = 0; x < selectedKeywords.length; x++) {
									  var keywordName = selectedKeywords[x][1].match(/(?<=\>)(.*)(?=\<)/)[0];
									  $.ajax({
										type: "POST",
										url: "includes/dashpages/cmanager/helpers/toggle_keywords.php",
										data: {
											toggle: 'archive',
											keywordName: keywordName,
											keywordDataBack: keywordDataBack,
											user_id: user_id,
											refresh_token: refresh_token,
											profileId: profileId
										}
									  });

									//code to make status toggle button greyed out
									//add notif to notify user it went through
								  }
							  }
						  })
		  }
					})
				}
			}
		  ],
		  select: {
			  style: "multi"
		  },
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
            { title: "Bid" },
            { title: "Impressions" },
            { title: "Clicks" },
            { title: "CTR" },
            { title: "Ad Spend" },
            { title: "Avg. CPC" },
            { title: "Units Sold" },
            { title: "Sales" },
			{ title: "CR" },
            { title: "ACoS" }
          ],

          drawCallback: function(settings) {
            // Set dataTableFlag to 1 whenever campaign manager is drawn
            dataTableFlag = 3;

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

          } // drawCallback (keyword manager)
        }; // kwOptions

        dt = $("#campaign_manager").DataTable(kwOptions);
      }, // success (keyword manager)

      error: function(msg) {
        alert(msg);
      } // error (keyword manager)

    });

  }); // .ag_link on click

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
	  var conversionRate = 0;

      for (j = 0; j < dtData.length; j++) {

        for (i = endArr; i <= startArr; i++) {
          impressionsSum += dtData[j][5][i];
          clicksSum      += dtData[j][6][i];
          ctrAvg         += dtData[j][7][i];
          adSpendSum     += dtData[j][8][i];
          avgCpcSumAvg   += dtData[j][9][i];
          unitsSoldSum   += dtData[j][10][i];
          salesSum       += dtData[j][11][i];
		}
;
        var acos     = (salesSum === 0) ? '-' : round((adSpendSum / salesSum) * 100, 2);
        adSpendSum   = round(adSpendSum, 2);
        salesSum     = round(salesSum, 2);
        ctrAvg       = round(ctrAvg / diffOfDays, 2);
        avgCpcSumAvg = round(avgCpcSumAvg / diffOfDays, 2);
		conversionRate = (clicksSum === 0) ? '-' : round((unitsSoldSum / clicksSum) * 100, 2);

        impressionsSum = (impressionsSum === 0) ? '-' : impressionsSum;
        clicksSum      = (clicksSum === 0) ? '-' : clicksSum;
        ctrAvg         = (ctrAvg === 0) ? '-' : ctrAvg + '%';
        adSpendSum     = (adSpendSum === 0) ? '-' : '$' + adSpendSum;
        avgCpcSumAvg   = (avgCpcSumAvg === 0) ? '-' : '$' + avgCpcSumAvg;
        unitsSoldSum   = (unitsSoldSum === 0) ? '-' : unitsSoldSum;
        salesSum       = (salesSum === 0) ? '-' : '$' + salesSum;
		conversionRate = (conversionRate === 0) ? '-' : conversionRate;

        newDtData.push([
          dtData[j][0],
          dtData[j][1],
          dtData[j][3],
          dtData[j][4],
          impressionsSum,
          clicksSum,
          ctrAvg,
          adSpendSum,
          avgCpcSumAvg,
          unitsSoldSum,
          salesSum,
		  conversionRate,
          acos]);

        impressionsSum = 0;
        clicksSum      = 0;
        ctrAvg         = 0;
        adSpendSum     = 0;
        avgCpcSumAvg   = 0;
        unitsSoldSum   = 0;
        salesSum       = 0;
		conversionRate = 0;
      }

      dt.clear().rows.add(newDtData).draw();

    }
    // If the adgroup table is being drawn
    else if (dataTableFlag === 2) {

      var newAdgroupData = [];
      var adgroupData    = window.rawAdgroupData;

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
	  var conversionRate = 0;

      for (j = 0; j < adgroupData.length; j++) {

        for (i = endArr; i <= startArr; i++) {
          impressionsSum += adgroupData[j][4][i];
          clicksSum      += adgroupData[j][5][i];
          ctrAvg         += adgroupData[j][6][i];
          adSpendSum     += adgroupData[j][7][i];
          avgCpcSumAvg   += adgroupData[j][8][i];
          unitsSoldSum   += adgroupData[j][9][i];
          salesSum       += adgroupData[j][10][i];
        }

        var acos     = (salesSum === 0) ? '-' : round((adSpendSum / salesSum) * 100, 2);
        adSpendSum   = round(adSpendSum, 2);
        salesSum     = round(salesSum, 2);
        ctrAvg       = round(ctrAvg / diffOfDays, 2);
        avgCpcSumAvg = round(avgCpcSumAvg / diffOfDays, 2);
		conversionRate = (clicksSum === 0) ? '-' : round((unitsSoldSum / clicksSum) * 100, 2);

        impressionsSum = (impressionsSum === 0) ? '-' : impressionsSum;
        clicksSum      = (clicksSum === 0) ? '-' : clicksSum;
        ctrAvg         = (ctrAvg === 0) ? '-' : ctrAvg + '%';
        adSpendSum     = (adSpendSum === 0) ? '-' : '$' + adSpendSum;
        avgCpcSumAvg   = (avgCpcSumAvg === 0) ? '-' : '$' + avgCpcSumAvg;
        unitsSoldSum   = (unitsSoldSum === 0) ? '-' : unitsSoldSum;
        salesSum       = (salesSum === 0) ? '-' : '$' + salesSum;
		conversionRate = (clicksSum === 0) ? '-' : conversionRate;

        newAdgroupData.push([
          adgroupData[j][0],
          adgroupData[j][1],
          adgroupData[j][3],
          impressionsSum,
          clicksSum,
          ctrAvg,
          adSpendSum,
          avgCpcSumAvg,
          unitsSoldSum,
          salesSum,
		  conversionRate,
          acos]);

        impressionsSum = 0;
        clicksSum      = 0;
        ctrAvg         = 0;
        adSpendSum     = 0;
        avgCpcSumAvg   = 0;
        unitsSoldSum   = 0;
        salesSum       = 0;
		conversionRate = 0;
      }

	  dt.clear().rows.add(newAdgroupData).draw();
    }
    // If the keyword table is currently drawn
    else if (dataTableFlag === 3) {

      var newKeywordData = [];
      var keywordData    = window.rawKeywordData;

	  var startArr = dateArr.indexOf(startIndex);
	  var endArr = dateArr.indexOf(endIndex);
	  var diffOfDays = startArr - endArr + 1;
      console.log('start Arr: ' + startArr, 'end Arr: ' + endArr);

	  var impressionsSum = 0;
      var clicksSum      = 0;
      var ctrAvg         = 0;
      var adSpendSum     = 0;
      var avgCpcSumAvg   = 0;
      var unitsSoldSum   = 0;
      var salesSum       = 0;
	  var conversionRate = 0;

	  for (x = 0; x < keywordData.length; x++) {
		  for (y = endArr; y <= startArr; y++) {
			  impressionsSum += keywordData[x][5][y];
			  clicksSum 	 += keywordData[x][6][y];
			  ctrAvg 		 += keywordData[x][7][y];
			  adSpendSum 	 += keywordData[x][8][y];
			  avgCpcSumAvg 	 += keywordData[x][9][y];
			  unitsSoldSum 	 += keywordData[x][10][y];
			  salesSum 		 += keywordData[x][11][y];
		  }

		var acos     = (salesSum === 0) ? '-' : round((adSpendSum / salesSum) * 100, 2);
		adSpendSum   = round(adSpendSum, 2);
		salesSum     = round(salesSum, 2);
		ctrAvg       = round(ctrAvg / diffOfDays, 2);
		avgCpcSumAvg = round(avgCpcSumAvg / diffOfDays, 2);
		conversionRate = (clicksSum === 0) ? '-' : round((unitsSoldSum / clicksSum) * 100, 2);

		impressionsSum = (impressionsSum === 0) ? '-' : impressionsSum;
        clicksSum      = (clicksSum === 0) ? '-' : clicksSum;
        ctrAvg         = (ctrAvg === 0) ? '-' : ctrAvg + '%';
        adSpendSum     = (adSpendSum === 0) ? '-' : '$' + adSpendSum;
        avgCpcSumAvg   = (avgCpcSumAvg === 0) ? '-' : '$' + avgCpcSumAvg;
        unitsSoldSum   = (unitsSoldSum === 0) ? '-' : unitsSoldSum;
        salesSum       = (salesSum === 0) ? '-' : '$' + salesSum;
		conversionRate = (clicksSum === 0) ? '-' : conversionRate;

		newKeywordData.push([
			keywordData[x][0],
			keywordData[x][1],
			keywordData[x][3],
			keywordData[x][4],
			impressionsSum,
			clicksSum,
			ctrAvg,
			adSpendSum,
			avgCpcSumAvg,
			unitsSoldSum,
			salesSum,
			conversionRate,
			acos
		]);

		impressionsSum = 0;
        clicksSum      = 0;
        ctrAvg         = 0;
        adSpendSum     = 0;
        avgCpcSumAvg   = 0;
        unitsSoldSum   = 0;
        salesSum       = 0;
		conversionRate = 0;
	  }

	  dt.clear().rows.add(newKeywordData).draw();
    }



  }

}); //document.ready

</script>

<div class="modal fade" id="c_addNegKw" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h5 class="modal-title" id="exampleModalLabel">Add Negative Keywords</h5>
      </div>
      <div class="modal-body">
				<label for="c_addNegKw_matchType">Match Type</label>
				<select class="form-control" id="c_addNegKw_matchType">
		      <option value="negativeExact">Negative Exact</option>
		      <option value="negativePhrase">Negative Phrase</option>
		    </select>
				<hr />
				<label for="c_addnegKw_text">Negative Keyword List</label>
        <textarea class="form-control neg_kw" id="c_addnegKw_text" placeholder="Enter negative keywords separated by a new line" rows="15"></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" id="c_addNegKw_submit" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>
