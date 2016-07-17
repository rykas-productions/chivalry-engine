-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jun 30, 2016 at 08:17 PM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `cengine`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE IF NOT EXISTS `announcements` (
  `ann_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ann_text` text NOT NULL,
  `ann_time` int(11) unsigned NOT NULL,
  `ann_poster` int(11) unsigned NOT NULL,
  PRIMARY KEY (`ann_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `attacklogs`
--

CREATE TABLE IF NOT EXISTS `attacklogs` (
  `log_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `attacker` int(11) unsigned NOT NULL,
  `attacked` int(11) unsigned NOT NULL,
  `result` enum('won','lost') NOT NULL,
  `time` int(11) unsigned NOT NULL,
  `stole` tinyint(4) NOT NULL,
  `attacklog` text NOT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `crons`
--

CREATE TABLE IF NOT EXISTS `crons` (
  `file` varchar(30) COLLATE latin1_general_ci NOT NULL,
  `nextUpdate` int(11) unsigned NOT NULL,
  PRIMARY KEY (`file`),
  UNIQUE KEY `file` (`file`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dungeon`
--

CREATE TABLE IF NOT EXISTS `dungeon` (
  `dungeon_user` int(11) unsigned NOT NULL,
  `dungeon_reason` text NOT NULL,
  `dungeon_in` int(11) unsigned NOT NULL,
  `dungeon_out` int(11) unsigned NOT NULL,
  PRIMARY KEY (`dungeon_user`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `enemy`
--

CREATE TABLE IF NOT EXISTS `enemy` (
  `enemy_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `enemy_user` int(11) unsigned NOT NULL,
  `enemy_adder` int(11) unsigned NOT NULL,
  `comment` text NOT NULL,
  PRIMARY KEY (`enemy_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `estates`
--

CREATE TABLE IF NOT EXISTS `estates` (
  `house_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `house_name` tinytext NOT NULL,
  `house_price` int(11) unsigned NOT NULL,
  `house_will` int(11) unsigned NOT NULL,
  PRIMARY KEY (`house_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `estates`
--

INSERT INTO `estates` (`house_id`, `house_name`, `house_price`, `house_will`) VALUES
(1, 'Nude and Proud', 0, 100);

-- --------------------------------------------------------

--
-- Table structure for table `fedjail`
--

CREATE TABLE IF NOT EXISTS `fedjail` (
  `fed_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fed_userid` int(11) unsigned NOT NULL,
  `fed_days` int(11) unsigned NOT NULL,
  `fed_jailedby` int(11) unsigned NOT NULL,
  `fed_reason` text NOT NULL,
  PRIMARY KEY (`fed_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `forum_forums`
--

CREATE TABLE IF NOT EXISTS `forum_forums` (
  `ff_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ff_name` tinytext NOT NULL,
  `ff_desc` tinytext NOT NULL,
  `ff_lp_t_id` int(10) unsigned NOT NULL,
  `ff_lp_poster_id` int(11) NOT NULL,
  `ff_auth` enum('public','staff') NOT NULL,
  `ff_lp_time` int(10) unsigned NOT NULL,
  UNIQUE KEY `ff_id` (`ff_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `forum_posts`
--

CREATE TABLE IF NOT EXISTS `forum_posts` (
  `fp_id` int(10) NOT NULL AUTO_INCREMENT,
  `fp_poster_id` int(10) unsigned NOT NULL,
  `ff_id` int(10) unsigned NOT NULL,
  `fp_time` int(10) unsigned NOT NULL,
  `fp_topic_id` int(10) unsigned NOT NULL,
  `fp_editor_id` int(10) unsigned NOT NULL,
  `fp_edit_count` int(10) unsigned NOT NULL,
  `fp_editor_time` int(10) unsigned NOT NULL,
  `fp_text` text NOT NULL,
  PRIMARY KEY (`fp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `forum_topics`
--

CREATE TABLE IF NOT EXISTS `forum_topics` (
  `ft_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ft_forum_id` int(10) unsigned NOT NULL,
  `ft_name` tinytext NOT NULL,
  `ft_desc` tinytext NOT NULL,
  `ft_posts` int(10) unsigned NOT NULL,
  `ft_owner_id` int(10) unsigned NOT NULL,
  `ft_last_id` int(10) unsigned NOT NULL,
  `ft_start_time` int(10) unsigned NOT NULL,
  `ft_last_time` int(10) unsigned NOT NULL,
  `ft_pinned` tinytext NOT NULL,
  `ft_locked` tinyint(4) NOT NULL,
  PRIMARY KEY (`ft_id`),
  UNIQUE KEY `ft_id` (`ft_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `friends`
--

CREATE TABLE IF NOT EXISTS `friends` (
  `friend_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `friended` int(11) unsigned NOT NULL,
  `friender` int(11) unsigned NOT NULL,
  `comment` text NOT NULL,
  PRIMARY KEY (`friend_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `guild`
--

CREATE TABLE IF NOT EXISTS `guild` (
  `guild_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `guild_town_id` int(11) unsigned NOT NULL,
  `guild_owner` int(11) unsigned NOT NULL,
  `guild_coowner` int(11) unsigned NOT NULL,
  `guild_primcurr` int(11) unsigned NOT NULL,
  `guild_seccurr` int(11) unsigned NOT NULL,
  `guild_hasarmory` enum('false','true') NOT NULL DEFAULT 'false',
  `guild_capacity` int(11) unsigned NOT NULL,
  `guild_name` text NOT NULL,
  `guild_desc` text NOT NULL,
  `guild_level` int(11) unsigned NOT NULL,
  `guild_xp` int(11) unsigned NOT NULL,
  PRIMARY KEY (`guild_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `guild`
--

INSERT INTO `guild` (`guild_id`, `guild_town_id`, `guild_owner`, `guild_coowner`, `guild_primcurr`, `guild_seccurr`, `guild_hasarmory`, `guild_capacity`, `guild_name`, `guild_desc`, `guild_level`, `guild_xp`) VALUES
(1, 1, 1, 1, 1, 1, 'false', 5, 'test', 'test', 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `infirmary`
--

CREATE TABLE IF NOT EXISTS `infirmary` (
  `infirmary_user` int(11) unsigned NOT NULL,
  `infirmary_reason` text NOT NULL,
  `infirmary_in` int(11) unsigned NOT NULL,
  `infirmary_out` int(11) unsigned NOT NULL,
  PRIMARY KEY (`infirmary_user`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE IF NOT EXISTS `inventory` (
  `inv_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `inv_itemid` int(11) unsigned NOT NULL,
  `inv_userid` int(11) unsigned NOT NULL,
  `inv_qty` int(11) unsigned NOT NULL,
  PRIMARY KEY (`inv_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE IF NOT EXISTS `items` (
  `itmid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `itmtype` int(11) unsigned NOT NULL,
  `itmname` text NOT NULL,
  `itmdesc` text NOT NULL,
  `itmbuyprice` int(11) unsigned NOT NULL,
  `itmsellprice` int(11) unsigned NOT NULL,
  `itmbuyable` enum('false','true') NOT NULL,
  `effect1_on` enum('false','true') NOT NULL,
  `effect1` text NOT NULL,
  `effect2_on` enum('false','true') NOT NULL,
  `effect2` text NOT NULL,
  `effect3_on` enum('false','true') NOT NULL,
  `effect3` text NOT NULL,
  `weapon` int(11) unsigned NOT NULL,
  `armor` int(11) unsigned NOT NULL,
  PRIMARY KEY (`itmid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `itemtypes`
--

CREATE TABLE IF NOT EXISTS `itemtypes` (
  `itmtypeid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `itmtypename` text NOT NULL,
  PRIMARY KEY (`itmtypeid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE IF NOT EXISTS `login_attempts` (
  `ip` tinytext NOT NULL,
  `userid` int(10) unsigned NOT NULL,
  `timestamp` int(11) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `login_attempts`
--

INSERT INTO `login_attempts` (`ip`, `userid`, `timestamp`) VALUES
('192.168.128.113', 6, 1466643890);

-- --------------------------------------------------------

--
-- Table structure for table `logs_training`
--

CREATE TABLE IF NOT EXISTS `logs_training` (
  `log_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `log_user` int(11) unsigned NOT NULL,
  `log_stat` enum('Strength','Agility','Guard','Labor') NOT NULL,
  `log_gain` int(11) unsigned NOT NULL,
  `log_time` int(11) unsigned NOT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `mail`
--

CREATE TABLE IF NOT EXISTS `mail` (
  `mail_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mail_to` int(11) unsigned NOT NULL,
  `mail_from` int(11) unsigned NOT NULL,
  `mail_status` enum('unread','read') NOT NULL,
  `mail_subject` text NOT NULL,
  `mail_text` text NOT NULL,
  `mail_time` int(11) unsigned NOT NULL,
  PRIMARY KEY (`mail_id`),
  FULLTEXT KEY `mail_subject` (`mail_subject`,`mail_text`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `newspaper_ads`
--

CREATE TABLE IF NOT EXISTS `newspaper_ads` (
  `news_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `news_cost` int(11) unsigned NOT NULL,
  `news_start` int(11) unsigned NOT NULL,
  `news_end` int(11) unsigned NOT NULL,
  `news_owner` int(11) unsigned NOT NULL,
  `news_text` text NOT NULL,
  PRIMARY KEY (`news_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `newspaper_ads`
--

INSERT INTO `newspaper_ads` (`news_id`, `news_cost`, `news_start`, `news_end`, `news_owner`, `news_text`) VALUES
(1, 125000, 0, 4054546460, 1, 'hi');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE IF NOT EXISTS `notifications` (
  `notif_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `notif_user` int(11) unsigned NOT NULL,
  `notif_time` int(11) unsigned NOT NULL,
  `notif_status` enum('unread','read') NOT NULL,
  `notif_text` text NOT NULL,
  PRIMARY KEY (`notif_id`),
  FULLTEXT KEY `notif_text` (`notif_text`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE IF NOT EXISTS `permissions` (
  `perm_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `perm_user` int(11) unsigned NOT NULL,
  `perm_name` tinytext NOT NULL,
  `perm_disable` enum('true','false') NOT NULL DEFAULT 'true',
  PRIMARY KEY (`perm_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `referals`
--

CREATE TABLE IF NOT EXISTS `referals` (
  `referalid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `referal_userid` int(11) unsigned NOT NULL,
  `referal_ip` tinytext NOT NULL,
  `refered_id` int(11) unsigned NOT NULL,
  `refered_ip` tinytext NOT NULL,
  `time` int(11) unsigned NOT NULL,
  PRIMARY KEY (`referalid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE IF NOT EXISTS `reports` (
  `report_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `reporter_id` int(11) unsigned NOT NULL,
  `reportee_id` int(11) unsigned NOT NULL,
  `report_text` text NOT NULL,
  PRIMARY KEY (`report_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `setting_id` tinyint(11) unsigned NOT NULL AUTO_INCREMENT,
  `setting_name` text NOT NULL,
  `setting_value` text NOT NULL,
  PRIMARY KEY (`setting_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`setting_id`, `setting_name`, `setting_value`) VALUES
(1, 'ReferalKickback', '25'),
(2, 'RegistrationCaptcha', 'OFF'),
(3, 'HTTPS_Support', 'false'),
(4, 'AttackEnergyCost', '100'),
(5, 'FGPassword', 'h8z7abKWPJjI10r9'),
(6, 'FGUsername', '8WDgqcFYA0WPc3J5'),
(7, 'MaxAttacksPerSession', '100');

-- --------------------------------------------------------

--
-- Table structure for table `stafflogs`
--

CREATE TABLE IF NOT EXISTS `stafflogs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user` int(11) unsigned NOT NULL,
  `time` int(11) unsigned NOT NULL,
  `action` text NOT NULL,
  `ip` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `town`
--

CREATE TABLE IF NOT EXISTS `town` (
  `town_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `town_name` tinytext NOT NULL,
  `town_min_level` int(11) unsigned NOT NULL,
  `town_guild_owner` int(11) unsigned NOT NULL,
  `town_tax` tinyint(11) unsigned NOT NULL,
  PRIMARY KEY (`town_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `town`
--

INSERT INTO `town` (`town_id`, `town_name`, `town_min_level`, `town_guild_owner`, `town_tax`) VALUES
(1, 'Cornrye', 1, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `userid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` text NOT NULL,
  `user_level` enum('Admin','Forum Moderator','Assistant','Member','Web Developer','NPC') NOT NULL DEFAULT 'Member',
  `email` text NOT NULL,
  `password` text NOT NULL,
  `level` int(11) unsigned NOT NULL DEFAULT '1',
  `xp` bigint(11) NOT NULL DEFAULT '0',
  `gender` enum('Male','Female') NOT NULL DEFAULT 'Male',
  `class` enum('Warrior','Rogue','Defender') NOT NULL DEFAULT 'Warrior',
  `lastip` tinytext NOT NULL,
  `loginip` tinytext NOT NULL,
  `registerip` tinytext NOT NULL,
  `laston` int(11) unsigned NOT NULL,
  `last_login` int(11) unsigned NOT NULL,
  `registertime` int(11) unsigned NOT NULL,
  `will` int(11) unsigned NOT NULL DEFAULT '100',
  `maxwill` int(11) unsigned NOT NULL DEFAULT '100',
  `hp` int(11) unsigned NOT NULL DEFAULT '100',
  `maxhp` int(11) unsigned NOT NULL DEFAULT '100',
  `energy` int(11) unsigned NOT NULL DEFAULT '24',
  `maxenergy` int(11) unsigned NOT NULL DEFAULT '24',
  `brave` int(11) unsigned NOT NULL DEFAULT '10',
  `maxbrave` int(11) unsigned NOT NULL DEFAULT '10',
  `primary_currency` int(11) unsigned NOT NULL,
  `secondary_currency` int(11) unsigned NOT NULL,
  `bank` bigint(11) NOT NULL DEFAULT '-1',
  `attacking` int(11) unsigned NOT NULL DEFAULT '0',
  `vip_days` int(11) unsigned NOT NULL,
  `force_logout` enum('false','true') NOT NULL DEFAULT 'false',
  `display_pic` text NOT NULL,
  `signature` text NOT NULL,
  `personal_notes` text NOT NULL,
  `announcements` int(11) unsigned NOT NULL,
  `equip_primary` int(11) unsigned NOT NULL,
  `equip_secondary` int(11) unsigned NOT NULL,
  `equip_armor` int(11) unsigned NOT NULL,
  `guild` int(11) unsigned NOT NULL DEFAULT '0',
  `fedjail` int(11) unsigned NOT NULL DEFAULT '0',
  `staff_notes` mediumtext NOT NULL,
  `location` tinyint(11) unsigned NOT NULL DEFAULT '1',
  `timezone` enum('Pacific/Wake','Pacific/Apia','America/Adak','America/Anchorage','America/Los_Angeles','America/Denver','America/Chicago','America/New_York','America/Halifax','America/Godthab','America/Noronha','Atlantic/Cape_Verde','Europe/London','Europe/Berlin','Europe/Bucharest','Europe/Moscow','Asia/Tehran','Asia/Muscat','Asia/Kabul','Asia/Karachi','Asia/Calcutta','Asia/Katmandu','Asia/Novosibirsks','America/Godthab','Asia/Rangoon','Asia/Bangkok','Australia/Perth','Asia/Tokyo','Australia/Darwin','Australia/Sydney','Asia/Magadan','Pacific/Auckland','Pacific/Tongatapu') NOT NULL DEFAULT 'Europe/London',
  `description` text NOT NULL,
  PRIMARY KEY (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `userstats`
--

CREATE TABLE IF NOT EXISTS `userstats` (
  `userid` int(11) unsigned NOT NULL,
  `strength` bigint(11) unsigned NOT NULL,
  `agility` bigint(11) unsigned NOT NULL,
  `guard` bigint(11) unsigned NOT NULL,
  `iq` bigint(11) unsigned NOT NULL,
  `labor` bigint(11) unsigned NOT NULL,
  PRIMARY KEY (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
