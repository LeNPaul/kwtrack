<?php

/*
 * getTitleFromASIN(String $asin) => String $title 
 *    -->  Retrieves title of the provided ASIN and inserts it into db
 */
function getTitleFromASIN($asin) {
  $url = 'https://www.amazon.com/dp/' . $asin;
  $page = hQuery::fromUrl($url, ['Accept' => 'text/html,application/xhtml+xml;q=0.9,*/*;q=0.8']);
  $result = $page->find('#productTitle');
  $title = '';
  if ( $result ) {
    foreach ($result as $i => $e) {
      $title = trim($e->text());
    }
  }
  return $title;
}

function insertASIN($pdo, $asin, $shortTitle) {
  $title = getTitleFromASIN($asin);
  $sql = 'INSERT INTO asins (asin, prod_title, prod_short_title) VALUES (:x, :title, :shortTitle)';
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(
    ':x'          => $asin,
    ':title'      => $title,
    ':shortTitle' => $shortTitle
  ));
}


?>