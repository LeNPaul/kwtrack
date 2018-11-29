<?php
/*
 *  Final step for dashboard pages
 *  User will see this after all preliminary importing has been completed.
 */
require './includes/dashpages/helper.inc.php';

// Check if user has 59 entries for their metrics
// If yes, they are on their first day and we need to only retrieve 59 days of data

$sql = "SELECT impressions FROM campaigns WHERE user_id={$user_id}";
$stmt = $pdo->query($sql);
$result = $stmt->fetch(PDO::FETCH_COLUMN);
$a = unserialize($result);

$adSpendArr = array_reverse(
  calculateMetrics(
    multiUnserialize(getMetricData($pdo, 'ad_spend', $_SESSION['user_id'])),
    count($a),
    'ad_spend'));

$ppcSalesArr = array_reverse(
  calculateMetrics(
    multiUnserialize(getMetricData($pdo, 'sales', $_SESSION['user_id'])),
    count($a),
    'ad_spend'));
  
$acos 			 = [];
$displayACoS = 0;
$adSpend 		 = array_sum($adSpendArr);
$ppcSales 	 = array_sum($ppcSalesArr);
$roas 			 = ($adSpend == 0) ? 0.00 : round($ppcSales / $adSpend, 2);

for ($i = 0; $i < count($adSpendArr); $i++) {
	if ($ppcSalesArr[$i] == 0) {
		$acos[] = 0;
	} else {
		$acos[] = round((double)($adSpendArr[$i] / $ppcSalesArr[$i]) * 100, 2);
	}
}

$displayACoS = ($ppcSales == 0) ? 0.00 : round((double)($adSpend / $ppcSales) * 100, 2);

// Set the time zone to Pacific Time Zone (PT)
date_default_timezone_set('America/Los_Angeles');

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

  <div class="col-lg-4 col-md-12 col-sm-12">
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

<div class="row">
  <div class="col-lg-6 col-md-6 col-sm-6">
    <div class="card card-stats">
      <div class="card-body ">
        <div class="row">
          <div class="col-5 col-md-4">
            <div class="icon-big text-center">
              <i class="fa fa-money text-success"></i>
            </div>
          </div>
          <div class="col-7 col-md-8">
            <div class="numbers">
              <p class="card-category">ROAS</p>
              <p class="card-title"><?= '$' . $roas ?>
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
  
  <div class="col-lg-6 col-md-6 col-sm-6">
    <div class="card card-stats">
      <div class="card-body ">
        <div class="row">
          <div class="col-5 col-md-4">
            <div class="icon-big text-center">
              <i class="fa fa-money text-success"></i>
            </div>
          </div>
          <div class="col-7 col-md-8">
            <div class="numbers">
              <p class="card-category">Conversion Rate</p>
              <p class="card-title"><?= '$' . $roas ?>
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

	  <!-- <div class="card-body">
		<canvas id="pieChart" width="1000" height="400"></canvas>
	  </div> -->

      <div class="card-body">
			<div id="reportrange">
				<i class="fa fa-calendar"></i>
				<span></span> <i class="fa fa-caret-down"></i>
			</div>
			<br>

			<div class="chartWrapper" width="1000" height="400" style="position: relative">
				<!--<canvas id="dummy" width="1000" height="400" style="position: absolute"></canvas>
				<canvas id="cursor" width="1000" height="400" style="top:0; left:0; position: absolute"></canvas>
				<canvas id="tooltips" width="1000" height="400" style="top:0;, left:0;, position: absolute"></canvas>-->
				<canvas id="lineChart" width="1000" height="400" ></canvas>
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

<!--  Row for pie chart (PPC Sales vs Total Sales) and Top Performing CSTs  -->
<div class="row">
	<!--  Pie Chart -->
	<div class="col-lg-6 col-md-12 col-sm-12">

	</div>

	<!--  Top Performing CSTs -->
	<div class="col-lg-6 col-md-12 col-sm-12">
		<div class="card">
      <div class="card-body ">
        <div class="row">

          <div class="col-12 col-md-12">
            <div class="numbers text-center">
              <p class="card-title">Top Performing Keywords
              <p>
            </div>
          </div>

        </div>

				<div class="row">
					<div class="col-12 col-md-12">
						Placeholder
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

<?php include './includes/dashpages/main/assets/dashboard_chart.php'; ?>
