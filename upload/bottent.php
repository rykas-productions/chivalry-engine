<?php
/*
	File:		bottent.php
	Created: 	4/4/2016 at 11:54PM Eastern Time
	Info: 		A list of the setup bots in game. Players can attack them
				for an item drop once every pre-defined period.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
$macropage = ('bottent.php');
require('globals.php');
if ($api->UserStatus($userid,'dungeon') || $api->UserStatus($userid,'infirmary'))
{
	alert('danger',"Uh Oh!","You cannot visit the NPC Battle List while in the infirmary or dungeon.",true,'explore.php');
	die($h->endpage());
}
$query = $db->query("/*qc=on*/SELECT * FROM `botlist` ORDER BY `botuser` ASC");
echo "<div class='card'>
    <div class='card-header'>
        <i class='game-icon game-icon-guards'></i> NPC Battle List
    </div>
    <div class='card-body'>
        This is a list of all known {$set['WebsiteName']} Challenge NPCs. Each bot drops an item to help you travels. There is 
        a cooldown for item drops, which is also displayed on this list. The item drop is only received when you mug the NPC.<hr />";
//List all the bots.
while ($result = $db->fetch_row($query)) 
{
    //Grab the last time the user attacked this bot.
    $timequery = $db->query("/*qc=on*/SELECT `lasthit` FROM `botlist_hits` WHERE `userid` = {$userid} && `botid` = {$result['botuser']}");
    $r2 = $db->fetch_single($timequery);
    //Grab bot's stats.
    $r3 = $db->fetch_row($db->query("/*qc=on*/SELECT `strength`,`agility`,`guard` FROM `userstats` WHERE `userid` = {$result['botuser']}"));
    $ustats = $ir['strength'] + $ir['agility'] + $ir['guard'];
    $themstats = $r3['strength'] + $r3['agility'] + $r3['guard'];
    //Chance the user can beat the bot.
    $chance = round((($ustats / $themstats) * 100) / 2, 1);
    $chance = ($chance < 100) ? $chance : 100;
    //Assign bot name to variable to cut down on queries.
    $botname = $api->SystemUserIDtoName($result['botuser']);
    //Player cannot attack the bot.
    if ((time() <= ($r2 + $result['botcooldown'])) && ($r2 > 0)) {
        $cooldown = ($r2 + $result['botcooldown']) - time();
        $attack = "Cooldown Remaining: " . ParseTimestamp($cooldown);
    } //Player CAN attack the bot.
    else {
		$attack = "<a href='attack.php?user={$result['botuser']}&ref=bottent' class='btn btn-danger btn-block' style='font-size: 1.75rem;'>
						<i class='game-icon game-icon-swords-emblem'></i>
					</a><small>Victory Odds: {$chance}%</small>";
    }
	echo "
	<div class='row'>
		<div class='col-12 col-sm col-xl-3'>
			<a href='profile.php?user={$result['botuser']}'>{$botname}</a> [{$result['botuser']}]<br />
            <small>Level: " . $api->UserInfoGet($result['botuser'], 'level') . "</small>
        </div>
		<div class='col-12 col-sm'>
            <div class='row'>
                <div class='col-12 col-xxxl'>
		             Cooldown: " . ParseTimestamp($result['botcooldown']) . "
                </div>
                <div class='col-12 col-xxxl'>
	               Drop: " . $api->SystemItemIDtoName($result['botitem']) . "   
                </div>
            </div>
		</div>
		<div class='col-12 col-lg-4 col-xl-4'>
			{$attack}
		</div>
	</div>
	<hr />";
}
echo "</div></div>";
$h->endpage();