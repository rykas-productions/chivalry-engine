/*
	File:		cengine.sql
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		The database information to run Chivalry Engine V2.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
	MIT License

	Copyright (c) 2019 TheMasterGeneral

	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in all
	copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
	SOFTWARE.
*/
-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jul 24, 2017 at 06:12 PM
-- Server version: 10.1.19-MariaDB
-- PHP Version: 7.0.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `chivalry_engine`
--

-- --------------------------------------------------------

--
-- Table structure for table `academy`
--

CREATE TABLE `academy` (
  `ac_id` int(11) UNSIGNED NOT NULL,
  `ac_name` text NOT NULL,
  `ac_desc` text NOT NULL,
  `ac_cost` int(11) UNSIGNED NOT NULL,
  `ac_level` int(11) UNSIGNED NOT NULL,
  `ac_days` int(11) UNSIGNED NOT NULL,
  `ac_str` int(11) UNSIGNED NOT NULL,
  `ac_agl` int(11) UNSIGNED NOT NULL,
  `ac_grd` int(11) UNSIGNED NOT NULL,
  `ac_lab` int(11) UNSIGNED NOT NULL,
  `ac_iq` int(11) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `academy_done`
--

CREATE TABLE `academy_done` (
  `userid` int(11) UNSIGNED NOT NULL,
  `course` int(11) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
-- Table structure for table `botlist`
--

CREATE TABLE `botlist` (
  `botid` int(11) UNSIGNED NOT NULL,
  `botuser` int(11) UNSIGNED NOT NULL,
  `botitem` int(11) UNSIGNED NOT NULL,
  `botcooldown` int(11) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `botlist_hits`
--

CREATE TABLE `botlist_hits` (
  `userid` int(11) UNSIGNED NOT NULL,
  `botid` int(11) UNSIGNED NOT NULL,
  `lasthit` int(11) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `contact_list`
--

CREATE TABLE `contact_list` (
  `c_ID` int(11) UNSIGNED NOT NULL,
  `c_ADDED` int(11) UNSIGNED NOT NULL,
  `c_ADDER` int(11) UNSIGNED NOT NULL
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
  `house_will` int(11) UNSIGNED NOT NULL,
  `house_level` int(11) UNSIGNED DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `estates` (`house_id`, `house_name`, `house_price`, `house_will`, `house_level`) VALUES
(1, 'Default Estate', 0, 100, 1);

-- --------------------------------------------------------

--
-- Table structure for table `fedjail`
--

CREATE TABLE `fedjail` (
  `fed_id` int(11) UNSIGNED NOT NULL,
  `fed_userid` int(11) UNSIGNED NOT NULL,
  `fed_out` int(11) UNSIGNED NOT NULL,
  `fed_jailedby` int(11) UNSIGNED NOT NULL,
  `fed_reason` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `forum_bans`
--

CREATE TABLE `forum_bans` (
  `fb_id` int(11) UNSIGNED NOT NULL,
  `fb_user` int(11) UNSIGNED NOT NULL,
  `fb_banner` int(11) UNSIGNED NOT NULL,
  `fb_time` int(11) UNSIGNED NOT NULL,
  `fb_reason` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
-- Table structure for table `gamerules`
--

CREATE TABLE `gamerules` (
  `rule_id` int(11) UNSIGNED NOT NULL,
  `rule_text` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `guild`
--

CREATE TABLE `guild` (
  `guild_id` int(11) UNSIGNED NOT NULL,
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

-- --------------------------------------------------------

--
-- Table structure for table `guild_applications`
--

CREATE TABLE `guild_applications` (
  `ga_id` int(11) UNSIGNED NOT NULL,
  `ga_user` int(11) UNSIGNED NOT NULL,
  `ga_guild` int(11) UNSIGNED NOT NULL,
  `ga_time` int(11) UNSIGNED NOT NULL,
  `ga_text` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `guild_armory`
--

CREATE TABLE `guild_armory` (
  `gaID` INT(11) UNSIGNED NULL AUTO_INCREMENT ,
  `gaGUILD` INT(11) UNSIGNED NOT NULL ,
  `gaITEM` INT(11) UNSIGNED NOT NULL ,
  `gaQTY` INT(11) UNSIGNED NOT NULL ,
   UNIQUE (`gaID`)
) ENGINE = MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `guild_crimes`
--

CREATE TABLE `guild_crimes` (
  `gcID` INT(11) UNSIGNED NULL DEFAULT NULL AUTO_INCREMENT ,
  `gcNAME` INT(11) UNSIGNED NOT NULL ,
  `gcUSERS` INT(11) UNSIGNED NOT NULL ,
  `gcSTART` TEXT NOT NULL ,
  `gcSUCC` TEXT NOT NULL ,
  `gcFAIL` TEXT NOT NULL ,
  `gcMINCASH` INT(11) UNSIGNED NOT NULL ,
  `gcMAXCASH` INT(11) UNSIGNED NOT NULL ,
  PRIMARY KEY (`gcID`)
) ENGINE = MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `guild_crime_log`
--

CREATE TABLE `guild_crime_log` (
  `gclID` INT(11) UNSIGNED NULL DEFAULT NULL AUTO_INCREMENT ,
  `gclCID` INT(11) UNSIGNED NOT NULL ,
  `gclGUILD` INT(11) UNSIGNED NOT NULL ,
  `gclLOG` TEXT NOT NULL ,
  `gclRESULT` TEXT NOT NULL ,
  `gclWINNING` INT(11) UNSIGNED NOT NULL ,
  `gclTIME` INT(11) UNSIGNED NOT NULL ,
  PRIMARY KEY (`gclID`)
) ENGINE = MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `guild_notifications`
--

CREATE TABLE `guild_notifications` (
  `gn_id` int(11) UNSIGNED NOT NULL,
  `gn_guild` int(11) UNSIGNED NOT NULL,
  `gn_time` int(11) UNSIGNED NOT NULL,
  `gn_text` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `guild_wars`
--

CREATE TABLE `guild_wars` (
  `gw_id` int(11) UNSIGNED NOT NULL,
  `gw_declarer` int(11) UNSIGNED NOT NULL,
  `gw_declaree` int(11) UNSIGNED NOT NULL,
  `gw_drpoints` int(11) UNSIGNED NOT NULL,
  `gw_depoints` int(11) UNSIGNED NOT NULL,
  `gw_end` int(11) UNSIGNED NOT NULL,
  `gw_winner` int(11) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
-- Table structure for table `ipban`
--

CREATE TABLE `ipban` (
  `ip_id` int(11) UNSIGNED NOT NULL,
  `ip_ip` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `itemauction`
--

CREATE TABLE `itemauction` (
  `ia_id` int(11) UNSIGNED NOT NULL,
  `ia_adder` int(11) UNSIGNED NOT NULL,
  `ia_item` int(11) UNSIGNED NOT NULL,
  `ia_qty` int(11) UNSIGNED NOT NULL,
  `ia_end` int(11) UNSIGNED NOT NULL,
  `ia_bidder` int(11) UNSIGNED NOT NULL,
  `ia_bid` bigint(11) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `itemmarket`
--

CREATE TABLE `itemmarket` (
  `imID` int(11) NOT NULL,
  `imITEM` int(11) NOT NULL DEFAULT '0',
  `imADDER` int(11) NOT NULL DEFAULT '0',
  `imPRICE` int(11) NOT NULL DEFAULT '0',
  `imCURRENCY` enum('primary','secondary') NOT NULL DEFAULT 'primary',
  `imQTY` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
  `itmeffects_toggle` text NOT NULL,
  `itmeffects_stat` text NOT NULL,
  `itmeffects_dir` text NOT NULL,
  `itmeffects_amount` text NOT NULL,
  `itmeffects_type` text NOT NULL,
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


-- -------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `jRANK` INT(11) UNSIGNED NULL DEFAULT NULL AUTO_INCREMENT ,
  `jNAME` TEXT NOT NULL ,
  `jSTART` INT(11) UNSIGNED NOT NULL ,
  `jDESC` TEXT NOT NULL ,
  `jBOSS` INT NOT NULL ,
  UNIQUE (`jRANK`)
) ENGINE = MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `job_ranks`
--

CREATE TABLE `job_ranks` (
  `jrID` INT(11) UNSIGNED NULL DEFAULT NULL AUTO_INCREMENT ,
  `jrJOB` INT(11) UNSIGNED NOT NULL ,
  `jrPRIMPAY` INT(11) UNSIGNED NOT NULL ,
  `jrSECONDARY` INT(11) UNSIGNED NOT NULL ,
  `jrACT` INT(11) UNSIGNED NOT NULL ,
  `jrSTR` INT(11) UNSIGNED NOT NULL ,
  `jrLAB` INT(11) UNSIGNED NOT NULL ,
  `jrIQ` INT(11) UNSIGNED NOT NULL ,
  UNIQUE (`jrID`)
) ENGINE = MyISAM;

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
-- Table structure for table `mail_bans`
--
CREATE TABLE `mail_bans` (
  `mbID` INT(11) UNSIGNED NULL DEFAULT NULL AUTO_INCREMENT ,
  `mbUSER` INT(11) UNSIGNED NOT NULL ,
  `mbREASON` TEXT NOT NULL ,
  `mbBANNER` INT(11) UNSIGNED NOT NULL ,
  `mbTIME` INT(11) UNSIGNED NOT NULL ,
  PRIMARY KEY (`mbID`)
) ENGINE = MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `mining`
--

CREATE TABLE `mining` (
  `userid` int(11) UNSIGNED NOT NULL,
  `max_miningpower` int(11) UNSIGNED NOT NULL,
  `miningpower` int(11) UNSIGNED NOT NULL,
  `miningxp` decimal(11,0) UNSIGNED NOT NULL,
  `buyable_power` int(11) UNSIGNED NOT NULL,
  `mining_level` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mining_data`
--

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
-- Table structure for table `promo_codes`
--

CREATE TABLE `promo_codes` (
  `promo_id` int(11) UNSIGNED NOT NULL,
  `promo_code` text NOT NULL,
  `promo_item` int(11) UNSIGNED NOT NULL,
  `promo_use` int(11) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pw_recovery`
--

CREATE TABLE `pw_recovery` (
  `pwr_id` int(11) UNSIGNED NOT NULL,
  `pwr_ip` text NOT NULL,
  `pwr_email` text NOT NULL,
  `pwr_code` text NOT NULL,
  `pwr_expire` int(11) UNSIGNED NOT NULL
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
-- Table structure for table `russian_roulette`
--

CREATE TABLE `russian_roulette` (
  `challengee` int(11) UNSIGNED NOT NULL,
  `challenger` int(11) UNSIGNED NOT NULL,
  `reward` int(11) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sec_market`
--

CREATE TABLE `sec_market` (
  `sec_id` int(11) UNSIGNED NOT NULL,
  `sec_user` int(11) UNSIGNED NOT NULL,
  `sec_cost` int(11) UNSIGNED NOT NULL,
  `sec_total` int(11) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
(4, 'AttackEnergyCost', '50'),
(5, 'MaxAttacksPerSession', '50'),
(6, 'GUILD_PRICE', '500000'),
(7, 'GUILD_LEVEL', '25'),
(8, 'bank_cost', '5000'),
(9, 'bank_maxfee', '5000'),
(10, 'Revalidate_Time', '300'),
(11, 'brave_refill_cost', '10'),
(12, 'energy_refill_cost', '10'),
(13, 'iq_per_sec', '5'),
(14, 'will_refill_cost', '5'),
(15, 'bankfee_percent', '10'),
(16, 'sending_email', 'account@example.com'),
(17, 'enforce_no_multis', 'true');

-- --------------------------------------------------------

--
-- Table structure for table `shopitems`
--

CREATE TABLE `shopitems` (
  `sitemID` int(11) NOT NULL,
  `sitemSHOP` int(11) NOT NULL DEFAULT '0',
  `sitemITEMID` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `shops`
--

CREATE TABLE `shops` (
  `shopID` int(11) NOT NULL,
  `shopLOCATION` int(11) NOT NULL DEFAULT '0',
  `shopNAME` varchar(255) NOT NULL DEFAULT '',
  `shopDESCRIPTION` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `smelt_inprogress`
--

CREATE TABLE `smelt_inprogress` (
  `sip_id` int(11) UNSIGNED NOT NULL,
  `sip_user` int(11) UNSIGNED NOT NULL,
  `sip_recipe` int(11) UNSIGNED NOT NULL,
  `sip_time` int(11) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `smelt_recipes`
--

CREATE TABLE `smelt_recipes` (
  `smelt_id` int(11) UNSIGNED NOT NULL,
  `smelt_time` int(11) UNSIGNED NOT NULL,
  `smelt_items` text NOT NULL,
  `smelt_quantity` text CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `smelt_output` int(11) UNSIGNED NOT NULL,
  `smelt_qty_output` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `town`
--

CREATE TABLE `town` (
  `town_id` int(11) UNSIGNED NOT NULL,
  `town_name` tinytext NOT NULL,
  `town_min_level` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `town` (`town_id`, `town_name`, `town_min_level`) VALUES
(1, 'Default Town', 1);


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
  `description` text NOT NULL,
  `last_verified` int(11) UNSIGNED NOT NULL,
  `need_verify` tinyint(4) NOT NULL,
  `course` int(11) UNSIGNED NOT NULL,
  `course_complete` int(11) UNSIGNED NOT NULL,
  `email_optin` tinyint(1) NOT NULL DEFAULT '1'
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
-- Table structure for table `vips_accepted`
--

CREATE TABLE `vips_accepted` (
  `vipID` int(11) UNSIGNED NOT NULL,
  `vipBUYER` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `vipFOR` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `vipPACKID` int(11) UNSIGNED NOT NULL,
  `vipTIME` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `vipTXN` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `vip_listing`
--

CREATE TABLE `vip_listing` (
  `vip_id` int(11) UNSIGNED NOT NULL,
  `vip_item` int(11) UNSIGNED NOT NULL,
  `vip_cost` decimal(10,2) NOT NULL,
  `vip_qty` int(11) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academy`
--
ALTER TABLE `academy`
  ADD UNIQUE KEY (`ac_id`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD UNIQUE KEY (`ann_id`);

--
-- Indexes for table `botlist`
--
ALTER TABLE `botlist`
  ADD UNIQUE KEY `botid` (`botid`);

--
-- Indexes for table `contact_list`
--
ALTER TABLE `contact_list`
  ADD UNIQUE KEY `c_ID` (`c_ID`);

--
-- Indexes for table `crimegroups`
--
ALTER TABLE `crimegroups`
  ADD UNIQUE KEY (`cgID`);

--
-- Indexes for table `crimes`
--
ALTER TABLE `crimes`
  ADD UNIQUE KEY (`crimeID`);

--
-- Indexes for table `crons`
--
ALTER TABLE `crons`
  ADD UNIQUE KEY (`file`);

--
-- Indexes for table `dungeon`
--
ALTER TABLE `dungeon`
  ADD UNIQUE KEY (`dungeon_user`);

--
-- Indexes for table `enemy`
--
ALTER TABLE `enemy`
  ADD UNIQUE KEY (`enemy_id`);

--
-- Indexes for table `estates`
--
ALTER TABLE `estates`
  ADD UNIQUE KEY (`house_id`);

--
-- Indexes for table `fedjail`
--
ALTER TABLE `fedjail`
  ADD UNIQUE KEY (`fed_id`);

--
-- Indexes for table `forum_bans`
--
ALTER TABLE `forum_bans`
  ADD UNIQUE KEY `fb_id` (`fb_id`);

--
-- Indexes for table `forum_forums`
--
ALTER TABLE `forum_forums`
  ADD UNIQUE KEY `ff_id` (`ff_id`);

--
-- Indexes for table `forum_posts`
--
ALTER TABLE `forum_posts`
  ADD UNIQUE KEY (`fp_id`);

--
-- Indexes for table `forum_topics`
--
ALTER TABLE `forum_topics`
  ADD UNIQUE KEY (`ft_id`);

--
-- Indexes for table `friends`
--
ALTER TABLE `friends`
  ADD UNIQUE KEY (`friend_id`);

--
-- Indexes for table `gamerules`
--
ALTER TABLE `gamerules`
  ADD UNIQUE KEY `rule_id` (`rule_id`);

--
-- Indexes for table `guild`
--
ALTER TABLE `guild`
  ADD UNIQUE KEY (`guild_id`);

--
-- Indexes for table `guild_applications`
--
ALTER TABLE `guild_applications`
  ADD UNIQUE KEY `ga_id` (`ga_id`);

--
-- Indexes for table `guild_notifications`
--
ALTER TABLE `guild_notifications`
  ADD UNIQUE KEY `gn_id` (`gn_id`);

--
-- Indexes for table `guild_wars`
--
ALTER TABLE `guild_wars`
  ADD UNIQUE KEY `gw_id` (`gw_id`);

--
-- Indexes for table `infirmary`
--
ALTER TABLE `infirmary`
  ADD UNIQUE KEY (`infirmary_user`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD UNIQUE KEY (`inv_id`);

--
-- Indexes for table `ipban`
--
ALTER TABLE `ipban`
  ADD UNIQUE KEY `ip_id` (`ip_id`);

--
-- Indexes for table `itemauction`
--
ALTER TABLE `itemauction`
  ADD UNIQUE KEY `ia_id` (`ia_id`);

--
-- Indexes for table `itemmarket`
--
ALTER TABLE `itemmarket`
  ADD UNIQUE KEY (`imID`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD UNIQUE KEY (`itmid`);

--
-- Indexes for table `itemselllogs`
--
ALTER TABLE `itemselllogs`
  ADD UNIQUE KEY `logid` (`logid`);

--
-- Indexes for table `itemtypes`
--
ALTER TABLE `itemtypes`
  ADD UNIQUE KEY (`itmtypeid`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD UNIQUE KEY `log_id` (`log_id`);

--
-- Indexes for table `mail`
--
ALTER TABLE `mail`
  ADD UNIQUE KEY (`mail_id`);

--
-- Indexes for table `mining`
--
ALTER TABLE `mining`
  ADD UNIQUE KEY `userid` (`userid`);

--
-- Indexes for table `mining_data`
--
ALTER TABLE `mining_data`
  ADD UNIQUE KEY `mine_id` (`mine_id`);

--
-- Indexes for table `modules`
--
ALTER TABLE `modules`
  ADD UNIQUE KEY `module_id` (`module_id`);

--
-- Indexes for table `newspaper_ads`
--
ALTER TABLE `newspaper_ads`
  ADD UNIQUE KEY (`news_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD UNIQUE KEY (`notif_id`);

--
-- Indexes for table `polls`
--
ALTER TABLE `polls`
  ADD UNIQUE KEY (`id`);

--
-- Indexes for table `promo_codes`
--
ALTER TABLE `promo_codes`
  ADD UNIQUE KEY `promo_id` (`promo_id`);

--
-- Indexes for table `pw_recovery`
--
ALTER TABLE `pw_recovery`
  ADD UNIQUE KEY (`pwr_id`);

--
-- Indexes for table `referals`
--
ALTER TABLE `referals`
  ADD UNIQUE KEY (`referalid`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD UNIQUE KEY (`report_id`);

--
-- Indexes for table `sec_market`
--
ALTER TABLE `sec_market`
  ADD UNIQUE KEY `sec_id` (`sec_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD UNIQUE KEY (`setting_id`);

--
-- Indexes for table `shopitems`
--
ALTER TABLE `shopitems`
  ADD UNIQUE KEY (`sitemID`);

--
-- Indexes for table `shops`
--
ALTER TABLE `shops`
  ADD UNIQUE KEY (`shopID`);

--
-- Indexes for table `smelt_inprogress`
--
ALTER TABLE `smelt_inprogress`
  ADD UNIQUE KEY `sip_id` (`sip_id`);

--
-- Indexes for table `smelt_recipes`
--
ALTER TABLE `smelt_recipes`
  ADD UNIQUE KEY `smelt_id` (`smelt_id`);

--
-- Indexes for table `town`
--
ALTER TABLE `town`
  ADD UNIQUE KEY (`town_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD UNIQUE KEY (`userid`);

--
-- Indexes for table `userstats`
--
ALTER TABLE `userstats`
  ADD UNIQUE KEY (`userid`);

--
-- Indexes for table `uservotes`
--
ALTER TABLE `uservotes`
  ADD UNIQUE KEY (`userid`);

--
-- Indexes for table `vips_accepted`
--
ALTER TABLE `vips_accepted`
  ADD UNIQUE KEY `vipID` (`vipID`);

--
-- Indexes for table `vip_listing`
--
ALTER TABLE `vip_listing`
  ADD UNIQUE KEY `vip_id` (`vip_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `academy`
--
ALTER TABLE `academy`
  MODIFY `ac_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `ann_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `botlist`
--
ALTER TABLE `botlist`
  MODIFY `botid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `contact_list`
--
ALTER TABLE `contact_list`
  MODIFY `c_ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
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
  MODIFY `house_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `fedjail`
--
ALTER TABLE `fedjail`
  MODIFY `fed_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `forum_bans`
--
ALTER TABLE `forum_bans`
  MODIFY `fb_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
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
-- AUTO_INCREMENT for table `gamerules`
--
ALTER TABLE `gamerules`
  MODIFY `rule_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `guild`
--
ALTER TABLE `guild`
  MODIFY `guild_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `guild_applications`
--
ALTER TABLE `guild_applications`
  MODIFY `ga_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `guild_notifications`
--
ALTER TABLE `guild_notifications`
  MODIFY `gn_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `guild_wars`
--
ALTER TABLE `guild_wars`
  MODIFY `gw_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `inv_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ipban`
--
ALTER TABLE `ipban`
  MODIFY `ip_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `itemauction`
--
ALTER TABLE `itemauction`
  MODIFY `ia_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `itemmarket`
--
ALTER TABLE `itemmarket`
  MODIFY `imID` int(11) NOT NULL AUTO_INCREMENT;
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
-- AUTO_INCREMENT for table `mining_data`
--
ALTER TABLE `mining_data`
  MODIFY `mine_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
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
-- AUTO_INCREMENT for table `polls`
--
ALTER TABLE `polls`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `promo_codes`
--
ALTER TABLE `promo_codes`
  MODIFY `promo_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `pw_recovery`
--
ALTER TABLE `pw_recovery`
  MODIFY `pwr_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
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
-- AUTO_INCREMENT for table `sec_market`
--
ALTER TABLE `sec_market`
  MODIFY `sec_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `setting_id` tinyint(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT for table `shopitems`
--
ALTER TABLE `shopitems`
  MODIFY `sitemID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `shops`
--
ALTER TABLE `shops`
  MODIFY `shopID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `smelt_inprogress`
--
ALTER TABLE `smelt_inprogress`
  MODIFY `sip_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `smelt_recipes`
--
ALTER TABLE `smelt_recipes`
  MODIFY `smelt_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `town`
--
ALTER TABLE `town`
  MODIFY `town_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vips_accepted`
--
ALTER TABLE `vips_accepted`
  MODIFY `vipID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vip_listing`
--
ALTER TABLE `vip_listing`
  MODIFY `vip_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

  ALTER TABLE `job_ranks` ADD `jrRANK` TEXT NOT NULL AFTER `jrID`;
  ALTER TABLE `jobs` CHANGE `jBOSS` `jBOSS` TEXT NOT NULL;
  ALTER TABLE `users` ADD `job` INT(11) UNSIGNED NOT NULL AFTER `secondary_currency`,
  ADD `jobrank` INT(11) UNSIGNED NOT NULL AFTER `job`;
  ALTER TABLE `users` ADD `jobwork` INT(11) UNSIGNED NOT NULL AFTER `jobrank`;
  ALTER TABLE `guild_crimes` CHANGE `gcNAME` `gcNAME` TEXT NOT NULL;
ALTER TABLE `guild` ADD `guild_crime` INT(11) UNSIGNED NOT NULL AFTER `guild_announcement`, ADD `guild_crime_done` INT(11) UNSIGNED NOT NULL AFTER `guild_crime`;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
