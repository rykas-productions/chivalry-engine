<?php
function doDailyDistrictTick()
{
	global $db, $api;
	$districtConfig['WarriorCostDaily'] = 500;
	$districtConfig['ArcherCostDaily'] = 1000;
	$districtConfig['GeneralCostDaily'] = 12500;
	$db->query("UPDATE `guild_district_info` SET `warriors_bought` = 0, `archers_bought` = 0, `moves` = 2");
	$q=$db->query("SELECT * FROM `guild_district_info`");
	while ($r=$db->fetch_row($q))
	{
		$upkeepFee=0;
		$warriors = countDeployedWarriors($r['guild_id']);
		$archers = countDeployedArchers($r['guild_id']);
		$generals = countDeployedGenerals($r['guild_id']);
		if ($warriors > 0)
			$upkeepFee=$upkeepFee + ($warriors * $districtConfig['WarriorCostDaily']);
		if ($archers > 0)
			$upkeepFee=$upkeepFee + ($archers * $districtConfig['ArcherCostDaily']);
		if ($generals > 0)
			$upkeepFee=$upkeepFee + ($generals * $districtConfig['GeneralCostDaily']);
		if ($upkeepFee > 0)
		{
			$db->query("UPDATE `guild` SET `guild_primcurr` = `guild_primcurr` - {$upkeepFee} WHERE `guild_id` = {$r['guild_id']}");
			addToEconomyLog('Guild Upkeep', 'copper', $upkeepFee*-1);
			$api->GuildAddNotification($r['guild_id'],"Your guild has been charged a district's upkeep fee of " . number_format($upkeepFee) . " Copper Coins.");
		}
	}
	districtRewards();
}
function countActiveGuildMembers24Hr($guild_id)
{
	global $db;
	$last_on = time() - (1440*60);
	$q = $db->query("/*qc=on*/SELECT `username` FROM `users` WHERE `guild` = {$guild_id} AND `laston` > {$last_on}");
	return $db->num_rows($q);
}
function countDeployedWarriors($guild_id)
{
	global $db;
	$q=$db->query("SELECT SUM(`district_melee`) FROM `guild_districts` WHERE `district_owner` = {$guild_id}");
	return $db->fetch_single($q);
}
function countDeployedArchers($guild_id)
{
	global $db;
	$q=$db->query("SELECT SUM(`district_range`) FROM `guild_districts` WHERE `district_owner` = {$guild_id}");
	return $db->fetch_single($q);
}
function countDeployedGenerals($guild_id)
{
	global $db;
	$q=$db->query("SELECT SUM(`district_general`) FROM `guild_districts` WHERE `district_owner` = {$guild_id}");
	return $db->fetch_single($q);
}
function districtRewards()
{
	districtRewardMostControlledTiles();
	districtRewardMostDeployedUnits();
}

function districtRewardMostDeployedUnits()
{
	global $db, $api;
	$winnerguild = 0;
	$currentmax = 0;
	$q = $db->query("SELECT `guild_id` FROM `guild` WHERE `guild_id` != 1 AND `guild_id` != 16");
	while ($r = $db->fetch_row($q))
	{
		$currentGuildID = $r['guild_id'];
		$currentGuild = 0;
		$q2 = $db->query("SELECT `district_melee`, `district_range` FROM `guild_districts` WHERE `district_owner` = {$r['guild_id']}");
		while ($r2 = $db->fetch_row($q2))
		{
			$currentGuild = $currentGuild + ($r2['district_melee'] + $r2['district_range']);
		}
		if ($currentGuild > $currentmax)
		{
			$currentmax = $currentGuild;
			$winnerguild = $currentGuildID;
		}
	}
	$api->GameAddNotification(1,"{$winnerguild} with {$currentmax}.");
	$api->GuildAddItem($winnerguild,205,2);
	$api->GuildAddNotification($winnerguild, "Your guild has the most deployed units on the guild districts and received two {$api->SystemItemIDtoName(205)} to your armory.");
}

function districtRewardMostControlledTiles()
{
	global $db, $api;
	$winnerguild = 0;
	$currentmax = 0;
	$q = $db->query("SELECT `guild_id` FROM `guild` WHERE `guild_id` != 1 AND `guild_id` != 16");
	while ($r = $db->fetch_row($q))
	{
		$currentGuildID = $r['guild_id'];
		$currentGuild = 0;
		$q2 = $db->query("SELECT `district_owner` FROM `guild_districts` WHERE `district_owner` = {$r['guild_id']}");
		while ($r2 = $db->fetch_row($q2))
		{
			$currentGuild = $currentGuild + 1;
		}
		if ($currentGuild > $currentmax)
		{
			$currentmax = $currentGuild;
			$winnerguild = $currentGuildID;
		}
	}
	$api->GameAddNotification(1,"{$winnerguild} with {$currentmax}.");
	$api->GuildAddItem($winnerguild,205,2);
	$api->GuildAddNotification($winnerguild, "Your guild has the most controlled tiles on the guild districts and received two {$api->SystemItemIDtoName(205)} to your armory.");
}