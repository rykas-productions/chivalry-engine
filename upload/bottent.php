<?php
/*
	File:		bottent.php
	Created: 	4/4/2016 at 11:54PM Eastern Time
	Info: 		A list of the setup bots in game. Players can attack them
				for an item drop once every pre-defined period.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
$macropage=('bottent.php');
require('globals.php');
echo "<h3>Bot Tent</h3><hr />Welcome to the Bot Tent. Here you may challenge NPCs to battle. If you win, you'll receive
    an item. These items may or may not be useful in your adventures. To deter players getting massive amounts of items,
    you can only attack these NPCs every so often. Their cooldown is listed here as well. To receive the item, you must
    mug the bot.<hr />";
$query=$db->query("SELECT * FROM `botlist`");
echo "<table class='table table-bordered'>
<tr>
	<th>
		Bot Name
	</th>
	<th class='hidden-xs'>
		Bot Level
	</th>
	<th class='hidden-xs'>
		Bot Cooldown
	</th>
	<th>
		Bot Item Drop
	</th>
	<th>
		Attack
	</th>
</tr>";
//List all the bots.
while ($result = $db->fetch_row($query))
{
    //Grab the last time the user attacked this bot.
	$timequery=$db->query("SELECT `lasthit` FROM `botlist_hits` WHERE `userid` = {$userid} && `botid` = {$result['botuser']}");
	$r2=$db->fetch_single($timequery);
    //Grab bot's stats.
	$r3 = $db->fetch_row($db->query("SELECT `strength`,`agility`,`guard` FROM `userstats` WHERE `userid` = {$result['botuser']}"));
	$ustats=$ir['strength']+$ir['agility']+$ir['guard'];
	$themstats=$r3['strength']+$r3['agility']+$r3['guard'];
    //Chance the user can beat the bot.
	$chance = round((($ustats / $themstats) * 100)/2,1);
    //Player cannot attack the bot.
	if ((time() <= ($r2 + $result['botcooldown'])) && ($r2 > 0))
	{
		$cooldown=($r2 + $result['botcooldown']) - time();
		$attack="Cooldown Remaining: " . ParseTimestamp($cooldown);
	}
    //Player CAN attack the bot.
	else
	{
		$attack="<form action='attack.php'>
					<input type='hidden' name='user' value='{$result['botuser']}'>
					<input type='submit' class='btn btn-danger' value='Attack " . $api->SystemUserIDtoName($result['botuser']) . "'>
					</form>
					({$lang['BOTTENT_CHANCE']} {$chance}%)";
	}
    //Table row formatting.
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