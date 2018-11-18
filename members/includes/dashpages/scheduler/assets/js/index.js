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
          {
            text: "Edit Ad Schedule",
            action: function ( e, dt, node, config ) {
              console.log(e);
            }
          },
          'selectAll',
          'selectNone'
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
          { title: "Scheduled", width: 70}
        ]
      };

      var campaignTable = $("#campaign_list").DataTable(campaignTableOptions);

    },

    error: function(err) {
      console.log(err);
    }
  });

  $("#ad_scheduler").on("click", function(){
    var campaignTable = $("campaign_list").DataTable();

    console.log(campaignTable.rows( { selected: true } ));
  });

});
