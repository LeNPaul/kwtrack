<?php
/*
 *  Final step for dashboard pages
 *  User will see this after all preliminary importing has been completed.
 */
require_once './includes/dashpages/main/assets/Metric.php';
require './includes/dashpages/helper.inc.php';

$user_id = $_SESSION['user_id'];

// Check if user has 59 entries for their metrics
// If yes, they are on their first day and we need to only retrieve 59 days of data

$sql = "SELECT impressions FROM campaigns WHERE user_id={$user_id}";
$stmt = $pdo->query($sql);
$result = $stmt->fetch(PDO::FETCH_COLUMN);
$a = unserialize($result);

$adSpend     = new Metric($user_id, 'ad_spend', count($a), $pdo);
$ppcSales    = new Metric($user_id, 'sales', count($a), $pdo);
$impressions = new Metric($user_id, 'impressions', count($a), $pdo);
$unitsSold   = new Metric($user_id, 'units_sold', count($a), $pdo);
$clicks      = new Metric($user_id, 'clicks', count($a), $pdo);
$ctr         = new Metric($user_id, 'ctr', count($a), $pdo);
$avgCpc      = new Metric($user_id, 'avg_cpc', count($a), $pdo);
$acos 			 = new Metric($user_id, 'acos', count($a), $pdo);
$roas 			 = new Metric($user_id, 'roas', count($a), $pdo);

$adSpendArr     = $adSpend->getMetricArr();
$ppcSalesArr    = $ppcSales->getMetricArr();
$impressionsArr = $impressions->getMetricArr();
$unitsSoldArr   = $unitsSold->getMetricArr();
$clicksArr      = $clicks->getMetricArr();
$ctrArr         = $ctr->getMetricArr();
$avgCpc         = $avgCpc->getMetricArr();
$acosArr 				= $acos->getAcosArr();

$acos 			 = [];
$displayACoS = 0;
$adSpend 		 = array_sum($adSpendArr);
$adSales 	 	 = $ppcSales->getMetric();

$displayACoS = ($adSales == 0) ? 0.00 : round((double)($adSpend / $adSales) * 100, 2);
$displayRoas = ($adSpend == 0) ? 0.00 : round($adSales / $adSpend, 2);

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
	<div class="col-lg-6 col-md-6 col-sm-6">
		<div class="card card-stats">
			<div class="card-body ">
				<div class="row">

					<div class="col-3 col-md-2">
						<div class="icon-big text-center icon-danger">
							<i class="nc-icon nc-tag-content text-danger"></i>
						</div>
					</div>

					<div class="col-9 col-md-10">
						<div class="numbers">
							<h6 class="card-subtitle">
								Advertising Performance
							</h6>
						</div>

            <div style="overflow: hidden;">
              <p class="metric-name">Advertising Sales</p>
              <p class="metric"><?= '$' . $adSales ?></p>
            </div>
            <hr style="border-top: dashed 1px rgb(196,194,187);" />
            <div style="overflow: hidden;">
              <p class="metric-name">Advertising Spend</p>
              <p class="metric"><?= '$' . array_sum($adSpendArr) ?></p>
            </div>
            <hr style="border-top: dashed 1px rgb(196,194,187);" />
            <div style="overflow: hidden;">
              <p class="metric-name">Conversion Rate</p>
              <p class="metric"><?= round((count($unitsSoldArr) / array_sum($clicksArr)) * 100, 2) . '%' ?></p>
            </div>
            <hr style="border-top: dashed 1px rgb(196,194,187);" />
            <div style="overflow: hidden;">
              <p class="metric-name">ROAS</p>
              <p class="metric"><?= '$' . $displayRoas ?></p>
            </div>
            <hr style="border-top: dashed 1px rgb(196,194,187);" />
            <div style="overflow: hidden;">
              <p class="metric-name">ACOS</p>
              <p class="metric"><?= $displayACoS . "%" ?></p>
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

					<div class="col-3 col-md-2">
						<div class="icon-big text-center icon-danger">
							<i class="nc-icon nc-tag-content text-danger"></i>
						</div>
					</div>

					<div class="col-9 col-md-10">
						<div class="numbers">
							<h6 class="card-subtitle">
								Advertising Analytics
							</h6>
						</div>

						<div style="overflow: hidden;">
							<p class="metric-name">Impressions</p>
							<p class="metric"><?= array_sum($impressionsArr) ?></p>
						</div>

						<hr style="border-top: dashed 1px rgb(196,194,187);" />
						<div style="overflow: hidden;">
							<p class="metric-name">Clicks</p>
							<p class="metric"><?= array_sum($clicksArr) ?></p>
						</div>

						<hr style="border-top: dashed 1px rgb(196,194,187);" />
						<div style="overflow: hidden;">
							<p class="metric-name">Click-Thru Rate</p>
							<p class="metric"><?= round((array_sum($ctrArr) / count($ctrArr)) * 100, 2) . '%' ?></p>
						</div>

						<hr style="border-top: dashed 1px rgb(196,194,187);" />
						<div style="overflow: hidden;">
							<p class="metric-name">Average CPC</p>
							<p class="metric"><?= '$' . round((array_sum($avgCpc) / count($avgCpc)), 2) ?></p>
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.js"></script>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12">
		<div class="card ">

      <div class="card-header text-center">
        <h4 class="card-title">Advertising Analytics</h4>
        <p class="card-category">Ad Spend, Advertising Sales, and ACoS</p>
      </div>

	  <!-- <div class="card-body">
		<canvas id="pieChart" width="1000" height="400"></canvas>
	  </div> -->

    <div class="card-body">

		<div class="row">
			<div class="col-sm-6">
				<select class="custom-select" onchange="change.call(this, event)">
					<option selected value="acosArr">% ACoS</option>
					<option value="adSpendArr">$ Ad Spend</option>
					<option value="impressionsArr"># Impressions</option>
					<option value="avgCpc">$ Average CPC</option>
				</select>
			</div>
			<div class="col-sm-6">
				<select class="custom-select" onchange="change.call(this, event)">
					<option selected value="ppcSalesArr">$ PPC Sales</option>
				  <option value="clicksArr"># Clicks</option>
					<option value="unitsSoldArr"># Units Sold</option>
				  <option value="ctrArr">% CTR</option>
				</select>
			</div>
		</div>

			<div class="chartWrapper" width="1000" height="400">
				<!--<canvas id="dummy" width="1000" height="400" style="position: absolute"></canvas>
				<canvas id="cursor" width="1000" height="400" style="top:0; left:0; position: absolute"></canvas>
				<canvas id="tooltips" width="1000" height="400" style="top:0;, left:0;, position: absolute"></canvas>-->
				<canvas id="lineChart" width="1000" height="400" ></canvas>
			</div>

			<br>
			<div id="reportrange">
				<i class="fa fa-calendar"></i>
				<span></span> <i class="fa fa-caret-down"></i>
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
	<!--  Pie Chart for Ad Spend for all campaigns -->
	<div class="col-lg-6 col-md-12 col-sm-12">
		<div class="card">
      <div class="card-body ">
        <div class="row">
					<div class="col-12 col-md-12">
						<div class="numbers text-center">
							<p class="card-title">Ad Spend Breakdown<p>
						</div>
					</div>
				</div>

				<div class="row">
					<p>
						placeholder for pie chart
					</p>
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
