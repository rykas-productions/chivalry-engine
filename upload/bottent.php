<?php
/*
	File:		bottent.php
	Created: 	4/4/2016 at 11:54PM Eastern Time
	Info: 		A list of the setup bots in game. Players can attack them
				for an item drop once every pre-defined period.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require('globals.php');
echo "<h3>{$lang['BOTTENT_TITLE']}</h3><hr />{$lang['BOTTENT_DESC']}<hr />";
$query=$db->query("SELECT * FROM `botlist`");
echo "<table class='table table-bordered'>
<tr>
	<th>
		{$lang['BOTTENT_TH']}
	</th>
	<th class='hidden-xs'>
		{$lang['BOTTENT_TH1']}
	</th>
	<th class='hidden-xs'>
		{$lang['BOTTENT_TH2']}
	</th>
	<th>
		{$lang['BOTTENT_TH3']}
	</th>
	<th>
		{$lang['BOTTENT_TH4']}
	</th>
</tr>";
while ($result = $db->fetch_row($query))
{
	$timequery=$db->query("SELECT `lasthit` FROM `botlist_hits` WHERE `userid` = {$userid} && `botid` = {$result['botuser']}");
	$r2=$db->fetch_single($timequery);
	$r3 = $db->fetch_row($db->query("SELECT `strength`,`agility`,`guard` FROM `userstats` WHERE `userid` = {$result['botuser']}"));
	$ustats=$ir['strength']+$ir['agility']+$ir['guard'];
	$themstats=$r3['strength']+$r3['agility']+$r3['guard'];
	$chance = round((($ustats / $themstats) * 100)/2,1);
	if ((time() <= ($r2 + $result['botcooldown'])) && ($r2 > 0))
	{
		$cooldown=($r2 + $result['botcooldown']) - time();
		$attack="{$lang['BOTTENT_WAIT']} " . ParseTimestamp($cooldown);
	}
	else
	{
		$attack="<form action='attack.php'>
					<input type='hidden' name='user' value='{$result['botuser']}'>
					<input type='submit' class='btn btn-danger' value='Attack " . $api->SystemUserIDtoName($result['botuser']) . "'>
					</form>
					({$lang['BOTTENT_CHANCE']} {$chance}%)";
	}
	echo "
	<tr>
		<td>
			" . $api->SystemUserIDtoName($result['botuser']) . " [{$result['botuser']}]
		</td>
		<td class='hidden-xs'>
			" . $api->UserInfoGet($result['botuser'],'level') . "
		</td>
		<td class='hidden-xs'>
			" . ParseTimestamp($result['botcooldown']) . "
		</td>
		<td>
			" . $api->SystemItemIDtoName($result['botitem']) . "
		</td>
		<td>
			{$attack}
		</td>
	</tr>";
}
echo"</table>";
$h->endpage();