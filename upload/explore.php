<?php
/*
	File:		explore.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		The gateway to many things around your game.
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
require("globals.php");
//Anti-refresh RNG.
$tresder = (randomNumber(100, 999));
$time = time();
//Select users in infirmary and dungeon to list later on the page.
$dung_count = $db->fetch_single($db->query("SELECT COUNT(`dungeon_user`) FROM `dungeon` WHERE `dungeon_out` > {$time}"));
$infirm_count = $db->fetch_single($db->query("SELECT COUNT(`infirmary_user`) FROM `infirmary` WHERE `infirmary_out` > {$time}"));
if (empty($dung_count)) {
    $dung_count = 0;
}
if (empty($infirm_count)) {
    $infirm_count = 0;
}
//Block access if user is in the infirmary.
if ($api->user->inInfirmary($userid)) {
    alert('danger', "Unconscious!", "You cannot visit the town while you're in the infirmary.", false);
    die($h->endpage());
}
//Block access if user is in the dungeon.
if ($api->user->inDungeon($userid)) {
    alert('danger', "Locked Up!", "You cannot visit the town while you're in the dungeon.");
    die($h->endpage());
}
echo "<h4>You begin exploring {$api->game->getTownNameFromID($ir['location'])}. You find a few things that could keep you occupied.</h4>
<div class='row'>
		<div class='col-sm'>
			<u><b>Shopping District</b></u><br />
			<a href='shops.php'>Local Shops</a><br />
			<a href='itemmarket.php'>Item Market</a><br />
            <a href='secmarket.php'>{$_CONFIG['secondary_currency']} Market</a><br />
		</div>
		<div class='col-sm'>
			<u><b>Financial District</b></u><br />
                <a href='job.php'>Work Center</a><br />
                <a href='bank.php'>City Bank</a><br />
                <a href='estates.php'>Estate Agent</a><br />
                <a href='travel.php'>Travel Agent</a><br />
                <a href='temple.php'>Temple of Fortune</a><br />
		</div>
		<div class='col-sm'>
			<u><b>Working District</b></u><br />
			<a href='mine.php'>Dangerous Mines</a><br />
            <a href='smelt.php'>Blacksmith's Smeltery</a><br />
            <a href='bottent.php'>NPC Battle List</a><br />
		</div>
	</div>
	<div class='row'>
		<div class='col-sm'>
			<u><b>Administration District</b></u><br />
				<a href='users.php'>Player List</a><br />
				<a href='usersonline.php'>Players Online</a><br />
				<a href='staff.php'>{$set['WebsiteName']} Staff</a><br />
				<a href='fedjail.php'>Federal Dungeon</a><br />
				<a href='stats.php'>Game Statistics</a><br />
				<a href='playerreport.php'>Player Report</a><br />
				<a href='announcements.php'>Announcements </a>
		</div>
		<div class='col-sm'>
			<u><b>Gambling District</b></u><br />
				<a href='russianroulette.php'>Russian Roulette</a><br />
                <a href='hilow.php?tresde={$tresder}'>High/Low</a><br />
				<a href='roulette.php?tresde={$tresder}'>Roulette Table</a><br />
                <a href='slots.php?tresde={$tresder}'>Slot Machines</a>
			</div>
		<div class='col-sm'>
			<u><b>Danger District</b></u><br />";
					//User is in a guild.
					if ($ir['guild'] > 0) {
						echo "<a href='viewguild.php'>Visit Your Guild</a><br />";
					}
					echo "
					<a href='guilds.php'>Guild Listing</a><br />
					<a href='guilds.php?action=wars'>Guild Wars</a>
		</div>
	</div>
	<div class='row'>
		<div class='col-sm'>
			<u><b>Social District</b></u><br />
			<a href='polling.php'>Polling Center</a><br />
			<a href='halloffame.php'>Hall of Fame</a><br />
			<a href='tutorial.php'>{$set['WebsiteName']} Tutorial</a>
		</div>
	</div><hr />";
//referral link.
echo "	<div class='row'>
			<div class='col-md-12'>
				Share your referral link to gain 25 {$_CONFIG['secondary_currency']} every time a friend joins!<br />
				<code>{$domain}/register.php?REF={$userid}</code>
			</div>
		</div>";
$h->endpage();