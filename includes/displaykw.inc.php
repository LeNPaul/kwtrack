<?php
include_once('./database/pdo.inc.php');
include_once('./includes/rankfinder.inc.php');
require('./charts/includes/ChartJS.php');

if (!$pdo->query('SELECT asin FROM asins')->fetch(PDO::FETCH_ASSOC)) {
  echo '<div class="alert alert-danger" role="alert">Mans never input any ASINS styll eh</div>';
}

function outputCards($pdo) {
  $asinList = $pdo->query('SELECT asin FROM asins')->fetchAll(PDO::FETCH_ASSOC);

  for ($i = 0; $i < sizeof($asinList); $i++) {
    // Query short title from db
    $sql = 'SELECT prod_short_title FROM asins WHERE asin="'. $asinList[$i]['asin'] . '"';
    $title = $pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
    echo('<div id="accordion" role="tablist">
            <div class="card">
              <div class="card-header asin-header">
                <a role="tab" id="heading'.$i.'" data-toggle="collapse" href="#collapse'.$i.'" aria-expanded="true" aria-controls="collapse'.$i.'">'. $title['prod_short_title'] .'</a>
              </div>

              <div id="collapse'.$i.'" class="collapse" role="tabpanel" aria-labelledby="heading'.$i.'" data-parent="#accordion">
                <div class="card-body">
                  <table class="table table-hover">
                    <tr>
                      <th>Keyword</th>
                      <th>Trend Graph</th>
                      <th><a href="?kwsort=page">Page</a></th>
                      <th><a href="?kwsort=rank">Rank</a></th>
                      <th>Del</th>
                    </tr>
    ');
    // Retrieve kws associated with current ASIN in iteration
    $sql = 'SELECT keywords.keyword, oldranks.page, oldranks.rank, MAX(oldranks.rank_id) 
            FROM asins 
              JOIN keywords JOIN oldranks
            ON asins.asin_id = keywords.asin_id 
            AND oldranks.kw_id = keywords.kw_id
            AND asins.asin="'.$asinList[$i]['asin'].'" 
            GROUP BY keywords.keyword';
    if ($_GET['kwsort'] == 'page') {
      $sql .= ' ORDER BY oldranks.page ASC';
    } elseif ($_GET['kwsort'] == 'rank') {
      $sql .= ' ORDER BY oldranks.page ASC, oldranks.rank ASC';      
    }

    $kwToAsinList = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    // Output tr's w/ kw rankings
    for ($j = 0; $j < sizeof($kwToAsinList); $j++) {
      // First, find kw_id of current kw
      $sql = 'SELECT kw_id FROM keywords WHERE keyword="'.$kwToAsinList[$j]['keyword'].'"';
      $kw_id = $pdo->query($sql)->fetchColumn();

      // Then, find all historical rank data of current kw and place it in $rankData
      $sql = 'SELECT * FROM oldranks WHERE kw_id="' . $kw_id . '"';
      $result = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
      $rankData = array();
      for ($x = 0; $x < sizeof($result); $x++) {
        $currentRank;
        if (intval($result[$x]['page']) > 1) {
          $currentRank = -1 * (17 * intval($result[$x]['page']) + intval($result[$x]['rank']));
          array_push($rankData, $currentRank);
        } else {
          $currentRank = -1 * intval($result[$x]['rank']);
          array_push($rankData, $currentRank);
        }
      }
      
      /*
      echo $kwToAsinList[$j]['keyword'] . ':';
      echo $result[sizeof($result) - 1]['page'] . ', ';
      echo $result[sizeof($result) - 1]['rank'].'<br>';
      */

      // Put rank of current keyword in array $rankOfKw
      //    --> latest rank of keyword will be the LAST entry of $result
      $rankOfKw = array(
        'page' => $result[sizeof($result) - 1]['page'],
        'rank' => $result[sizeof($result) - 1]['rank']
      );

      // Define maximum value of current trend graph
      $maxValue = max($rankData);

      /* START GENERATING TREND GRAPHS */

      $options = array(
        'responsive' => false,
        'animation' => false,
        'maintainAspectRatio' => false,
        'legend' => array( 'display' => false ),
        'scales' => array(

          'xAxes' => array(array(
            'display' => false,
            'gridLines' => array( 'display' => false )
          )
          ),

          'yAxes' => array(array(
            'display' => false,
            'gridLines' => array(  'display' => false ),

            'ticks' => array(
              'beginAtZero' => true,
              'stepSize'   => 1,
              'max'        => $maxValue
            )
          )
          )
        )
      );
      $dataset = array(
        'data'             => $rankData,
        'borderWidth'      => 1.5,
        'borderColor'      => 'lightblue',
        'fill'             => false,
        'lineTension'      => 0,
        'pointRadius'      => 0
      );
      $attributes = array('height' => 50, 'width' => 200);
      // Set x-axis labels to 7 days
      $trendGraph = new ChartJS('line', [1,2,3,4,5,6,7], $options, $attributes);
      $trendGraph->addDataset($dataset);

      /* FINISH TREND GRAPH BS */

      echo('<tr>
        <td>' . $kwToAsinList[$j]['keyword'] . "</td>
        <td> $trendGraph </td>
        <td>" . $rankOfKw['page'] . '</td>
        <td>' . $rankOfKw['rank'] . '</td>
        <td>
          <form method="POST">
            <button type="submit" name="btnDeleteKw" value="'. $kwToAsinList[$j]['keyword'] .'" class="btn btn-danger del-kw"><i class="icon-trash"></i></button>
          </form>
        </td>
      </tr>');
    }
    echo('</table></div></div></div></div><hr>');
    
  }
}


// If delete button is pressed, then run this shit
if (!empty($_POST['btnDeleteKw'])) {
  deleteKw($pdo, $_POST['btnDeleteKw']);
  echo createAlert('success', '<b>'. $_POST['btnDeleteKw'] . '</b> has been deleted.');
}

outputCards($pdo);

?>