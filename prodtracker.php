<?php include_once './includes/editshorttitle.inc.php'; ?>

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

<?php require_once './includes/nav.inc.php'; ?>

<header id="header" class="prodtr">
  <div class="container">
    <h3><i class="icon-gear"></i> Dashboard - Product Tracker<small></small></h3>
  </div>
</header>

<section id="main-content">
<!--  <div  class="container-fluid">-->
    <div class="row">
      <div class="col-md-3">
        <div class="nav-side-menu">
          <div class="brand">Menu</div>
          <i class="fa fa-bars fa-2x toggle-btn" data-toggle="collapse" data-target="#menu-content"></i>
          <div class="menu-list">
            <ul id="menu-content" class="menu-content collapse out">
              <li class="single">
                <a href="prodtracker.php">
                  <i class="fa icon-gears fa-lg"></i> Overview
                </a>
              </li>
              
              <li data-toggle="collapse" data-target="#brandMenu" class="collapsed">
                <a href="#">
                  <i class="fa icon-building fa-lg"></i> Brands
                  <span class="arrow"></span>
                </a>
              </li>
              <ul class="sub-menu collapse" id="brandMenu">
                <li>
                  <a href="?manage=brands">Manage Brands <i class="fa-lg icon-gears sub-menu-item"></i></a>
                </li>
              </ul>
              
              
              <li data-toggle="collapse" data-target="#prodMenu" class="collapsed">
                <a href="#"><i class="fa icon-sitemap fa-lg"></i> Products <span class="arrow"></span></a>
              </li>
              <ul class="sub-menu collapse" id="prodMenu">
                <li>
                  <a href="?manage=products">Manage Products <i class="fa-lg icon-gears sub-menu-item"></i></a>
                </li>
              </ul>
              
              
            </ul>
          </div>
        </div>
      </div>
      <div class="col-md-9 col-sm-offset-1">
        <?php include_once './includes/prodtracker/pagemanager.inc.php'; ?>
      </div>
    </div>
<!--  </div>-->
</section>


<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
</body>
</html>