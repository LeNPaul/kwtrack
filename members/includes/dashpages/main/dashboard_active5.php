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
	$dateArr[] = date("d/m", strtotime("-".$j." days"));
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
            <div class="icon-big text-center icon-warning">
              <i class="nc-icon nc-globe text-warning"></i>
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
          <i class="fa fa-refresh"></i> Last updated on ..date..
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

<button type="button" id="fourDays" onclick="buttonClick('4')">4 days</button>
<button type="button" id="sixtyDays" onclick="buttonClick()">60 days</button>

<canvas id="testChart" width="1000" height="400"></canvas>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.js"></script>
<script>
var ctx = document.getElementById("testChart");
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
			borderColor: "rgb(151, 253, 143)"
		}, {
			label: "PPC Sales",
			data: ppcSalesArr,
			fill: false,
			borderColor: "rgb(127, 225, 255)"
		}, {
			label: "PPC ACoS",
			data: ppcAcosArr,
			fill: false,
			borderColor: "rgb(252, 108, 108)"
		}]
	}

var myChart = new Chart(ctx, {
	type: "line",
	data: data,
	options: {
		responsive: false
	}
});

function buttonClick(numDays) {
	if (numDays == 4) {
		myChart.data.labels = [dateArr[0], dateArr[1], dateArr[2], dateArr[3]];
		myChart.update();
	} else {
		myChart.data.labels = dateArr;
		myChart.update();
	}
}	
</script>
