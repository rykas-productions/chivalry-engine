<?php
function isGuildStaff()
{
	global $gd, $userid;
	if ($gd['guild_owner'] == $userid || $gd['guild_coowner'] == $userid || $gd['guild_app_manager'] == $userid || $gd['guild_vault_manager'] == $userid|| $gd['guild_crime_lord'] == $userid)
		return true;
}

function isGuildLeadership()
{
	global $gd, $userid;
	if (isGuildLeader() || isGuildCoLeader())
		return true;
}

function isGuildAppManager()
{
	global $gd, $userid;
	if (isGuildLeadership() || $gd['guild_app_manager'] == $userid)
		return true;
}

function isGuildCrimeLord()
{
	global $gd, $userid;
	if (isGuildLeadership() || $gd['guild_crime_lord'] == $userid)
		return true;
}

function isGuildVaultManager()
{
	global $gd, $userid;
	if (isGuildLeadership() || $gd['guild_vault_manager'] == $userid)
		return true;
}

function isGuildLeader()
{
	global $gd, $userid;
	if ($gd['guild_owner'] == $userid)
		return true;
}

function isGuildCoLeader()
{
	global $gd, $userid;
	if ($gd['guild_coowner'] == $userid || isGuildLeader())
		return true;
}

function updateDonations($guildid,$userid,$type,$increase)
{
	global $db;
	$q=$db->query("/*qc=on*/SELECT * FROM `guild_donations` WHERE `guildid` = {$guildid} AND `userid` = {$userid}");
	if ($db->num_rows($q) == 0)
	{
		$db->query("INSERT INTO `guild_donations` (`userid`, `guildid`, `copper`, `tokens`, `xp`) VALUES ('{$userid}', '{$guildid}', '0', '0', '0')");
	}
	$db->query("UPDATE `guild_donations` SET `{$type}` = `{$type}` + {$increase} WHERE `userid` = {$userid} AND `guildid` = {$guildid}");
}