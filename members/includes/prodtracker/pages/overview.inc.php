<?php
require_once './database/pdo.inc.php';

/* Get number of brands */
$sql = 'SELECT COUNT(*) FROM brands';
$numBrands = $pdo->query($sql)->fetch(PDO::FETCH_COLUMN);

/* Get number of products */
$sql = 'SELECT COUNT(*) FROM products';
$numProducts = $pdo->query($sql)->fetch(PDO::FETCH_COLUMN);

/* Calculate average profit margin for all products*/
$sql = 'SELECT prod_profit FROM products';
$profitList = $pdo->query($sql)->fetchAll(PDO::FETCH_COLUMN);
$avgProfitMargin = array_sum($profitList) / count($profitList);

/* Calculate average ROI of all products*/
$sql = 'SELECT prod_roi FROM products';
$roiList = $pdo->query($sql)->fetchAll(PDO::FETCH_COLUMN);
$avgROI = array_sum($roiList) / count($roiList);

?>

<h1 class="overview text-center">Inventory Overview</h1>

<div class="container">
  
  <div class="row overview">
    <div class="col-md-6">
      <div class="card bg-overview text-white text-center">
        <div class="card-body">
          <h3 class="card-title"><i class="icon-building fa-lg"></i> Brands</h3>
          <h2 class="card-text"><?= $numBrands ?></h2>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card bg-overview text-white text-center">
        <div class="card-body">
          <h3 class="card-title"><i class="icon-sitemap fa-lg"></i> Products</h3>
          <h2 class="card-text"><?= $numProducts ?></h2>
        </div>
      </div>
    </div>
  </div>
  
  <div class="row overview">
    <div class="col-md-6">
      <div class="card bg-success text-white text-center">
        <div class="card-body">
          <h3 class="card-title"><i class="icon-trophy fa-lg"></i> Average Gross Profit Margin</h3>
          <h2 class="card-text"><?= number_format($avgProfitMargin, 2) ?>%</h2>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card bg-success text-white text-center">
        <div class="card-body">
          <h3 class="card-title"><i class="icon-lightbulb fa-lg"></i> Average Gross ROI</h3>
          <h2 class="card-text"><?= number_format($avgROI, 2) ?>%</h2>
        </div>
      </div>
    </div>
  </div>
  
</div>