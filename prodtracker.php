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

<?php require_once './includes/nav.inc.php'; ?>

<header id="header" class="prodtr">
  <div class="container">
    <h3><i class="icon-gear"></i> Dashboard - Product Tracker<small></small></h3>
  </div>
</header>

<section id="main-content">
<!--  <div class="container-fluid">-->
    <div class="row">
      <nav class="col-md-2 d-none d-md-block bg-light sidebar">
        <div class="sidebar-sticky">
          <ul class="nav flex-column">
            <li class="nav-item prodtr">
              <a class="nav-link prodtr" href="#">
                <span class="icon-home"></span> Brands <span class="sr-only">(current)</span>
              </a>
            </li>
            <li class="nav-item prodtr">
              <a class="nav-link prodtr" href="#">
                <span data-feather="file"></span>
                Orders
              </a>
            </li>
            <li class="nav-item prodtr">
              <a class="nav-link prodtr" href="#">
                <span data-feather="shopping-cart"></span>
                Products
              </a>
            </li>
          </ul>
        </div>
      </nav>
    </div>
<!--  </div>-->
</section>


<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
</body>
</html>