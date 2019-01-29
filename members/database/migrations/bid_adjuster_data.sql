
-- Drop the procedure so we can recreate it
drop procedure if exists bid_adjuster_data;

DELIMITER $$
create procedure bid_adjuster_data
(
-- How far back to look when comparing bids against each other.
in p_start_date datetime,
-- How much time a bid is given to perform before switching.
in p_min_bid_duration int
)
begin

	-- Constraints on the system
    declare min_bid decimal(19,4) default 0.02;
    declare max_bid decimal(19,4) default 1000;
    declare min_impressions decimal(19,5) default 500;

    -- enabled keywords for a given user
	drop temporary table if exists keywords_to_process;
	create temporary table keywords_to_process
    select
		metrics_data.*,
        metrics_data.ad_spend/metrics_data.clicks as avg_cpc,
        lifetime_metrics_data.acos as lifetime_acos,
        -- bat should never be equal to 0
        case when users.bat = 0 then 0.05 else users.bat end as bat,
        ifnull(ifnull(campaigns.target_acos, users.target_acos), 0.15) as target_acos,
        -- Within 5% of lifetime acos
        convert(null, decimal(19,4)) as new_bid,
        convert(null, decimal(19,4)) as new_bid_increased_value,
        convert(null, decimal(19,4)) as new_bid_decreased_value,
        convert(null, decimal(19,4)) as lifetime_vs_target_acos_ratio,
        false as lifetime_acos_greater_than_target,
        false as is_greater_than_double_cpc
	from
	(
		select
			user_id,
			amz_campaign_id,
			amz_adgroup_id,
			amz_kw_id,
			bid,
			sum(impressions) as impressions,
			sum(clicks) as clicks,
			sum(ad_spend) as ad_spend,
			sum(sales) as sales,
			sum(ad_spend)/sum(sales) as acos,
            -- min(`date`) as min_date,
            -- max(`date`) as max_date,
            count(distinct(`date`)) as total_days
		from ppc_keyword_metrics
		-- Include the start date to create a window of data
		where `date` >= p_start_date
		group by
			user_id,
			amz_campaign_id,
			amz_adgroup_id,
			amz_kw_id,
			bid
	) as metrics_data
    inner join
    -- Also include life time values
    (
		select
			amz_kw_id,
			sum(impressions) as impressions,
			sum(clicks) as clicks,
			sum(ad_spend) as ad_spend,
			sum(sales) as sales,
			sum(ad_spend)/sum(sales) as acos
		from ppc_keyword_metrics
		group by amz_kw_id
	) as lifetime_metrics_data
    on lifetime_metrics_data.amz_kw_id = metrics_data.amz_kw_id
    -- We need to get the latest bid by looking at the latest row
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
		) as T on T.amz_kw_id = P.amz_kw_id and T.latest_date = P.`date`
	) as latest_bid
	on metrics_data.amz_kw_id = latest_bid.amz_kw_id and metrics_data.bid = latest_bid.bid
    -- Get campaign level target acos

    inner join ppc_keywords on metrics_data.amz_kw_id = ppc_keywords.amz_kw_id
    inner join users on metrics_data.user_id = users.user_id
    inner join campaigns on metrics_data.amz_campaign_id = campaigns.amz_campaign_id
    where ppc_keywords.status = 'enabled' and metrics_data.total_days >= p_min_bid_duration;

	-- 1. If there are no impressions we need to decrease the bat.
    update keywords_to_process set
		bat = (bat / 2)
	where impressions <= min_impressions;

    -- 2. Calculate the ratio for lifetime compared to target acos
    update keywords_to_process set
		lifetime_vs_target_acos_ratio = abs(lifetime_acos - target_acos)/target_acos
	where lifetime_acos is not null;

	-- 3. Calculate the new bat value based on the ratio
    update keywords_to_process set
		bat = least(bat, ifnull(lifetime_vs_target_acos_ratio, bat))
    where lifetime_acos is not null;

    -- 4. Calculate some bid values and control variables
    update keywords_to_process set
		new_bid_increased_value = bid * (1 + bat),
		new_bid_decreased_value = bid * (1 - bat);

	-- 5. Round the bid changes to the nearest cent
   update keywords_to_process set
		new_bid_increased_value = ceil(new_bid_increased_value * 100)/100,
		new_bid_decreased_value = floor(new_bid_decreased_value * 100)/100;

    -- 6. Also make sure the bid changes to not go beyond the limits
    update keywords_to_process set
		new_bid_increased_value = greatest(least(new_bid_increased_value, max_bid), min_bid),
		new_bid_decreased_value = greatest(least(new_bid_decreased_value, max_bid), min_bid);

	-- 7. Set some control variables
    update keywords_to_process set
        lifetime_acos_greater_than_target =
			lifetime_acos > target_acos,
		is_greater_than_double_cpc =
			bid > 2 * avg_cpc;


	-- Update Path 1: If there are no impressions increase the bid
    update keywords_to_process set new_bid = new_bid_increased_value
	where impressions <= min_impressions;

    -- Update Path 2: When lifetime kw ACoS < target ACoS ==> profitable keyword
    update keywords_to_process set new_bid = new_bid_increased_value
    where lifetime_acos_greater_than_target = false
		and is_greater_than_double_cpc = false
        -- Only process bids which haven't already been flagged
        and new_bid is not null;

    -- Update Path 3: When lifetime kw ACoS > target ACoS ==> unprofitable keyword
    update keywords_to_process set new_bid = new_bid_decreased_value
    where lifetime_acos_greater_than_target = true;

	-- Now select all keywords which have been processed
    select * from keywords_to_process
    where new_bid is not null;

    -- Drop all the tables in the end
	drop temporary table if exists keywords_to_process;

end$$

DELIMITER ;

select * from ppc_keywords;


call bid_adjuster_data(date_add(now(), interval -30 day), 3)