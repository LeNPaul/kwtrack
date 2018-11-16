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
        select: true,
        scrollX: true,
        paging: true,
        pagingType: "full_numbers",
        lengthMenu: [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "All"]
          ],
        data: JSON.parse(campaignList),
        columns: [
          { title: "Select", width: 40},
          { title: "Campaign Name"},
          { title: "Scheduled", width: 50}
        ]
      };

      var campaignTable = $("#campaign_list").DataTable(campaignTableOptions);
      
    },

    error: function(err) {
      console.log(err);
    }
  });

});
