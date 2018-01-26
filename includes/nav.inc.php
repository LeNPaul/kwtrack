<?php

function outputNav($currentPage) {
  $place1 = '';
  $active1 = '';
  $place2 = '';
  $active2 = '';
  $place3 = '';
  $active3 = '';

  if ($currentPage == 'index') {
    $place1 = '<span class="sr-only">(current)</span>';
    $active1 = 'active';
  } else if ($currentPage == 'edittitles') {
    $place2 = '<span class="sr-only">(current)</span>';
    $active2 = 'active';    
  } else if ($currentPage == 'prodtracker') {
    $place3 = '<span class="sr-only">(current)</span>';
    $active3 = 'active';    
  }

  echo '<section id="navigation">
  <nav class="navbar navbar-expand-lg navbar-dark main-color-bg">
    <a class="navbar-brand" href="#"><h4>AG Dashboard</h4></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item ' . $active1 . '">
          <a class="nav-link" href="./index.php">Dashboard Home' . $place1 . '</a>
        </li>
        <li class="nav-item ' . $active2 . '">
          <a class="nav-link" href="./edittitles.php">Edit Short Titles ' . $place2 . '</a>
        </li>
        <li class="nav-item ' . $active3 . '">
          <a class="nav-link" href="./prodtracker.php">Product Tracker ' . $place3 . '</a>
        </li>
      </ul>
    </div>
  </nav>
</section>';
}

$currentPage = pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME);
outputNav($currentPage);
?>



