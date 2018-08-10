<?php
session_start();
require './members/database/pdo.inc.php';

// Make sure email and hash are set in $_GET
if (isset($_GET['email']) && !empty($_GET['email']) && isset($_GET['hash']) && !empty($_GET['hash'])) {
  // Escape strings to block SQL injections
  $email = htmlentities($_GET['email']);
  $hash = htmlentities($_GET['hash']);

  // Make sure user with email and hash exists in the database
  $sql = "SELECT * FROM users WHERE email='$email' AND hash='$hash'";
  $stmt = $pdo->query($sql);
  $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

  if (count($results) == 0) { // User does not exist
    $_SESSION['message'] = createAlert('danger', 'You have entered an invalid URL for password reset.');
    header("location: forgot.php");
    exit();
  }
} else {
  $_SESSION['message'] = createAlert('danger', 'An error has occurred. Please try again.');
  header("location: forgot.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="description" content="">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <!-- The above 4 meta tags *must* come first in the head; any other head content must come *after* these tags -->

  <!-- Title  -->
  <title>PPCology | The World's Leading Amazon PPC Tool</title>

  <!-- Favicon  -->
  <link rel="icon" href="img/core-img/favicon.ico">

  <!-- ***** All CSS Files ***** -->

  <!-- Style css -->
  <link rel="stylesheet" href="style.css">

  <!-- Skin css -->
  <link rel="stylesheet" href="css/skin.css">

</head>

<body class="gradients red">

<!-- Preloader Start -->
<div id="preloader">
  <div class="apland-load"></div>
</div>

<!-- ***** Header Start ***** -->
<header class="header_area">
  <div class="main_header_area animated">
    <div class="container h-100">
      <nav class="navbar h-100 navbar-expand-lg">
        <!-- Logo -->
        <a class="navbar-brand" href="index.html"><img src="img/core-img/logo.png" alt=""></a>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#aplandmenu" aria-controls="aplandmenu" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>

        <div class="collapse navbar-collapse" id="aplandmenu">

          <ul class="navbar-nav ml-auto" id="corenav">
            <!-- Start Dropdown Sample
            <li class="nav-item dropdown active">
                <a class="nav-link dropdown-toggle" href="#" id="homeDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Home <span class="sr-only">(current)</span></a>
                <div class="dropdown-menu" aria-labelledby="homeDropdown">
                    <a class="dropdown-item" href="index.html">Default</a>
                    <a class="dropdown-item" href="index.html">Subscribe Form</a>
                    <a class="dropdown-item" href="index-slider.html">Slideshow Version</a>
                    <a class="dropdown-item" href="index-gradients.html">Gradients Version</a>
                    <a class="dropdown-item" href="index-image.html">Static Image</a>
                    <a class="dropdown-item" href="index-video.html">Video Background</a>
                    <a class="dropdown-item" href="index-dark.html">Dark Version</a>
                    <a class="dropdown-item" href="index-rtl.html">RTL Version</a>
                    <a class="dropdown-item" href="index-fluid.html">Fluid Version</a>
                    <a class="dropdown-item" href="index-static.html">Static No Scroll</a>
                </div>
            </li>
            End Dropdown Sample -->


        </div>
      </nav>
    </div>
  </div>
</header>
<!-- ***** Header End ***** -->

<!--Login Area Start -->
<section class="about_area section_padding_100_70">
  <div class="container">
    <div class="row">
      <div class="card col-12 centered">
        <div class="card-body">
          <div class="section_heading text-center" id="login-header">
            <h3>Password <span>Reset</span></h3>
            <p>Enter your new password below in order to login</p>
          </div>

          <?php
            if (isset($_SESSION['message'])) {
              echo $_SESSION['message'];
              unset($_SESSION['message']);
            }
          ?>

          <form id="login-form" action="reset_password.php" method="post">

            <div class="row">
              <div class="col-md-3 col-sm-1 col-lg-2"></div>
              <div class="form-group col-md-6 col-sm-10 col-lg-8">
                <label for="userEmail">New Password</label>
                <input type="password" class="form-control" name="newpassword" id="userEmail" placeholder="Enter email" required>
              </div>
              <div class="col-md-3 col-sm-1col-lg-2"></div>
            </div>

            <div class="row">
              <div class="col-md-3 col-sm-1 col-lg-2"></div>
              <div class="form-group col-md-6 col-sm-10 col-lg-8">
                <label for="userEmail">Re-enter New Password</label>
                <input type="password" class="form-control" name="newpasswordconfirm" id="userEmail" placeholder="Enter email" required>
              </div>
              <div class="col-md-3 col-sm-1col-lg-2"></div>
            </div>

            <div class="col-12 text-center">
              <button type="submit" class="btn btn-danger centered" id="login-btn" name="newPassBtn" value="x">Reset Password</button>
            </div>

            <!-- This input field is needed, to get the email of the user -->
            <input type="hidden" name="email" value="<?= $email ?>">
            <input type="hidden" name="hash" value="<?= $hash ?>">

          </form>
        </div>
      </div>
    </div>
  </div>
</section>
<!--Login Area End-->

<!-- ***** Footer Area Start ***** -->
<footer class="footer_area">
  <!-- Bottom Footer Area Start -->
  <div class="footer_bottom_area">
    <div class="container">
      <div class="row">
        <!-- Footer Social Area -->
        <div class="col-12">
          <!-- Footer Menu Area -->
          <div class="footer_menu">
            <ul>
              <li><a href="#">About Us</a></li>
              <li><a href="#">Corporate Sale</a></li>
              <li><a href="#">Privacy Policy</a></li>
              <li><a href="#">Term &amp; Conditions</a></li>
              <li><a href="#">Help Center</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="footer_copywrite_area">
    <div class="container">
      <div class="row">
        <div class="col-12">
          <!-- Footer CopyrightArea -->
          <div class="footer_bottom text-center">
            <p>&copy; Copyright 2018, PPCology</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</footer>
<!-- ***** Footer Area End ***** -->

<!-- ***** All jQuery Plugins ***** -->

<!-- jQuery(necessary for all JavaScript plugins) -->
<script src="js/jquery/jquery-2.2.4.min.js"></script>
<!-- Popper js -->
<script src="js/bootstrap/popper.min.js"></script>
<!-- Bootstrap js -->
<script src="js/bootstrap/bootstrap.min.js"></script>
<!-- Plugins js -->
<script src="js/include-all-plugins.min.js"></script>
<!-- Active js -->
<script src="js/active.js"></script>
<!-- Google Maps js -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDk9KNSL1jTv4MY9Pza6w8DJkpI_nHyCnk"></script>
<script src="js/google-map/map-active.js"></script>

<!-- Password Confirmation Script -->
<script>
var password = document.getElementById("newpassword")
  , confirm_password = document.getElementById("newpasswordconfirm");

function validatePassword(){
  if(password.value != confirm_password.value) {
    confirm_password.setCustomValidity("Passwords do not match");
  } else {
    confirm_password.setCustomValidity('');
  }
}

password.onchange = validatePassword;
confirm_password.onkeyup = validatePassword;
</script>

</body>

</html>
