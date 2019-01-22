
-- Drop the procedure so we can recreate it
drop procedure if exists bid_adjuster_data;

DELIMITER $$
create procedure bid_adjuster_data
(in p_user_id int(11))
begin

	-- enabled keywords for a given user
	create temporary table keywords_to_process
    select
        0 as should_update,
		campaigns.user_id,
        -- bat should never be equal to 0
        case when bat = 0 then 0.05 else bat end as bat,
        latest_bid.bid as bid,
		-- TODO: Compute the new bid
        bid as new_bid,
        -- TODO: Replace with real column later
        0.2 as campaign_target_acos,
        metrics_data.acos as keyword_acos,
        metrics_data.ad_spend as keyword_ad_spend,
        metrics_data.sales as keyword_sales,
        metrics_data.impressions as impressions,
        campaigns.amz_campaign_id,
        ppc_keywords.amz_adgroup_id,
        ppc_keywords.amz_kw_id
	from ppc_keywords
    inner join ad_groups on ad_groups.amz_adgroup_id = ppc_keywords.amz_adgroup_id
    inner join campaigns on campaigns.amz_campaign_id = ad_groups.amz_campaign_id
    inner join users on users.user_id = campaigns.user_id
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
		limit 500000
	) as Latest_Bid
	on ppc_keywords.amz_kw_id = Latest_Bid.amz_kw_id
	inner join
    (
		select

		ppc_keyword_metrics.amz_kw_id,
        sum(impressions) as impressions,
        sum(ad_spend) as ad_spend,
        sum(sales) as sales,
		sum(ad_spend)/sum(sales) as acos
		from ppc_keyword_metrics
		inner join ad_groups on ad_groups.amz_adgroup_id = ppc_keyword_metrics.amz_adgroup_id
		inner join campaigns on campaigns.amz_campaign_id = ad_groups.amz_campaign_id
		where campaigns.user_id = p_user_id
		group by ppc_keyword_metrics.amz_kw_id
    ) as metrics_data on metrics_data.amz_kw_id = ppc_keywords.amz_kw_id
    where campaigns.user_id = p_user_id and ppc_keywords.status = 'enabled';


    -- An example of updating the keywords_to_process
    update keywords_to_process set should_update = (case when bid = 0.6 then 1 else 0 end);



    -- Only return keywords that need to change
    select * from keywords_to_process
    where should_update = 1;


    drop table keywords_to_process;

end$$

DELIMITER ;


-- You can call this stored procedure in PHP and get the table result set
call bid_adjuster_data(2)