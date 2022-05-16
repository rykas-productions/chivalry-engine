<?php
/*
	File: stats/stats.php
	Created: 6/1/2016 at 6:06PM Eastern Time
	Info: Fetches information from the database and displays it on the statistics page
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine
*/
//Select count of players's gender
$Male = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `users` WHERE `gender` = 'Male' AND `user_level` != 'NPC'  AND `userid` != 1"));
$Female = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `users` WHERE `gender` = 'Female' AND `user_level` != 'NPC'  AND `userid` != 1"));
$OtherGender = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `users` WHERE `gender` = 'Other' AND `user_level` != 'NPC'  AND `userid` != 1"));

//Select count of players's class
$Warrior = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `users` WHERE `class` = 'Warrior' AND `user_level` != 'NPC'  AND `userid` != 1"));
$Rogue = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `users` WHERE `class` = 'Rogue' AND `user_level` != 'NPC'  AND `userid` != 1"));
$Defender = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `users` WHERE `class` = 'Guardian' AND `user_level` != 'NPC'  AND `userid` != 1"));

//Select the Total Primary Currency in the game.
$TotalPrimaryCurrency = $db->fetch_single($db->query("/*qc=on*/SELECT SUM(`primary_currency`) FROM `users` WHERE `user_level` != 'NPC'  AND `userid` != 1"));
$TotalGuildPC = $db->fetch_single($db->query("/*qc=on*/SELECT SUM(`guild_primcurr`) FROM `guild` WHERE `guild_id` != 20"));
$TotalGuildSC = $db->fetch_single($db->query("/*qc=on*/SELECT SUM(`guild_seccurr`) FROM `guild` WHERE `guild_id` != 20"));
$TotalInvestmentPC = $db->fetch_single($db->query("/*qc=on*/SELECT SUM(`amount`) FROM `bank_investments` WHERE `userid` != 1"));

//Select the Total Secondary Currency in the game.
$tsc1 = $db->fetch_single($db->query("/*qc=on*/SELECT SUM(`secondary_currency`) FROM `users` WHERE `user_level` != 'NPC'  AND `userid` != 1"));
$tsc2 = $db->fetch_single($db->query("/*qc=on*/SELECT SUM(`tokenbank`) FROM `users` WHERE `user_level` != 'NPC'  AND `userid` != 1"));
$TotalSecondaryCurrency=$tsc1+$tsc2;

//Select the total for primary currency in bank.
$TotalBank = $db->fetch_single($db->query("/*qc=on*/SELECT SUM(`bank`) FROM `users` WHERE `user_level` != 'NPC' AND `bank` > -1  AND `userid` != 1"));
$TotalBankToken = $db->fetch_single($db->query("/*qc=on*/SELECT SUM(`tokenbank`) FROM `users` WHERE `user_level` != 'NPC' AND `tokenbank` > -1  AND `userid` != 1"));
$TotalBigBank = $db->fetch_single($db->query("/*qc=on*/SELECT SUM(`bigbank`) FROM `users` WHERE `user_level` != 'NPC' AND `bigbank` > -1  AND `userid` != 1"));
$TotalVaultBank = $db->fetch_single($db->query("/*qc=on*/SELECT SUM(`vaultbank`) FROM `users` WHERE `user_level` != 'NPC' AND `bigbank` > -1  AND `userid` != 1"));
$TotalEstateVault = $db->fetch_single($db->query("/*qc=on*/SELECT SUM(`vault`) FROM `user_estates`"));

//Select total count of register users.
$TotalUserCount = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `users` WHERE `user_level` != 'NPC'  AND `userid` != 1"));
//Figure out average primary currency per player by dividing the total primary currency by the total amount of users
//then round up.
$AveragePrimaryCurrencyPerPlayer = round($TotalPrimaryCurrency / $TotalUserCount);
$AverageTokenBank = round($TotalBankToken / $TotalUserCount);
$AverageBank = round($TotalBank / $TotalUserCount);
$AverageBigBank = round($TotalBigBank / $TotalUserCount);
$AverageVaultBank = round($TotalVaultBank / $TotalUserCount);

//Figure out average secondary currency per player by dividing the total secondary currency by the total amount of users
//then round up.
$AverageSecondaryCurrencyPerPlayer = round($TotalSecondaryCurrency / $TotalUserCount);

//Select the total amount of guilds in the game.
$TotalGuildCount = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`guild_id`) FROM `guild` WHERE `guild_id` != 1"));

$TotalNotif = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`notif_id`) FROM `notifications`"));
$TotalMail = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`mail_id`) FROM `mail`"));

//All the primary currency
$TotalBankandPC = $TotalBank + $TotalPrimaryCurrency + $TotalInvestmentPC + $TotalGuildPC + $TotalBigBank + $TotalVaultBank + $TotalEstateVault;
$TotalBankandSC = $TotalBankToken + $TotalSecondaryCurrency + $TotalGuildSC;

//Avg token price
$totalcost=$db->fetch_single($db->query("SELECT SUM(`token_total`) FROM `token_market_avg`"));
$totaltokens=$db->fetch_single($db->query("SELECT SUM(`token_sold`) FROM `token_market_avg`"));
$avgprice = $totalcost / $totaltokens;

//total estates owned
$TotalEstatesOwned = $db->fetch_single($db->query("SELECT COUNT(`ue_id`) FROM `user_estates` WHERE `estate` > 1"));
