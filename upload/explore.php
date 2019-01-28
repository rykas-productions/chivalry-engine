<?php
/*
	File:		explore.php
	Created: 	4/5/2016 at 12:00AM Eastern Time
	Info: 		Gateway to many things around the game.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require("globals.php");
//Anti-refresh RNG.
$tresder = (Random(100, 999));
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
if ($api->UserStatus($ir['userid'], 'infirmary')) {
    alert('danger', "Unconscious!", "You cannot visit the town while you're in the infirmary.", false);
    die($h->endpage());
}
//Block access if user is in the dungeon.
if ($api->UserStatus($ir['userid'], 'dungeon')) {
    alert('danger', "Locked Up!", "You cannot visit the town while you're in the dungeon.");
    die($h->endpage());
}
echo "<h4>You begin exploring {$api->SystemTownIDtoName($ir['location'])}. You find a few things that could keep you occupied.</h4>
<div class='row'>
		<div class='col-sm'>
			<u><b>Shopping District</b></u><br />
			<a href='shops.php'>Local Shops</a><br />
			<a href='itemmarket.php'>Item Market</a><br />
            <a href='secmarket.php'>Secondary Currency Market</a><br />
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
				<a href='announcements.php'>Announcements <span class='badge badge-pill badge-primary'>{$ir['announcements']}</span></a>
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
				Share your referral link to gain 25 Secondary Currency every time a friend joins!<br />
				<code>{$domain}/register.php?REF={$userid}</code>
			</div>
		</div>";
$h->endpage();