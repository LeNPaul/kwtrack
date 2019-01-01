<?php


?>

<h2 class="text-center">Campaign Manager</h2>

<div>
  <div id="campaignRange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 33%">
    <i class="fa fa-calendar"></i>
    <span></span> <i class="fa fa-caret-down"></i>
  </div>
</div>
<br />
<div>
  <nav aria-label="breadcrumb" id="cmanager_breadcrumbs" role="navigation">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><h6 id="bc"><a href="javascript:void(0)" class="all_link">All Campaigns</a></h6></li>
    </ol>
  </nav>
</div>

<div class="tabset">
  <!-- Tab 1 -->
  <input type="radio" name="tabset" id="tab1" aria-controls="cmanager" checked>
  <label for="tab1">Ad Groups</label>
  <!-- Tab 2 -- TODO: FIX BUG WHERE ARROW KEY CAN NAVIGATE TO HIDDEN TAB -->
  <input type="radio" name="tabset" id="tab2" aria-controls="neg_keywords">
  <label id="neg_keywords_tab" for="tab2" style="visibility:hidden;">Negative Keywords</label>
  
  <div class="tab-panels">
    <section id="cmanager" class="tab-panel">
      <div class="row">
        <div class="col-12 col-sm-12 col-md-12 col-lg-12">
          <table id="campaign_manager" class="table table-light table-hover row-border order-column" cellpadding="0" cellspacing="0" border="0" width="100%"></table>
        </div>
      </div>
    </section>
    
    <section id="neg_keywords" class="tab-panel">
      <div class="row">
        <div class="col-12 col-sm-12 col-md-12 col-lg-12">
          <table id="neg_keyword_table" class="table table-light table-hover row-border order-column" cellpadding="0" cellspacing="0" border="0" width="100%"></table>
        </div>
      </div>
    </section>
  </div>
</div>


<script>
  $(document).ready( function () {


    var start = moment().subtract(59, 'days');
    var end = moment();
    var dt = null;  // Will be set later

    var formatDate = function(begin, finish){
      $('#campaignRange span').html(begin.format('MMMM D, YYYY') + ' - ' + finish.format('MMMM D, YYYY'));
    }

    var drp = $('#campaignRange').daterangepicker({
      maxDate: moment(),
      minDate: moment().subtract(59, 'days'),
      startDate: start,
      endDate: end,
      ranges: {
        'Today': [moment(), moment()],
        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        'Last 60 days': [moment().subtract(59, 'days'), moment()],
        'This Month': [moment().startOf('month'), moment().endOf('month')],
        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
      }
    }, function(begin, finish){
      formatDate(begin, finish);
      // Reload the data
      dt.ajax.reload();
    });
    formatDate(start, end);
    
    
    var campaignOptions = {
      dom: '<"#dt_topBar.row"<"col-md-5" B><"col-md-2"<"#info_selected">><"col-md-2" l><"col-md-3" f>> rt <"row"<"col-md-3"i><"col-md-9"p>>',
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
          text: 'Bulk Actions',
          className: 'btn-bulk-action'
        }
      ],
      //TODO: dont make this multi, use row().select() and trigger when row clicked
      select: {
        style: 'multi'
      },
      language: {
        select: {
          rows: ""
        }
      },
      order: [[ 1, "asc" ]],
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
      columns: [
        { title: "Status", "orderDataType": "dom-text-toggle"},
        { title: "Campaign Name"},
        { title: "Budget", "orderDataType": "dom-text"},
        { title: "Targeting Type" },
        { title: "Impressions" },
        { title: "Clicks" },
        { title: "CTR" },
        { title: "Spend" },
        { title: "CPC" },
        { title: "Units Sold" },
        { title: "Sales" },
        { title: "CR" },
        { title: "ACoS" }
      ],
      ajax: {
        url: 'includes/dashpages/cmanager/helpers/get_data.php',
        data: function(){
          // Get the start and end date
          return {
            start: drp.data('daterangepicker').startDate.toJSON(),
            end: drp.data('daterangepicker').endDate.toJSON()
          };
        }
      }
    }; //campaignOptions

    dt  = $('#campaign_manager').DataTable(campaignOptions);
    $(".btn-deselect").css("visibility", "hidden");
    $(".btn-bulk-action").css("visibility", "hidden");
    
  });

  
</script>