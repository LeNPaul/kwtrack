<?php
/*
 *  Final step for dashboard pages
 *  User will see this after all preliminary importing has been completed.
 */

require './includes/dashpages/helper.inc.php';

// Grab metric data for all campaigns and store in an array for each metric
$adSpend = calculateMetrics(multiUnserialize(getMetricData($pdo, 'ad_spend', 0)), 2, 'adSpend');
$ppcSales = calculateMetrics(multiUnserialize(getMetricData($pdo, 'sales', 0)), 2, 'ppcSales');
$ppcSales = 98.12;
if ($ppcSales == 0) {
  $acos = 0;
} else {
  $acos = round((double)($adSpend / $ppcSales) * 100, 3);
}

?>


<!--  Row for PPC metrics: ad spend, ppc sales, ppc ACoS -->
<div class="row">
  <div class="col-sm-4">
    <div class="card text-white bg-success mb-3">
      <!--    <div class="card-header">Header</div>-->
      <div class="card-body">
        <h5 class="card-title">Ad Spend</h5>
        <p class="card-text"><?= '$' . $adSpend ?></p>
      </div>
    </div>
  </div>
  
  <div class="col-sm-4">
    <div class="card text-white bg-success mb-3">
      <div class="card-body">
        <h5 class="card-title">PPC Sales</h5>
        <p class="card-text"><?= '$' . $ppcSales ?></p>
      </div>
    </div>
  </div>
  
  <div class="col-sm-4">
    <div class="card text-white bg-success mb-3">
      <div class="card-body">
        <h5 class="card-title">PPC ACoS</h5>
        <p class="card-text"><?= $acos . '%' ?></p>
      </div>
    </div>
  </div>
</div>

<!--  Row for organic metrics: true ACoS, total sales, organic sales -->
<div class="row">
  <div class="col-sm-4">
    <div class="card text-white bg-success">
      <!--    <div class="card-header">Header</div>-->
      <div class="card-body">
        <h5 class="card-title">True ACoS</h5>
        <p class="card-text">test</p>
      </div>
    </div>
  </div>
  
  <div class="col-sm-4">
    <div class="card text-white bg-success">
      <div class="card-body">
        <h5 class="card-title">Total Sales</h5>
        <p class="card-text">test</p>
      </div>
    </div>
  </div>
  
  <div class="col-sm-4">
    <div class="card text-white bg-success">
      <div class="card-body">
        <h5 class="card-title">Organic Sales</h5>
        <p class="card-text">test</p>
      </div>
    </div>
  </div>
  
</div>

