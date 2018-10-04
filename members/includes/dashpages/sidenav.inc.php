<?php

function outputSideNav($currentPage) {
  $active1 = '';

  $active2   = '';
  $cmExpand  = '';
  $cmExpand2 = '';

  $active3 = '';
  $active4 = '';
  $active5 = '';
  $active6 = '';
  $active7 = '';

  if ($currentPage == 'dashboard' && empty($_GET)) {
    $active1 = 'class="active"';
  } elseif ($currentPage == 'dashboard' && $_GET['p'] == 'cm') {
    $active2   = 'class="active"';
    $cmExpand  = ' aria-expanded="true"';
    $cmExpand2 = 'show';
  }

  echo '<div class="sidebar" data-active-color="info">
        <!--
          Tip 1: You can change the color of the sidebar using: data-color="blue | green | orange | red | yellow"
        -->
        <div class="logo">
          <a href="#" class="simple-text logo-mini">
            <div class="logo-image-small">
              <!-- <img src="assets/img/logo-small.png"> -->
            </div>
          </a>
          <a href="dashboard.php" class="simple-text logo-normal">
            PPCOLOGY
            <!-- <div class="logo-image-big">
              <img src="assets/img/logo-big.png">
            </div> -->
          </a>
        </div>
        <div class="sidebar-wrapper">
          <div class="user">
            <div class="info">
              <a data-toggle="collapse" href="#collapseExample" class="collapsed">
                <span>
                  ' . $_SESSION["first_name"] . ' ' . $_SESSION["last_name"] . '
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
                      <span class="sidebar-mini-icon">MPR</span>
                      <span class="sidebar-normal">My Products</span>
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
            <li ' . $active1 . '>
              <a href="dashboard.php">
                <i class="nc-icon nc-bank"></i>
                <p>Dashboard</p>
              </a>
            </li>
            <li>
              <a data-toggle="collapse" href="#cm"' . $cmExpand . '>
                <i class="nc-icon nc-book-bookmark"></i>
                <p>
                  Campaign Manager
                  <b class="caret"></b>
                </p>
              </a>
              <div class="collapse ' . $cmExpand2 . '" id="cm">
                <ul class="nav">
                  <li ' . $active2 . '>
                    <a href="dashboard.php?p=cm">
                      <span class="sidebar-mini-icon">VEC</span>
                      <span class="sidebar-normal"> View and Edit Campaigns </span>
                    </a>
                  </li>
                  <li ' . $active3 . '>
                    <a href="../examples/pages/login.html">
                      <span class="sidebar-mini-icon">PC</span>
                      <span class="sidebar-normal"> Profitable CSTs </span>
                    </a>
                  </li>
                  <li ' . $active4 . '>
                    <a href="../examples/pages/register.html">
                      <span class="sidebar-mini-icon">UC</span>
                      <span class="sidebar-normal"> Unprofitable CSTs </span>
                    </a>
                  </li>
                  <li ' . $active5 . '>
                    <a href="../examples/pages/lock.html">
                      <span class="sidebar-mini-icon">CW</span>
                      <span class="sidebar-normal"> CST Watchlist </span>
                    </a>
                  </li>
                  <li ' . $active6 . '>
                    <a href="../examples/pages/user.html">
                      <span class="sidebar-mini-icon">OS</span>
                      <span class="sidebar-normal"> Optimization Suggestions </span>
                    </a>
                  </li>
                </ul>
              </div>
            </li>
            <li ' . $active7 . '>
              <a href="#">
                <i class="nc-icon nc-bank"></i>
                <p>Keyword Rank Tracker</p>
              </a>
            </li>
          </ul>
        </div>
      </div>';
}

$currentPage = pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME);
outputSideNav($currentPage);
?>
