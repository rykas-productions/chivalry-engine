-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 28, 2016 at 12:46 AM
-- Server version: 5.7.14
-- PHP Version: 7.0.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cengine`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `ann_id` int(11) UNSIGNED NOT NULL,
  `ann_text` text NOT NULL,
  `ann_time` int(11) UNSIGNED NOT NULL,
  `ann_poster` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `crimegroups`
--

CREATE TABLE `crimegroups` (
  `cgID` int(11) UNSIGNED NOT NULL,
  `cgNAME` text NOT NULL,
  `cgORDER` int(11) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `crimes`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `crons`
--

CREATE TABLE `crons` (
  `file` varchar(30) COLLATE latin1_general_ci NOT NULL,
  `nextUpdate` int(11) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dungeon`
--

CREATE TABLE `dungeon` (
  `dungeon_user` int(11) UNSIGNED NOT NULL,
  `dungeon_reason` text NOT NULL,
  `dungeon_in` int(11) UNSIGNED NOT NULL,
  `dungeon_out` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `enemy`
--

CREATE TABLE `enemy` (
  `enemy_id` int(11) UNSIGNED NOT NULL,
  `enemy_user` int(11) UNSIGNED NOT NULL,
  `enemy_adder` int(11) UNSIGNED NOT NULL,
  `comment` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `estates`
--

CREATE TABLE `estates` (
  `house_id` int(11) UNSIGNED NOT NULL,
  `house_name` tinytext NOT NULL,
  `house_price` int(11) UNSIGNED NOT NULL,
  `house_will` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fedjail`
--

CREATE TABLE `fedjail` (
  `fed_id` int(11) UNSIGNED NOT NULL,
  `fed_userid` int(11) UNSIGNED NOT NULL,
  `fed_days` int(11) UNSIGNED NOT NULL,
  `fed_jailedby` int(11) UNSIGNED NOT NULL,
  `fed_reason` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `forum_forums`
--

CREATE TABLE `forum_forums` (
  `ff_id` int(10) UNSIGNED NOT NULL,
  `ff_name` tinytext NOT NULL,
  `ff_desc` tinytext NOT NULL,
  `ff_lp_t_id` int(10) UNSIGNED NOT NULL,
  `ff_lp_poster_id` int(11) NOT NULL,
  `ff_auth` enum('public','staff') NOT NULL,
  `ff_lp_time` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `forum_posts`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `forum_topics`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `friends`
--

CREATE TABLE `friends` (
  `friend_id` int(11) UNSIGNED NOT NULL,
  `friended` int(11) UNSIGNED NOT NULL,
  `friender` int(11) UNSIGNED NOT NULL,
  `comment` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `guild`
--

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
  `guild_xp` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `infirmary`
--

CREATE TABLE `infirmary` (
  `infirmary_user` int(11) UNSIGNED NOT NULL,
  `infirmary_reason` text NOT NULL,
  `infirmary_in` int(11) UNSIGNED NOT NULL,
  `infirmary_out` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `inv_id` int(11) UNSIGNED NOT NULL,
  `inv_itemid` int(11) UNSIGNED NOT NULL,
  `inv_userid` int(11) UNSIGNED NOT NULL,
  `inv_qty` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `itemselllogs`
--

CREATE TABLE `itemselllogs` (
  `logid` int(11) UNSIGNED NOT NULL,
  `userid` int(11) UNSIGNED NOT NULL,
  `item_id` int(11) UNSIGNED NOT NULL,
  `price` int(11) UNSIGNED NOT NULL,
  `qty` int(11) UNSIGNED NOT NULL,
  `time` int(11) UNSIGNED NOT NULL,
  `log` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `itemtypes`
--

CREATE TABLE `itemtypes` (
  `itmtypeid` int(11) UNSIGNED NOT NULL,
  `itmtypename` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `ip` tinytext NOT NULL,
  `userid` int(10) UNSIGNED NOT NULL,
  `timestamp` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `log_id` int(11) UNSIGNED NOT NULL,
  `log_type` text NOT NULL,
  `log_user` int(11) UNSIGNED NOT NULL,
  `log_time` int(11) UNSIGNED NOT NULL,
  `log_text` text NOT NULL,
  `log_ip` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mail`
--

CREATE TABLE `mail` (
  `mail_id` int(11) UNSIGNED NOT NULL,
  `mail_to` int(11) UNSIGNED NOT NULL,
  `mail_from` int(11) UNSIGNED NOT NULL,
  `mail_status` enum('unread','read') NOT NULL,
  `mail_subject` text NOT NULL,
  `mail_text` text NOT NULL,
  `mail_time` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `newspaper_ads`
--

CREATE TABLE `newspaper_ads` (
  `news_id` int(11) UNSIGNED NOT NULL,
  `news_cost` int(11) UNSIGNED NOT NULL,
  `news_start` int(11) UNSIGNED NOT NULL,
  `news_end` int(11) UNSIGNED NOT NULL,
  `news_owner` int(11) UNSIGNED NOT NULL,
  `news_text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notif_id` int(11) UNSIGNED NOT NULL,
  `notif_user` int(11) UNSIGNED NOT NULL,
  `notif_time` int(11) UNSIGNED NOT NULL,
  `notif_status` enum('unread','read') NOT NULL,
  `notif_text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `perm_id` int(11) UNSIGNED NOT NULL,
  `perm_user` int(11) UNSIGNED NOT NULL,
  `perm_name` tinytext NOT NULL,
  `perm_disable` enum('true','false') NOT NULL DEFAULT 'true'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `polls`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `referals`
--

CREATE TABLE `referals` (
  `referalid` int(11) UNSIGNED NOT NULL,
  `referal_userid` int(11) UNSIGNED NOT NULL,
  `referal_ip` tinytext NOT NULL,
  `refered_id` int(11) UNSIGNED NOT NULL,
  `refered_ip` tinytext NOT NULL,
  `time` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `report_id` int(11) UNSIGNED NOT NULL,
  `reporter_id` int(11) UNSIGNED NOT NULL,
  `reportee_id` int(11) UNSIGNED NOT NULL,
  `report_text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `setting_id` tinyint(11) UNSIGNED NOT NULL,
  `setting_name` text NOT NULL,
  `setting_value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`setting_id`, `setting_name`, `setting_value`) VALUES
(1, 'ReferalKickback', '25'),
(2, 'RegistrationCaptcha', 'OFF'),
(3, 'HTTPS_Support', 'false'),
(4, 'AttackEnergyCost', '100'),
(5, 'MaxAttacksPerSession', '100');

-- --------------------------------------------------------

--
-- Table structure for table `tmg_mines_data`
--

CREATE TABLE `tmg_mines_data` (
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

-- --------------------------------------------------------

--
-- Table structure for table `tmg_mining`
--

CREATE TABLE `tmg_mining` (
  `userid` int(11) UNSIGNED NOT NULL,
  `max_miningpower` int(11) NOT NULL,
  `miningpower` int(11) NOT NULL,
  `miningxp` varchar(11) NOT NULL,
  `buyable_power` smallint(11) NOT NULL,
  `mining_level` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Mining Mod Update from TMG';

--
-- Dumping data for table `tmg_mining`
--

INSERT INTO `tmg_mining` (`userid`, `max_miningpower`, `miningpower`, `miningxp`, `buyable_power`, `mining_level`) VALUES
(1, 100, 100, '0', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `town`
--

CREATE TABLE `town` (
  `town_id` int(11) UNSIGNED NOT NULL,
  `town_name` tinytext NOT NULL,
  `town_min_level` int(11) UNSIGNED NOT NULL,
  `town_guild_owner` int(11) UNSIGNED NOT NULL,
  `town_tax` tinyint(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `town`
--

INSERT INTO `town` (`town_id`, `town_name`, `town_min_level`, `town_guild_owner`, `town_tax`) VALUES
(1, 'Cornrye', 1, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `userdata`
--

CREATE TABLE `userdata` (
  `userid` int(11) UNSIGNED NOT NULL,
  `useragent` text NOT NULL,
  `screensize` text NOT NULL,
  `os` text NOT NULL,
  `browser` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

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
  `course` int(11) UNSIGNED NOT NULL,
  `days_left` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `userstats`
--

CREATE TABLE `userstats` (
  `userid` int(11) UNSIGNED NOT NULL,
  `strength` bigint(11) UNSIGNED NOT NULL,
  `agility` bigint(11) UNSIGNED NOT NULL,
  `guard` bigint(11) UNSIGNED NOT NULL,
  `iq` bigint(11) UNSIGNED NOT NULL,
  `labor` bigint(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `uservotes`
--

CREATE TABLE `uservotes` (
  `userid` int(11) UNSIGNED NOT NULL,
  `voted` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `academy`
--

CREATE TABLE `academy` ( 
	`academyid` INT(11) UNSIGNED NULL DEFAULT NULL AUTO_INCREMENT , 
	`academyname` TEXT NOT NULL , 
	`academydesc` TEXT NOT NULL , 
	`academycost` INT(11) UNSIGNED NOT NULL , 
	`academylevel` INT(11) UNSIGNED NOT NULL , 
	`academydays` INT(11) UNSIGNED NOT NULL , 
	`effect1_on` BOOLEAN NOT NULL , 
	`effect1` TEXT NOT NULL , 
	`effect2_on` BOOLEAN NOT NULL , 
	`effect2` TEXT NOT NULL , 
	`effect3_on` BOOLEAN NOT NULL , 
	`effect3` TEXT NOT NULL , 
	`effect4_on` BOOLEAN NOT NULL , 
	`effect4` TEXT NOT NULL , 
	UNIQUE (`academyid`)) ENGINE = MyISAM;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`ann_id`);

--
-- Indexes for table `crimegroups`
--
ALTER TABLE `crimegroups`
  ADD PRIMARY KEY (`cgID`);

--
-- Indexes for table `crimes`
--
ALTER TABLE `crimes`
  ADD PRIMARY KEY (`crimeID`);

--
-- Indexes for table `crons`
--
ALTER TABLE `crons`
  ADD PRIMARY KEY (`file`),
  ADD UNIQUE KEY `file` (`file`);

--
-- Indexes for table `dungeon`
--
ALTER TABLE `dungeon`
  ADD PRIMARY KEY (`dungeon_user`);

--
-- Indexes for table `enemy`
--
ALTER TABLE `enemy`
  ADD PRIMARY KEY (`enemy_id`);

--
-- Indexes for table `estates`
--
ALTER TABLE `estates`
  ADD PRIMARY KEY (`house_id`);

--
-- Indexes for table `fedjail`
--
ALTER TABLE `fedjail`
  ADD PRIMARY KEY (`fed_id`);

--
-- Indexes for table `forum_forums`
--
ALTER TABLE `forum_forums`
  ADD UNIQUE KEY `ff_id` (`ff_id`);

--
-- Indexes for table `forum_posts`
--
ALTER TABLE `forum_posts`
  ADD PRIMARY KEY (`fp_id`);

--
-- Indexes for table `forum_topics`
--
ALTER TABLE `forum_topics`
  ADD PRIMARY KEY (`ft_id`),
  ADD UNIQUE KEY `ft_id` (`ft_id`);

--
-- Indexes for table `friends`
--
ALTER TABLE `friends`
  ADD PRIMARY KEY (`friend_id`);

--
-- Indexes for table `guild`
--
ALTER TABLE `guild`
  ADD PRIMARY KEY (`guild_id`);

--
-- Indexes for table `infirmary`
--
ALTER TABLE `infirmary`
  ADD PRIMARY KEY (`infirmary_user`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`inv_id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`itmid`);

--
-- Indexes for table `itemselllogs`
--
ALTER TABLE `itemselllogs`
  ADD UNIQUE KEY `logid` (`logid`);

--
-- Indexes for table `itemtypes`
--
ALTER TABLE `itemtypes`
  ADD PRIMARY KEY (`itmtypeid`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD UNIQUE KEY `log_id` (`log_id`);

--
-- Indexes for table `mail`
--
ALTER TABLE `mail`
  ADD PRIMARY KEY (`mail_id`);
ALTER TABLE `mail` ADD FULLTEXT KEY `mail_subject` (`mail_subject`,`mail_text`);

--
-- Indexes for table `modules`
--
ALTER TABLE `modules`
  ADD UNIQUE KEY `module_id` (`module_id`);

--
-- Indexes for table `newspaper_ads`
--
ALTER TABLE `newspaper_ads`
  ADD PRIMARY KEY (`news_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notif_id`);
ALTER TABLE `notifications` ADD FULLTEXT KEY `notif_text` (`notif_text`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`perm_id`);

--
-- Indexes for table `polls`
--
ALTER TABLE `polls`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `referals`
--
ALTER TABLE `referals`
  ADD PRIMARY KEY (`referalid`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`report_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`setting_id`);

--
-- Indexes for table `tmg_mines_data`
--
ALTER TABLE `tmg_mines_data`
  ADD PRIMARY KEY (`mine_id`);

--
-- Indexes for table `tmg_mining`
--
ALTER TABLE `tmg_mining`
  ADD PRIMARY KEY (`userid`);

--
-- Indexes for table `town`
--
ALTER TABLE `town`
  ADD PRIMARY KEY (`town_id`);

--
-- Indexes for table `userdata`
--
ALTER TABLE `userdata`
  ADD UNIQUE KEY `unique` (`userid`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userid`);

--
-- Indexes for table `userstats`
--
ALTER TABLE `userstats`
  ADD PRIMARY KEY (`userid`);

--
-- Indexes for table `uservotes`
--
ALTER TABLE `uservotes`
  ADD PRIMARY KEY (`userid`);

--
-- Indexes for table `academy`
--
ALTER TABLE `academy` 
	ADD PRIMARY KEY (`academyid`)
  
--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `ann_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `crimegroups`
--
ALTER TABLE `crimegroups`
  MODIFY `cgID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `crimes`
--
ALTER TABLE `crimes`
  MODIFY `crimeID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `enemy`
--
ALTER TABLE `enemy`
  MODIFY `enemy_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `estates`
--
ALTER TABLE `estates`
  MODIFY `house_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `fedjail`
--
ALTER TABLE `fedjail`
  MODIFY `fed_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `forum_forums`
--
ALTER TABLE `forum_forums`
  MODIFY `ff_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `forum_posts`
--
ALTER TABLE `forum_posts`
  MODIFY `fp_id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `forum_topics`
--
ALTER TABLE `forum_topics`
  MODIFY `ft_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `friends`
--
ALTER TABLE `friends`
  MODIFY `friend_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `guild`
--
ALTER TABLE `guild`
  MODIFY `guild_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `inv_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `itmid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `itemselllogs`
--
ALTER TABLE `itemselllogs`
  MODIFY `logid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `itemtypes`
--
ALTER TABLE `itemtypes`
  MODIFY `itmtypeid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `log_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mail`
--
ALTER TABLE `mail`
  MODIFY `mail_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `modules`
--
ALTER TABLE `modules`
  MODIFY `module_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `newspaper_ads`
--
ALTER TABLE `newspaper_ads`
  MODIFY `news_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notif_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `perm_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `polls`
--
ALTER TABLE `polls`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `referals`
--
ALTER TABLE `referals`
  MODIFY `referalid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `report_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `setting_id` tinyint(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
--
-- AUTO_INCREMENT for table `tmg_mines_data`
--
ALTER TABLE `tmg_mines_data`
  MODIFY `mine_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `town`
--
ALTER TABLE `town`
  MODIFY `town_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `userdata`
--
ALTER TABLE `userdata`
  MODIFY `userid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
  
--
-- AUTO_INCREMENT for table `academy`
--

ALTER TABLE `academy`
  MODIFY `academyid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;


CREATE TABLE `shops` (
  `shopID` int(11) NOT NULL auto_increment,
  `shopLOCATION` int(11) NOT NULL default '0',
  `shopNAME` varchar(255) NOT NULL default '',
  `shopDESCRIPTION` text NOT NULL,
  PRIMARY KEY  (`shopID`)
) ENGINE=MyISAM ;

CREATE TABLE `shopitems` (
  `sitemID` int(11) NOT NULL auto_increment,
  `sitemSHOP` int(11) NOT NULL default '0',
  `sitemITEMID` int(11) NOT NULL default '0',
  PRIMARY KEY  (`sitemID`)
) ENGINE=MyISAM ;

CREATE TABLE `itemmarket` (
  `imID` int(11) NOT NULL auto_increment,
  `imITEM` int(11) NOT NULL default '0',
  `imADDER` int(11) NOT NULL default '0',
  `imPRICE` int(11) NOT NULL default '0',
  `imCURRENCY` enum('primary','secondary') NOT NULL default 'primary',
  `imQTY` int(11) NOT NULL default '0',
  PRIMARY KEY  (`imID`)
) ENGINE=MyISAM ;

INSERT INTO `settings` 
(`setting_id`, `setting_name`, `setting_value`) VALUES 
 (NULL, 'GUILD_PRICE', '500000'), 
 (NULL, 'GUILD_LEVEL', '25');
 
 CREATE TABLE `gamerules` ( 
	`rule_id` INT(11) UNSIGNED NULL DEFAULT NULL AUTO_INCREMENT , 
	`rule_text` TEXT NOT NULL , 
	UNIQUE (`rule_id`)
) ENGINE = MyISAM;

INSERT INTO `settings` (`setting_id`, `setting_name`, `setting_value`) VALUES  (NULL, 'bank_cost', '5000');
ALTER TABLE `guild` ADD `guild_announcement` TEXT NOT NULL AFTER `guild_xp`;
INSERT INTO `settings` (`setting_id`, `setting_name`, `setting_value`) VALUES (NULL, 'bank_maxfee', '5000'), (NULL, 'bank_feepercent', '10');

CREATE TABLE `promo_codes` (
  `promo_id` int(11) UNSIGNED NOT NULL,
  `promo_code` text NOT NULL,
  `promo_item` int(11) UNSIGNED NOT NULL,
  `promo_use` int(11) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `promo_codes`
--
ALTER TABLE `promo_codes`
  ADD UNIQUE KEY `promo_id` (`promo_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `promo_codes`
--
ALTER TABLE `promo_codes`
  MODIFY `promo_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
  
  ALTER TABLE `estates` ADD `house_level` INT(11) UNSIGNED NULL DEFAULT '1' AFTER `house_will`;
  ALTER TABLE `users` ADD `last_verified` INT(11) UNSIGNED NOT NULL AFTER `theme`;
  ALTER TABLE `users` ADD `need_verify` TINYINT NOT NULL AFTER `last_verified`;
  INSERT INTO `estates` (`house_id`, `house_name`, `house_price`, `house_will`, `house_level`) VALUES (1, 'Default House', 100, 100, 1);
  INSERT INTO `settings` (`setting_id`, `setting_name`, `setting_value`) VALUES (NULL, 'reCaptcha_public', 'PleaseUpdate'), (NULL, 'reCaptcha_private', 'PleaseUpdate');
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
