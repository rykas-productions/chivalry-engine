<?php
/*
	File: stats/stats.php
	Created: 6/1/2016 at 6:06PM Eastern Time
	Info: Fetches information from the database and displays it on the statistics page
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine
*/
if (!isset($_GET['active']))
{
	//Select count of players with and without a bank account
	$NotOwnedBank = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `users` WHERE `bank` = '-1' AND `user_level` != 'NPC' AND `userid` != 1"));
	$OwnedBank = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `users` WHERE `bank` > '-1' AND `user_level` != 'NPC'  AND `userid` != 1"));

	//Select count of players's gender
	$Male = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `users` WHERE `gender` = 'Male' AND `user_level` != 'NPC'  AND `userid` != 1"));
	$Female = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `users` WHERE `gender` = 'Female' AND `user_level` != 'NPC'  AND `userid` != 1"));

	//Select count of players's class
	$Warrior = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `users` WHERE `class` = 'Warrior' AND `user_level` != 'NPC'  AND `userid` != 1"));
	$Rogue = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `users` WHERE `class` = 'Rogue' AND `user_level` != 'NPC'  AND `userid` != 1"));
	$Defender = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `users` WHERE `class` = 'Guardian' AND `user_level` != 'NPC'  AND `userid` != 1"));

	//Select the Total Primary Currency in the game.
	$TotalPrimaryCurrency = $db->fetch_single($db->query("/*qc=on*/SELECT SUM(`primary_currency`) FROM `users` WHERE `user_level` != 'NPC'  AND `userid` != 1"));
	$TotalGuildPC = $db->fetch_single($db->query("/*qc=on*/SELECT SUM(`guild_primcurr`) FROM `guild` WHERE `guild_id` != 1"));
	$TotalGuildSC = $db->fetch_single($db->query("/*qc=on*/SELECT SUM(`guild_seccurr`) FROM `guild` WHERE `guild_id` != 1"));
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

	//Select total count of register users.
	$TotalUserCount = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `users` WHERE `user_level` != 'NPC'  AND `userid` != 1"));

}
else
{
	$last_on = time() - (86400*7);
	//Select count of players with and without a bank account
	$NotOwnedBank = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `users` WHERE `bank` = '-1' AND `user_level` != 'NPC' AND `userid` != 1 AND `laston` > {$last_on}"));
	$OwnedBank = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `users` WHERE `bank` > '-1' AND `user_level` != 'NPC'  AND `userid` != 1 AND `laston` > {$last_on}"));

	//Select count of players's gender
	$Male = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `users` WHERE `gender` = 'Male' AND `user_level` != 'NPC'  AND `userid` != 1 AND `laston` > {$last_on}"));
	$Female = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `users` WHERE `gender` = 'Female' AND `user_level` != 'NPC'  AND `userid` != 1 AND `laston` > {$last_on}"));

	//Select count of players's class
	$Warrior = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `users` WHERE `class` = 'Warrior' AND `user_level` != 'NPC'  AND `userid` != 1 AND `laston` > {$last_on}"));
	$Rogue = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `users` WHERE `class` = 'Rogue' AND `user_level` != 'NPC'  AND `userid` != 1 AND `laston` > {$last_on}"));
	$Defender = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `users` WHERE `class` = 'Guardian' AND `user_level` != 'NPC'  AND `userid` != 1 AND `laston` > {$last_on}"));

	//Select the Total Primary Currency in the game.
	$TotalPrimaryCurrency = $db->fetch_single($db->query("/*qc=on*/SELECT SUM(`primary_currency`) FROM `users` WHERE `user_level` != 'NPC'  AND `userid` != 1 AND `laston` > {$last_on}"));
	$TotalGuildPC = $db->fetch_single($db->query("/*qc=on*/SELECT SUM(`guild_primcurr`) FROM `guild` WHERE `guild_id` != 1"));
	$TotalGuildSC = $db->fetch_single($db->query("/*qc=on*/SELECT SUM(`guild_seccurr`) FROM `guild` WHERE `guild_id` != 1"));
	$TotalInvestmentPC = $db->fetch_single($db->query("/*qc=on*/SELECT SUM(`amount`) FROM `bank_investments` WHERE `userid` != 1"));

	//Select the Total Secondary Currency in the game.
	$TotalSecondaryCurrency = $db->fetch_single($db->query("/*qc=on*/SELECT SUM(`secondary_currency`) FROM `users` WHERE `user_level` != 'NPC'  AND `userid` != 1 AND `laston` > {$last_on}"));

	//Select the total for primary currency in bank.
	$TotalBank = $db->fetch_single($db->query("/*qc=on*/SELECT SUM(`bank`) FROM `users` WHERE `user_level` != 'NPC' AND `bank` > -1  AND `userid` != 1 AND `laston` > {$last_on}"));
	$TotalBankToken = $db->fetch_single($db->query("/*qc=on*/SELECT SUM(`tokenbank`) FROM `users` WHERE `user_level` != 'NPC' AND `tokenbank` > -1  AND `userid` != 1 AND `laston` > {$last_on}"));
	$TotalBigBank = $db->fetch_single($db->query("/*qc=on*/SELECT SUM(`bigbank`) FROM `users` WHERE `user_level` != 'NPC' AND `bigbank` > -1  AND `userid` != 1 AND `laston` > {$last_on}"));
	$TotalVaultBank = $db->fetch_single($db->query("/*qc=on*/SELECT SUM(`vaultbank`) FROM `users` WHERE `user_level` != 'NPC' AND `bigbank` > -1  AND `userid` != 1 AND `laston` > {$last_on}"));

	//Select total count of register users.
	$TotalUserCount = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `users` WHERE `user_level` != 'NPC'  AND `userid` != 1 AND `laston` > {$last_on}"));
}
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
$TotalGuildCount = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`guild_id`) FROM `guild` WHERE `guild_id` != 1"));

$TotalNotif = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`notif_id`) FROM `notifications`"));
$TotalMail = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`mail_id`) FROM `mail`"));

//Theme Choice
$Default = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `user_settings` WHERE `theme` = 1"));
$Darkly = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `user_settings` WHERE `theme` = 2"));
$Slate = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `user_settings` WHERE `theme` = 3"));
$Cyborg = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `user_settings` WHERE `theme` = 4"));
$United = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `user_settings` WHERE `theme` = 5"));
$Cerulean = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `user_settings` WHERE `theme` = 6"));
$Castle = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `user_settings` WHERE `theme` = 7"));
$Sunset = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `user_settings` WHERE `theme` = 8"));

//All the primary currency
$TotalBankandPC = $TotalBank + $TotalPrimaryCurrency + $TotalInvestmentPC + $TotalGuildPC + $TotalBigBank + $TotalVaultBank;
$TotalBankandSC = $TotalBankToken + $TotalSecondaryCurrency + $TotalGuildSC;