<?php include_once './includes/editshorttitle.inc.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Ace Global Dashboard | Keyword Tracker</title>

  <!-- BOOTSTRAP STYLES -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
  <link rel="stylesheet" type="text/css" href="./style.css">
  <link href="http://netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">
</head>
<body>

<section id="navigation">
    <nav class="navbar navbar-expand-lg navbar-dark main-color-bg">
      <a class="navbar-brand" href="#"><h4>AG Dashboard</h4></a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" href="./index.php">Dashboard Home </a>
          </li>
          <li class="nav-item active">
            <a class="nav-link" href="./edittitles.php">Edit Short Titles<span class="sr-only">(current)</span></a>
          </li>
        </ul>
      </div>
    </nav>
</section>

<header id="header">
  <div class="container">
    <h3><i class="icon-gear"></i> Dashboard - Edit Short Product Titles<small></small></h3>
  </div>
</header>



<section id="main-content">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <p class="card-header main-color-bg" id="main-content-bar"><i class="icon-gears"></i> Edit Short Product Titles</p>
          <div class="card-body">
            <form method="POST">
            <?= $alert ?>
              <div class="form-group">
                <label>ASIN</label><br>
                <?php generateDropdown($pdo) ?>
              </div>
              <div class="form-group">
                <label>New Product Short Name</label>
                <input type="text" name="newProdName" class="form-control">
              </div>
              <button type="submit" name="btnChangeProdName" value="y" class="btn btn-primary">Submit Changes</button>
            </form>
            
          </div>
        </div>
      </div>
    </div>
  </div>
</section>


<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
</body>
</html>