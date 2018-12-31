<?php
/*
 *  Final step for dashboard pages
 *  User will see this after all preliminary importing has been completed.
 */
require_once './database/MetricsQueryBuilder.php';
require './includes/dashpages/helper.inc.php';

$user_id = $_SESSION['user_id'];

// Check if user has 59 entries for their metrics
// If yes, they are on their first day and we need to only retrieve 59 days of data
$builder = new MetricsQueryBuilder();
$builder->userId = $user_id;
$result = $builder->execute($pdo);
$metrics = $result[0];

// Date metrics
$builder->includeDate = true;
$builder->orderBy = 'date';
$builder->orderByDesc = false;
$result = $builder->execute($pdo);

// Set the time zone to Pacific Time Zone (PT)
date_default_timezone_set('America/Los_Angeles');

$adSpendArr = [];
$ppcSalesArr = [];
$acosArr = [];
$dateArr = [];

foreach ($result as $m){
  $dateArr[] = date("M d", strtotime($m['date']));
  $adSpendArr[] = $m['ad_spend'];
  $ppcSalesArr[] = $m['sales'];
  $acosArr[] = round($m['acos'] * 100, 2);
}

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
              <p class="metric"><?= $metrics['sales_formatted'] ?></p>
            </div>
            <hr style="border-top: dashed 1px rgb(196,194,187);" />
            <div style="overflow: hidden;">
              <p class="metric-name">Advertising Spend</p>
              <p class="metric"><?= $metrics['ad_spend_formatted'] ?></p>
            </div>
            <hr style="border-top: dashed 1px rgb(196,194,187);" />
            <div style="overflow: hidden;">
              <p class="metric-name">Conversion Rate</p>
              <p class="metric"><?= $metrics['cvr_formatted'] ?></p>
            </div>
            <hr style="border-top: dashed 1px rgb(196,194,187);" />
            <div style="overflow: hidden;">
              <p class="metric-name">ROAS</p>
              <p class="metric"><?= $metrics['roas_formatted'] ?></p>
            </div>
            <hr style="border-top: dashed 1px rgb(196,194,187);" />
            <div style="overflow: hidden;">
              <p class="metric-name">ACOS</p>
              <p class="metric"><?= $metrics['acos_formatted'] ?></p>
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
							<p class="metric"><?= $metrics['impressions_formatted'] ?></p>
						</div>

						<hr style="border-top: dashed 1px rgb(196,194,187);" />
						<div style="overflow: hidden;">
							<p class="metric-name">Clicks</p>
							<p class="metric"><?= $metrics['clicks_formatted'] ?></p>
						</div>

						<hr style="border-top: dashed 1px rgb(196,194,187);" />
						<div style="overflow: hidden;">
							<p class="metric-name">Click-Thru Rate</p>
							<p class="metric"><?= $metrics['ctr_formatted'] ?></p>
						</div>

						<hr style="border-top: dashed 1px rgb(196,194,187);" />
						<div style="overflow: hidden;">
							<p class="metric-name">Average CPC</p>
							<p class="metric"><?= $metrics['avg_cpc_formatted'] ?></p>
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
			<div id="reportrange">
				<i class="fa fa-calendar"></i>
				<span></span> <i class="fa fa-caret-down"></i>
			</div>
			<br>

			<div class="chartWrapper" width="1000" height="400">
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
