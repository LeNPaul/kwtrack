<?php
/*
 * Profile page for each user. Displays user profile information from DB and allows user to edit their information
 */
session_start();
require '../../../database/pdo.inc.php';

// Check if user is not logged in. Redirect to login page if not logged in.
checkLoggedIn();

// Grab user info from db and store it into vars. (Passed thru session vars after successful login)
$first

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <link rel="apple-touch-icon" sizes="76x76" href="../../../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../../../assets/img/favicon.png">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <title>
    PPCOLOGY | User Profile
  </title>
  <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" />
  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" rel="stylesheet">
  <!-- CSS Files -->
  <link href="../../../assets/css/bootstrap.min.css" rel="stylesheet" />
  <link href="../../../assets/css/paper-dashboard.css?v=2.0.1" rel="stylesheet" />
  <!-- CSS Just for demo purpose, don't include it in your project -->
  <link href="../../../assets/demo/demo.css" rel="stylesheet" />
</head>

<body class="">
<div class="wrapper ">
  <div class="sidebar" data-color="red" data-active-color="danger">
    <!--
      Tip 1: You can change the color of the sidebar using: data-color="blue | green | orange | red | yellow"
  -->
    <div class="logo">
      <a href="http://www.creative-tim.com" class="simple-text logo-mini">
        <div class="logo-image-small">
          <img src="../../../assets/img/logo-small.png">
        </div>
      </a>
      <a href="#" class="simple-text logo-normal">
        PPCOLOGY
        <!-- <div class="logo-image-big">
          <img src="../../../assets/img/logo-big.png">
        </div> -->
      </a>
    </div>
    <div class="sidebar-wrapper">
      <div class="user">
        <div class="photo">
          <img src="../../../assets/img/faces/ayo-ogunseinde-2.jpg" />
        </div>
        <div class="info">
          <a data-toggle="collapse" href="#collapseExample" class="collapsed">
              <span>
                Chet Faker
                <b class="caret"></b>
              </span>
          </a>
          <div class="clearfix"></div>
          <div class="collapse" id="collapseExample">
            <ul class="nav">
              <li>
                <a href="#">
                  <span class="sidebar-mini-icon">MP</span>
                  <span class="sidebar-normal">My Profile</span>
                </a>
              </li>
              <li>
                <a href="#">
                  <span class="sidebar-mini-icon">EP</span>
                  <span class="sidebar-normal">Edit Profile</span>
                </a>
              </li>
              <li>
                <a href="#">
                  <span class="sidebar-mini-icon">S</span>
                  <span class="sidebar-normal">Settings</span>
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <ul class="nav">
        <li>
          <a href="../../examples/dashboard.html">
            <i class="nc-icon nc-bank"></i>
            <p>Dashboard</p>
          </a>
        </li>
        <li>
          <a data-toggle="collapse" href="#pagesExamples">
            <i class="nc-icon nc-book-bookmark"></i>
            <p>
              Pages
              <b class="caret"></b>
            </p>
          </a>
          <div class="collapse " id="pagesExamples">
            <ul class="nav">
              <li>
                <a href="../../examples/pages/timeline.html">
                  <span class="sidebar-mini-icon">T</span>
                  <span class="sidebar-normal"> Timeline </span>
                </a>
              </li>
              <li>
                <a href="../../examples/pages/login.html">
                  <span class="sidebar-mini-icon">L</span>
                  <span class="sidebar-normal"> Login </span>
                </a>
              </li>
              <li>
                <a href="../../examples/pages/register.html">
                  <span class="sidebar-mini-icon">R</span>
                  <span class="sidebar-normal"> Register </span>
                </a>
              </li>
              <li>
                <a href="../../examples/pages/lock.html">
                  <span class="sidebar-mini-icon">LS</span>
                  <span class="sidebar-normal"> Lock Screen </span>
                </a>
              </li>
              <li class="active">
                <a href="../../examples/pages/user.html">
                  <span class="sidebar-mini-icon">UP</span>
                  <span class="sidebar-normal"> User Profile </span>
                </a>
              </li>
            </ul>
          </div>
        </li>
        <li>
          <a data-toggle="collapse" href="#componentsExamples">
            <i class="nc-icon nc-layout-11"></i>
            <p>
              Components
              <b class="caret"></b>
            </p>
          </a>
          <div class="collapse " id="componentsExamples">
            <ul class="nav">
              <li>
                <a href="../../examples/components/buttons.html">
                  <span class="sidebar-mini-icon">B</span>
                  <span class="sidebar-normal"> Buttons </span>
                </a>
              </li>
              <li>
                <a href="../../examples/components/grid.html">
                  <span class="sidebar-mini-icon">G</span>
                  <span class="sidebar-normal"> Grid System </span>
                </a>
              </li>
              <li>
                <a href="../../examples/components/panels.html">
                  <span class="sidebar-mini-icon">P</span>
                  <span class="sidebar-normal"> Panels </span>
                </a>
              </li>
              <li>
                <a href="../../examples/components/sweet-alert.html">
                  <span class="sidebar-mini-icon">SA</span>
                  <span class="sidebar-normal"> Sweet Alert </span>
                </a>
              </li>
              <li>
                <a href="../../examples/components/notifications.html">
                  <span class="sidebar-mini-icon">N</span>
                  <span class="sidebar-normal"> Notifications </span>
                </a>
              </li>
              <li>
                <a href="../../examples/components/icons.html">
                  <span class="sidebar-mini-icon">I</span>
                  <span class="sidebar-normal"> Icons </span>
                </a>
              </li>
              <li>
                <a href="../../examples/components/typography.html">
                  <span class="sidebar-mini-icon">T</span>
                  <span class="sidebar-normal"> Typography </span>
                </a>
              </li>
            </ul>
          </div>
        </li>
        <li>
          <a data-toggle="collapse" href="#formsExamples">
            <i class="nc-icon nc-ruler-pencil"></i>
            <p>
              Forms
              <b class="caret"></b>
            </p>
          </a>
          <div class="collapse " id="formsExamples">
            <ul class="nav">
              <li>
                <a href="../../examples/forms/regular.html">
                  <span class="sidebar-mini-icon">RF</span>
                  <span class="sidebar-normal"> Regular Forms </span>
                </a>
              </li>
              <li>
                <a href="../../examples/forms/extended.html">
                  <span class="sidebar-mini-icon">EF</span>
                  <span class="sidebar-normal"> Extended Forms </span>
                </a>
              </li>
              <li>
                <a href="../../examples/forms/validation.html">
                  <span class="sidebar-mini-icon">V</span>
                  <span class="sidebar-normal"> Validation Forms </span>
                </a>
              </li>
              <li>
                <a href="../../examples/forms/wizard.html">
                  <span class="sidebar-mini-icon">W</span>
                  <span class="sidebar-normal"> Wizard </span>
                </a>
              </li>
            </ul>
          </div>
        </li>
        <li>
          <a data-toggle="collapse" href="#tablesExamples">
            <i class="nc-icon nc-single-copy-04"></i>
            <p>
              Tables
              <b class="caret"></b>
            </p>
          </a>
          <div class="collapse " id="tablesExamples">
            <ul class="nav">
              <li>
                <a href="../../examples/tables/regular.html">
                  <span class="sidebar-mini-icon">RT</span>
                  <span class="sidebar-normal"> Regular Tables </span>
                </a>
              </li>
              <li>
                <a href="../../examples/tables/extended.html">
                  <span class="sidebar-mini-icon">ET</span>
                  <span class="sidebar-normal"> Extended Tables </span>
                </a>
              </li>
              <li>
                <a href="../../examples/tables/datatables.net.html">
                  <span class="sidebar-mini-icon">DT</span>
                  <span class="sidebar-normal"> DataTables.net </span>
                </a>
              </li>
            </ul>
          </div>
        </li>
        <li>
          <a data-toggle="collapse" href="#mapsExamples">
            <i class="nc-icon nc-pin-3"></i>
            <p>
              Maps
              <b class="caret"></b>
            </p>
          </a>
          <div class="collapse " id="mapsExamples">
            <ul class="nav">
              <li>
                <a href="../../examples/maps/google.html">
                  <span class="sidebar-mini-icon">GM</span>
                  <span class="sidebar-normal"> Google Maps </span>
                </a>
              </li>
              <li>
                <a href="../../examples/maps/fullscreen.html">
                  <span class="sidebar-mini-icon">FSM</span>
                  <span class="sidebar-normal"> Full Screen Map </span>
                </a>
              </li>
              <li>
                <a href="../../examples/maps/vector.html">
                  <span class="sidebar-mini-icon">VM</span>
                  <span class="sidebar-normal"> Vector Map </span>
                </a>
              </li>
            </ul>
          </div>
        </li>
        <li>
          <a href="../../examples/widgets.html">
            <i class="nc-icon nc-box"></i>
            <p>Widgets</p>
          </a>
        </li>
        <li>
          <a href="../../examples/charts.html">
            <i class="nc-icon nc-chart-bar-32"></i>
            <p>Charts</p>
          </a>
        </li>
        <li>
          <a href="../../examples/calendar.html">
            <i class="nc-icon nc-calendar-60"></i>
            <p>Calendar</p>
          </a>
        </li>
      </ul>
    </div>
  </div>
  
  <div class="main-panel">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-absolute fixed-top navbar-transparent">
      <div class="container-fluid">
        <div class="navbar-wrapper">
          <div class="navbar-minimize">
            <button id="minimizeSidebar" class="btn btn-icon btn-round">
              <i class="nc-icon nc-minimal-right text-center visible-on-sidebar-mini"></i>
              <i class="nc-icon nc-minimal-left text-center visible-on-sidebar-regular"></i>
            </button>
          </div>
          <div class="navbar-toggle">
            <button type="button" class="navbar-toggler">
              <span class="navbar-toggler-bar bar1"></span>
              <span class="navbar-toggler-bar bar2"></span>
              <span class="navbar-toggler-bar bar3"></span>
            </button>
          </div>
          <a class="navbar-brand" href="#pablo">Paper Dashboard 2 PRO</a>
        </div>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navigation" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-bar navbar-kebab"></span>
          <span class="navbar-toggler-bar navbar-kebab"></span>
          <span class="navbar-toggler-bar navbar-kebab"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navigation">
          <form>
            <div class="input-group no-border">
              <input type="text" value="" class="form-control" placeholder="Search...">
              <div class="input-group-append">
                <div class="input-group-text">
                  <i class="nc-icon nc-zoom-split"></i>
                </div>
              </div>
            </div>
          </form>
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link btn-magnify" href="#pablo">
                <i class="nc-icon nc-layout-11"></i>
                <p>
                  <span class="d-lg-none d-md-block">Stats</span>
                </p>
              </a>
            </li>
            <li class="nav-item btn-rotate dropdown">
              <a class="nav-link dropdown-toggle" href="http://example.com" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="nc-icon nc-bell-55"></i>
                <p>
                  <span class="d-lg-none d-md-block">Some Actions</span>
                </p>
              </a>
              <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                <a class="dropdown-item" href="#">Action</a>
                <a class="dropdown-item" href="#">Another action</a>
                <a class="dropdown-item" href="#">Something else here</a>
              </div>
            </li>
            <li class="nav-item">
              <a class="nav-link btn-rotate" href="#pablo">
                <i class="nc-icon nc-settings-gear-65"></i>
                <p>
                  <span class="d-lg-none d-md-block">Account</span>
                </p>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <!-- End Navbar -->
    <!-- <div class="panel-header panel-header-sm">

</div> -->
    <div class="content">
      <div class="row">
        <?php
        if (isset($_SESSION['message'])) {
          echo $_SESSION['message'];
          unset($_SESSION['message']);
        }
        ?>
        <div class="col-md-4">
          <div class="card card-user">
            <div class="image">
              <img src="../../../assets/img/bg/damir-bosnjak.jpg" alt="...">
            </div>
            <div class="card-body">
              <div class="author">
                <a href="#">
                  <img class="avatar border-gray" src="../../../assets/img/mike.jpg" alt="...">
                  <h5 class="title">Chet Faker</h5>
                </a>
                <p class="description">
                  @chetfaker
                </p>
              </div>
              <p class="description text-center">
                "I like the way you work it
                <br> No diggity
                <br> I wanna bag it up"
              </p>
            </div>
            <div class="card-footer">
              <hr>
              <div class="button-container">
                <div class="row">
                  <div class="col-lg-3 col-md-6 col-6 ml-auto">
                    <h5>12
                      <br>
                      <small>Files</small>
                    </h5>
                  </div>
                  <div class="col-lg-4 col-md-6 col-6 ml-auto mr-auto">
                    <h5>2GB
                      <br>
                      <small>Used</small>
                    </h5>
                  </div>
                  <div class="col-lg-3 mr-auto">
                    <h5>24,6$
                      <br>
                      <small>Spent</small>
                    </h5>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-md-8">
          <div class="card">
            <div class="card-header">
              <h5 class="title">Edit Profile</h5>
            </div>
            <div class="card-body">
              <form>
                <div class="row">
                  <div class="col-md-5 pr-1">
                    <div class="form-group">
                      <label>Company (disabled)</label>
                      <input type="text" class="form-control" disabled="" placeholder="Company" value="Creative Code Inc.">
                    </div>
                  </div>
                  <div class="col-md-3 px-1">
                    <div class="form-group">
                      <label>Username</label>
                      <input type="text" class="form-control" placeholder="Username" value="michael23">
                    </div>
                  </div>
                  <div class="col-md-4 pl-1">
                    <div class="form-group">
                      <label for="exampleInputEmail1">Email address</label>
                      <input type="email" class="form-control" placeholder="Email">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6 pr-1">
                    <div class="form-group">
                      <label>First Name</label>
                      <input type="text" class="form-control" placeholder="Company" value="Chet">
                    </div>
                  </div>
                  <div class="col-md-6 pl-1">
                    <div class="form-group">
                      <label>Last Name</label>
                      <input type="text" class="form-control" placeholder="Last Name" value="Faker">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Address</label>
                      <input type="text" class="form-control" placeholder="Home Address" value="Melbourne, Australia">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-4 pr-1">
                    <div class="form-group">
                      <label>City</label>
                      <input type="text" class="form-control" placeholder="City" value="Melbourne">
                    </div>
                  </div>
                  <div class="col-md-4 px-1">
                    <div class="form-group">
                      <label>Country</label>
                      <input type="text" class="form-control" placeholder="Country" value="Australia">
                    </div>
                  </div>
                  <div class="col-md-4 pl-1">
                    <div class="form-group">
                      <label>Postal Code</label>
                      <input type="number" class="form-control" placeholder="ZIP Code">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>About Me</label>
                      <textarea rows="4" cols="80" class="form-control textarea">Oh so, your weak rhyme You doubt I'll bother, reading into it</textarea>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
        
      </div>
    </div>
    
    
    <footer class="footer footer-black  footer-white ">
      <div class="container-fluid">
        <div class="row">
          <nav class="footer-nav">
            <ul>
              <li>
                <a href="https://www.creative-tim.com" target="_blank">Creative Tim</a>
              </li>
              <li>
                <a href="http://blog.creative-tim.com/" target="_blank">Blog</a>
              </li>
              <li>
                <a href="https://www.creative-tim.com/license" target="_blank">Licenses</a>
              </li>
            </ul>
          </nav>
          <div class="credits ml-auto">
              <span class="copyright">
                ©
                <script>
                  document.write(new Date().getFullYear())
                </script>, made with <i class="fa fa-heart heart"></i> by Creative Tim
              </span>
          </div>
        </div>
      </div>
    </footer>
  </div>
  
</div>
<!--   Core JS Files   -->
<script src="../../../assets/js/core/jquery.min.js"></script>
<script src="../../../assets/js/core/popper.min.js"></script>
<script src="../../../assets/js/core/bootstrap.min.js"></script>
<script src="../../../assets/js/plugins/perfect-scrollbar.jquery.min.js"></script>
<script src="../../../assets/js/plugins/moment.min.js"></script>
<!--  Plugin for Switches, full documentation here: http://www.jque.re/plugins/version3/bootstrap.switch/ -->
<script src="../../../assets/js/plugins/bootstrap-switch.js"></script>
<!--  Plugin for Sweet Alert -->
<script src="../../../assets/js/plugins/sweetalert2.min.js"></script>
<!-- Forms Validations Plugin -->
<script src="../../../assets/js/plugins/jquery.validate.min.js"></script>
<!--  Plugin for the Wizard, full documentation here: https://github.com/VinceG/twitter-bootstrap-wizard -->
<script src="../../../assets/js/plugins/jquery.bootstrap-wizard.js"></script>
<!--	Plugin for Select, full documentation here: http://silviomoreto.github.io/bootstrap-select -->
<script src="../../../assets/js/plugins/bootstrap-selectpicker.js"></script>
<!--  Plugin for the DateTimePicker, full documentation here: https://eonasdan.github.io/bootstrap-datetimepicker/ -->
<script src="../../../assets/js/plugins/bootstrap-datetimepicker.js"></script>
<!--  DataTables.net Plugin, full documentation here: https://datatables.net/    -->
<script src="../../../assets/js/plugins/jquery.dataTables.min.js"></script>
<!--	Plugin for Tags, full documentation here: https://github.com/bootstrap-tagsinput/bootstrap-tagsinputs  -->
<script src="../../../assets/js/plugins/bootstrap-tagsinput.js"></script>
<!-- Plugin for Fileupload, full documentation here: http://www.jasny.net/bootstrap/javascript/#fileinput -->
<script src="../../../assets/js/plugins/jasny-bootstrap.min.js"></script>
<!--  Full Calendar Plugin, full documentation here: https://github.com/fullcalendar/fullcalendar    -->
<script src="../../../assets/js/plugins/fullcalendar.min.js"></script>
<!-- Vector Map plugin, full documentation here: http://jvectormap.com/documentation/ -->
<script src="../../../assets/js/plugins/jquery-jvectormap.js"></script>
<!--  Plugin for the Bootstrap Table -->
<script src="../../../assets/js/plugins/nouislider.min.js"></script>
<!--  Google Maps Plugin    -->
<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_KEY_HERE"></script>
<!-- Chart JS -->
<script src="../../../assets/js/plugins/chartjs.min.js"></script>
<!--  Notifications Plugin    -->
<script src="../../../assets/js/plugins/bootstrap-notify.js"></script>
<!-- Control Center for Now Ui Dashboard: parallax effects, scripts for the example pages etc -->
<script src="../../../assets/js/paper-dashboard.min.js?v=2.0.1" type="text/javascript"></script>
<!-- Paper Dashboard DEMO methods, don't include it in your project! -->
<script src="../../../assets/demo/demo.js"></script>
</body>

</html>
