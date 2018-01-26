<?php
require_once './database/pdo.inc.php';

?>

<h1 class="overview text-center">Inventory Overview</h1>

<div class="container">
  
  <div class="row overview">
    <div class="col-md-6">
      <div class="card bg-overview text-white text-center">
        <div class="card-body">
          <h3 class="card-title"><i class="icon-building fa-lg"></i> Brands</h3>
          <h2 class="card-text">3</h2>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card bg-overview text-white text-center">
        <div class="card-body">
          <h3 class="card-title"><i class="icon-sitemap fa-lg"></i> Products</h3>
          <h2 class="card-text">12</h2>
        </div>
      </div>
    </div>
  </div>
  
  <div class="row overview">
    <div class="col-md-6">
      <div class="card bg-success text-white text-center">
        <div class="card-body">
          <h3 class="card-title"><i class="icon-building fa-lg"></i> Average Gross Profit Margin</h3>
          <h2 class="card-text">3</h2>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card bg-success text-white text-center">
        <div class="card-body">
          <h3 class="card-title"><i class="icon-sitemap fa-lg"></i> Average Gross ROI</h3>
          <h2 class="card-text">12</h2>
        </div>
      </div>
    </div>
  </div>
  
</div>