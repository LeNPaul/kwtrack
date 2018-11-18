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
        dom: '<"#dt_topBar.row"<"col-md-7"B<"#info_selected">><"col-md-2"l><"col-md-3"f>>rt<"row"<"col-md-3"i><"col-md-9"p>>',
        buttons: [
          'selectAll',
          {
            extend: 'selectNone',
            text: 'Deselect All',
            className: 'btn-deselect'
          },
          {
            text: "Edit Ad Schedule",
            className: "btn-success btn-scheduler",
            action: function ( e, dt, node, config ) {
              console.log(dt);
              console.log(dt.rows( '.selected' ).data());
              var selectedCampaigns = dt.rows( '.selected' ).data();
              var campaignIdArr = [];
              for (i = 0; i < selectedCampaigns.length; i++) {
                console.log(selectedCampaigns[i]);
              }

            }
          }
        ],
        select: {
          style: 'multi',
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
      console.log(campaignsSelected[0]);
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
});
