<?php
/*
	File: stats/stats.php
	Created: 6/1/2016 at 6:06PM Eastern Time
	Info: Fetches information from the database and displays it on the statistics page
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine
*/

//Select count of players with and without a bank account
$NotOwnedBank = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `users` WHERE `bank` = '-1' AND `user_level` != 'NPC' AND `userid` != 1"));
$OwnedBank = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `users` WHERE `bank` > '-1' AND `user_level` != 'NPC'  AND `userid` != 1"));

//Select count of players's gender
$Male = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `users` WHERE `gender` = 'Male' AND `user_level` != 'NPC'  AND `userid` != 1"));
$Female = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `users` WHERE `gender` = 'Female' AND `user_level` != 'NPC'  AND `userid` != 1"));

//Select count of players's class
$Warrior = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `users` WHERE `class` = 'Warrior' AND `user_level` != 'NPC'  AND `userid` != 1"));
$Rogue = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `users` WHERE `class` = 'Rogue' AND `user_level` != 'NPC'  AND `userid` != 1"));
$Defender = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `users` WHERE `class` = 'Guardian' AND `user_level` != 'NPC'  AND `userid` != 1"));

//Select the Total Primary Currency in the game.
$TotalPrimaryCurrency = $db->fetch_single($db->query("SELECT SUM(`primary_currency`) FROM `users` WHERE `user_level` != 'NPC'  AND `userid` != 1"));
$TotalGuildPC = $db->fetch_single($db->query("SELECT SUM(`guild_primcurr`) FROM `guild` WHERE `guild_id` != 1"));
$TotalGuildSC = $db->fetch_single($db->query("SELECT SUM(`guild_seccurr`) FROM `guild` WHERE `guild_id` != 1"));
$TotalInvestmentPC = $db->fetch_single($db->query("SELECT SUM(`amount`) FROM `bank_investments` WHERE `userid` != 1"));

//Select the Total Secondary Currency in the game.
$TotalSecondaryCurrency = $db->fetch_single($db->query("SELECT SUM(`secondary_currency`) FROM `users` WHERE `user_level` != 'NPC'  AND `userid` != 1"));

//Select the total for primary currency in bank.
$TotalBank = $db->fetch_single($db->query("SELECT SUM(`bank`) FROM `users` WHERE `user_level` != 'NPC' AND `bank` > -1  AND `userid` != 1"));
$TotalBankToken = $db->fetch_single($db->query("SELECT SUM(`tokenbank`) FROM `users` WHERE `user_level` != 'NPC' AND `tokenbank` > -1  AND `userid` != 1"));
$TotalBigBank = $db->fetch_single($db->query("SELECT SUM(`bigbank`) FROM `users` WHERE `user_level` != 'NPC' AND `bigbank` > -1  AND `userid` != 1"));


//All the primary currency
$TotalBankandPC = $TotalBank + $TotalPrimaryCurrency + $TotalInvestmentPC + $TotalGuildPC + $TotalBigBank;
$TotalBankandSC = $TotalBankToken + $TotalSecondaryCurrency + $TotalGuildSC;

//Select total count of register users.
$TotalUserCount = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `users` WHERE `user_level` != 'NPC'  AND `userid` != 1"));

//Figure out average primary currency per player by dividing the total primary currency by the total amount of users
//then round up.
$AveragePrimaryCurrencyPerPlayer = round($TotalPrimaryCurrency / $TotalUserCount);
$AverageTokenBank = round($TotalBankToken / $TotalUserCount);
$AverageBank = round($TotalBank / $TotalUserCount);
$AverageBigBank = round($TotalBigBank / $TotalUserCount);

//Figure out average secondary currency per player by dividing the total secondary currency by the total amount of users
//then round up.
$AverageSecondaryCurrencyPerPlayer = round($TotalSecondaryCurrency / $TotalUserCount);

//Select the total amount of guilds in the game.
$TotalGuildCount = $db->fetch_single($db->query("SELECT COUNT(`guild_id`) FROM `guild` WHERE `guild_id` != 1"));

$TotalNotif = $db->fetch_single($db->query("SELECT COUNT(`notif_id`) FROM `notifications`"));
$TotalMail = $db->fetch_single($db->query("SELECT COUNT(`mail_id`) FROM `mail`"));

//Operating System
$Win7 = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `userdata` WHERE `os` = 'Windows 7'"));
$Win8 = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `userdata` WHERE `os` = 'Windows 8'"));
$Win81 = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `userdata` WHERE `os` = 'Windows 8.1'"));
$Win10 = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `userdata` WHERE `os` = 'Windows 10'"));
$WinXP = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `userdata` WHERE `os` = 'Windows XP'"));
$WinV = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `userdata` WHERE `os` = 'Windows Vista'"));
$OSX = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `userdata` WHERE `os` = 'Mac OS X'"));
$OS9 = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `userdata` WHERE `os` = 'Mac OS 9'"));
$Linux = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `userdata` WHERE `os` = 'Linux'"));
$Ubuntu = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `userdata` WHERE `os` = 'Ubuntu'"));
$iPhone = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `userdata` WHERE `os` = 'iPhone'"));
$iPod = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `userdata` WHERE `os` = 'iPod'"));
$iPad = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `userdata` WHERE `os` = 'iPad'"));
$Android = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `userdata` WHERE `os` = 'Android'"));
$Blackberry = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `userdata` WHERE `os` = 'Blackberry'"));
$Mobile = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `userdata` WHERE `os` = 'Mobile'"));
$WinPho = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `userdata` WHERE `os` = 'Windows Phone'"));
$UnknownOS = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `userdata` WHERE `os` = 'Unknown OS Platform'"));
$ChromeOS = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `userdata` WHERE `os` = 'Chrome OS'"));

//Browser Choice
$Chrome = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `userdata` WHERE `browser` = 'Chrome'"));
$IE = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `userdata` WHERE `browser` = 'Internet Explorer'"));
$FF = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `userdata` WHERE `browser` = 'Firefox'"));
$Safari = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `userdata` WHERE `browser` = 'Safari'"));
$Edge = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `userdata` WHERE `browser` = 'Edge'"));
$Opera = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `userdata` WHERE `browser` = 'Opera'"));
$NS = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `userdata` WHERE `browser` = 'Netscape'"));
$Maxthon = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `userdata` WHERE `browser` = 'Maxthon'"));
$Konqueror = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `userdata` WHERE `browser` = 'Konquerer'"));
$MobileBro = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `userdata` WHERE `browser` = 'Handheld Browser'"));
$App = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `userdata` WHERE `browser` = 'App'"));
$UnknownBro = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `userdata` WHERE `browser` = 'Unknown Browser'"));
