$(function(){
  var campaignTableList = 0;
  var user_id = $("#uid").val();

  $.ajax({
    type: "POST",
    url: "includes/dashpages/scheduler/assets/api/__get_campaign_list.php",
    data: {
      user_id: user_id
    },

    success: function(campaignList) {
      campaignTableList = JSON.parse(campaignList);
      var campaignTableOptions = {
        scrollX: true,
        paging: true,
        pagingType: "full_numbers",
        lengthMenu: [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "All"]
          ],
        data: campaignTableList,
        columns: [
          { title: "Select"},
          { title: "Campaign Name"},
          { title: "Scheduled"}
        ]
      };

      var campaignTable = $("#campaign_list").DataTable(campaignTableOptions);
    },

    error: function(err) {
      console.log(err);
    }
  });

});
