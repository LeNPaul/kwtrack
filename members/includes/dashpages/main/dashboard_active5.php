<?php
/*
 *  Final step for dashboard pages
 *  User will see this after all preliminary importing has been completed.
 */
require './includes/dashpages/helper.inc.php';

// Grab metric data for all campaigns and store in an array for each metric
$adSpendArr = array_reverse(
	calculateMetrics(
	multiUnserialize(getMetricData($pdo, 'ad_spend', $_SESSION['user_id'])),
		60,
		'ad_spend'));

$ppcSalesArr = array_reverse(
	calculateMetrics(
		multiUnserialize(getMetricData($pdo, 'sales', $_SESSION['user_id'])),
		60,
		'ad_spend'));

$acos = [];
$displayACoS = 0;
$adSpend = array_sum($adSpendArr);
$ppcSales = array_sum($ppcSalesArr);

for ($i = 0; $i < count($adSpendArr); $i++) {
	if ($ppcSalesArr[$i] == 0) {
		$acos[] = 0;
	} else {
		$acos[] = round((double)($adSpendArr[$i] / $ppcSalesArr[$i]) * 100, 2);
	}
}

$displayACoS = ($ppcSales == 0) ? 0.00 : round((double)($adSpend / $ppcSales) * 100, 2);

$dateArr = [];
$dateArr[] = date("M d");

for ($j = 1; $j < 60; $j++) {
	$dateArr[] = date("M d", strtotime("-".$j." days"));
}

$dateArr = array_reverse($dateArr);

?>

<!--  Row for PPC metrics: ad spend, ppc sales, ppc ACoS -->
<div class="row">
  <div class="col-lg-4 col-md-6 col-sm-6">
    <div class="card card-stats">
      <div class="card-body ">
        <div class="row">

          <div class="col-5 col-md-4">
            <div class="icon-big text-center icon-danger">
              <i class="nc-icon nc-tag-content text-danger"></i>
            </div>
          </div>

          <div class="col-7 col-md-8">
            <div class="numbers">
              <p class="card-category">
								Ad Spend

							</p>
              <p class="card-title"><?= '$' . $adSpend ?>
              <p>
            </div>
          </div>

        </div>
      </div>
      <div class="card-footer ">
        <hr>
        <div class="stats">
          <i class="fa fa-refresh"></i> Last updated on <?= date("d/m/y") ?>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4 col-md-6 col-sm-6">
    <div class="card card-stats">
      <div class="card-body ">
        <div class="row">
          <div class="col-5 col-md-4">
            <div class="icon-big text-center icon-success">
              <i class="nc-icon nc-money-coins text-success"></i>
            </div>
          </div>
          <div class="col-7 col-md-8">
            <div class="numbers">
              <p class="card-category">PPC Sales</p>
              <p class="card-title"><?= '$' . $ppcSales ?>
              <p>
            </div>
          </div>
        </div>
      </div>
      <div class="card-footer ">
        <hr>
        <div class="stats">
          <i class="fa fa-refresh"></i> Last updated on <?= date("d/m/y") ?>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4 col-md-6 col-sm-6">
    <div class="card card-stats">
      <div class="card-body ">
        <div class="row">
          <div class="col-5 col-md-4">
            <div class="icon-big text-center">
              <i class="fa fa-money text-primary"></i>
            </div>
          </div>
          <div class="col-7 col-md-8">
            <div class="numbers">
              <p class="card-category">PPC ACoS</p>
              <p class="card-title"><?=  $displayACoS . '%' ?>
              <p>
            </div>
          </div>
        </div>
      </div>
      <div class="card-footer ">
        <hr>
        <div class="stats">
          <i class="fa fa-refresh"></i> Last updated on <?= date("d/m/y") ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!--  Row for organic metrics: true ACoS, total sales, organic sales -->
<!--
<div class="row">
  <div class="col-lg-4 col-md-6 col-sm-6">
    <div class="card card-stats">
      <div class="card-body ">
        <div class="row">
          <div class="col-5 col-md-4">
            <div class="icon-big text-center icon-warning">
              <i class="nc-icon nc-globe text-warning"></i>
            </div>
          </div>
          <div class="col-7 col-md-8">
            <div class="numbers">
              <p class="card-category">True ACoS</p>
              <p class="card-title">test
              <p>
            </div>
          </div>
        </div>
      </div>
      <div class="card-footer ">
        <hr>
        <div class="stats">
          <i class="fa fa-refresh"></i> Last updated on ..date..
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4 col-md-6 col-sm-6">
    <div class="card card-stats">
      <div class="card-body ">
        <div class="row">
          <div class="col-5 col-md-4">
            <div class="icon-big text-center icon-warning">
              <i class="nc-icon nc-globe text-warning"></i>
            </div>
          </div>
          <div class="col-7 col-md-8">
            <div class="numbers">
              <p class="card-category">Total Sales</p>
              <p class="card-title">test
              <p>
            </div>
          </div>
        </div>
      </div>
      <div class="card-footer ">
        <hr>
        <div class="stats">
          <i class="fa fa-refresh"></i> Last updated on ..date..
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4 col-md-6 col-sm-6">
    <div class="card card-stats">
      <div class="card-body ">
        <div class="row">
          <div class="col-5 col-md-4">
            <div class="icon-big text-center icon-warning">
              <i class="nc-icon nc-globe text-warning"></i>
            </div>
          </div>
          <div class="col-7 col-md-8">
            <div class="numbers">
              <p class="card-category">Organic Sales</p>
              <p class="card-title">test
              <p>
            </div>
          </div>
        </div>
      </div>
      <div class="card-footer ">
        <hr>
        <div class="stats">
          <i class="fa fa-refresh"></i> Last updated on ..date..
        </div>
      </div>
    </div>
  </div>
</div> -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.js"></script>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12">
		<div class="card ">

      <div class="card-header text-center">
        <h4 class="card-title">PPC Analytics</h4>
        <p class="card-category">Ad Spend, PPC Sales, and PPC ACoS</p>
      </div>

	  <div class="card-body">
		<canvas id="pieChart" width="1000" height="400"></canvas>
	  </div>

      <div class="card-body">
		<div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
			<i class="fa fa-calendar"></i>
			<span></span> <i class="fa fa-caret-down"></i>
		</div>
		<br>

		<canvas id="lineChart" width="1000" height="400"></canvas>
	  </div>

      <div class="card-footer ">
        <hr>
        <div class="stats">
          <i class="fa fa-refresh"></i> Last updated on ..date..
        </div>
      </div>
    </div>
	</div>
</div>

<script type="text/javascript">

$(function() {

    var start = moment().subtract(59, 'days');
    var end = moment();

    function cb(begin, finish) {
		start = begin.format('MMM DD');
		end = finish.format('MMM DD');
		chartUpdate(start, end);
        $('#reportrange span').html(begin.format('MMMM D, YYYY') + ' - ' + finish.format('MMMM D, YYYY'));
    }

    $('#reportrange').daterangepicker({
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
    }, cb);
});

var ctx = document.getElementById("lineChart");
var ctx2 = document.getElementById("pieChart");
var adSpendArr = <?= json_encode($adSpendArr); ?>;
var ppcSalesArr = <?= json_encode($ppcSalesArr); ?>;
var ppcAcosArr = <?= json_encode($acos); ?>;
var dateArr = <?= json_encode($dateArr); ?>;

var pieData = {
	datasets: [{
		data: [10, 20, 30],
		backgroundColor: ["rgb(244, 72, 66, 0.1)", "rgb(89, 255, 152, 0.1)", "rgb(114, 187, 255, 0.4)"]
	}],

	labels: [
		'True ACoS',
		'Total Sales',
		'Organic Sales'
	]
};

var lineData = {
		labels: dateArr,
		datasets: [{
			label: "Ad Spend ($)",
			yAxisID: 'A',
			data: adSpendArr,
			fill: 'origin',
			pointRadius: 2.5,
			hoverRadius: 4,
			pointBorderColor: '#ffffff',
			pointBorderWidth: 1,
			pointBackgroundColor: "rgb(244, 72, 66)",
			hoverBorderWidth: 3,
			backgroundColor: "rgba(244, 72, 66, 0.1)",
			borderWidth: 1.5,
			borderColor: "rgba(244, 72, 66, 0.7)",
			type: 'line'
		}, {
			label: "PPC Sales ($)",
			yAxisID: 'A',
			data: ppcSalesArr,
			fill: true,
			pointRadius: 2.5,
			hoverRadius: 4,
			pointBorderColor: '#ffffff',
			pointBorderWidth: 1,
			pointBackgroundColor: "rgb(89, 255, 152)",
			hoverBorderWidth: 3,
			backgroundColor: "rgba(89, 255, 152, 0.1)",
			borderWidth: 1.2,
			borderColor: "rgba(89, 255, 152, 0.7)",
			type: 'line'
		}, {
			label: "PPC ACoS (%)",
			yAxisID: 'B',
			data: ppcAcosArr,
			fill: true,
			backgroundColor: "rgba(114, 187, 255, 0.4)",
		}]
	};

var pieChart = new Chart(ctx2, {
	type: "pie",
	data: pieData
});

var myChart = new Chart(ctx, {
	type: "bar",
	data: lineData,

	options: {
		responsive: true,

		toolip: {
			mode: 'index',
			intersect: false
		},

		elements: {
			line: {
				//fill: '-1'
			}
		},
		scales: {
			// Remove grid lines
	    xAxes: [{
		            gridLines: {
		                color: "rgba(0, 0, 0, 0)",
		            }
	            }],
	    yAxes: [{
								id: 'A',
                type: 'linear',
								position: 'left',
								gridLines: {
	                color: "rgba(0, 0, 0, 0)",
		            },
								ticks: {
									callback: function(value, index, values) {
                  	return '$' + value;
                  }
								}
								//stacked: true
	            }, {
			      		id: 'B',
			        	type: 'linear',
			        	position: 'right',
								gridLines: {
	                color: "rgba(0, 0, 0, 0)",
		            },
								ticks: {
									beginAtZero: true,
									display: false,
									max: Math.round(Math.max.apply(null, ppcSalesArr)) * 2,
									callback: function(value, index, values) {
                  	return value + '%';
                  }
								}
							}]
    }



	}
});

function chartUpdate(startUpdate, endUpdate) {
	startArr = dateArr.indexOf(startUpdate);
	endArr = dateArr.indexOf(endUpdate);

	subLabels = dateArr.slice(startArr, endArr + 1);
	subAdSpend = adSpendArr.slice(startArr, endArr + 1);
	subSales = ppcSalesArr.slice(startArr, endArr + 1);
	subAcos = ppcAcosArr.slice(startArr, endArr + 1);

	myChart.data.labels = subLabels;
	myChart.data.datasets[0].data = subAdSpend;
	myChart.data.datasets[1].data = subSales;
	myChart.data.datasets[2].data = subAcos;

	myChart.update();
};
</script>
