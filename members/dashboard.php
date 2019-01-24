<?php
/*
 * Main dashboard for users. Displays general advertising metrics for the user
 */

session_start();
require './database/pdo.inc.php';

// Check if user is not logged in. Redirect to login page if not logged in.
checkLoggedIn();

// Grab current active level to actively set $_SESSION['active']
$sql = 'SELECT active FROM users WHERE user_id=' . $_SESSION['user_id'];
$stmt = $pdo->query($sql);
$result = $stmt->fetchAll(PDO::FETCH_COLUMN);
$_SESSION['active'] = intval($result[0]);

$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <link rel="apple-touch-icon" sizes="76x76" href="assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="assets/img/favicon.png">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

  <title>
    PPCOLOGY
  </title>
  <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />

  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" />
  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" rel="stylesheet">

  <!-- CSS Files -->
  <link href="assets/css/bootstrap.min.css" rel="stylesheet" />
  <link href="assets/css/paper-dashboard.css?v=2.0.1" rel="stylesheet" />
  <link href="assets/css/dashboard_active5.css" rel="stylesheet" />
  <link href="assets/css/tabs.css" rel="stylesheet" />
  <link href="assets/css/cmanager.css" rel="stylesheet" />
  <link href="assets/css/loader.css" rel="stylesheet" />
  <link href="assets/css/settings.css" rel="stylesheet" />

  <link href="node_modules/flag-icon-css/assets/docs.css" rel="stylesheet">
  <link href="node_modules/flag-icon-css/css/flag-icon.css" rel="stylesheet">

  <!--  Datatables CSS Files -->
  <!-- <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.bootstrap4.min.css" /> -->
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedcolumns/3.2.5/css/fixedColumns.bootstrap4.min.css" />
  <!-- <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.2/css/responsive.bootstrap4.min.css" /> -->

  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt-1.10.18/b-1.5.4/b-colvis-1.5.4/fc-3.2.5/datatables.min.css"/>

  <!-- Date Range Picker CSS File -->
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

  <!--  SweetAlert2 CSS File -->
  <link rel="stylesheet" type="text/css" href="assets/css/sweetalert2.min.css" />

  <!--  Pretty Checkbox -->
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/pretty-checkbox@3.0/dist/pretty-checkbox.min.css" />
</head>

<body class="">
  <script src="assets/js/core/jquery.min.js"></script>
  <script src="assets/js/plugins/moment.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

  <div class="loading">Loading&#8230;</div>

  <div class="wrapper ">

    <?php require './includes/dashpages/sidenav.inc.php'?>

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
              <a class="navbar-brand" href="#pablo">PPCOLOGY</a>
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
                <li class="nav-item btn-rotate dropdown">
                  <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="nc-icon nc-settings-gear-65"></i>
                    <p>
                      <span class="d-lg-none d-md-block">Account</span>
                    </p>
                  </a>
                  <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                    <a class="dropdown-item" href="dashboard?p=s">Settings</a>
                    <a class="dropdown-item" href="logout.php">Logout</a>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </nav>
        <!-- End Navbar -->

        <!--   ORIGINAL CONTENT. EDIT THIS AFTER FINISHING DASHBOARD_ACTIVE1.PHP   -->
        <!--<div class="content">
          <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6">
              <div class="card card-stats">
                <div class="card-body ">
                  <div class="row">
                    <div class="col-5 col-md-4">
                      <div class="icon-big text-center icon-warning">
                        <i class="nc-icon nc-globe text-warning"></i>
                      </div>
                    </div>
                    <div class="col-7 col-md-8">
                      <div class="numbers">
                        <p class="card-category">Capacity</p>
                        <p class="card-title">150GB
                          <p>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="card-footer ">
                  <hr>
                  <div class="stats">
                    <i class="fa fa-refresh"></i> Update Now
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
              <div class="card card-stats">
                <div class="card-body ">
                  <div class="row">
                    <div class="col-5 col-md-4">
                      <div class="icon-big text-center icon-warning">
                        <i class="nc-icon nc-money-coins text-success"></i>
                      </div>
                    </div>
                    <div class="col-7 col-md-8">
                      <div class="numbers">
                        <p class="card-category">Revenue</p>
                        <p class="card-title">$ 1,345
                          <p>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="card-footer ">
                  <hr>
                  <div class="stats">
                    <i class="fa fa-calendar-o"></i> Last day
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
              <div class="card card-stats">
                <div class="card-body ">
                  <div class="row">
                    <div class="col-5 col-md-4">
                      <div class="icon-big text-center icon-warning">
                        <i class="nc-icon nc-vector text-danger"></i>
                      </div>
                    </div>
                    <div class="col-7 col-md-8">
                      <div class="numbers">
                        <p class="card-category">Errors</p>
                        <p class="card-title">23
                          <p>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="card-footer ">
                  <hr>
                  <div class="stats">
                    <i class="fa fa-clock-o"></i> In the last hour
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
              <div class="card card-stats">
                <div class="card-body ">
                  <div class="row">
                    <div class="col-5 col-md-4">
                      <div class="icon-big text-center icon-warning">
                        <i class="nc-icon nc-favourite-28 text-primary"></i>
                      </div>
                    </div>
                    <div class="col-7 col-md-8">
                      <div class="numbers">
                        <p class="card-category">Followers</p>
                        <p class="card-title">+45K
                          <p>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="card-footer ">
                  <hr>
                  <div class="stats">
                    <i class="fa fa-refresh"></i> Update now
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-4 col-sm-6">
              <div class="card">
                <div class="card-header">
                  <div class="row">
                    <div class="col-sm-7">
                      <div class="numbers pull-left">
                        $34,657
                      </div>
                    </div>
                    <div class="col-sm-5">
                      <div class="pull-right">
                        <span class="badge badge-pill badge-success">
                          +18%
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="card-body">
                  <h6 class="big-title">total earnings in last ten quarters</h6>
                  <canvas id="activeUsers" width="826" height="380"></canvas>
                </div>
                <div class="card-footer">
                  <hr>
                  <div class="row">
                    <div class="col-sm-7">
                      <div class="footer-title">Financial Statistics</div>
                    </div>
                    <div class="col-sm-5">
                      <div class="pull-right">
                        <button class="btn btn-success btn-round btn-icon btn-sm">
                          <i class="nc-icon nc-simple-add"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="card">
                <div class="card-header">
                  <div class="row">
                    <div class="col-sm-7">
                      <div class="numbers pull-left">
                        169
                      </div>
                    </div>
                    <div class="col-sm-5">
                      <div class="pull-right">
                        <span class="badge badge-pill badge-danger">
                          -14%
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="card-body">
                  <h6 class="big-title">total subscriptions in last 7 days</h6>
                  <canvas id="emailsCampaignChart" width="826" height="380"></canvas>
                </div>
                <div class="card-footer">
                  <hr>
                  <div class="row">
                    <div class="col-sm-7">
                      <div class="footer-title">View all members</div>
                    </div>
                    <div class="col-sm-5">
                      <div class="pull-right">
                        <button class="btn btn-danger btn-round btn-icon btn-sm">
                          <i class="nc-icon nc-button-play"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="card">
                <div class="card-header">
                  <div class="row">
                    <div class="col-sm-7">
                      <div class="numbers pull-left">
                        8,960
                      </div>
                    </div>
                    <div class="col-sm-5">
                      <div class="pull-right">
                        <span class="badge badge-pill badge-warning">
                          ~51%
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="card-body">
                  <h6 class="big-title">total downloads in last 6 years</h6>
                  <canvas id="activeCountries" width="826" height="380"></canvas>
                </div>
                <div class="card-footer">
                  <hr>
                  <div class="row">
                    <div class="col-sm-7">
                      <div class="footer-title">View more details</div>
                    </div>
                    <div class="col-sm-5">
                      <div class="pull-right">
                        <button class="btn btn-warning btn-round btn-icon btn-sm">
                          <i class="nc-icon nc-alert-circle-i"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="card ">
                <div class="card-header ">
                  <h4 class="card-title">Global Sales by Top Locations</h4>
                  <p class="card-category">All products that were shipped</p>
                </div>
                <div class="card-body ">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="table-responsive">
                        <table class="table">
                          <tbody>
                            <tr>
                              <td>
                                <div class="flag">
                                  <img src="assets/img/flags/US.png">
                                </div>
                              </td>
                              <td>USA</td>
                              <td class="text-right">
                                2.920
                              </td>
                              <td class="text-right">
                                53.23%
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <div class="flag">
                                  <img src="assets/img/flags/DE.png">
                                </div>
                              </td>
                              <td>Germany</td>
                              <td class="text-right">
                                1.300
                              </td>
                              <td class="text-right">
                                20.43%
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <div class="flag">
                                  <img src="assets/img/flags/AU.png">
                                </div>
                              </td>
                              <td>Australia</td>
                              <td class="text-right">
                                760
                              </td>
                              <td class="text-right">
                                10.35%
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <div class="flag">
                                  <img src="assets/img/flags/GB.png">
                                </div>
                              </td>
                              <td>United Kingdom</td>
                              <td class="text-right">
                                690
                              </td>
                              <td class="text-right">
                                7.87%
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <div class="flag">
                                  <img src="assets/img/flags/RO.png">
                                </div>
                              </td>
                              <td>Romania</td>
                              <td class="text-right">
                                600
                              </td>
                              <td class="text-right">
                                5.94%
                              </td>
                            </tr>
                            <tr>
                              <td>
                                <div class="flag">
                                  <img src="assets/img/flags/BR.png">
                                </div>
                              </td>
                              <td>Brasil</td>
                              <td class="text-right">
                                550
                              </td>
                              <td class="text-right">
                                4.34%
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                    <div class="col-md-6 ml-auto mr-auto">
                      <div id="worldMap" style="height: 300px;"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="card  card-tasks">
                <div class="card-header ">
                  <h4 class="card-title">Tasks</h4>
                  <h5 class="card-category">Backend development</h5>
                </div>
                <div class="card-body ">
                  <div class="table-full-width table-responsive">
                    <table class="table">
                      <tbody>
                        <tr>
                          <td>
                            <div class="form-check">
                              <label class="form-check-label">
                                <input class="form-check-input" type="checkbox" checked>
                                <span class="form-check-sign"></span>
                              </label>
                            </div>
                          </td>
                          <td class="img-row">
                            <div class="img-wrapper">
                              <img src="assets/img/faces/ayo-ogunseinde-2.jpg" class="img-raised" />
                            </div>
                          </td>
                          <td class="text-left">Sign contract for "What are conference organizers afraid of?"</td>
                          <td class="td-actions text-right">
                            <button type="button" rel="tooltip" title="" class="btn btn-info btn-round btn-icon btn-icon-mini btn-neutral" data-original-title="Edit Task">
                              <i class="nc-icon nc-ruler-pencil"></i>
                            </button>
                            <button type="button" rel="tooltip" title="" class="btn btn-danger btn-round btn-icon btn-icon-mini btn-neutral" data-original-title="Remove">
                              <i class="nc-icon nc-simple-remove"></i>
                            </button>
                          </td>
                        </tr>
                        <tr>
                          <td>
                            <div class="form-check">
                              <label class="form-check-label">
                                <input class="form-check-input" type="checkbox">
                                <span class="form-check-sign"></span>
                              </label>
                            </div>
                          </td>
                          <td class="img-row">
                            <div class="img-wrapper">
                              <img src="assets/img/faces/erik-lucatero-2.jpg" class="img-raised" />
                            </div>
                          </td>
                          <td class="text-left">Lines From Great Russian Literature? Or E-mails From My Boss?</td>
                          <td class="td-actions text-right">
                            <button type="button" rel="tooltip" title="" class="btn btn-info btn-round btn-icon btn-icon-mini btn-neutral" data-original-title="Edit Task">
                              <i class="nc-icon nc-ruler-pencil"></i>
                            </button>
                            <button type="button" rel="tooltip" title="" class="btn btn-danger btn-round btn-icon btn-icon-mini btn-neutral" data-original-title="Remove">
                              <i class="nc-icon nc-simple-remove"></i>
                            </button>
                          </td>
                        </tr>
                        <tr>
                          <td>
                            <div class="form-check">
                              <label class="form-check-label">
                                <input class="form-check-input" type="checkbox" checked>
                                <span class="form-check-sign"></span>
                              </label>
                            </div>
                          </td>
                          <td class="img-row">
                            <div class="img-wrapper">
                              <img src="assets/img/faces/kaci-baum-2.jpg" class="img-raised" />
                            </div>
                          </td>
                          <td class="text-left">Using dummy content or fake information in the Web design process can result in products with unrealistic
                          </td>
                          <td class="td-actions text-right">
                            <button type="button" rel="tooltip" title="" class="btn btn-info btn-round btn-icon btn-icon-mini btn-neutral" data-original-title="Edit Task">
                              <i class="nc-icon nc-ruler-pencil"></i>
                            </button>
                            <button type="button" rel="tooltip" title="" class="btn btn-danger btn-round btn-icon btn-icon-mini btn-neutral" data-original-title="Remove">
                              <i class="nc-icon nc-simple-remove"></i>
                            </button>
                          </td>
                        </tr>
                        <tr>
                          <td>
                            <div class="form-check">
                              <label class="form-check-label">
                                <input class="form-check-input" type="checkbox">
                                <span class="form-check-sign"></span>
                              </label>
                            </div>
                          </td>
                          <td class="img-row">
                            <div class="img-wrapper">
                              <img src="assets/img/faces/joe-gardner-2.jpg" class="img-raised" />
                            </div>
                          </td>
                          <td class="text-left">But I must explain to you how all this mistaken idea of denouncing pleasure</td>
                          <td class="td-actions text-right">
                            <button type="button" rel="tooltip" title="" class="btn btn-info btn-round btn-icon btn-icon-mini btn-neutral" data-original-title="Edit Task">
                              <i class="nc-icon nc-ruler-pencil"></i>
                            </button>
                            <button type="button" rel="tooltip" title="" class="btn btn-danger btn-round btn-icon btn-icon-mini btn-neutral" data-original-title="Remove">
                              <i class="nc-icon nc-simple-remove"></i>
                            </button>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
                <div class="card-footer ">
                  <hr>
                  <div class="stats">
                    <i class="fa fa-refresh spin"></i> Updated 3 minutes ago
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="card ">
                <div class="card-header ">
                  <h4 class="card-title">2018 Sales</h4>
                  <p class="card-category">All products including Taxes</p>
                </div>
                <div class="card-body ">
                  <canvas id="chartActivity"></canvas>
                </div>
                <div class="card-footer ">
                  <div class="legend">
                    <i class="fa fa-circle text-info"></i> Tesla Model S
                    <i class="fa fa-circle text-danger"></i> BMW 5 Series
                  </div>
                  <hr>
                  <div class="stats">
                    <i class="fa fa-check"></i> Data information certified
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-3">
              <div class="card ">
                <div class="card-header ">
                  <h5 class="card-title">Email Statistics</h5>
                  <p class="card-category">Last Campaign Performance</p>
                </div>
                <div class="card-body ">
                  <canvas id="chartDonut1" class="ct-chart ct-perfect-fourth" width="456" height="300"></canvas>
                </div>
                <div class="card-footer ">
                  <div class="legend">
                    <i class="fa fa-circle text-info"></i> Open
                  </div>
                  <hr>
                  <div class="stats">
                    <i class="fa fa-calendar"></i> Number of emails sent
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card ">
                <div class="card-header ">
                  <h5 class="card-title">New Visitators</h5>
                  <p class="card-category">Out Of Total Number</p>
                </div>
                <div class="card-body ">
                  <canvas id="chartDonut2" class="ct-chart ct-perfect-fourth" width="456" height="300"></canvas>
                </div>
                <div class="card-footer ">
                  <div class="legend">
                    <i class="fa fa-circle text-warning"></i> Visited
                  </div>
                  <hr>
                  <div class="stats">
                    <i class="fa fa-check"></i> Campaign sent 2 days ago
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card ">
                <div class="card-header ">
                  <h5 class="card-title">Orders</h5>
                  <p class="card-category">Total number</p>
                </div>
                <div class="card-body ">
                  <canvas id="chartDonut3" class="ct-chart ct-perfect-fourth" width="456" height="300"></canvas>
                </div>
                <div class="card-footer ">
                  <div class="legend">
                    <i class="fa fa-circle text-danger"></i> Completed
                  </div>
                  <hr>
                  <div class="stats">
                    <i class="fa fa-clock-o"></i> Updated 3 minutes ago
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card ">
                <div class="card-header ">
                  <h5 class="card-title">Subscriptions</h5>
                  <p class="card-category">Our Users</p>
                </div>
                <div class="card-body ">
                  <canvas id="chartDonut4" class="ct-chart ct-perfect-fourth" width="456" height="300"></canvas>
                </div>
                <div class="card-footer ">
                  <div class="legend">
                    <i class="fa fa-circle text-secondary"></i> Ended
                  </div>
                  <hr>
                  <div class="stats">
                    <i class="fa fa-history"></i> Total users
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>-->

        <div class="content">
          <?php
          //echo $_SERVER['DOCUMENT_ROOT'] . '<br>';
          if ($_SESSION['active'] == 0 && empty($_GET['p'])) {
            include './includes/dashpages/main/dashboard_active0.php';
          } elseif ($_SESSION['active'] == 1 && empty($_GET['p'])) {
            include './includes/dashpages/main/dashboard_active1.php';
          } elseif ($_SESSION['active'] == 2 && empty($_GET['p'])) {
            include './includes/dashpages/main/dashboard_active2.php';
          } elseif ($_SESSION['active'] == 3 && empty($_GET['p'])) {
            include './includes/dashpages/main/dashboard_active3.php';
          } elseif ($_SESSION['active'] == 4 && empty($_GET['p'])) {
            include './includes/dashpages/main/dashboard_active4.php';
          } elseif ($_SESSION['active'] == 5 && empty($_GET['p'])) {
            include './includes/dashpages/main/dashboard_active5.php';
          } elseif (isset($_GET['p']) && $_GET['p'] == 'cm') {
            include './includes/dashpages/cmanager/campaign_manager.php';
          } elseif (isset($_GET['p']) && $_GET['p'] == 'cm2') {
            include './includes/dashpages/cmanager/campaign_manager2.php';
          }elseif (isset($_GET['p']) && $_GET['p'] == 's') {
            include './includes/dashpages/profile/settings.php';
          } elseif (isset($_GET['p']) && $_GET['p'] == 'as' && isset($_GET['sp']) && $_GET['sp'] == 'e') {
            include './includes/dashpages/scheduler/edit_schedule.php';
          } elseif (isset($_GET['p']) && $_GET['p'] == 'as') {
            include './includes/dashpages/scheduler/scheduler.php';
          }
          ?>
        </div>


        <!-- <footer class="footer footer-black  footer-white ">
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
        </footer> -->

    </div>
  </div>

  <!--   Core JS Files   -->
  <script src="assets/js/core/popper.min.js"></script>
  <script src="assets/js/core/bootstrap.min.js"></script>
  <script src="assets/js/plugins/perfect-scrollbar.jquery.min.js"></script>
  <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
  <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" type="text/css" rel="stylesheet">
  <!--  Plugin for Switches, full documentation here: http://www.jque.re/plugins/version3/bootstrap.switch/ -->
  <script src="assets/js/plugins/bootstrap-switch.js"></script>
  <!--  Plugin for Sweet Alert -->
  <script src="https://cdn.jsdelivr.net/npm/promise-polyfill@8/dist/polyfill.js"></script>
  <script src="assets/js/plugins/sweetalert2.min.js"></script>
  <!-- Forms Validations Plugin -->
  <script src="assets/js/plugins/jquery.validate.min.js"></script>
  <!--  Plugin for the Wizard, full documentation here: https://github.com/VinceG/twitter-bootstrap-wizard -->
  <script src="assets/js/plugins/jquery.bootstrap-wizard.js"></script>
  <!--	Plugin for Select, full documentation here: http://silviomoreto.github.io/bootstrap-select -->
  <script src="assets/js/plugins/bootstrap-selectpicker.js"></script>
  <!--  Plugin for the DateTimePicker, full documentation here: https://eonasdan.github.io/bootstrap-datetimepicker/ -->
  <script src="assets/js/plugins/bootstrap-datetimepicker.js"></script>

  <!--  DataTables.net Plugin, full documentation here: https://datatables.net/    -->
  <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.18/b-1.5.4/b-colvis-1.5.4/fc-3.2.5/sl-1.2.6/datatables.min.js"></script>

  <!--<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>

  <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/fixedcolumns/3.2.5/js/dataTables.fixedColumns.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.2.2/js/dataTables.responsive.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.2.2/js/responsive.bootstrap4.min.js"></script>
  <script src="https://cdn.datatables.net/select/1.2.6/js/dataTables.select.min.js"></script>-->


  <!--	Plugin for Tags, full documentation here: https://github.com/bootstrap-tagsinput/bootstrap-tagsinputs  -->
  <script src="assets/js/plugins/bootstrap-tagsinput.js"></script>
  <!-- Plugin for Fileupload, full documentation here: http://www.jasny.net/bootstrap/javascript/#fileinput -->
  <script src="assets/js/plugins/jasny-bootstrap.min.js"></script>
  <!--  Full Calendar Plugin, full documentation here: https://github.com/fullcalendar/fullcalendar    -->
  <script src="assets/js/plugins/fullcalendar.min.js"></script>
  <!-- Vector Map plugin, full documentation here: http://jvectormap.com/documentation/ -->
  <script src="assets/js/plugins/jquery-jvectormap.js"></script>
  <!--  Plugin for the Bootstrap Table -->
  <script src="assets/js/plugins/nouislider.min.js"></script>
  <!--  Google Maps Plugin  -->
  <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_KEY_HERE"></script>
  <!-- Chart JS -->
  <!--  <script src="assets/js/plugins/chartjs.min.js"></script>-->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js"></script>
  <!--  Notifications Plugin    -->
  <script src="assets/js/plugins/bootstrap-notify.js"></script>
  <!-- Control Center for Now Ui Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="assets/js/paper-dashboard.min.js?v=2.0.1" type="text/javascript"></script>

  <script>
    $(window).on("load", (function() {
      $(".loading").fadeOut("slow");
    }));
  </script>
</body>

</html>
