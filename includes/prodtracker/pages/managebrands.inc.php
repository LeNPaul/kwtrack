<?php
require_once './database/pdo.inc.php';

function genProdDelModal($prodName, $prod_id, $id) {
  return '<div class="modal fade inverted" id="modalDel' . $id . '" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Delete '.$prodName.'?</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <p>All information for this product will be deleted. This cannot be undone.
                     Are you sure you want to delete <b>' . $prodName . '</b>?</p>
                </div>
                <div class="modal-footer">
                  <form method="post">
                    <button type="submit" name="btnDelProd" value="' . $prod_id . '" class="btn btn-danger">Delete</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  </form>
                </div>
              </div>
            </div>
          </div>';
}

function genProdTable($prodList) {
  $tdhtml = '';
  
  for ($i = 0; $i < sizeof($prodList); $i++) {
    $generatedTDs = '';
    // Product name
    $generatedTDs .= '<td>' . $prodList[$i]['prod_name'] . '</td>';
    // Cost Per Unit
    $generatedTDs .= '<td class="expense ">$' . number_format($prodList[$i]['prod_cost'], 2) . '</td>';
    // Cost of shipping
    $generatedTDs .= '<td class="expense ">$' . number_format($prodList[$i]['prod_ship_cost'], 2) . '</td>';
    // Landed Cost
    $landedCost = number_format(($prodList[$i]['prod_cost'] + $prodList[$i]['prod_ship_cost']), 2);
    $generatedTDs .= '<td class="expense ">$' . $landedCost . '</td>';
    // AMZ Fees
    $generatedTDs .= '<td class="expense ">$' . number_format($prodList[$i]['prod_amz_fees'], 2) . '</td>';
    // Sale Price
    $generatedTDs .= '<td class="income ">$' . number_format($prodList[$i]['prod_sale_price'], 2) . '</td>';
    // Net Revenue
    $netRev = number_format(($prodList[$i]['prod_sale_price'] - $prodList[$i]['prod_amz_fees']), 2);
    $generatedTDs .= '<td class="income ">$' . $netRev . '</td>';
    // Profit
    $profit = number_format(($netRev - $landedCost), 2);
    $generatedTDs .= '<td class="income ">$' . $profit . '</td>';
    // Profit Margin
    $generatedTDs .= '<td class="income ">' . number_format(100*($profit/$prodList[$i]['prod_sale_price']), 2) . '%</td>';
    // Gross ROI
    $generatedTDs .= '<td class="income ">' . number_format(100*($profit/$landedCost), 2) . '%</td>';
    // Actions
    $editIcon = '<i class="fa-lg icon-edit text-warning"></i>';
    $deleteIcon = '<a href="#" style="text-decoration:none;" name="btnDeleteProduct" data-toggle="modal" data-target="#modalDel' . $i . '">
                     <i class="fa-lg icon-trash text-danger"></i>
                   </a>' . genProdDelModal($prodList[$i]['prod_name'], $prodList[$i]['product_id'], $i);
    
    $generatedTDs .=  '<td class="text-center">' . $editIcon . '  ' . $deleteIcon . '</td>';
    
    $tr = '<tr>' . $generatedTDs . '</tr>';
    $tdhtml .= $tr;
  }
  
  $tableNest = '<table class="table table-hover text-center">
                  <thead>
                    <th scope="col">Product</th>
                    <th scope="col">Cost Per Unit</th>
                    <th scope="col">Cost of Shipping</th>
                    <th scope="col">Landed Cost</th>
                    <th scope="col">AMZ Fees</th>
                    <th scope="col">Sale Price</th>
                    <th scope="col">Net Revenue</th>
                    <th scope="col">Profit</th>
                    <th scope="col">Profit Margin</th>
                    <th scope="col">Gross ROI</th>
                    <th scope="col">Actions</th>
                  </thead>
                  <tbody>
                  ' . $tdhtml . '
                  </tbody>
                </table>';
  return $tableNest;
}

function genAccordionCard($pdo, $i, $brandName, $brand_id) {
  $prodListHTML = '';
  /* Check if there are any products under $brandName */
  $sql = 'SELECT product_id, brand_id, prod_name, prod_cost, prod_ship_cost,
          prod_amz_fees, prod_sale_price FROM products WHERE brand_id='.$brand_id;
  $prodList = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
  
  /* If there are no products, then set $prodListHTML to an error message */
  if (empty($prodList)) {
    $prodListHTML = 'No products have been added to <b>' . $brandName . '</b> yet.';
  } else {
    $prodListHTML = genProdTable($prodList);
  }
  
  return '<div class="card">
            <div class="card-header brand-header" id="heading' . $i . '">
                <a class="btn btn-link" data-toggle="collapse" data-target="#collapse' . $i . '" aria-expanded="true" aria-controls="collapse' . $i . '">
                  ' . $brandName . '
                </a>
              <div class="btn-group dropleft">
                <button type="button" class="btn btn-sm btn-secondary dropdown-toggle dropdown-toggle-split pull-right brand-action" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  Actions
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                  <a class="dropdown-item" href="#">Add a Product</a>
                  <a class="dropdown-item" href="#">Edit Brand Name</a>
                  <a class="dropdown-item bg-danger text-white" href="#">Delete Brand</a>
                </div>
              </div>
            </div>
        
            <div id="collapse' . $i . '" class="collapse" aria-labelledby="heading' . $i . '" data-parent="#accordion">
              <div class="card-body">
                ' . $prodListHTML . '
              </div>
            </div>
          </div>';
}

function genBrandCards($pdo) {
  $sql = 'SELECT brand_name FROM brands';
  $brandList = $pdo->query($sql)->fetchAll(PDO::FETCH_COLUMN);
  $sql = 'SELECT brand_id FROM brands';
  $brand_ids = $pdo->query($sql)->fetchAll(PDO::FETCH_COLUMN);
  $brandHTML = '';
  for ($i = 0; $i < sizeof($brandList); $i++) {
    $brandHTML .= genAccordionCard($pdo, $i, $brandList[$i], $brand_ids[$i]);
  }
  
  $accordionNest = '<div id="accordion">
                    ' . $brandHTML . '
                    </div>';
  return $accordionNest;
}

/* Check if a product was deleted */
if (isset($_POST['btnDelProd'])) {
  $prod_id = htmlentities($_POST['btnDelProd']);
  // Get product name
  $sql = 'SELECT prod_name FROM products WHERE product_id='.$prod_id;
  $prodName = $pdo->query($sql)->fetch(PDO::FETCH_COLUMN);
  $sql = 'DELETE FROM products WHERE product_id=:prod_id';
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(
      ':prod_id' => $prod_id
  ));
  $_SESSION['alert'] = createAlert('success', '<b>' . $prodName . '</b> has been deleted.');
  header("Location: prodtracker.php?manage=brands");
  exit();
}

/* Check if a brand was added */
if (isset($_POST['btnAddBrand']) && !empty($_POST['brandName'])) {
  $_SESSION['addBrandName'] = htmlentities($_POST['brandName']);
  $sql = 'INSERT INTO brands (brand_name) VALUES (:brandName)';
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(
    ':brandName' => $_SESSION['addBrandName']
  ));
  $_SESSION['alert'] = createAlert('success', 'Your brand <b>'.$_SESSION['brandName'].'</b> has been added!');
  header("Location: prodtracker.php?manage=brands");
  exit();
} else if (isset($_POST['btnAddBrand']) && empty($_POST['brandName'])) {
  $_SESSION['alert'] = createAlert('danger', 'Please enter a brand name.');
  header("Location: prodtracker.php?manage=brands");
  exit();
}

/* Check if user has any brands in the db */
$sql = 'SELECT COUNT(*) FROM brands';
$brandCount = $pdo->query($sql)->fetchColumn();
if ($brandCount == 0) {
  $_SESSION['alert'] = createAlert('warning', 'Whoa there! It looks like you haven\'t added a brand yet. Let\'s add a brand and get started.');
}

?>

<h1 class="overview text-center">
  Manage Brands
  <button type="button" class="btn btn-success pull-right action-btn" data-toggle="modal" data-target="#addBrandModal">
    Add a Brand
  </button>
</h1>

<div class="modal fade" id="addBrandModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <form method="post">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">Add A Brand</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          
            <div class="form-group">
              <label>Brand Name</label>
              <input type="text" name="brandName" class="form-control" />
            </div>
          
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" name="btnAddBrand" value="1" class="btn btn-success">Add Brand</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="container-brands">
      <!-- Echo all alerts here -->
      <?php
        if (isset($_SESSION['alert'])) {
          echo $_SESSION['alert'];
          unset($_SESSION['alert']);
        }
        
        echo genBrandCards($pdo);
      ?>
</div>