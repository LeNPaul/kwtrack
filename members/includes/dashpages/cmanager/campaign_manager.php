<h2 class="text-center">Campaign Manager</h2>

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

<div class="tabset">
  <!-- Tab 1 -->
  <input type="radio" name="tabset" id="tab1" aria-controls="cmanager" checked>
  <label for="tab1">Ad Groups</label>
  <!-- Tab 2 -- TODO: FIX BUG WHERE ARROW KEY CAN NAVIGATE TO HIDDEN TAB -->
  <input type="radio" name="tabset" id="tab2" aria-controls="neg_keywords">
  <label id="neg_keywords_tab" for="tab2" style="visibility:hidden;">Negative Keywords</label>
  
  <div class="tab-panels">
    <section id="cmanager" class="tab-panel">
      <div class="row">
        <div class="col-12 col-sm-12 col-md-12 col-lg-12">
          <table id="campaign_manager" class="table table-light table-hover row-border order-column" cellpadding="0" cellspacing="0" border="0" width="100%"></table>
        </div>
      </div>
    </section>
    
    <section id="neg_keywords" class="tab-panel">
      <div class="row">
        <div class="col-12 col-sm-12 col-md-12 col-lg-12">
          <table id="neg_keyword_table" class="table table-light table-hover row-border order-column" cellpadding="0" cellspacing="0" border="0" width="100%"></table>
        </div>
      </div>
    </section>
  </div>
</div>

<script>
  var negKwTableFlag	= 0;
  var currentCampaign = "";
  var adGroupName 		= "";
  var allCampaigns 		= "<a href=\"javascript:void(0)\" class=\"all_link\">All Campaigns</a>";

  var sleep = function (time) {
    return new Promise( function(resolve){ return setTimeout(resolve, time); } );
  };

  $(document).ready( function () {

    // Holds the data table variable
    var dt = null;
    // Used to keep track of the current breadcrumbs
    var breadcrumbs = [];

    var updateBreadcrumbs = function(){

      var html = allCampaigns;

      for (var i = 0; i < breadcrumbs.length; i++) {
        html += " <b>/</b> ";
        var breadcrumb = breadcrumbs[i];
        // Check if this is the last breadcrumb
        if (i == breadcrumbs.length - 1){
          html += breadcrumb.name;
        }else{
          // Only show a link when not the last breadcrumb
          html += "<a href=\"javascript:void(0)\" class=\"name " +
            breadcrumb.linkClass + "\" id=\"" + breadcrumb.id + "\">" + breadcrumb.name + "</a>";
        }
      }

      // Update the html
      $("#bc").html(html);
    };

    /* START: DATA RANGE PICKER */
    var start = moment().subtract(59, 'days');
    var end = moment();

    var formatDate = function(begin, finish) {
      $('#campaignRange span').html(begin.format('MMMM D, YYYY') + ' - ' + finish.format('MMMM D, YYYY'));
    };

    var datePicker = $('#campaignRange').daterangepicker({
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
    }, function(begin, finish){
      formatDate(begin, finish);
      // IMPORTANT: This reload function will use the ajax options
      // supplied by the datatable to get the latest data from the server.
      // This allows you to also update the database and retrieve new data
      // in the datatable without recreating it. This will be useful when
      // updating status, default bids, etc...
      dt.ajax.reload();
    });
    formatDate(start, end);
    /* END: DATA RANGE PICKER */

    var clearTable = function(){
      if (dt) {
        dt.destroy();
        $("#campaign_manager").empty();
        dt = null;
      }
    };

    /* Start: initCampaignsTable */
    var initCampaignsTable = function(){

      var campaignOptions = {
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
              var c_list            = [];

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

              const {value: bulkAction} = swal({
                title: 'Bulk Actions',
                input: 'select',
                inputOptions: {
                  'addCampaigns': 'Add To Campaign Group',
                  'addKw': 'Add Keywords',
                  'addNegKw': 'Add Negative Keywords',
                  'pauseCampaign': 'Pause Campaign(s)',
                  'enableCampaign': 'Enable Campaign(s)',
                  'archiveCampaign': 'Archive Campaign(s)',
                  'changeBudget': 'Change Budget'
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
                .then(function (result) {
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

                    $("#c_addNegKw_submit").on("click", function () {
                      // TODO: Format negKeywordList as specified in bulk_add_neg_keywords.php comments
                      //    	 from textarea input.
                      var isText = ($("#c_addnegKw_text").val()) ? true : false;

                      if (isText) {
                        var negKeywordList = [];
                        var lines          = $("#c_addnegKw_text").val().split('\n');
                        var matchType      = $("#c_addNegKw_matchType").val();

                        for (i = 0; i < lines.length; i++) {
                          // TODO: Sanitate input with RegEx
                          var arr = {
                            "campaignId": null,
                            "keywordText": lines[i],
                            "matchType": matchType,
                            "state": "enabled"
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

                          success: function (data) {
                            console.log(data);
                            $.notify({
                              icon: "nc-icon nc-bell-55",
                              message: "Negative keywords have successfully been added to all selected campaigns. Your negative keyword list on PPCOLOGY will update at 1AM tomorrow."
                            }, {
                              type: 'success',
                              timer: 2000,
                              placement: {
                                from: 'bottom',
                                align: 'right'
                              }
                            });
                          },

                          error: function (data) {

                          }
                        });
                      } else {
                        $.notify({
                          icon: "nc-icon nc-bell-55",
                          message: "Please enter your new negative keywords."
                        }, {
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
                      .then(function (result) {
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
        order: [[1, "asc"]],
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
        columns: [
          {title: "Status", "orderDataType": "dom-text-toggle"},
          {title: "Campaign Name"},
          {title: "Budget", "orderDataType": "dom-text"},
          {title: "Targeting Type"},
          {title: "Impressions"},
          {title: "Clicks"},
          {title: "CTR"},
          {title: "Spend"},
          {title: "CPC"},
          {title: "Units Sold"},
          {title: "Sales"},
          {title: "CR"},
          {title: "ACoS"}
        ],

        drawCallback: function (settings) {
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
          $(".input-group input.form-control").on("focus", function () {
            $(this).next().children().show();
          });
          $(".input-group input.form-control").on("blur", function () {
            $(this).next().children().hide(200);
          });
          $('.input-group input.form-control').keypress(function (e) {
            var key = e.which;

            if (key == 13) {
              $(this).next().children("button").click();
              return false;
            }
          });

        }, //drawCallback
        ajax: {
          url: 'includes/dashpages/cmanager/helpers/get_data.php',
          data: function(){
            var drp = datePicker.data('daterangepicker');
            // Get the start and end date
            return {
              start: drp.startDate.toJSON(),
              end: drp.endDate.toJSON()
            };
          }
        }
      }; //campaignOptions

      clearTable();
      dt = $('#campaign_manager').DataTable(campaignOptions);
      $(".btn-deselect").css("visibility", "hidden");
      $(".btn-bulk-action").css("visibility", "hidden");

      // Hide negative keywords tab
      $("#neg_keywords_tab").attr("style", "visibility: hidden");

      // Clear the breadcrumbs and update them
      breadcrumbs = [];
      updateBreadcrumbs();
    };

    // Initialize the table on startup
    initCampaignsTable();

    /* End: initCampaignsTable */

    /* Start: initAdGroupsTable */
    var initAdGroupsTable = function(campaignName, campaignId){

      var adGroupOptions = {
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


        }, // drawCallback
        ajax: {
          url: 'includes/dashpages/cmanager/helpers/get_data.php',
          data: function(){
            var drp = datePicker.data('daterangepicker');
            // Get the start and end date
            return {
              campaignId: campaignId,
              start: drp.startDate.toJSON(),
              end: drp.endDate.toJSON()
            };
          }
        }
      }; // adgrOptions


      clearTable();
      dt = $('#campaign_manager').DataTable(adGroupOptions);
      $(".btn-deselect").css("visibility", "hidden");
      $(".btn-bulk-action").css("visibility", "hidden");

      // Show negative keywords tab when user is in an ad group
      $("#neg_keywords_tab").attr("style", "");

      // Add the campaign to the breadcrumbs
      breadcrumbs = [{
        name: campaignName,
        id: campaignId,
        linkClass: 'c_link'
      }];
      updateBreadcrumbs();
    };
    /* End: initAdGroupsTable */

    /* Start: initKeywordsTable */
    var initKeywordsTable = function(adGroupName, adGroupId){

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

        }, // drawCallback (keyword manager)
        ajax: {
          url: 'includes/dashpages/cmanager/helpers/get_data.php',
          data: function(){
            var drp = datePicker.data('daterangepicker');
            // Get the start and end date
            return {
              adGroupId: adGroupId,
              start: drp.startDate.toJSON(),
              end: drp.endDate.toJSON()
            };
          }
        }
      }; // kwOptions

      clearTable();
      dt = $('#campaign_manager').DataTable(kwOptions);

      // Add the campaign to the breadcrumbs
      breadcrumbs.push({
        name: adGroupName,
        id: adGroupId,
        linkClass: 'ag_link'
      });
      updateBreadcrumbs();
    };
    /* End: initKeywordsTable */


    /* START: Create the campaign data table */

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
      initCampaignsTable();
    });

    //when user clicks on a campaign link
    $("#campaign_manager, .breadcrumb").on("click", ".c_link", function() {
      initAdGroupsTable($(this).html(), $(this).attr('id'));
    });

    //when user clicks on an adgroup link
    $("#campaign_manager").on("click", ".ag_link",  function() {
      initKeywordsTable($(this).html(), $(this).attr('id'));
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
