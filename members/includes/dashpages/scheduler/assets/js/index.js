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
        dom: '<"#dt_topBar.row"<"col-md-7"B><"col-md-2"l><"col-md-3"f>>rt<"row"<"col-md-3"i><"col-md-9"p>>',
        buttons: [
          'selectAll',
          'selectNone',
          {
            text: "Edit Ad Schedule",
            className: "btn-success btn-scheduler",
            action: function ( e, dt, node, config ) {
              console.log(dt);
              console.log(dt.rows( '.selected' ).any());
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
        data: JSON.parse(campaignList),
        columns: [
          { title: "Campaign Name"},
          { title: "Scheduled", width: 100}
        ]
      };

      var campaignTable = $("#campaign_list").DataTable(campaignTableOptions);

      $(".btn-scheduler").css("display", "none");

    },

    error: function(err) {
      console.log(err);
    }
  });

  $("#campaign_list_wrapper").on("click", function(){
    var dt = $("#campaign_list").dataTable();
    console.log(dt);
  });
});
