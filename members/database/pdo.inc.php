<?php
use PDO;

require_once $_SERVER['DOCUMENT_ROOT'] . '/members/node_modules/hquery.php/hquery.php';

// Set the cache path 
hQuery::$cache_path = "./cache";

try {
  $pdo = new PDO('mysql:host=localhost;port=3306;dbname=pp554547_ppcology', 'pp554547_ppc', '@Rl)CWz6N;d&');
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $er) {
  echo $e->getMessage();
}

function createAlert($contextual, $alertMsg) {
  $alertHTML = '<div class="alert fade show alert-dismissable alert-'.$contextual.'" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                  </button>
                  '. $alertMsg .'
                </div>';
  return $alertHTML;
}

/* HELPER FUNCTIONS */

/*
 *
 *
 *
 */
function executeDb($pdo, $sql, $bindedArr) {
  return $pdo->prepare($sql)->execute($bindedArr);
}

/*
 *  deleteKw(PDO $pdo, String $kw) => void
 *    --> Deletes keyword $kw from the db
 * 
 *      --> PDO $pdo    - db handler
 *      --> String $kw  - keyword to delete
 * 
 */
function deleteKw($pdo, $kw) {
  // Find kw_id of $kw
  $sql = 'SELECT kw_id FROM keywords WHERE keyword="'.$kw.'"';
  $kw_id = $pdo->query($sql)->fetch(PDO::FETCH_COLUMN);
  // Delete that bitch from oldranks first
  $sql = 'DELETE FROM oldranks WHERE kw_id=:kw_id';
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(
    ':kw_id' => $kw_id
  ));
  // Then we delete that bitch from keywords
  $sql = 'DELETE FROM keywords WHERE keyword=:keyword';
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(
    ':keyword' => $kw
  ));
}

/*
 *  deleteAsin(PDO $pdo, String $asin) => void
 *    --> Deletes asin $asin, and all it's keywords and ranks, from the db
 *
 *      --> PDO $pdo     - db connection
 *      --> String $asin - asin to delete (10 chars)
 */

function deleteAsin($pdo, $asin) {
  // Get asin_id from asin and put in $asin_id
  $asin_id = intval($pdo->query("SELECT asin_id FROM asins WHERE asin='$asin'")->fetch(PDO::FETCH_COLUMN));
  echo $asin_id;

  // Retrieve list of keywords and place it in $kwList
  $sql = 'SELECT keyword FROM keywords WHERE asin_id='.$asin_id;
  $kwList = $pdo->query($sql)->fetchAll(PDO::FETCH_COLUMN);
  echo '<pre>';
  print_r($kwList);
  echo '</pre>';

  // Delete all keywords in $kwList from `oldranks`
  for ($i = 0; $i < sizeof($kwList); $i++) {
    deleteKw($pdo, $kwList[$i]);
  }

  // Finally, delete ASIN itself
  $sql = 'DELETE FROM asins WHERE asin=:asin';
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(
    ':asin' => $asin
  ));
}

/*
 *  getProductsOnPage(Int $pageNum, String $kw) => Array $products
 *    --> Outputs an array of SERPs from an AMZ search for $kw to $products
 * 
 *      --> Int $pageNum - Page number to search for $asin
 *      --> String $kw - CST for search
 */
function getProductsOnPage($pageNum, $kw) {
  // GET the document and set it to $doc
  $amzUrl = 'https://www.amazon.com/s/ref=nb_sb_noss_2?url=search-alias%3Daps&page='. $pageNum .'&field-keywords='.$kw;
  $doc = hQuery::fromUrl($amzUrl, ['Accept' => 'text/html,application/xhtml+xml;q=0.9,*/*;q=0.8']);

  // Scrape the shit out of $amzUrl and store HTML tags into $result
  $result = $doc->find('h2');

  // Initiate $products array which will contain a list of all products
  $products = array();

  // If the result of find() is not empty
  // Then $result is a collection of elements (hQuery_Element)
  if ( $result ) {
    // Iterate over the result
    foreach($result as $pos => $a) {
      // Make it look pretty af
      array_push($products, trim($a->text()));
    }
  }

  // Get rid of [Sponsored] ads to show TRUE SERPs
  for ($i = 0; $i < sizeof($products); $i++) {
    $isSponsored = strpos($products[$i], '[Sponsored]');
    // If it does, replace $products[i] with 'removed'
    if ( $isSponsored === 0) {
      $products[$i] = 'removed';
    }
  }

  return $products;
}

/*
 *  findOnPage(Array $products, String $listingTitle) => Array [Bool, Int $curPageRank]
 *    --> Return array that indidicates array[0] => true if listing was found on current page
 *    --> and array[1] => rank of listing if found on current page. If array[0] === false, then 
 *    --> array[1] = -1
 * 
 *      --> Array $products - Array of SERPs
 *      --> String $listingTitle - Exact title of listing
 */

function findOnPage($products, $listingTitle) {
  // Initiate $curPageRank to find KW ranking on CURRENT PAGE
  $curPageRank = 1;

  $found = false;

  // Check current page's results to see if our ASIN is in there
  for ($i = 0; $i < sizeof($products); $i++) {
    if ($products[$i] != $listingTitle && $products[$i] != 'removed') {
      $curPageRank++;
    }
    if ($products[$i] == $listingTitle) {
      $found = true;
      break;
    }
  }

  return ($found) ? [true, $curPageRank] : [false, -1];
}

/* END HELPER FUNCTIONS */

/*
 *  updateRanks(PDO $pdo, String $kw, String $asin) => Array [Int/String $pageNum, Int/String $kwrank]
 *    --> Returns array that indicates what page $asin is on for keyword $kw and also
 *        inserts ranking into db table 'oldranks'
 *        
 *      --> String $kw - keyword to search for
 *      --> String $asin - asin to search for
 *      --> Int $pageNum - page asin was found in
 *      --> Int $kwrank - rank in SERPs for asin
 */
function updateRanks($pdo, $kw, $asin) {
  $isFound = false;
  // If $kw has any spaces, change them to + for insertion into URL
  $kw = str_replace(' ', '+', $kw);
  $kwrank = 0;
  $pageNum = 1;

  // Query exact long title of $asin from db and assign to $sellerListingTitle
  $sql = "SELECT prod_title FROM asins WHERE asin='$asin'";
  $sellerListingTitle = $pdo->query($sql)->fetch(PDO::FETCH_ASSOC)['prod_title'];
 
  while (!$isFound) {
    $products = getProductsOnPage($pageNum, $kw);
    $x = findOnPage($products, $sellerListingTitle);
    if ($pageNum == 10) {
      $kwrank = 180;
      break;
    }
    // If product was found, then $kwrank = $x[1];
    if ($x[0] == true) {
      $kwrank = $x[1];
      $isFound = true;
      break;
    } 
    // If product wasn't found on this page
    else {
      $pageNum++;
    }
  }

  // Change kw back to spaces
  $kwSpaces = str_replace('+', ' ', $kw);

  // Figure out what kw_id for current keyword is
  $sql = "SELECT kw_id FROM keywords WHERE keyword='$kwSpaces'";
  $kw_id = $pdo->query($sql)->fetch(PDO::FETCH_ASSOC)['kw_id'];

  if ($pageNum >= 10) {
    // Before returning rank, insert rank into 'oldranks'
    $sql = "INSERT INTO oldranks (page, rank, kw_id) VALUES (:pageNum, :kwrank, :kw_id)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
      ':pageNum' => $pageNum,
      ':kwrank'  => $kwrank,
      ':kw_id'   => $kw_id
    ));
    return ['10+', '>240'];
  } else {
    // Before returning rank, insert rank into 'oldranks'
    $sql = "INSERT INTO oldranks (page, rank, kw_id) VALUES (:pageNum, :kwrank, :kw_id)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
      ':pageNum' => $pageNum,
      ':kwrank'  => $kwrank,
      ':kw_id'   => $kw_id
    ));
    return [$pageNum, $kwrank];
  }
}