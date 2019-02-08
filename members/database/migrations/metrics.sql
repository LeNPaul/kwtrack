CREATE TABLE ppc_keyword_metrics (

  user_id int(11) not null,
  amz_campaign_id bigint(20) not null,
  amz_adgroup_id bigint(20) not null,
  amz_kw_id bigint(20) not null,
  `date` datetime not null,

  bid decimal(19,4),
  impressions decimal(19,4),
  clicks decimal(19,4),
  ad_spend decimal(19,4),
  units_sold decimal(19,4),
  sales decimal(19,4),


  unique key (amz_kw_id, `date`)

) ENGINE=MyISAM  DEFAULT CHARSET=latin1;


-- Remove unused columns from the campaigns table

alter table campaigns drop column impressions;
alter table campaigns drop column clicks;
alter table campaigns drop column ctr;
alter table campaigns drop column ad_spend;
alter table campaigns drop column avg_cpc;
alter table campaigns drop column units_sold;
alter table campaigns drop column sales;


-- Remove unused columns from the ad_groups table


alter table ad_groups drop column user_id;
alter table ad_groups drop column impressions;
alter table ad_groups drop column clicks;
alter table ad_groups drop column ctr;
alter table ad_groups drop column ad_spend;
alter table ad_groups drop column avg_cpc;
alter table ad_groups drop column units_sold;
alter table ad_groups drop column sales;


-- Remove unused columns from the ppc_keywords table


alter table ppc_keywords drop column kw_id;
alter table ppc_keywords drop column amz_campaign_id;
alter table ppc_keywords drop column user_id;

alter table ppc_keywords drop column bid;
alter table ppc_keywords drop column impressions;
alter table ppc_keywords drop column clicks;
alter table ppc_keywords drop column ctr;
alter table ppc_keywords drop column ad_spend;
alter table ppc_keywords drop column avg_cpc;
alter table ppc_keywords drop column units_sold;
alter table ppc_keywords drop column sales;

-- Add user_id to negative keyword tables

ALTER TABLE adgroup_neg_kw
  ADD COLUMN user_id INT(11) NOT NULL,
  ADD FOREIGN KEY fk_user_id(user_id) REFERENCES users(user_id);

ALTER TABLE campaign_neg_kw
  ADD COLUMN user_id INT(11) NOT NULL,
  ADD FOREIGN KEY fk_user_id(user_id) REFERENCES users(user_id);

-- Modify DB to accommodate bid adjustments

ALTER TABLE `users` CHANGE `bat` `bat` DECIMAL(3, 2) NOT NULL DEFAULT '0.05';
ALTER TABLE `campaigns` ADD `target_acos` DECIMAL(4, 2) UNSIGNED NULL AFTER `schedule`;
ALTER TABLE `users` ADD `target_acos` DECIMAL(4, 2) UNSIGNED NOT NULL DEFAULT 0.15;

-- Add the bid and suggested bid into the keywords table.
ALTER TABLE ppc_keywords add `bid` decimal(19,4) null;
ALTER TABLE ppc_keywords add `suggested_bid` decimal(19,4) null;

-- Add user settings table

create table settings (
	  user_id int(11) not null,
    spyder_campaigns bool default 1,
    target_acos decimal(19,4) default null,

    neg_kw_min_clicks int(11) default 15,
    neg_kw_min_ctr decimal(19,5) default 0.002,

    new_kw_min_units int(11) default 1,
    new_kw_min_clicks int(11) default 2,
    new_kw_min_cvr decimal(19,5) default 0.05,

    foreign key fk_user_id(user_id) REFERENCES users(user_id)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;