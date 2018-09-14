<?php
/*
 *  Final step for dashboard pages
 *  User will see this after all preliminary importing has been completed.
 */

require './includes/dashpages/helper.inc.php';

// Grab metric data for all campaigns and store in an array for each metric
$adSpendArr = calculateMetrics(multiUnserialize(getMetricData($pdo, 'ad_spend', 2)), 1, 'ad_spend');
$ppcSalesArr = calculateMetrics(multiUnserialize(getMetricData($pdo, 'sales', 2)), 1,'ad_spend');

echo '<pre>';
multiUnserialize(getMetricData($pdo, 'ad_spend', 0));
echo '</pre>';


if ($ppcSales == 0) {
  $acos = 0;
} else {
  $acos = round((double)($adSpend / $ppcSales) * 100, 2);
}

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
              <p class="card-title"><?= $acos . '%' ?>
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.js"></script>
<canvas id="testChart" width="1000" height="400"></canvas>
<script>
var ctx = document.getElementById("testChart");
var adSpendArr = <?php echo json_encode($adSpendArr); ?>;
alert(adSpendArr[0]);
var ppcSalesArr = <?php echo json_encode($ppcSalesArr); ?>;
var ppcAcosArr = [];

var data = {
		labels: ["day1", "day2", "day3", "day4", "day5", "day6", "day7", "day8", "day9", "day10", "day11", "day12", "day13", "day14", "day15", "day16", "day17", "day18", "day19", "day20", "day21", "day22", "day23", "day24", "day25", "day26", "day27", "day28", "day29", "day30", "day31", "day32", "day33", "day34", "day35", "day36", "day37", "day38", "day39", "day40", "day41", "day42", "day43", "day44", "day45", "day46", "day47", "day48", "day49", "day50", "day51", "day52", "day53", "day54", "day55", "day56", "day57", "day58", "day59", "day60", ], 
		datasets: [{
			label: "Ad Spend", 
			data: adSpendArr, 
			fill: false, 
			borderColor: "rgb(151, 253, 143)"
		}, {
			label: "PPC Sales",
			data: [53, 14, 68, 42, 80],
			fill: false,
			borderColor: "rgb(127, 225, 255)"
		}, {
			label: "PPC ACoS",
			data: [1, 24, 64, 30, 60],
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
</script>
