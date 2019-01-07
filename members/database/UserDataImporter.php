<?php
namespace AmazonAdvertisingApi;
require_once '../includes/AmazonAdvertisingApi/Client.php';
require_once './pdo.inc.php';
use PDO;
use PDOException;

// TODO: Remove testing only
$importer = new UserDataImporter();
$importer->import(2, 58);

class UserDataImporter {

  private $user_id;
  private $region;
  private $days_to_import;
  private $client;

  private $amz_campaigns;
  private $amz_ad_groups;
  private $amz_keywords;

  public function import($user_id, $days_to_import, $region = 'na'){

    $this->user_id = $user_id;
    $this->days_to_import = $days_to_import;
    $this->region = $region;
    $this->client = $this->get_amz_client();

    // Import campaigns
    $this->amz_campaigns = $this->safe_json_decode($this->client->listCampaigns()['response']);
    $this->import_campaigns();

    // Import ad groups
    $this->amz_ad_groups = $this->safe_json_decode($this->client->listAdGroups()['response']);
    $this->import_ad_groups();

    // Import keywords
    $this->client->completeRequestSnapshot("keywords");
    $this->amz_keywords = $this->client->completeGetSnapshot();
    $this->import_keywords();

    // Import the metrics
    for ($i = 0; $i < $days_to_import; $i++) {
      $date = date('Ymd', strtotime('-' . ($i + 2) . ' days'));
      $this->client->completeRequestReport($date);
      $this->import_metrics($date, $this->client->completeGetReport());
    }

    // Import campaign negative keywords
    $this->import_campaign_neg_keywords();

    // Import adgroup negative keywords
    $this->import_ad_group_neg_keywords();
  }

  // big integers are losing their precision during decoding which results in weird behaviour.
  // Example: 203125468806302 gets turned into 203125468806300
  private function safe_json_decode($obj){
    return json_decode($obj, true, 512, JSON_BIGINT_AS_STRING);
  }

  // Create an amazon client connection for the provided user.
  private function get_amz_client(){
    global $pdo;
    // Get refresh token and profileId from database using user_id
    $sql = 'SELECT refresh_token, profileId FROM users WHERE user_id=' . $this->user_id;
    $stmt = $pdo->query($sql);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $refreshToken = $result[0]['refresh_token'];
    $profileId = $result[0]['profileId'];

    // Instantiate client for advertising API
    $config = array(
      "clientId" => "amzn1.application-oa2-client.4246e0f086e441259742c758f63ca0bf",
      "clientSecret" => "9c9e07b214926479e14a0781051ecc3ad9b29686d3cef24e15eb130a47cabeb3",
      "refreshToken" => $refreshToken,
      "region" => $this->region,
      "sandbox" => false,
    );
    $client = new Client($config);
    $client->profileId = $profileId;

    return $client;
  }

  private function create_lookup($array, $property){
    $lookup = array();
    foreach($array as $e){
      $lookup[$e[$property]] = $e;
    }
    return $lookup;
  }

  private function import_campaigns(){
    global $pdo;

    // Get all campaigns from the DB
    $db_campaigns = $pdo
        ->query("SELECT amz_campaign_id FROM campaigns WHERE user_id={$this->user_id}")
        ->fetchAll(PDO::FETCH_ASSOC);
    $db_campaigns =  $this->create_lookup($db_campaigns, 'amz_campaign_id');

    // Prepare some statements for update/insert
    $insert_stmt = $pdo->prepare("
      INSERT INTO campaigns
      (campaign_name,amz_campaign_id,user_id,campaign_type,targeting_type,daily_budget,status)
      VALUES
      (:campaign_name,:amz_campaign_id,:user_id,:campaign_type,:targeting_type,:daily_budget,:status)
    ");
    $update_stmt = $pdo->prepare("UPDATE campaigns SET status=:status WHERE amz_campaign_id=:amz_campaign_id");

    foreach ($this->amz_campaigns as $amz_campaign){

      if (isset($db_campaigns[$amz_campaign['campaignId']])){
        // TODO: Only update if the state has changed
        $update_stmt->execute(array(
          ':amz_campaign_id'	=> $amz_campaign['campaignId'],
          ':status'						=> $amz_campaign['state']
        ));
      }else{
        $insert_stmt->execute(array(
          ':campaign_name'		=> $amz_campaign['name'],
          ':amz_campaign_id'	=> $amz_campaign['campaignId'],
          ':user_id'					=> $this->user_id,
          ':campaign_type'		=> $amz_campaign['campaignType'],
          ':targeting_type'		=> ($amz_campaign['targetingType'] == 'manual') ? 'Manual' : 'Automatic',
          ':status'						=> $amz_campaign['state'],
          ':daily_budget'			=> $amz_campaign['dailyBudget']
        ));
      }
    }
  }

  private function import_ad_groups(){
    global $pdo;

    // Get all ad groups from the DB
    $db_ad_groups = $pdo
      ->query("
        select amz_adgroup_id from campaigns
        inner join ad_groups on ad_groups.amz_campaign_id = campaigns.amz_campaign_id
        WHERE campaigns.user_id = {$this->user_id}
      ")
      ->fetchAll(PDO::FETCH_ASSOC);
    $db_ad_groups = $this->create_lookup($db_ad_groups, 'amz_adgroup_id');

    // Prepare some statements for update/insert
    $insert_stmt = $pdo->prepare("
      INSERT INTO ad_groups
      (amz_adgroup_id, ad_group_name, amz_campaign_id, status, default_bid)
      VALUES
      (:amz_adgroup_id, :ad_group_name, :amz_campaign_id, :status, :default_bid)
    ");
    $update_stmt = $pdo->prepare("UPDATE ad_groups SET status=:status, default_bid=:default_bid WHERE amz_adgroup_id=:amz_adgroup_id");

    foreach ($this->amz_ad_groups as $amz_ad_group) {

      if (isset($db_ad_groups[$amz_ad_group['adGroupId']])) {
        // TODO: Only update if the state or default bid has changed
        $update_stmt->execute(array(
          ':amz_adgroup_id' => $amz_ad_group['adGroupId'],
          ':default_bid' => $amz_ad_group['defaultBid'],
          ':status' => $amz_ad_group['state']
        ));
      } else {
        $insert_stmt->execute(array(
          ':amz_adgroup_id'		=> $amz_ad_group['adGroupId'],
          ':ad_group_name'		=> $amz_ad_group['name'],
          ':amz_campaign_id'	=> $amz_ad_group['campaignId'],
          ':status'						=> $amz_ad_group['state'],
          ':default_bid'      => $amz_ad_group['defaultBid']
        ));
      }
    }
  }

  private function import_keywords(){
    global $pdo;

    // Get all existing keywords from the DB
    $db_keywords = $pdo
      ->query("
        select ppc_keywords.amz_kw_id, ppc_keywords.status from ppc_keywords
        inner join ad_groups on ad_groups.amz_adgroup_id = ppc_keywords.amz_adgroup_id
        inner join campaigns on campaigns.amz_campaign_id = ad_groups.amz_campaign_id
        WHERE campaigns.user_id = {$this->user_id}
      ")
      ->fetchAll(PDO::FETCH_ASSOC);
    $db_keywords = $this->create_lookup($db_keywords, 'amz_kw_id');

    $insert_stmt = $pdo->prepare("
      INSERT INTO ppc_keywords
      (amz_kw_id, amz_adgroup_id, status, keyword_text, match_type)
      VALUES
      (:amz_kw_id, :amz_adgroup_id, :status, :keyword_text, :match_type)
    ");
    $update_stmt = $pdo->prepare("
      UPDATE ppc_keywords
      SET status=:status
      WHERE amz_kw_id=:amz_kw_id");

    foreach ($this->amz_keywords as $amz_keyword) {

      // Get the db keyword
      $db_keyword = isset($db_keywords[$amz_keyword['keywordId']])
        ? $db_keywords[$amz_keyword['keywordId']]
        : null;

      // Did we find the keyword in the database?
      if ($db_keyword != null) {
        if ($db_keyword['status'] != $amz_keyword['state']) {
          $update_stmt->execute(array(
            ':amz_kw_id' => $amz_keyword['keywordId'],
            ':status' => $amz_keyword['state']
          ));
        }
      } else {
        $insert_stmt->execute(array(
          ':amz_kw_id'		    => $amz_keyword['keywordId'],
          ':amz_adgroup_id'		=> $amz_keyword['adGroupId'],
          ':status'	          => $amz_keyword['state'],
          ':keyword_text'     => $amz_keyword['keywordText'],
          ':match_type'       => $amz_keyword['matchType']
        ));
      }
    }
  }

  private function import_metrics($date, $metrics){
    global $pdo;

    $ad_groups_lookup = $this->create_lookup($this->amz_ad_groups, 'adGroupId');

    $rows_to_insert = array();
    foreach ($metrics as $amz_metric) {

      // Check if the bid is set, otherwise use the ad group default bid
      $bid = null;
      if (isset($amz_metric['bid'])) {
        $bid = $amz_metric['bid'];
      }else{
        $ad_group = $ad_groups_lookup[$amz_metric['adGroupId']];
        if ($ad_group != null) {
          $bid = $ad_group['defaultBid'];
        }
      }

      $impressions = $amz_metric['impressions'];
      $clicks      = $amz_metric['clicks'];
      $ad_spend    = $amz_metric['cost'];
      $sales       = $amz_metric['attributedSales7d'];
      $units_sold  = $amz_metric['attributedUnitsOrdered7d'];

      $rows_to_insert[] = array(
        'user_id' => $this->user_id,
        'amz_campaign_id' => $amz_metric['campaignId'],
        'amz_adgroup_id' => $amz_metric['adGroupId'],
        'amz_kw_id' => $amz_metric['keywordId'],
        'date' => $date,
        'bid' => $bid,
        'impressions' => $impressions,
        'clicks' => $clicks,
        'ad_spend' => $ad_spend,
        'units_sold' => $units_sold,
        'sales' => $sales
      );
    }

    //Call our custom function.
    try{
      foreach (array_chunk($rows_to_insert, 100) as $batch) {
        $this->pdoMultiInsert('ppc_keyword_metrics', $batch, $pdo);
      }
    }catch (PDOException $exception){
      // If there are any duplicate metrics already stored then the insert will fail for the
      // entire batch.
    }
  }

  private function import_ad_group_neg_keywords() {
    global $pdo;

    $result = $this->safe_json_decode($this->client->listNegativeKeywords(array("stateFilter" => "enabled"))['response']);

    for ($i = 0; $i < count($result); $i++) {
      $sql = 'INSERT INTO adgroup_neg_kw (kw_id, amz_adgroup_id, keyword_text, state, match_type)
              VALUES (:kw_id, :amz_adgroup_id, :keyword_text, :state, :match_type)';
      $stmt = $pdo->prepare($sql);
      $stmt->execute(array(
        ':kw_id'          => $result[$i]['keywordId'],
        ':amz_adgroup_id' => $result[$i]['adGroupId'],
        ':keyword_text'   => $result[$i]['keywordText'],
        ':state'          => $result[$i]['state'],
        ':match_type'     => $result[$i]['matchType']
      ));
    }
  }

  private function import_campaign_neg_keywords() {
    global $pdo;

    $result = $this->safe_json_decode($this->client->listCampaignNegativeKeywords(array("stateFilter" => "enabled"))['response']);

    for ($i = 0; $i < count($result); $i++) {
      $sql = 'INSERT INTO campaign_neg_kw (kw_id, amz_campaign_id, keyword_text, state, match_type)
              VALUES (:kw_id, :amz_campaign_id, :keyword_text, :state, :match_type)';
      $stmt = $pdo->prepare($sql);
      $stmt->execute(array(
        ':kw_id'          => $result[$i]['keywordId'],
        ':amz_campaign_id'=> $result[$i]['campaignId'],
        ':keyword_text'   => $result[$i]['keywordText'],
        ':state'          => $result[$i]['state'],
        ':match_type'     => $result[$i]['matchType']
      ));
    }
  }


  /**
   * http://thisinterestsme.com/pdo-prepared-multi-inserts/
   * A custom function that automatically constructs a multi insert statement.
   *
   * @param string $tableName Name of the table we are inserting into.
   * @param array $data An "array of arrays" containing our row data.
   * @param PDO $pdoObject Our PDO object.
   * @return boolean TRUE on success. FALSE on failure.
   */
  private function pdoMultiInsert($tableName, $data, $pdoObject){

    //Will contain SQL snippets.
    $rowsSQL = array();

    //Will contain the values that we need to bind.
    $toBind = array();

    //Get a list of column names to use in the SQL statement.
    $columnNames = array_keys($data[0]);

    //Loop through our $data array.
    foreach($data as $arrayIndex => $row){
      $params = array();
      foreach($row as $columnName => $columnValue){
        $param = ":" . $columnName . $arrayIndex;
        $params[] = $param;
        $toBind[$param] = $columnValue;
      }
      $rowsSQL[] = "(" . implode(", ", $params) . ")";
    }

    //Construct our SQL statement
    $sql = "INSERT INTO `$tableName` (" . implode(", ", $columnNames) . ") VALUES " . implode(", ", $rowsSQL);

    //Prepare our PDO statement.
    $pdoStatement = $pdoObject->prepare($sql);

    //Bind our values.
    foreach($toBind as $param => $val){
      $pdoStatement->bindValue($param, $val);
    }

    //Execute our statement (i.e. insert the data).
    return $pdoStatement->execute();
  }
}

?>
