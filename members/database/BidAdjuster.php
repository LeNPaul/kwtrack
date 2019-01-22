<?php
namespace AmazonAdvertisingApi;
require_once '../includes/AmazonAdvertisingApi/Client.php';
require_once dirname(__FILE__) . "/pdo.inc.php";

use PDO;
use PDOException;

ini_set('precision', 30);

class BidAdjuster
{
  private $historical_impressions; // Array of integers
  private $current_bid;
  private $new_bid;

  private $bat_percent;
  private $bat_dollars;

  private $global_target_acos;
  private $campaign_target_acos;
  private $target_acos; // Determined by existence of campaign_target_acos

  private $kw_avg_cpc;
  private $kw_lifetime_acos;
  private $kw_id;

  public function __construct()
  {
    // Instantiate object with correct property types
    $this->historical_impressions = [];
    $this->current_bid            = 0.75;
    $this->new_bid                = 0.75;
    $this->bat_percent            = 0.05;
    $this->bat_dollars            = 0.15;
    $this->global_target_acos     = 0.15;
    $this->campaign_target_acos   = 0.15;
    $this->target_acos      = 0.15;
    $this->kw_avg_cpc             = 0.12;
    $this->kw_lifetime_acos      = 0.12;
    $this->kw_id                  = 123123123213;
  }

  public function adjust_bid($config)
  {
    // Input correct parameters from config array
    $this->kw_id = $config['kw_id'];

    $this->historical_impressions = $config['impressions'];
    $this->current_bid            = $config['current_bid'];
    $this->new_bid                = $config['new_bid'];
    $this->bat_percent            = $config['bat_percent'];

    $this->bat_dollars = $this->keyword_bid * $this->bat_percent;

    $this->kw_lifetime_acos    = $config['kw_lifetime_acos'];
    $this->global_target_acos   = $config['global_target_acos'];
    $this->campaign_target_acos = $config['campaign_target_acos'];

    $this->kw_avg_cpc = $config['kw_avg_cpc'];

    // Get number of days since last bid change

    // Did it get any impressions since last bid change?

      /* IF NO */

      // bat_dollars = (bat_percent / 2) * current_bid

      // Is bat_dollars < 0.01?

        /* IF YES */

        // bat_dollars = 0.01

        // new_bid = current_bid + bat_dollars

        // Update bid server side

        /* IF NO */

        // new_bid = current_bid + bat_dollars

        // Update bid server side

      /* IF YES */

      // Does the campaign containing the kw have a campaign level target ACoS?

      /* IF YES --> target_acos = campaign_target_acos */
      /* IF NO --> target_acos = global_target_acos */

  }
}
?>
