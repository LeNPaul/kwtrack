<?php
/* Registration process, inserts user into database
   and sends account confirmation email
 */
session_start();
if ( empty($_SESSION) ) { session_start(); }

include './members/database/pdo.inc.php';

// Check if user went thru LwA and got their access token
// If there is an access_token in the URL, then they finished LwA authentication
if (!empty($_GET['access_token'])) {
  $access_token = htmlspecialchars($_GET['access_token']);
}

// Check if the Register button has been clicked
if (isset($_POST['register'])) {
 // Check if user with that email already exists
 $stmt = $pdo->query("SELECT * FROM users WHERE email='$email'");
 $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

 // If count($results) > 0, then we know that the user exists
 if (count($result) > 0) {
   $_SESSION['message'] = createAlert('danger', 'User with this email already exists.');
   header("location: register.php");
   exit();
 } else { // Email doesn't exist in the database, so create new account

   // Set session variables to be used on profile.php page
   $_SESSION['email'] = $_POST['email'];
   $_SESSION['first_name'] = $_POST['firstname'];
   $_SESSION['last_name'] = $_POST['lastname'];

   // Escape all $_POST vars to protect against SQL injection
   $first_name = htmlspecialchars($_POST['firstname']);
   $last_name = htmlspecialchars($_POST['lastname']);
   $email = htmlspecialchars($_POST['email']);
   $password = htmlspecialchars(password_hash($_POST['password'], PASSWORD_BCRYPT));
   $hash = htmlspecialchars(md5(rand(0, 1000)));
   
   // Insert SQL --> add PLAN LEVEL once we figure out plan pricing
   $sql = 'INSERT INTO users (first_name, last_name, email, password, hash)
           VALUES (:first_name, :last_name, :email, :password, :hash)';
   $stmt = $pdo->prepare($sql);
   $stmt->execute(array(
     ':first_name'    => $first_name,
     ':last_name'     => $last_name,
     ':email'         => $email,
     ':password'      => $password,
     ':hash'          => $hash
   ));

   // Set session vars for new user
   $_SESSION['active'] = 0; // active = 0 for users that haven't verified their emails yet
   $_SESSION['logged_in'] = true; // So we know the user has logged in
   $_SESSION['message'] = createAlert('success',
                          "Confirmation link has been sent to $email. Please verify your account
                          by clicking on the link in the message!");

   // Send registration confirmation link
   $to = $email;
   $subject = 'PPCOLOGY Account Verification';
   $messageBody = "
   Hello $first_name,

   Thanks for signing up with PPCOLOGY!

   Please click this link to activate your account:

   https://ppcology.io/verify.php?email=$email&hash=$hash";

   mail($to, $subject, $messageBody);
   $_SESSION['message'] = createAlert('success', 'Your account has been successfully created. Please check your email to verify your account.');
   header("location: login.php");
   exit();
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
<div id="amazon-root"></div>
<script type="text/javascript">
    window.onAmazonLoginReady = function() {
        amazon.Login.setClientId('amzn1.application-oa2-client.4246e0f086e441259742c758f63ca0bf');
    };
    (function(d) {
        var a = d.createElement('script'); a.type = 'text/javascript';
        a.async = true; a.id = 'amazon-login-sdk';
        a.src = 'https://api-cdn.amazon.com/sdk/login1.js';
        d.getElementById('amazon-root').appendChild(a);
    })(document);
</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!-- Color Picker Start
<div class="color_picker_area">
    <div class="color_picker_switcher">
        <i class="pe-7s-paint-bucket"></i>
    </div>
    <form>
        <h4>Pick a Color</h4>
        <input class="select_opt" type="radio" name="cor" id="cor1" value="green">
        <label for="cor1" class="cor1" title="Green"></label>
        <input class="select_opt" type="radio" name="cor" id="cor2" value="">
        <label for="cor2" class="cor2" title="Default"></label>
        <input class="select_opt" type="radio" name="cor" id="cor3" value="purple">
        <label for="cor3" class="cor3" title="Purple"></label>
        <input class="select_opt" type="radio" name="cor" id="cor4" value="red">
        <label for="cor4" class="cor4" title="Red"></label>
        <input class="select_opt" type="radio" name="cor" id="cor5" value="pink">
        <label for="cor5" class="cor5" title="Pink"></label>
        <input class="select_opt" type="radio" name="cor" id="cor6" value="deepPurple">
        <label for="cor6" class="cor6" title="Deep Purple"></label>
        <input class="select_opt" type="radio" name="cor" id="cor7" value="naval">
        <label for="cor7" class="cor7" title="Naval"></label>
        <input class="select_opt" type="radio" name="cor" id="cor8" value="cyan">
        <label for="cor8" class="cor8" title="Cyan"></label>
        <input class="select_opt" type="radio" name="cor" id="cor9" value="blue">
        <label for="cor9" class="cor9" title="Blue"></label>
        <input class="select_opt" type="radio" name="cor" id="cor10" value="deepRose">
        <label for="cor10" class="cor10" title="Deep Rose"></label>
    </form>
</div>
Color Picker End -->

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

<!-- ***** Product Area Start ***** -->
<section class="about_area section_padding_100_70" id="product">

  <?php
  if (isset($_SESSION['message'])) {
    echo $_SESSION['message'];
    unset($_SESSION['message']);
  }
  ?>

  <div class="container">
    <div class="row">
      <div class="col-12">
        <!-- Section Heading -->
        <div class="section_heading text-center">
          <h3>Join the <span>Club</span></h3>
          <p>Follow the steps below to register for a PPCology account and start maximizing your profits!</p>
        </div>
      </div>
    </div>

    <div class="row justify-content-center align-items-center">
      <div class="col-12 col-md-6 col-lg-4">
        <div class="about_product_discription">
          <div class="row">
            <form method="post">

              <div class="col-12">
                <div class="single_about_part wow fadeInUp" data-wow-delay="0.2s">
                  <div id="register_feature_icon">
                    <!-- Adjust width of line in style.css line 703 -->
                    <i class="pe-7s-users" aria-hidden="true"></i>
                  </div>
                  <h3>Login with Amazon</h3>
                  <p>Login to your Amazon account and we will import all the important data we need.</p>
                </div>
              </div>

              <div class="col-12 add-margin">
                <a href id="LoginWithAmazon">
                  <img border="0" alt="Login with Amazon" src="https://images-na.ssl-images-amazon.com/images/G/01/lwa/btnLWA_gold_312x64.png" width="312" height="64" />
                </a>
				<script type="text/javascript">
					document.getElementById('LoginWithAmazon').onclick = function() {
						setTimeout(window.doLogin, 1);
						return false;
					};
					window.doLogin = function() {
						options = {};
						options.scope = 'profile';
						amazon.Login.authorize(options, function(response) {
							if (response.error) {
								alert('oauth error ' + response.error);
							return;
							}
							amazon.Login.retrieveProfile(response.access_token, function(response) {
								alert(response.access_token);
								alert('Hello ' + response.profile_name);
								alert('Your email address is ' + response.profile.PrimaryEmail);
								alert('Your unique ID is ' + response.profile.CustomerId);
							});
						});
					};
				</script>
              </div>

              <!-- Personal Information Section -->
              <div class="col-12">
                <div class="single_about_part wow fadeInUp active" data-wow-delay="0.4s">
                  <div id="register_feature_icon">
                    <i class="pe-7s-diamond" aria-hidden="true"></i>
                  </div>
                  <h3>Personal Information</h3>
                  <p>Enter your personal information so we can best serve you.</p>
                </div>
              </div>

              <div class="col-12">
                <div class="form-group">
                  <label for="firstname">First Name</label>
                  <input required type="text" class="form-control" id="firstname" name="firstname" placeholder="Enter your first name">
                </div>
                <div class="form-group">
                  <label for="lastname">Last Name</label>
                  <input required type="text" class="form-control" id="lastname" name="lastname" placeholder="Enter your last name">
                </div>
                <div class="form-group">
                  <label for="email">E-Mail</label>
                  <input required type="email" class="form-control" id="email" name="email" aria-describedby="emailHelp" placeholder="Enter email">
                </div>
                <div class="form-group">
                  <label for="password">Password</label>
                  <input required pattern=".{8,}" type="password" class="form-control" id="password" name="password" placeholder="Enter password">
                </div>
                <div class="form-group">
                  <input required pattern=".{8,}" type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm password">
                </div>
              </div>

              <!-- Plan Selection -->
              <div class="col-12">
                <div class="single_about_part wow fadeInUp" data-wow-delay="0.4s">
                  <div id="register_feature_icon">
                    <i class="pe-7s-diamond" aria-hidden="true"></i>
                  </div>
                  <h3>Select Your Plan</h3>
                  <p>Choose the plan that best suits your business' needs.</p>
                </div>
              </div>

              <label class="check-container col-12">
                By clicking on create account, you agree to our terms of service and that you have read our privacy policy, including our cookie use policy
                <input type="checkbox">
                <span class="checkmark"></span>
              </label>

              <div class="col-12">
                <button type="submit" class="btn btn-lg btn-danger" id="register-btn" name="register">Start 14-Day Free Trial</button>
              </div>

          </form>

          </div>
        </div>
      </div>

      <!-- About Product Thumb Area Start -->

    </div>
  </div>
</section>
<!-- ***** Product Area End ***** -->

<!-- ***** Footer Area Start ***** -->
<footer class="footer_area">
  <!-- Bottom Footer Area Start -->
  <div class="footer_bottom_area">
    <div class="container">
      <div class="row">
        <!-- Footer Social Area -->
        <div class="col-12">
          <div class="footer_social_area">
            <a href="#" data-toggle="tooltip" data-placement="top" title="Facebook"><i class="fa fa-facebook"></i></a>
            <a href="#" data-toggle="tooltip" data-placement="top" title="Google Plus"><i class="fa fa-google-plus"></i></a>
            <a href="#" data-toggle="tooltip" data-placement="top" title="Pinterest"><i class="fa fa-pinterest"></i></a>
            <a href="#" data-toggle="tooltip" data-placement="top" title="Skype"><i class="fa fa-skype"></i></a>
            <a href="#" data-toggle="tooltip" data-placement="top" title="Twitter"><i class="fa fa-twitter"></i></a>
          </div>
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
var password = document.getElementById("password")
  , confirm_password = document.getElementById("confirm_password");

function validatePassword(){
  if(password.value != confirm_password.value) {
    confirm_password.setCustomValidity("Passwords Don't Match");
  } else {
    confirm_password.setCustomValidity('');
  }
}

password.onchange = validatePassword;
confirm_password.onkeyup = validatePassword;
</script>
</body>

</html>
