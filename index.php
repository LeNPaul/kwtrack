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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>  

<!-- <script type="text/javascript">
  function generateChart(data) {
        let json = $.parseJSON(data);
        console.log(json);
        let ranks = [];
        for (let i = 0; i < json.length; i++) {
          let currentRank;
          if (json[i]['page'] > 1) {
            currentRank = -1 * 17 * parseInt(json[i]['page']) + parseInt(json[i]['rank']);
            ranks.push(parseInt(currentRank));
          } else {
            currentRank = -1 * parseInt(json[i]['rank']);
            ranks.push(parseInt(currentRank));          
          }
        }
  
        let maxValue = ranks.reduce((a, b) => Math.max(a, b));
        console.log(ranks);
  
        $('.chart').each((index, element) => {
          let chartData = {
            labels:[1,2,3,4,5,6,7],
            datasets: [{
              label: "rank",
              borderWidth: 1.5,
              data: ranks,
              backgroundColor: "red",
              borderColor: "lightblue",
              fill: false,
              lineTension: 0,
              pointRadius: 0
            }]
          };
    
          let options = {
            maintainAspectRatio: false,
            legend: { display: false },
            scales: {
              xAxes: [{
                display: false,
                gridLines: { display: false }
              }],
              yAxes: [{
                display: false,
                gridLines: { display: false },
                ticks: { 
                  beginAtZero: true,
                  stepSize: 1,
                  max: maxValue
                }
              }]
            }
          };
  
          let ctx = element.getContext('2d');
          window.myLine = new Chart(ctx, {
            type: "line",
            data: chartData,
            options: options
          });
        });
</script> -->



<section id="navigation">
    <nav class="navbar navbar-expand-lg navbar-dark main-color-bg">
      <a class="navbar-brand" href="#"><h4>AG Dashboard</h4></a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item active">
            <a class="nav-link" href="./index.php">Dashboard Home<span class="sr-only">(current)</span></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="./edittitles.php">Edit Short Titles</a>
          </li>
        </ul>
      </div>
    </nav>
</section>

<header id="header">
  <div class="container">
    <h3><i class="icon-gear"></i> Dashboard - Edit keywords<small></small></h3>
  </div>
</header>


<section id="main-content">
  <div class="container">
    <div class="row">

      <div class="col-md-3">
        
        <div class="card">
          <p class="card-header main-color-bg" id="main-content-bar"><i class="icon-pencil"></i> Add New Keyword</p>
          <div class="card-body">
            <form method="POST" class="card-text">
              <div class="form-group">

                <?php include_once './includes/addkw.inc.php'; ?>

                <label>Keyword</label>
                <input type="text" class="form-control" name="keyword">
              </div>

              <div class="form-group">
                <label>ASIN</label>
                <?php generateDropdown($pdo) ?>
              </div>

              <button class="btn btn-primary" type="submit" name="addkw" value="y">Add Keyword</button>
            </form>
          </div>
        </div>

        <hr>

        <div class="card">
          <p class="card-header main-color-bg" id="main-content-bar"><i class="icon-book"></i> Add New ASIN</p>
          <div class="card-body">

            <?php include_once './includes/insertasin.inc.php'; ?>

            <form method="POST">
              <div class="form-group">
                <label>ASIN</label>
                <input type="text" class="form-control" name="newAsin">
              </div>
              <div class="form-group">
                <label>Product Short Title</label>
                <input type="text" class="form-control" name="prodShortTitle">
              </div>
              <button class="btn btn-primary" name="addAsin" value="x" type="submit">Add ASIN</button>
            </form>
          </div>
        </div>
        
      </div>

      <div class="col-md-9">
        <div class="card">
          <div class="card-header main-color-bg" id="main-content-bar">
            <div class="row">
              <div class="col-md-8">
                <i class="icon-reorder"></i> Keywords
              </div>
              <div class="col-md-4">
                <form method="POST">
                  <button type="submit" name="btnUpdateRanks" value="y" class="btn btn-success" href="#">Update Keyword Rankings</button>
                </form>
              </div>
            </div>
          </div>

          <div class="card-body">
            <?php include_once './includes/rankfinder.inc.php'; ?>
            <!-- TABLE OF KWS -->
            <?php include_once '/includes/displaykw.inc.php'; ?>
          </div>
        </div>
      </div>
    
    </div>
  </div>
</section>


<!-- <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script> -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
<script src="./charts/includes/Chart.js"></script>
<script src="./charts/includes/driver.js"></script>
    <script>
      (function() {
        loadChartJsPhp();
      })();
    </script>
</body>
</html>