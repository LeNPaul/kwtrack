<?php
/*
 *  Final step for dashboard pages
 *  User will see this after all preliminary importing has been completed.
 */
require './includes/dashpages/helper.inc.php';

// Grab metric data for all campaigns and store in an array for each metric
$adSpendArr = array_reverse(calculateMetrics(multiUnserialize(getMetricData($pdo, 'ad_spend', $_SESSION['user_id'])), 1, 'ad_spend'));
$ppcSalesArr = array_reverse(calculateMetrics(multiUnserialize(getMetricData($pdo, 'sales', $_SESSION['user_id'])), 1,'ad_spend'));

$adSpend = array_sum($adSpendArr);
$ppcSales = array_sum($ppcSalesArr);
$acos = [];

for ($i = 0; $i < count($adSpendArr); $i++) {
	if ($ppcSalesArr[$i] == 0) {
		$acos[] = 0;
	} else {
		$acos[] = round((double)($adSpendArr[$i] / $ppcSalesArr[$i]) * 100, 2);
	}
}

$dateArr = [];
$dateArr[] = date("d/m");

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
              <p class="card-category">Ad Spend</p>
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
              <p class="card-title"><?= round((double)($adSpend / $ppcSales) * 100, 2) . '%' ?>
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

      <div class="card-header ">
        <h4 class="card-title">PPC Analytics</h4>
        <p class="card-category">Ad Spend, PPC Sales, and PPC ACoS</p>
      </div>

      <div class="card-body ">

		<p>Custom Date: <input id="datePicker"></p>
		<button type="button" id="fourDays" onclick="buttonClick('7')"> This Week</button>
		<button type="button" id="fourteenDays" onclick="buttonClick('14')">2 Weeks</button>
		<button type="button" id="thirtyDays" onclick="buttonClick('30')">1 Month</button>
		<button type="button" id="sixtyDays" onclick="buttonClick('60')">2 Months</button>

		<canvas id="Chart" width="1000" height="400"></canvas>
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

<script>
var ctx = document.getElementById("Chart");
var adSpendArr = <?= json_encode($adSpendArr); ?>;
var ppcSalesArr = <?= json_encode($ppcSalesArr); ?>;
var ppcAcosArr = <?= json_encode($acos); ?>;
var dateArr = <?= json_encode($dateArr); ?>;

var data = {
		labels: dateArr,

		datasets: [{
			label: "Ad Spend",
			data: adSpendArr,
			fill: false,
			backgroundColor: "rgb(244, 72, 66)",
			borderColor: "rgb(244, 72, 66)",
			type: 'line'
		}, {
			label: "PPC Sales",
			data: ppcSalesArr,
			fill: false,
			backgroundColor: "rgb(89, 255, 152)",
			borderColor: "rgb(89, 255, 152)",
			type: 'line'
		}, {
			label: "PPC ACoS",
			data: ppcAcosArr,
			fill: true,
			backgroundColor: "rgb(114, 187, 255)"
		}]
	}

var myChart = new Chart(ctx, {
	type: "bar",
	data: data,

	options: {
		responsive: true,

		scales: {
			// Remove grid lines
	    xAxes: [{
		            gridLines: {
		                color: "rgba(0, 0, 0, 0)",
		            }
	            }]/*,
	    yAxes: [{
                gridLines: {
                    color: "rgba(0, 0, 0, 0)",
                }
	            }]*/
    }
	}
});

function buttonClick(numDays) {
	switch (numDays) {
		case "7":
			myChart.data.labels = dateArr.slice(0, 7);
			myChart.update();
			break;

		case "14":
			myChart.data.labels = dateArr.slice(0, 14);
			myChart.update();
			break;

		case "30":
			myChart,data.labels = dateArr.slice(0, 30);
			myChart.update();
			break;

		default:
			myChart.data.labels = dateArr;
			myChart.update();
	}
}
</script>
