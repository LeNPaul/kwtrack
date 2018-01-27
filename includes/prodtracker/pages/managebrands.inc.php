<?php
require_once './database/pdo.inc.php';

function genAccordionCard($pdo, $i, $brandName, $brand_id) {
  $prodListHTML = '';
  /* Check if there are any products under $brandName */
  $sql = 'SELECT brand_id, prod_name, prod_cost, prod_ship_cost,
          prod_amz_fees, prod_sale_price FROM products';
  $prodList = $pdo->query($sql)->fetchAll();
  
  /* If there are no products, then set $prodListHTML to an error message */
  if (empty($prodList)) {
    $prodListHTML = 'No products have been added yet.';
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
                  <a class="dropdown-item" href="#">Delete a Product</a>
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

<div class="container">
  <!-- Echo all alerts here -->
  <?php
    if (isset($_SESSION['alert'])) {
      echo $_SESSION['alert'];
      unset($_SESSION['alert']);
    }
    
    echo genBrandCards($pdo);
  ?>

</div>