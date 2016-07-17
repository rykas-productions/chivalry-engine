<?php
/*
	File: stats/stats.php
	Created: 6/1/2016 at 6:06PM Eastern Time
	Info: Fetches information from the database and displays it on the statistics page
	Author: TheMasterGeneral
	Website: http://mastergeneral156.pcriot.com/
*/

//Select count of players with and without a bank account
$NotOwnedBank=$db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `users` WHERE `bank` = '-1'"));
$OwnedBank=$db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `users` WHERE `bank` > '-1'"));

//Select count of players's gender
$Male=$db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `users` WHERE `gender` = 'Male'"));
$Female=$db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `users` WHERE `gender` = 'Female'"));

//Select count of players's class
$Warrior=$db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `users` WHERE `class` = 'Warrior'"));
$Rogue=$db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `users` WHERE `class` = 'Rogue'"));
$Defender=$db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `users` WHERE `class` = 'Defender'"));

//Select the Total Primary Currency in the game.
$TotalPrimaryCurrency=$db->fetch_single($db->query("SELECT SUM(`primary_currency`) FROM `users`"));

//Select the Total Secondary Currency in the game.
$TotalSecondaryCurrency=$db->fetch_single($db->query("SELECT SUM(`secondary_currency`) FROM `users`"));

//Select total count of register users.
$TotalUserCount=$db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `users`"));

//Figure out average primary currency per player by dividing the total primary currency by the total amount of users
//then round up.
$AveragePrimaryCurrencyPerPlayer=round($TotalPrimaryCurrency / $TotalUserCount);

//Figure out average secondary currency per player by dividing the total secondary currency by the total amount of users
//then round up.
$AverageSecondaryCurrencyPerPlayer=round($TotalSecondaryCurrency / $TotalUserCount);

//Select the total amount of guilds in the game.
$TotalGuildCount=$db->fetch_single($db->query("SELECT COUNT(`guild_id`) FROM `guild`"));