<?php
/*
	File:		bottent.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		A list of the in-game non-playable-characters, allowing the 
				player to attack one every pre-defined period for an item drop.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
	MIT License

	Copyright (c) 2019 TheMasterGeneral

	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in all
	copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
	SOFTWARE.
*/
$macropage = ('bottent.php');
require('globals.php');
echo "<h3>Bot Tent</h3><hr />Welcome to the Bot Tent. Here you may challenge NPCs to battle. If you win, you'll receive
    an item. These items may or may not be useful in your adventures. To deter players getting massive amounts of items,
    you can only attack these NPCs every so often. Their cooldown is listed here as well. To receive the item, you must
    mug the bot.<hr />";
$query = $db->query("SELECT * FROM `botlist`");
echo "<div class='cotainer'>
<div class='row'>
		<div class='col-sm'>
		    <h4>Bot</h4>
		</div>
		<div class='col-sm'>
		    <h4>Cooldown</h4>
		</div>
		<div class='col-sm'>
		    <h4>Drop Item</h4>
		</div>
		<div class='col-sm'>
		    <h4>Attack</h4>
		</div>
</div><hr />";
//List all the bots.
while ($result = $db->fetch_row($query)) {
    //Grab the last time the user attacked this bot.
    $timequery = $db->query("SELECT `lasthit` FROM `botlist_hits` WHERE `userid` = {$userid} && `botid` = {$result['botuser']}");
    $r2 = $db->fetch_single($timequery);
    //Grab bot's stats.
    $r3 = $db->fetch_row($db->query("SELECT `strength`,`agility`,`guard` FROM `userstats` WHERE `userid` = {$result['botuser']}"));
    $ustats = $ir['strength'] + $ir['agility'] + $ir['guard'];
    $themstats = $r3['strength'] + $r3['agility'] + $r3['guard'];
    //Chance the user can beat the bot.
    $chance = round((($ustats / $themstats) * 100) / 2, 1);
    $chance = ($chance < 100) ? $chance : 100;
    //Assign bot name to variable to cut down on queries.
    $botname = $api->user->getNameFromID($result['botuser']);
    //Player cannot attack the bot.
    if ((time() <= ($r2 + $result['botcooldown'])) && ($r2 > 0)) {
        $cooldown = ($r2 + $result['botcooldown']) - time();
        $attack = "Cooldown Remaining: " . timestampParse($cooldown);
    } //Player CAN attack the bot.
    else {
        $attack = "<form action='attack.php'>
					<input type='hidden' name='user' value='{$result['botuser']}'>
					<input type='submit' class='btn btn-danger' value='Attack {$botname}'>
					</form>
					(Odds of Victory {$chance}%)";
    }
    //Table row formatting.
    echo "
	<div class='row'>
		<div class='col-sm'>
			{$botname} [{$result['botuser']}]<br />
			Level " . $api->user->getInfo($result['botuser'], 'level') . "
		</div>
		<div class='col-sm'>
			" . timestampParse($result['botcooldown']) . "
		</div>
		<div class='col-sm'>
			" . $api->game->getItemNameFromID($result['botitem']) . "
		</div>
		<div class='col-sm'>
			{$attack}
		</div>
	</div>
	<hr />";
}
echo "</div>";
$h->endpage();