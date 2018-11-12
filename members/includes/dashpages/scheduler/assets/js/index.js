$(function(){
  var data;
  var user_id = $("#uid").val();

  $.ajax({
    type: "POST",
    url: "1includes/dashpages/scheduler/assets/api/__get_campaign_list.php",
    data: {
      user_id: user_id
    },

    success: function(campaignList) {
      console.log(campaignList);
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
    // fixedColumns: {
    //   leftColumns: 2
    // },
    data: dataset,
    columns: [
      { title: "Select"},
      { title: "Campaign Name"},
      { title: "Scheduled"}
    ]
  };

  var campaignTable = $("#campaign_list").DataTable(campaignOptions);
});
