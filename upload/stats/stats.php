<?php
/*
	File: stats/stats.php
	Created: 6/1/2016 at 6:06PM Eastern Time
	Info: Fetches information from the database and displays it on the statistics page
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine
*/

//Select count of players with and without a bank account
$NotOwnedBank = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `users` WHERE `bank` = '-1' AND `user_level` != 'NPC'"));
$OwnedBank = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `users` WHERE `bank` > '-1' AND `user_level` != 'NPC'"));

//Select count of players's gender
$Male = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `users` WHERE `gender` = 'Male' AND `user_level` != 'NPC'"));
$Female = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `users` WHERE `gender` = 'Female' AND `user_level` != 'NPC'"));

//Select count of players's class
$Warrior = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `users` WHERE `class` = 'Warrior' AND `user_level` != 'NPC'"));
$Rogue = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `users` WHERE `class` = 'Rogue' AND `user_level` != 'NPC'"));
$Defender = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `users` WHERE `class` = 'Defender' AND `user_level` != 'NPC'"));

//Select the Total Primary Currency in the game.
$TotalPrimaryCurrency = $db->fetch_single($db->query("SELECT SUM(`primary_currency`) FROM `users` WHERE `user_level` != 'NPC'"));

//Select the total for primary currency in bank.
$TotalBank = $db->fetch_single($db->query("SELECT SUM(`bank`) FROM `users` WHERE `user_level` != 'NPC' AND `bank` > -1"));

//All the primary currency
$TotalBankandPC = $TotalBank + $TotalPrimaryCurrency;

//Select the Total Secondary Currency in the game.
$TotalSecondaryCurrency = $db->fetch_single($db->query("SELECT SUM(`secondary_currency`) FROM `users` WHERE `user_level` != 'NPC'"));

//Select total count of register users.
$TotalUserCount = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `users` WHERE `user_level` != 'NPC'"));

//Figure out average primary currency per player by dividing the total primary currency by the total amount of users
//then round up.
$AveragePrimaryCurrencyPerPlayer = round($TotalPrimaryCurrency / $TotalUserCount);
$AverageBank = round($TotalBank / $TotalUserCount);

//Figure out average secondary currency per player by dividing the total secondary currency by the total amount of users
//then round up.
$AverageSecondaryCurrencyPerPlayer = round($TotalSecondaryCurrency / $TotalUserCount);

//Select the total amount of guilds in the game.
$TotalGuildCount = $db->fetch_single($db->query("SELECT COUNT(`guild_id`) FROM `guild`"));
