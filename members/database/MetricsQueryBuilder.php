<?php

// TODO: This query builder is not SQL injection proof.
class MetricsQueryBuilder {

  // Base query information
  public $userId;
  public $startDate;
  public $endDate;

  // Which to filter on
  public $campaignIds;
  public $keywordIds;
  public $adGroupIds;

  // Include aggregates for theses levels
  public $includeCampaigns;
  public $includeAdGroups;
  public $includeKeywords;
  public $includeDate;

  // Which column to order by?
  public $orderBy;
  public $orderByDesc;

  function __construct(){
    $this->campaignIds = [];
    $this->keywordIds = [];
    $this->adGroupIds = [];
  }

  public function getSql(){
    // Construct the group by clause
    $group_by = ['user_id'];
    
    // By default we do not perform an additional join for latest bid data
    $get_latest_bid_join = '';
    // The select for latest bid is therefore the same as the avg_bid
    $get_latest_bid_select = 'avg_bid as bid';
    
    if ($this->includeCampaigns) $group_by[] = 'amz_campaign_id';
    if ($this->includeAdGroups) $group_by[] = 'amz_adgroup_id';
    if ($this->includeKeywords) {
      $group_by[] = 'amz_kw_id';
      // This join will get the latest bid data only when amz_kw_ids are included
      // in the aggregate function.
      $get_latest_bid_join = "
        inner join
        (
          select P.amz_kw_id, P.bid from ppc_keyword_metrics as P
          inner join
          (
            select
              amz_kw_id,
              max(`date`) as latest_date
            from ppc_keyword_metrics
            group by amz_kw_id
          ) as latest_date_inner
          on latest_date_inner.amz_kw_id = P.amz_kw_id and latest_date_inner.latest_date = P.`date`
        ) as latest_bid
        on T.amz_kw_id = latest_bid.amz_kw_id
      ";
      
      $get_latest_bid_join = "
        inner join
        (
          select P.amz_kw_id, P.bid from ppc_keywords as P
        ) as latest_bid
        on T.amz_kw_id = latest_bid.amz_kw_id
      ";
      
      // The select statement can now be done using the latest bid data
      $get_latest_bid_select = 'latest_bid.bid as bid';
    }
    
    if ($this->includeDate) $group_by[] = '`date`';

    $group_by = implode(', ', $group_by);

    // Construct the where clause
    $where = ["user_id = $this->userId"];

    if ($this->startDate != null) $where[] = "`date` >= '$this->startDate'";
    if ($this->endDate != null) $where[] = "`date` <= '$this->endDate'";

    // Filter on campaign ids
    if (count($this->campaignIds) > 0){
      $campaign_ids = implode(', ', $this->campaignIds);
      $where[] = "amz_campaign_id in ($campaign_ids)";
    }

    // Filter on ad group ids
    if (count($this->adGroupIds) > 0){
      $ad_group_ids = implode(', ', $this->adGroupIds);
      $where[] = "amz_adgroup_id in ($ad_group_ids)";
    }

    // Filter on keyword ids
    if (count($this->keywordIds) > 0){
      $keyword_ids = implode(', ', $this->keywordIds);
      $where[] = "amz_kw_id in ($keyword_ids)";
    }

    // Combine all filters into one
    $where = implode(' and ', $where);

    // Order by clause
    $orderBy = '';
    if ($this->orderBy) {
      $orderBy = "order by `$this->orderBy`";
      if ($this->orderByDesc)
        $orderBy .= " desc";
      else
        $orderBy .= " asc";
    }

    // Included data
    $joins = [];
    if ($this->includeCampaigns) {
      $joins[] = 'campaigns on campaigns.amz_campaign_id = T2.amz_campaign_id';
    }
    if ($this->includeAdGroups) {
      $joins[] = 'ad_groups on ad_groups.amz_adgroup_id = T2.amz_adgroup_id';
    }
    if ($this->includeKeywords) {
      $joins[] = 'ppc_keywords on ppc_keywords.amz_kw_id = T2.amz_kw_id';
    }
    $joins = implode("\ninner join ", $joins);
    if (strlen($joins) > 0) $joins = "\ninner join " . $joins;

    return "
      select *,
        ifnull(cvr, 0) as cvr,
        ifnull(ctr, 0) as ctr,
        ifnull(avg_cpc, 0) as avg_cpc,
        ifnull(acos, 0) as acos,
        ifnull(cvr, 0) as roas,
        
        
        ifnull(concat('$', round(bid, 2)), '-') as bid_formatted,
        ifnull(nullif(round(impressions, 0), 0), '-') as impressions_formatted,
        ifnull(nullif(round(clicks, 0), 0), '-') as clicks_formatted,
        ifnull(nullif(concat('$', round(ad_spend, 2)), '$0.00'), '-') as ad_spend_formatted,
        ifnull(nullif(round(units_sold, 0), 0), '-') as units_sold_formatted,
        ifnull(nullif(concat('$', round(sales, 2)), '$0.00'), '-') as sales_formatted,
        ifnull(nullif(concat(round(cvr * 100, 2), '%'), 0), '-') as cvr_formatted,
        ifnull(nullif(concat(round(ctr * 100, 2), '%'), 0), '-') as ctr_formatted,
        
        concat('$', round(ifnull(ad_spend / units_sold, 0), 2)) as cost_per_unit_sold,
        round(ifnull(clicks / units_sold, 0), 2) as clicks_per_unit_sold,

        ifnull(concat('$', round(avg_cpc, 2)), '-') as avg_cpc_formatted,
        ifnull(concat(round(acos * 100, 2), '%'), '-') as acos_formatted,
        ifnull(concat('$', round(roas, 2)), '-') as roas_formatted
        from
        (
          select T.*,
          units_sold/clicks as cvr,
          clicks/impressions as ctr,
          ad_spend/clicks as avg_cpc,
          ad_spend/sales as acos,
          sales/ad_spend as roas,
          $get_latest_bid_select
          from
          (
            select
              $group_by,
              avg(bid) as avg_bid,
              sum(impressions) as impressions,
              sum(clicks) as clicks,
              sum(ad_spend) as ad_spend,
              sum(units_sold) as units_sold,
              sum(sales) as sales
            from ppc_keyword_metrics
            WHERE $where
            group by $group_by
          ) as T
          $get_latest_bid_join
        ) as T2
      $joins
      $orderBy
    ";
  }

  public function execute($pdo){
    $query = $this->getSql();
    $stmt = $pdo->query($query);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}

// Testing code

/*
$builder = new MetricsQueryBuilder();
$builder->userId = 2;
$builder->startDate = '2018-01-01';
$builder->endDate = '2019-01-01';

$builder->includeCampaigns = false;
$builder->includeAdGroups = false;
$builder->includeKeywords = false;
$builder->includeDate = false;
*/

//$query->campaignIds[] = '1459010863458';

//$query->adGroupIds[] = '67437052824191';
//$query->adGroupIds[] = '212951839040864';
//$query->adGroupIds[] = '230526793478360';

/*
$query->keywordIds = ['183611553411154',
'256992142697404',
'153325168590657',
  '244983488498386'];
*/
//$query->keywordIds = ['1459010863458', '3917622904824', '11261399254957'];

/*
global $pdo;

$query = $builder->getSql();
$stmt = $pdo->query($query);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);


var_dump($result);

*/


?>
