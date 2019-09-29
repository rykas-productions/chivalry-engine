-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 29, 2019 at 07:46 PM
-- Server version: 10.3.15-MariaDB-1
-- PHP Version: 7.3.4-2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
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
-- Table structure for table `users_account_data`
--

CREATE TABLE `users_account_data` (
  `userid` int(10) UNSIGNED NOT NULL,
  `loginTime` int(11) NOT NULL,
  `registrationTime` int(11) NOT NULL,
  `lastActionTime` int(11) NOT NULL,
  `profilePicture` text NOT NULL,
  `level` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `staffLevel` tinyint(4) NOT NULL DEFAULT 1,
  `experience` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `loginIP` text NOT NULL,
  `registrationIP` text NOT NULL,
  `lastActionIP` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `users_core`
--

CREATE TABLE `users_core` (
  `userid` int(11) UNSIGNED NOT NULL COMMENT 'Account ID',
  `username` text NOT NULL COMMENT 'Account name',
  `email` text NOT NULL COMMENT 'Account Email',
  `password` text NOT NULL COMMENT 'Account password'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users_account_data`
--
ALTER TABLE `users_account_data`
  ADD PRIMARY KEY (`userid`);

--
-- Indexes for table `users_core`
--
ALTER TABLE `users_core`
  ADD UNIQUE KEY `userid` (`userid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users_core`
--
ALTER TABLE `users_core`
  MODIFY `userid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Account ID';
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
