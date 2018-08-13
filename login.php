<?php
/*
 * User login process. Checks if user exists and password is correct
 */
session_start();
include './members/database/pdo.inc.php';

if (!empty($_POST['login'])) {
  // Escape email to protect against SQL injections
  $email = htmlentities($_POST['email']);
  // Grab user from database
  $sql = "SELECT * FROM users WHERE email='$email'";
  $stmt = $pdo->query($sql);
  $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
  if (count($results) == 0) { // User doesn't exist
    $_SESSION['message'] = createAlert('danger', "User with that email doesn't exist!");
    header("location: login.php");
    exit();
  } else { // User exists
    $user = $results[0];
    echo '<pre>';
    var_dump($user);
    echo $_POST['password'] . '<br />';
    echo $user['password'] . '<br />';
    echo $user['hash'] . '<br /><br />';
    
    echo password_get_info($user['password']) . '<br />';
    echo password_get_info(password_hash($_POST['password'], PASSWORD_BCRYPT)) . '<br />';
    
    echo password_verify($_POST['password'], $user['hash']) ? 'true' : 'false';

    echo '</pre>'; die;
    if (password_verify($_POST['password'], $user['password'])) { // If password was correct
      $_SESSION['email'] = $user['email'];
      $_SESSION['first_name'] = $user['first_name'];
      $_SESSION['last_name'] = $user['last_name'];
      $_SESSION['active'] = $user['active'];

      // This is how we'll know the user is logged in
      $_SESSION['logged_in'] = true;
      
      header("location: dashboard.php");
      exit();
    } else { // If password isn't correct
      $_SESSION['message'] = createAlert("danger", 'You have entered the wrong password, please try again.');
      header("location: login.php");
      exit();
    }
    
  }
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
            <h3>Login to <span>PPCOLOGY</span></h3>
            <p>Follow the steps below to register for a PPCology account and start maximizing your profits!</p>
          </div>

          <form id="login-form" method="post">
            
            <?php
            if (!empty($_SESSION['message'])) {
              echo $_SESSION['message'];
              unset($_SESSION['message']);
            }
  
            ?>
            
            <div class="row">
              <div class="col-md-3 col-sm-1 col-lg-2"></div>
              <div class="form-group col-md-6 col-sm-10 col-lg-8">
                <label for="userEmail">Email address</label>
                <input type="email" class="form-control" name="email" id="userEmail" placeholder="Enter email" required>
              </div>
              <div class="col-md-3 col-sm-1col-lg-2"></div>
            </div>

            <div class="row">
              <div class="col-md-3 col-sm-1 col-lg-2"></div>
              <div class="form-group col-md-6 col-sm-10 col-lg-8">
                <label for="userPassword">Password</label>
                <input type="password" class="form-control" name="password" id="userPassword" placeholder="Password" required>
              </div>
              <div class="col-md-3 col-sm-1 col-lg-2"></div>
            </div>

            <div class="col-12 text-center">
              <button type="submit" class="btn btn-danger centered" id="login-btn" name="login" value="x">Login</button>
            </div>

            <div class="col-12 text-center">
              <p id="register_text">
                Don't have an account yet?
                <a href="register.php">Sign Up Now</a>
              </p>
            </div>
  
            <div class="col-12 text-center">
              <p id="register_text">
                Forgot your password?
                <a href="forgot.php">Reset Password</a>
              </p>
            </div>

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

</body>

</html>
