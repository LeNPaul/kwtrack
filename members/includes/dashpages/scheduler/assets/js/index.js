$(function(){
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
