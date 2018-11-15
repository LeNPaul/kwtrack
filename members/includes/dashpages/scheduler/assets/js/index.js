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
      console.log(campaignTableList);
    },

    error: function(err) {
      console.log(err);
    }
  });

  console.log(campaignTableList);

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
