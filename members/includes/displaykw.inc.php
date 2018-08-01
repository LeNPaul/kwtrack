<?php
include_once( $_SERVER['DOCUMENT_ROOT'] . '/database/pdo.inc.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/includes/rankfinder.inc.php');
require($_SERVER['DOCUMENT_ROOT'] . '/charts/includes/ChartJS.php');

if (!$pdo->query('SELECT asin FROM asins')->fetch(PDO::FETCH_ASSOC)) {
  echo '<div class="alert alert-danger" role="alert">Mans never input any ASINS styll eh</div>';
}

/*
 *  genAsinDelModal(String $asin, String $shortTitle, Int $id) => String $modalHTML
 *    --> This function generates a modal popup and is called whenever an ASIN's delete button is pressed
 *
 *      --> String $asin        - The asin to generate the deletion modal for
 *      --> String $shortTitle  - The short title of $asin
 *      --> Int $id             - The unique id to be assigned to the modal
 *      --> String $modalHTML   - Bootstrap modal HTML being generated
 */

function genAsinDelModal($asin, $shortTitle, $id) {
  return '<div class="modal fade inverted" id="modalDel' . $id . '" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Delete '.$asin.'?</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <p>Are you sure you want to delete <b>' . $shortTitle . '</b>?</p>
                </div>
                <div class="modal-footer">
                  <form method="post">
                    <button type="submit" name="btnDelAsin" value="' . $asin . '" class="btn btn-danger">Delete <b> '. $asin . '</b></button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  </form>
                </div>
              </div>
            </div>
          </div>';
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
                <div class="row">
                  <div class="col-md-11">
                    <a role="tab" id="heading'.$i.'" data-toggle="collapse" href="#collapse'.$i.'" aria-expanded="true" aria-controls="collapse'.$i.'">'
                      . $title['prod_short_title'] . '
                    </a>
                  </div>
                  <div class="col-md-1">
                    <div class="container">
                      
                      <button type="button" name="btnDeleteAsin" data-toggle="modal" data-target="#modalDel' . $i . '" class="btn btn-danger del-asin">
                        <i class="icon-trash"></i>
                      </button>
                      
                      ' . genAsinDelModal($asinList[$i]['asin'], $title['prod_short_title'], $i) . '
                      
                    </div>
                  </div>
                </div>
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
    
    // Get asin_id so we can count how many keywords the current asin has in the iteration
    $sql = 'SELECT asin_id FROM asins WHERE asin="' . $asinList[$i]['asin'] . '"' ;
    $asin_id = $pdo->query($sql)->fetch(PDO::FETCH_COLUMN);
    
    // Get count of keywords for asin
    $sql = 'SELECT COUNT(*) FROM keywords WHERE asin_id="' . $asin_id . '"';
    $kwCount = intval($pdo->query($sql)->fetch(PDO::FETCH_COLUMN));
  
    // Retrieve kws associated with current ASIN in iteration
    $sql = 'SELECT * FROM (
                SELECT keywords.keyword, oldranks.page AS `page`, oldranks.rank AS `rank`, oldranks.rank_id
                FROM asins
                  JOIN keywords JOIN oldranks
                ON asins.asin_id = keywords.asin_id
                AND oldranks.kw_id = keywords.kw_id
                AND asins.asin="'.$asinList[$i]['asin'].'"
              GROUP BY oldranks.rank_id
              ORDER BY oldranks.rank_id DESC LIMIT ' . $kwCount . '
            ) AS `results`';
    
    if ($_GET['kwsort'] == 'page') {
      $sql .= ' ORDER BY page ASC';
    } elseif ($_GET['kwsort'] == 'rank') {
      $sql .= ' ORDER BY page ASC, rank ASC';
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
        'layout' => array(
          'padding' => array(
            'top'     => 1
          )
        ),
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
        'borderWidth'      => 2,
        'borderColor'      => 'lightblue',
        'fill'             => false,
        'lineTension'      => 0.4,
        'pointRadius'      => 0
      );
      $attributes = array('height' => 75, 'width' => 200);
      // Set x-axis labels to 7 days
      $trendGraph = new ChartJS('line', [1,2,3,4,5,6,7], $options, $attributes);
      $trendGraph->addDataset($dataset);
      
      /* FINISH TREND GRAPH BS */
  
      /* Find out historical rank data for current keyword so we can determine an increase or decrease in rank */
      $sql = 'SELECT page,rank FROM oldranks WHERE kw_id=' . $kw_id . ' ORDER BY rank_id DESC LIMIT 2';
      $oldranks = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

      // Turn ranks into integers so we can compare them
      $oldranks[0]['page'] = intval($oldranks[0]['page']);
      $oldranks[0]['rank'] = intval($oldranks[0]['rank']);
      $oldranks[1]['page'] = intval($oldranks[1]['page']);
      $oldranks[1]['rank'] = intval($oldranks[1]['rank']);
      
      if ($oldranks[1]['page'] > $oldranks[0]['page']) {
        $pageIcon = '<i class="icon-long-arrow-up text-success"></i>';
      } else if ($oldranks[1]['page'] == $oldranks[0]['page']) {
        $pageIcon = '<i class="icon-long-arrow-right text-warning"></i>';
      } else {
        $pageIcon = '<i class="icon-long-arrow-down text-danger"></i>';
      }
      
      if ($oldranks[1]['rank'] > $oldranks[0]['rank'] || $oldranks[1]['page'] > $oldranks[0]['page']) {
        $rankIcon = '<i class="icon-long-arrow-up text-success"></i>';
      } else if ($oldranks[1]['rank'] == $oldranks[0]['rank']) {
        $rankIcon = '<i class="icon-long-arrow-right text-warning"></i>';
      } else {
        $rankIcon = '<i class="icon-long-arrow-down text-danger"></i>';
      }


      /* Finish finding historical rank data */

      echo('<tr>
        <td>' . $kwToAsinList[$j]['keyword'] . "</td>
        <td> $trendGraph </td>
        <td>" . $rankOfKw['page'] . ' ' . $pageIcon .'</td>
        <td>' . $rankOfKw['rank'] . ' ' . $rankIcon .'</td>
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


outputCards($pdo);

?>