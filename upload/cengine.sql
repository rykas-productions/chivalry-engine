SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE TABLE `announcements` (
  `ann_id` int(11) UNSIGNED NOT NULL,
  `ann_text` text NOT NULL,
  `ann_time` int(11) UNSIGNED NOT NULL,
  `ann_poster` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `botlist` (
  `botid` int(11) UNSIGNED NOT NULL,
  `botuser` int(11) UNSIGNED NOT NULL,
  `botitem` int(11) UNSIGNED NOT NULL,
  `botcooldown` int(11) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `botlist_hits` (
  `userid` int(11) UNSIGNED NOT NULL,
  `botid` int(11) UNSIGNED NOT NULL,
  `lasthit` int(11) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `contact_list` (
  `c_ID` int(11) UNSIGNED NOT NULL,
  `c_ADDED` int(11) UNSIGNED NOT NULL,
  `c_ADDER` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `crimegroups` (
  `cgID` int(11) UNSIGNED NOT NULL,
  `cgNAME` text NOT NULL,
  `cgORDER` int(11) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `crimes` (
  `crimeID` int(11) UNSIGNED NOT NULL,
  `crimeNAME` text NOT NULL,
  `crimeBRAVE` int(11) UNSIGNED NOT NULL,
  `crimePERCFORM` text NOT NULL,
  `crimePRICURMIN` int(11) UNSIGNED NOT NULL,
  `crimePRICURMAX` int(11) UNSIGNED NOT NULL,
  `crimeSECCURMIN` int(11) UNSIGNED NOT NULL,
  `crimeSECURMAX` int(11) UNSIGNED NOT NULL,
  `crimeITEMSUC` int(11) UNSIGNED NOT NULL,
  `crimeGROUP` int(11) UNSIGNED NOT NULL,
  `crimeITEXT` text NOT NULL,
  `crimeSTEXT` text NOT NULL,
  `crimeFTEXT` text NOT NULL,
  `crimeDUNGMIN` int(11) UNSIGNED NOT NULL,
  `crimeDUNGMAX` int(11) UNSIGNED NOT NULL,
  `crimeDUNGREAS` text NOT NULL,
  `crimeXP` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `crons` (
  `file` varchar(30) COLLATE latin1_general_ci NOT NULL,
  `nextUpdate` int(11) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

CREATE TABLE `dungeon` (
  `dungeon_user` int(11) UNSIGNED NOT NULL,
  `dungeon_reason` text NOT NULL,
  `dungeon_in` int(11) UNSIGNED NOT NULL,
  `dungeon_out` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `enemy` (
  `enemy_id` int(11) UNSIGNED NOT NULL,
  `enemy_user` int(11) UNSIGNED NOT NULL,
  `enemy_adder` int(11) UNSIGNED NOT NULL,
  `comment` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `estates` (
  `house_id` int(11) UNSIGNED NOT NULL,
  `house_name` tinytext NOT NULL,
  `house_price` int(11) UNSIGNED NOT NULL,
  `house_will` int(11) UNSIGNED NOT NULL,
  `house_level` int(11) UNSIGNED DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `estates` (`house_id`, `house_name`, `house_price`, `house_will`, `house_level`) VALUES
(1, 'Default House', 101, 100, 1);

CREATE TABLE `fedjail` (
  `fed_id` int(11) UNSIGNED NOT NULL,
  `fed_userid` int(11) UNSIGNED NOT NULL,
  `fed_out` int(11) UNSIGNED NOT NULL,
  `fed_jailedby` int(11) UNSIGNED NOT NULL,
  `fed_reason` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `forum_bans` ( 
	`fb_id` INT(11) UNSIGNED NULL DEFAULT NULL AUTO_INCREMENT , 
	`fb_user` INT(11) UNSIGNED NOT NULL , 
	`fb_banner` INT(11) UNSIGNED NOT NULL , 
	`fb_time` INT(11) UNSIGNED NOT NULL , 
	`fb_reason` TEXT NOT NULL , 
	UNIQUE (`fb_id`)) ENGINE = MyISAM;

CREATE TABLE `forum_forums` (
  `ff_id` int(10) UNSIGNED NOT NULL,
  `ff_name` tinytext NOT NULL,
  `ff_desc` tinytext NOT NULL,
  `ff_lp_t_id` int(10) UNSIGNED NOT NULL,
  `ff_lp_poster_id` int(11) NOT NULL,
  `ff_auth` enum('public','staff') NOT NULL,
  `ff_lp_time` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `forum_posts` (
  `fp_id` int(10) NOT NULL,
  `fp_poster_id` int(10) UNSIGNED NOT NULL,
  `ff_id` int(10) UNSIGNED NOT NULL,
  `fp_time` int(10) UNSIGNED NOT NULL,
  `fp_topic_id` int(10) UNSIGNED NOT NULL,
  `fp_editor_id` int(10) UNSIGNED NOT NULL,
  `fp_edit_count` int(10) UNSIGNED NOT NULL,
  `fp_editor_time` int(10) UNSIGNED NOT NULL,
  `fp_text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `forum_topics` (
  `ft_id` int(10) UNSIGNED NOT NULL,
  `ft_forum_id` int(10) UNSIGNED NOT NULL,
  `ft_name` tinytext NOT NULL,
  `ft_desc` tinytext NOT NULL,
  `ft_posts` int(10) UNSIGNED NOT NULL,
  `ft_owner_id` int(10) UNSIGNED NOT NULL,
  `ft_last_id` int(10) UNSIGNED NOT NULL,
  `ft_start_time` int(10) UNSIGNED NOT NULL,
  `ft_last_time` int(10) UNSIGNED NOT NULL,
  `ft_pinned` tinytext NOT NULL,
  `ft_locked` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `friends` (
  `friend_id` int(11) UNSIGNED NOT NULL,
  `friended` int(11) UNSIGNED NOT NULL,
  `friender` int(11) UNSIGNED NOT NULL,
  `comment` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `gamerules` (
  `rule_id` int(11) UNSIGNED NOT NULL,
  `rule_text` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `guild` (
  `guild_id` int(11) UNSIGNED NOT NULL,
  `guild_town_id` int(11) UNSIGNED NOT NULL,
  `guild_owner` int(11) UNSIGNED NOT NULL,
  `guild_coowner` int(11) UNSIGNED NOT NULL,
  `guild_primcurr` int(11) UNSIGNED NOT NULL,
  `guild_seccurr` int(11) UNSIGNED NOT NULL,
  `guild_hasarmory` enum('false','true') NOT NULL DEFAULT 'false',
  `guild_capacity` int(11) UNSIGNED NOT NULL,
  `guild_name` text NOT NULL,
  `guild_desc` text NOT NULL,
  `guild_level` int(11) UNSIGNED NOT NULL,
  `guild_xp` int(11) UNSIGNED NOT NULL,
  `guild_announcement` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `guild_applications` (
  `ga_id` int(11) UNSIGNED NOT NULL,
  `ga_user` int(11) UNSIGNED NOT NULL,
  `ga_guild` int(11) UNSIGNED NOT NULL,
  `ga_time` int(11) UNSIGNED NOT NULL,
  `ga_text` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `guild_notifications` (
  `gn_id` int(11) UNSIGNED NOT NULL,
  `gn_guild` int(11) UNSIGNED NOT NULL,
  `gn_time` int(11) UNSIGNED NOT NULL,
  `gn_text` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `guild_wars` ( 
	`gw_id` INT(11) UNSIGNED NULL DEFAULT NULL AUTO_INCREMENT , 
	`gw_declarer` INT(11) UNSIGNED NOT NULL , 
	`gw_declaree` INT(11) UNSIGNED NOT NULL , 
	`gw_drpoints` INT(11) UNSIGNED NOT NULL , 
	`gw_depoints` INT(11) UNSIGNED NOT NULL , 
	`gw_end` INT(11) UNSIGNED NOT NULL , 
	`gw_winner` INT(11) UNSIGNED NOT NULL , 
	UNIQUE (`gw_id`)) ENGINE = MyISAM;

CREATE TABLE `infirmary` (
  `infirmary_user` int(11) UNSIGNED NOT NULL,
  `infirmary_reason` text NOT NULL,
  `infirmary_in` int(11) UNSIGNED NOT NULL,
  `infirmary_out` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `inventory` (
  `inv_id` int(11) UNSIGNED NOT NULL,
  `inv_itemid` int(11) UNSIGNED NOT NULL,
  `inv_userid` int(11) UNSIGNED NOT NULL,
  `inv_qty` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `itemauction` (
  `ia_id` INT(11) UNSIGNED NULL DEFAULT NULL AUTO_INCREMENT ,
  `ia_adder` INT(11) UNSIGNED NOT NULL ,
  `ia_item` INT(11) UNSIGNED NOT NULL ,
  `ia_qty` INT(11) UNSIGNED NOT NULL ,
  `ia_end` INT(11) UNSIGNED NOT NULL ,
  `ia_bidder` INT(11) UNSIGNED NOT NULL ,
  `ia_bid` BIGINT(11) UNSIGNED NOT NULL ,
  UNIQUE (`ia_id`)) ENGINE = MyISAM;

CREATE TABLE `itemmarket` (
  `imID` int(11) NOT NULL,
  `imITEM` int(11) NOT NULL DEFAULT '0',
  `imADDER` int(11) NOT NULL DEFAULT '0',
  `imPRICE` int(11) NOT NULL DEFAULT '0',
  `imCURRENCY` enum('primary','secondary') NOT NULL DEFAULT 'primary',
  `imQTY` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `items` (
  `itmid` int(11) UNSIGNED NOT NULL,
  `itmtype` int(11) UNSIGNED NOT NULL,
  `itmname` text NOT NULL,
  `itmdesc` text NOT NULL,
  `itmbuyprice` int(11) UNSIGNED NOT NULL,
  `itmsellprice` int(11) UNSIGNED NOT NULL,
  `itmbuyable` enum('false','true') NOT NULL,
  `effect1_on` enum('false','true') NOT NULL,
  `effect1` text NOT NULL,
  `effect2_on` enum('false','true') NOT NULL,
  `effect2` text NOT NULL,
  `effect3_on` enum('false','true') NOT NULL,
  `effect3` text NOT NULL,
  `weapon` int(11) UNSIGNED NOT NULL,
  `armor` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `itemselllogs` (
  `logid` int(11) UNSIGNED NOT NULL,
  `userid` int(11) UNSIGNED NOT NULL,
  `item_id` int(11) UNSIGNED NOT NULL,
  `price` int(11) UNSIGNED NOT NULL,
  `qty` int(11) UNSIGNED NOT NULL,
  `time` int(11) UNSIGNED NOT NULL,
  `log` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `itemtypes` (
  `itmtypeid` int(11) UNSIGNED NOT NULL,
  `itmtypename` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `login_attempts` (
  `ip` tinytext NOT NULL,
  `userid` int(10) UNSIGNED NOT NULL,
  `timestamp` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `logs` (
  `log_id` int(11) UNSIGNED NOT NULL,
  `log_type` text NOT NULL,
  `log_user` int(11) UNSIGNED NOT NULL,
  `log_time` int(11) UNSIGNED NOT NULL,
  `log_text` text NOT NULL,
  `log_ip` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `mail` (
  `mail_id` int(11) UNSIGNED NOT NULL,
  `mail_to` int(11) UNSIGNED NOT NULL,
  `mail_from` int(11) UNSIGNED NOT NULL,
  `mail_status` enum('unread','read') NOT NULL,
  `mail_subject` text NOT NULL,
  `mail_text` text NOT NULL,
  `mail_time` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `mining` (
  `userid` int(11) UNSIGNED NOT NULL,
  `max_miningpower` int(11) UNSIGNED NOT NULL,
  `miningpower` int(11) UNSIGNED NOT NULL,
  `miningxp` decimal(11,0) UNSIGNED NOT NULL,
  `buyable_power` int(11) UNSIGNED NOT NULL,
  `mining_level` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `mining_data` (
  `mine_id` int(11) UNSIGNED NOT NULL,
  `mine_location` int(11) UNSIGNED NOT NULL,
  `mine_level` int(11) UNSIGNED NOT NULL,
  `mine_copper_min` int(11) UNSIGNED NOT NULL,
  `mine_copper_max` int(11) UNSIGNED NOT NULL,
  `mine_silver_min` int(11) UNSIGNED NOT NULL,
  `mine_silver_max` int(11) UNSIGNED NOT NULL,
  `mine_gold_min` int(11) UNSIGNED NOT NULL,
  `mine_gold_max` int(11) UNSIGNED NOT NULL,
  `mine_pickaxe` int(11) UNSIGNED NOT NULL,
  `mine_iq` int(11) UNSIGNED NOT NULL,
  `mine_power_use` int(11) UNSIGNED NOT NULL,
  `mine_copper_item` int(11) UNSIGNED NOT NULL,
  `mine_silver_item` int(11) UNSIGNED NOT NULL,
  `mine_gold_item` int(11) UNSIGNED NOT NULL,
  `mine_gem_item` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `modules` (
  `module_id` int(11) UNSIGNED NOT NULL,
  `module_name` text NOT NULL,
  `module_author` text NOT NULL,
  `module_pages` text NOT NULL,
  `module_datatables` text NOT NULL,
  `module_api_version` text NOT NULL,
  `module_link` text NOT NULL,
  `module_update` text NOT NULL,
  `module_version` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `newspaper_ads` (
  `news_id` int(11) UNSIGNED NOT NULL,
  `news_cost` int(11) UNSIGNED NOT NULL,
  `news_start` int(11) UNSIGNED NOT NULL,
  `news_end` int(11) UNSIGNED NOT NULL,
  `news_owner` int(11) UNSIGNED NOT NULL,
  `news_text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `notifications` (
  `notif_id` int(11) UNSIGNED NOT NULL,
  `notif_user` int(11) UNSIGNED NOT NULL,
  `notif_time` int(11) UNSIGNED NOT NULL,
  `notif_status` enum('unread','read') NOT NULL,
  `notif_text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `permissions` (
  `perm_id` int(11) UNSIGNED NOT NULL,
  `perm_user` int(11) UNSIGNED NOT NULL,
  `perm_name` tinytext NOT NULL,
  `perm_disable` enum('true','false') NOT NULL DEFAULT 'true'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `polls` (
  `id` int(11) UNSIGNED NOT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '0',
  `question` text NOT NULL,
  `choice1` text NOT NULL,
  `choice2` text NOT NULL,
  `choice3` text NOT NULL,
  `choice4` text NOT NULL,
  `choice5` text NOT NULL,
  `choice6` text NOT NULL,
  `choice7` text NOT NULL,
  `choice8` text NOT NULL,
  `choice9` text NOT NULL,
  `choice10` text NOT NULL,
  `voted1` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `voted2` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `voted3` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `voted4` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `voted5` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `voted6` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `voted7` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `voted8` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `voted9` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `voted10` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `winner` tinyint(4) NOT NULL DEFAULT '0',
  `hidden` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `promo_codes` (
  `promo_id` int(11) UNSIGNED NOT NULL,
  `promo_code` text NOT NULL,
  `promo_item` int(11) UNSIGNED NOT NULL,
  `promo_use` int(11) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `pw_recovery` (
  `pwr_id` int(11) UNSIGNED NOT NULL,
  `pwr_ip` text NOT NULL,
  `pwr_email` text NOT NULL,
  `pwr_code` text NOT NULL,
  `pwr_expire` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `referals` (
  `referalid` int(11) UNSIGNED NOT NULL,
  `referal_userid` int(11) UNSIGNED NOT NULL,
  `referal_ip` tinytext NOT NULL,
  `refered_id` int(11) UNSIGNED NOT NULL,
  `refered_ip` tinytext NOT NULL,
  `time` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `reports` (
  `report_id` int(11) UNSIGNED NOT NULL,
  `reporter_id` int(11) UNSIGNED NOT NULL,
  `reportee_id` int(11) UNSIGNED NOT NULL,
  `report_text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `russian_roulette` (
  `challengee` int(11) UNSIGNED NOT NULL,
  `challenger` int(11) UNSIGNED NOT NULL,
  `reward` int(11) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `sec_market` ( 
	`sec_id` INT(11) UNSIGNED NULL DEFAULT NULL AUTO_INCREMENT , 
	`sec_user` INT(11) UNSIGNED NOT NULL , 
	`sec_cost` INT(11) UNSIGNED NOT NULL , 
	`sec_total` INT(11) UNSIGNED NOT NULL , 
	UNIQUE (`sec_id`)
) ENGINE = MyISAM;

CREATE TABLE `settings` (
  `setting_id` tinyint(11) UNSIGNED NOT NULL,
  `setting_name` text NOT NULL,
  `setting_value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `settings` (`setting_id`, `setting_name`, `setting_value`) VALUES
(1, 'ReferalKickback', '25'),
(2, 'RegistrationCaptcha', 'OFF'),
(3, 'HTTPS_Support', 'false'),
(4, 'AttackEnergyCost', '100'),
(5, 'MaxAttacksPerSession', '100'),
(6, 'GUILD_PRICE', '500000'),
(7, 'GUILD_LEVEL', '25'),
(8, 'bank_cost', '5000'),
(9, 'bank_maxfee', '5000'),
(10, 'bank_feepercent', '10'),
(11, 'max_sessiontime', '44'),
(12, 'Revalidate_Time', '300');

CREATE TABLE `shopitems` (
  `sitemID` int(11) NOT NULL,
  `sitemSHOP` int(11) NOT NULL DEFAULT '0',
  `sitemITEMID` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `shops` (
  `shopID` int(11) NOT NULL,
  `shopLOCATION` int(11) NOT NULL DEFAULT '0',
  `shopNAME` varchar(255) NOT NULL DEFAULT '',
  `shopDESCRIPTION` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `smelt_inprogress` (
  `sip_id` int(11) UNSIGNED NOT NULL,
  `sip_user` int(11) UNSIGNED NOT NULL,
  `sip_recipe` int(11) UNSIGNED NOT NULL,
  `sip_time` int(11) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `smelt_recipes` (
  `smelt_id` int(11) UNSIGNED NOT NULL,
  `smelt_time` int(11) UNSIGNED NOT NULL,
  `smelt_items` text NOT NULL,
  `smelt_quantity` text CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `smelt_output` int(11) UNSIGNED NOT NULL,
  `smelt_qty_output` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `town` (
  `town_id` int(11) UNSIGNED NOT NULL,
  `town_name` tinytext NOT NULL,
  `town_min_level` int(11) UNSIGNED NOT NULL,
  `town_guild_owner` int(11) UNSIGNED NOT NULL,
  `town_tax` tinyint(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `town` (`town_id`, `town_name`, `town_min_level`, `town_guild_owner`, `town_tax`) VALUES
(1, 'Cornrye', 1, 0, 20);

CREATE TABLE `userdata` (
  `userid` int(11) UNSIGNED NOT NULL,
  `useragent` text NOT NULL,
  `screensize` text NOT NULL,
  `os` text NOT NULL,
  `browser` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `users` (
  `userid` int(11) UNSIGNED NOT NULL,
  `username` text NOT NULL,
  `user_level` enum('Admin','Forum Moderator','Assistant','Member','Web Developer','NPC') NOT NULL DEFAULT 'Member',
  `email` text NOT NULL,
  `password` text NOT NULL,
  `level` int(11) UNSIGNED NOT NULL DEFAULT '1',
  `xp` bigint(11) NOT NULL DEFAULT '0',
  `gender` enum('Male','Female') NOT NULL DEFAULT 'Male',
  `class` enum('Warrior','Rogue','Defender') NOT NULL DEFAULT 'Warrior',
  `lastip` tinytext NOT NULL,
  `loginip` tinytext NOT NULL,
  `registerip` tinytext NOT NULL,
  `laston` int(11) UNSIGNED NOT NULL,
  `last_login` int(11) UNSIGNED NOT NULL,
  `registertime` int(11) UNSIGNED NOT NULL,
  `will` int(11) UNSIGNED NOT NULL DEFAULT '100',
  `maxwill` int(11) UNSIGNED NOT NULL DEFAULT '100',
  `hp` int(11) UNSIGNED NOT NULL DEFAULT '100',
  `maxhp` int(11) UNSIGNED NOT NULL DEFAULT '100',
  `energy` int(11) UNSIGNED NOT NULL DEFAULT '24',
  `maxenergy` int(11) UNSIGNED NOT NULL DEFAULT '24',
  `brave` int(11) UNSIGNED NOT NULL DEFAULT '10',
  `maxbrave` int(11) UNSIGNED NOT NULL DEFAULT '10',
  `primary_currency` int(11) UNSIGNED NOT NULL,
  `secondary_currency` int(11) UNSIGNED NOT NULL,
  `bank` bigint(11) NOT NULL DEFAULT '-1',
  `attacking` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `vip_days` int(11) UNSIGNED NOT NULL,
  `force_logout` enum('false','true') NOT NULL DEFAULT 'false',
  `display_pic` text NOT NULL,
  `signature` text NOT NULL,
  `personal_notes` text NOT NULL,
  `announcements` int(11) UNSIGNED NOT NULL,
  `equip_primary` int(11) UNSIGNED NOT NULL,
  `equip_secondary` int(11) UNSIGNED NOT NULL,
  `equip_armor` int(11) UNSIGNED NOT NULL,
  `guild` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `fedjail` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `staff_notes` mediumtext NOT NULL,
  `location` tinyint(11) UNSIGNED NOT NULL DEFAULT '1',
  `timezone` enum('Pacific/Wake','Pacific/Apia','America/Adak','America/Anchorage','America/Los_Angeles','America/Denver','America/Chicago','America/New_York','America/Halifax','America/Godthab','America/Noronha','Atlantic/Cape_Verde','Europe/London','Europe/Berlin','Europe/Bucharest','Europe/Moscow','Asia/Tehran','Asia/Muscat','Asia/Kabul','Asia/Karachi','Asia/Calcutta','Asia/Katmandu','Asia/Novosibirsks','America/Godthab','Asia/Rangoon','Asia/Bangkok','Australia/Perth','Asia/Tokyo','Australia/Darwin','Australia/Sydney','Asia/Magadan','Pacific/Auckland','Pacific/Tongatapu') NOT NULL DEFAULT 'Europe/London',
  `description` text NOT NULL,
  `theme` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `last_verified` int(11) UNSIGNED NOT NULL,
  `need_verify` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `userstats` (
  `userid` int(11) UNSIGNED NOT NULL,
  `strength` bigint(11) UNSIGNED NOT NULL,
  `agility` bigint(11) UNSIGNED NOT NULL,
  `guard` bigint(11) UNSIGNED NOT NULL,
  `iq` bigint(11) UNSIGNED NOT NULL,
  `labor` bigint(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `uservotes` (
  `userid` int(11) UNSIGNED NOT NULL,
  `voted` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `vips_accepted` (
  `vipID` int(11) UNSIGNED NOT NULL,
  `vipBUYER` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `vipFOR` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `vipPACKID` int(11) UNSIGNED NOT NULL,
  `vipTIME` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `vipTXN` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `vip_listing` (
  `vip_id` int(11) UNSIGNED NOT NULL,
  `vip_item` int(11) UNSIGNED NOT NULL,
  `vip_cost` decimal(10,2) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
  
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`ann_id`);

ALTER TABLE `botlist`
  ADD UNIQUE KEY `botid` (`botid`);

ALTER TABLE `contact_list`
  ADD UNIQUE KEY `c_ID` (`c_ID`);

ALTER TABLE `crimegroups`
  ADD PRIMARY KEY (`cgID`);

ALTER TABLE `crimes`
  ADD PRIMARY KEY (`crimeID`);

ALTER TABLE `crons`
  ADD PRIMARY KEY (`file`),
  ADD UNIQUE KEY `file` (`file`);
  
ALTER TABLE `dungeon`
  ADD PRIMARY KEY (`dungeon_user`);

ALTER TABLE `enemy`
  ADD PRIMARY KEY (`enemy_id`);

ALTER TABLE `estates`
  ADD PRIMARY KEY (`house_id`);

ALTER TABLE `fedjail`
  ADD PRIMARY KEY (`fed_id`);

ALTER TABLE `forum_forums`
  ADD UNIQUE KEY `ff_id` (`ff_id`);

ALTER TABLE `forum_posts`
  ADD PRIMARY KEY (`fp_id`);

ALTER TABLE `forum_topics`
  ADD PRIMARY KEY (`ft_id`),
  ADD UNIQUE KEY `ft_id` (`ft_id`);

ALTER TABLE `friends`
  ADD PRIMARY KEY (`friend_id`);

ALTER TABLE `gamerules`
  ADD UNIQUE KEY `rule_id` (`rule_id`);

ALTER TABLE `guild`
  ADD PRIMARY KEY (`guild_id`);

ALTER TABLE `guild_applications`
  ADD UNIQUE KEY `ga_id` (`ga_id`);

ALTER TABLE `guild_notifications`
  ADD UNIQUE KEY `gn_id` (`gn_id`);

ALTER TABLE `infirmary`
  ADD PRIMARY KEY (`infirmary_user`);

ALTER TABLE `inventory`
  ADD PRIMARY KEY (`inv_id`);

ALTER TABLE `itemmarket`
  ADD PRIMARY KEY (`imID`);

ALTER TABLE `items`
  ADD PRIMARY KEY (`itmid`);

ALTER TABLE `itemselllogs`
  ADD UNIQUE KEY `logid` (`logid`);

ALTER TABLE `itemtypes`
  ADD PRIMARY KEY (`itmtypeid`);

ALTER TABLE `logs`
  ADD UNIQUE KEY `log_id` (`log_id`);

ALTER TABLE `mail`
  ADD PRIMARY KEY (`mail_id`);
ALTER TABLE `mail` ADD FULLTEXT KEY `mail_subject` (`mail_subject`,`mail_text`);

ALTER TABLE `mining`
  ADD UNIQUE KEY `userid` (`userid`);

ALTER TABLE `mining_data`
  ADD UNIQUE KEY `mine_id` (`mine_id`);

ALTER TABLE `modules`
  ADD UNIQUE KEY `module_id` (`module_id`);

ALTER TABLE `newspaper_ads`
  ADD PRIMARY KEY (`news_id`);

ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notif_id`);
ALTER TABLE `notifications` ADD FULLTEXT KEY `notif_text` (`notif_text`);

ALTER TABLE `permissions`
  ADD PRIMARY KEY (`perm_id`);

ALTER TABLE `polls`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `promo_codes`
  ADD UNIQUE KEY `promo_id` (`promo_id`);

ALTER TABLE `pw_recovery`
  ADD PRIMARY KEY (`pwr_id`);

ALTER TABLE `referals`
  ADD PRIMARY KEY (`referalid`);

ALTER TABLE `reports`
  ADD PRIMARY KEY (`report_id`);

ALTER TABLE `settings`
  ADD PRIMARY KEY (`setting_id`);

ALTER TABLE `shopitems`
  ADD PRIMARY KEY (`sitemID`);

ALTER TABLE `shops`
  ADD PRIMARY KEY (`shopID`);

ALTER TABLE `smelt_inprogress`
  ADD UNIQUE KEY `sip_id` (`sip_id`);

ALTER TABLE `smelt_recipes`
  ADD UNIQUE KEY `smelt_id` (`smelt_id`);

ALTER TABLE `town`
  ADD PRIMARY KEY (`town_id`);

ALTER TABLE `userdata`
  ADD UNIQUE KEY `unique` (`userid`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`userid`);

ALTER TABLE `userstats`
  ADD PRIMARY KEY (`userid`);

ALTER TABLE `uservotes`
  ADD PRIMARY KEY (`userid`);

ALTER TABLE `vips_accepted`
  ADD UNIQUE KEY `vipID` (`vipID`);

ALTER TABLE `vip_listing`
  ADD UNIQUE KEY `vip_id` (`vip_id`);

ALTER TABLE `announcements`
  MODIFY `ann_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
  
ALTER TABLE `botlist`
  MODIFY `botid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `contact_list`
  MODIFY `c_ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `crimegroups`
  MODIFY `cgID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `crimes`
  MODIFY `crimeID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `enemy`
  MODIFY `enemy_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `estates`
  MODIFY `house_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `fedjail`
  MODIFY `fed_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `forum_forums`
  MODIFY `ff_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `forum_posts`
  MODIFY `fp_id` int(10) NOT NULL AUTO_INCREMENT;

ALTER TABLE `forum_topics`
  MODIFY `ft_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `friends`
  MODIFY `friend_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `gamerules`
  MODIFY `rule_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `guild`
  MODIFY `guild_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `guild_applications`
  MODIFY `ga_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `guild_notifications`
  MODIFY `gn_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `inventory`
  MODIFY `inv_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `itemmarket`
  MODIFY `imID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `items`
  MODIFY `itmid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `itemselllogs`
  MODIFY `logid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `itemtypes`
  MODIFY `itmtypeid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `logs`
  MODIFY `log_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `mail`
  MODIFY `mail_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `mining_data`
  MODIFY `mine_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `modules`
  MODIFY `module_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `newspaper_ads`
  MODIFY `news_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `notifications`
  MODIFY `notif_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `permissions`
  MODIFY `perm_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `polls`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `promo_codes`
  MODIFY `promo_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `pw_recovery`
  MODIFY `pwr_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `referals`
  MODIFY `referalid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `reports`
  MODIFY `report_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `settings`
  MODIFY `setting_id` tinyint(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

ALTER TABLE `shopitems`
  MODIFY `sitemID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `shops`
  MODIFY `shopID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `smelt_inprogress`
  MODIFY `sip_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `smelt_recipes`
  MODIFY `smelt_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `town`
  MODIFY `town_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `userdata`
  MODIFY `userid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `users`
  MODIFY `userid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `vips_accepted`
  MODIFY `vipID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `vip_listing`
  MODIFY `vip_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
  
INSERT INTO `settings` (`setting_id`, `setting_name`, `setting_value`) VALUES (NULL, 'energy_refill_cost', '10');
INSERT INTO `settings` (`setting_id`, `setting_name`, `setting_value`) VALUES (NULL, 'will_refill_cost', '5');
INSERT INTO `settings` (`setting_id`, `setting_name`, `setting_value`) VALUES (NULL, 'brave_refill_cost', '10');
INSERT INTO `settings` (`setting_id`, `setting_name`, `setting_value`) VALUES (NULL, 'iq_per_sec', '5');
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
