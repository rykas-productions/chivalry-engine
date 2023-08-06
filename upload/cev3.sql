-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 06, 2023 at 07:43 PM
-- Server version: 10.3.38-MariaDB-0+deb10u1
-- PHP Version: 7.3.27-1~deb10u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cev3`
--

-- --------------------------------------------------------

--
-- Table structure for table `game_announcements`
--

CREATE TABLE `game_announcements` (
  `annId` int(11) UNSIGNED NOT NULL,
  `annTime` int(11) UNSIGNED NOT NULL,
  `annUser` varchar(512) NOT NULL,
  `annText` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `game_settings`
--

CREATE TABLE `game_settings` (
  `setting_id` int(11) UNSIGNED NOT NULL,
  `setting_name` text NOT NULL,
  `setting_value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `game_settings`
--

INSERT INTO `game_settings` (`setting_id`, `setting_name`, `setting_value`) VALUES
(1, 'gym_config', '{\"moduleID\":\"gym\",\"moduleAuthor\":\"TheMasterGeneral\",\"moduleURL\":\"https://github.com/rykas-productions/chivalry-engine\",\"moduleVersion\":1,\"statMultiplier\":1,\"itemRequired\":0,\"vipDaysRequired\":false}'),
(2, 'bank_config', '{\"moduleID\":\"bank\",\"moduleAuthor\":\"TheMasterGeneral\",\"moduleURL\":\"https://github.com/rykas-productions/chivalry-engine\",\"moduleVersion\":1,\"bankOpeningFee\":5000,\"bankWithdrawPercent\":5,\"bankWithdrawMaxFee\":1000}'),
(3, 'slots_config', '{\"moduleID\":\"slots\",\"moduleAuthor\":\"TheMasterGeneral\",\"moduleURL\":\"https://github.com/rykas-productions/chivalry-engine\",\"moduleVersion\":1,\"maxBetPerLevel\":750,\"maxBetHardCap\":100000,\"threeSlotWinningMultipler\":75,\"twoSlotWinningMultipler\":35}'),
(4, 'chiv-eng:game_name', 'Chivalry Engine v3');

-- --------------------------------------------------------

--
-- Table structure for table `mail`
--

CREATE TABLE `mail` (
  `mailID` int(10) UNSIGNED NOT NULL,
  `mailFrom` varchar(512) NOT NULL,
  `mailTo` varchar(512) NOT NULL,
  `mailTime` int(11) NOT NULL,
  `mailSubject` text NOT NULL,
  `mailText` text NOT NULL,
  `mailReadTime` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users_account_data`
--

CREATE TABLE `users_account_data` (
  `userid` varchar(512) NOT NULL,
  `loginTime` int(11) NOT NULL,
  `registrationTime` int(11) NOT NULL,
  `lastActionTime` int(11) NOT NULL,
  `profilePicture` text NOT NULL,
  `staffLevel` tinyint(4) NOT NULL DEFAULT 1,
  `loginIP` text NOT NULL,
  `registrationIP` text NOT NULL,
  `lastActionIP` text NOT NULL,
  `unreadAnnouncements` int(11) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


--
-- Table structure for table `users_core`
--

CREATE TABLE `users_core` (
  `userid` varchar(512) DEFAULT NULL COMMENT 'Account ID',
  `username` text NOT NULL COMMENT 'Account name',
  `email` text NOT NULL COMMENT 'Account Email',
  `password` text NOT NULL COMMENT 'Account password'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users_infirmary`
--

CREATE TABLE `users_infirmary` (
  `infirmaryUserid` varchar(512) NOT NULL,
  `infirmaryOut` int(11) NOT NULL,
  `infirmaryReason` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users_ips`
--

CREATE TABLE `users_ips` (
  `userLogID` int(11) UNSIGNED NOT NULL,
  `userID` varchar(512) NOT NULL,
  `userIP` text NOT NULL,
  `userLastUsed` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users_stats`
--

CREATE TABLE `users_stats` (
  `userid` varchar(512) NOT NULL,
  `level` int(11) UNSIGNED NOT NULL DEFAULT 1,
  `experience` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `strength` int(11) UNSIGNED NOT NULL DEFAULT 10,
  `agility` int(11) UNSIGNED NOT NULL DEFAULT 10,
  `guard` int(11) UNSIGNED NOT NULL DEFAULT 10,
  `labor` int(11) UNSIGNED NOT NULL DEFAULT 10,
  `iq` int(11) UNSIGNED NOT NULL DEFAULT 10,
  `energy` int(11) UNSIGNED NOT NULL DEFAULT 10,
  `maxEnergy` int(11) UNSIGNED NOT NULL DEFAULT 10,
  `will` int(11) UNSIGNED NOT NULL DEFAULT 100,
  `maxWill` int(11) UNSIGNED NOT NULL DEFAULT 100,
  `brave` int(11) UNSIGNED NOT NULL DEFAULT 5,
  `maxBrave` int(11) UNSIGNED NOT NULL DEFAULT 5,
  `hp` int(11) UNSIGNED NOT NULL DEFAULT 100,
  `maxHP` int(11) UNSIGNED NOT NULL DEFAULT 100,
  `primaryCurrencyHeld` int(11) UNSIGNED NOT NULL DEFAULT 100,
  `primaryCurrencyBank` int(11) NOT NULL DEFAULT -1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `game_announcements`
--
ALTER TABLE `game_announcements`
  ADD UNIQUE KEY `annId` (`annId`) USING BTREE;

--
-- Indexes for table `game_settings`
--
ALTER TABLE `game_settings`
  ADD UNIQUE KEY `setting_id` (`setting_id`);

--
-- Indexes for table `mail`
--
ALTER TABLE `mail`
  ADD UNIQUE KEY `mailID` (`mailID`) USING BTREE;

--
-- Indexes for table `users_account_data`
--
ALTER TABLE `users_account_data`
  ADD UNIQUE KEY `userid` (`userid`) USING BTREE;

--
-- Indexes for table `users_core`
--
ALTER TABLE `users_core`
  ADD UNIQUE KEY `userid` (`userid`);

--
-- Indexes for table `users_infirmary`
--
ALTER TABLE `users_infirmary`
  ADD UNIQUE KEY `infirmaryUserid` (`infirmaryUserid`) USING BTREE;

--
-- Indexes for table `users_ips`
--
ALTER TABLE `users_ips`
  ADD UNIQUE KEY `userLogID` (`userLogID`) USING BTREE;

--
-- Indexes for table `users_stats`
--
ALTER TABLE `users_stats`
  ADD UNIQUE KEY `userid` (`userid`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `game_announcements`
--
ALTER TABLE `game_announcements`
  MODIFY `annId` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `game_settings`
--
ALTER TABLE `game_settings`
  MODIFY `setting_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `mail`
--
ALTER TABLE `mail`
  MODIFY `mailID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users_ips`
--
ALTER TABLE `users_ips`
  MODIFY `userLogID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
