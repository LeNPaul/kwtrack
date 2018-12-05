$(function(){
  var user_id = $("#uid").val();

  $.ajax({
    type: "POST",
    url: "includes/dashpages/scheduler/assets/api/__get_campaign_list.php",
    data: {
      user_id: user_id
    },

    success: function(campaignList) {

      var campaignTableOptions = {
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
            text: "Edit Ad Schedule",
            className: "btn-success btn-scheduler",

            action: function ( e, dt, node, config ) {
              var selectedCampaigns = dt.rows( '.selected' ).data();
              var campaignIdArr = [];
              // Populate list of campaign ID's
              for (i = 0; i < selectedCampaigns.length; i++) {
                var rx         = selectedCampaigns[i][1].match(/id="\d+/g);
                var campaignId = rx[0].replace("id=\"", "");
                campaignIdArr.push(campaignId);
              }

              campaignIdArr = JSON.stringify(campaignIdArr);

              $("#campaignIdList").val(campaignIdArr);

              swal({
                title: 'Confirm Editing of Schedules',
                html: '<p>Are you sure you want to edit ad schedules for all selected campaigns?</p><p>All schedules for your selected campaigns will be overwritten.</p><p>Continue?</p>',
                type: 'warning',
                showCancelButton: true,
                confirmButtonClass: "btn-success",
                cancelButtonClass: "btn-secondary",
				confirmButtonColor: '#009925',
				cancelButtonColor: '#d33',
				allowOutsideClick: false,
				allowEnterKey: false,
				allowEscapeKey: false
              })
              .then(function(result) {
                if (result.value) {
                  // Set cookie to get campaign id's during ad scheduling (l = list, cid = campaign id)
                  createCookie("l_cid", campaignIdArr, 1);
                  // Redirect to ad schedule editor
                  window.location.href = 'dashboard?p=as&sp=e';
                }
                // $("#campaignIdList").click();
              });
            }
          }
        ],
        select: {
          style: 'multi'
        },
        language: {
          select: {
            rows: ""
          }
        },
        scrollX: true,
        paging: true,
        pagingType: "full_numbers",
        lengthMenu: [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "All"]
          ],
        data: JSON.parse(campaignList),
        columns: [
          { title: "Campaign Name"},
          { title: "Scheduled", width: 100}
        ]
      };

      var campaignTable = $("#campaign_list").DataTable(campaignTableOptions);
      // Hide topBar buttons when nothing selected
      $(".btn-scheduler").css("visibility", "hidden");
      $(".btn-deselect").css("visibility", "hidden");
    },

    error: function(err) {
      console.log(err);
    }
  });

  // Show/hide "Edit Ad Schedule" button if there is anything selected
  $("body").on("mouseup", function() {
    var sleep = function (time) {
      return new Promise( function(resolve){ return setTimeout(resolve, time); } );
    };
    sleep(50).then(function() {
      var dt = $("#campaign_list").DataTable();
      var campaignsSelected = dt.rows( '.selected' );
      if (dt.rows( '.selected' ).any()) {
        $(".btn-scheduler").css("visibility", "visible");
        $(".btn-deselect").css("visibility", "visible");

        if (campaignsSelected[0].length === 1) {
          $("#info_selected").text(campaignsSelected[0].length + " campaign selected");
        } else {
          $("#info_selected").text(campaignsSelected[0].length + " campaigns selected");
        }
      } else {
        $(".btn-scheduler").css("visibility", "hidden");
        $(".btn-deselect").css("visibility", "hidden");

        $("#info_selected").text("");
      }
    });

  });

  // Functions to handle cookies
  function createCookie(name,value,days) {
  	if (days) {
  		var date = new Date();
  		date.setTime(date.getTime()+(days*24*60*60*1000));
  		var expires = "; expires="+date.toGMTString();
  	}
  	else var expires = "";
  	document.cookie = name+"="+value+expires+"; path=/";
  }

  function readCookie(name) {
  	var nameEQ = name + "=";
  	var ca = document.cookie.split(';');
  	for(var i=0;i < ca.length;i++) {
  		var c = ca[i];
  		while (c.charAt(0)==' ') c = c.substring(1,c.length);
  		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
  	}
  	return null;
  }

  function eraseCookie(name) {
  	createCookie(name,"",-1);
  }

});
