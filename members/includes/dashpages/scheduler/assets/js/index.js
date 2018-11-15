$(function(){
  var campaignTableList;
  var user_id = $("#uid").val();

  $.ajax({
    type: "POST",
    url: "includes/dashpages/scheduler/assets/api/__get_campaign_list.php",
    data: {
      user_id: user_id
    },

    success: function(campaignTableList) {
      campaignTableList = JSON.parse(campaignList);
    },

    error: function(err) {
      console.log(err);
    }
  });

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

  console.log(campaignTableList);
  var campaignTable = $("#campaign_list").DataTable(campaignTableOptions);
});
