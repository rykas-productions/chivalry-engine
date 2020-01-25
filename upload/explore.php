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
echo "<h4>You begin exploring the town. You find a few things that could keep you occupied.</h4></div>
<div class='col-md-4'>
	<ul class='nav flex-column nav-pills'>
		<li class='nav-item'>
			<a class='nav-link' data-toggle='tab' href='#SHOPS'>Shopping District</a>
		</li>
		<li class='nav-item'>
			<a class='nav-link' data-toggle='tab' href='#FD'>Financial District</a>
		</li>
		<li class='nav-item'>
			<a class='nav-link' data-toggle='tab' href='#HL'>Working District</a>
		</li>
		<li class='nav-item'>
			<a class='nav-link' data-toggle='tab' href='#ADMIN'>Administration District</a>
		</li>
		<li class='nav-item'>
			<a class='nav-link' data-toggle='tab' href='#GAMES'>Gambling District</a>
		</li>
		<li class='nav-item'>
			<a class='nav-link' data-toggle='tab' href='#GUILDS'>Guilds District</a>
		</li>
		<li class='nav-item'>
			<a class='nav-link' data-toggle='tab' href='#PINTER'>Social District</a>
		</li>
	</ul>
</div>
<div class='col-md-4'>
	<div class='tab-content'>
		<div id='SHOPS' class='tab-pane'>
			<div class='card'>
				<div class='card-body'>
					<a href='shops.php'>Local Shops</a><br />
					<a href='itemmarket.php'>Item Market</a><br />
					<a href='secmarket.php'>Secondary Currency Market</a><br />
				</div>
			</div>
		</div>
		<div id='FD' class='tab-pane'>
			<div class='card'>
				<div class='card-body'>
				    <a href='job.php'>Work Center</a><br />
					<a href='bank.php'>City Bank</a><br />
					<a href='estates.php'>Estate Agent</a><br />
					<a href='travel.php'>Travel Agent</a><br />
					<a href='temple.php'>Temple of Fortune</a><br />
				</div>
			</div>
		</div>
		<div id='HL' class='tab-pane'>
			<div class='card'>
				<div class='card-body'>
					<a href='mine.php'>Dangerous Mines</a><br />
					<a href='smelt.php'>Blacksmith's Smeltery</a><br />
					<a href='bottent.php'>NPC Battle List</a><br />
					<a href='gym.php'>The Gym</a><br />
					<a href='criminal.php'>Criminal Center</a><br />
					<a href='academy.php'>Learning Academy</a><br />
				</div>
			</div>
		</div>
		<div id='ADMIN' class='tab-pane'>
			<div class='card'>
				<div class='card-body'>
					<a href='users.php'>User List</a><br />
					<a href='usersonline.php'>Users Online List</a><br />
					<a href='staff.php'>{$set['WebsiteName']} Staff</a><br />
					<a href='fedjail.php'>Federal Dungeon</a><br />
					<a href='stats.php'>Game Statistics</a><br />
					<a href='playerreport.php'>Player Report</a><br />
					<a href='announcements.php'>Announcements <span class='badge badge-pill badge-primary'>{$ir['announcements']}</span></a><br />
					<a href='itemappendix.php'>Item Appendix</a><br />
				</div>
			</div>
		</div>
		<div id='GAMES' class='tab-pane'>
			<div class='card'>
				<div class='card-body'>
					<a href='russianroulette.php'>Russian Roulette</a><br />
					<a href='hilow.php?tresde={$tresder}'>High/Low</a><br />
					<a href='roulette.php?tresde={$tresder}'>Roulette Table</a><br />
					<a href='slots.php?tresde={$tresder}'>Slot Machines</a><br />
				</div>
			</div>
		</div>
		<div id='GUILDS' class='tab-pane'>
			<div class='card'>
				<div class='card-body'>";
//User is in a guild.
if ($ir['guild'] > 0) {
    echo "<a href='viewguild.php'>Visit Your Guild</a><br />";
}
echo "
					<a href='guilds.php'>Guild Listing</a><br />
					<a href='guilds.php?action=wars'>Guild Wars</a><br />
				</div>
			</div>
		</div>
		<div id='PINTER' class='tab-pane'>
			<div class='card'>
				<div class='card-body'>
				    <a href='dungeon.php'>Dungeon <span class='badge badge-pill badge-primary'>{$dung_count}</span></a><br />
					<a href='infirmary.php'>Infirmary <span class='badge badge-pill badge-primary'>{$infirm_count}</span></a><br />
					<a href='forums.php'>{$set['WebsiteName']} Forums</a><br />
					<a href='newspaper.php'>{$set['WebsiteName']} Newspaper</a><br />
					<a href='polling.php'>Polling Center</a><br />
					<a href='halloffame.php'>Hall of Fame</a><br />
					<a href='tutorial.php'>{$set['WebsiteName']} Tutorial</a><br />
				</div>
			</div>
		</div>
	</div>
</div>
<div class='col-md-4'>
	<div class='card'>
		<div class='card-header'>
			Top 10 Players
		</div>
		<div class='card-body'>";
$Rank = 0;
$RankPlayerQuery =
    $db->query("SELECT u.`userid`, `level`, `username`, `strength`, `agility`, `guard`, `labor`, `IQ`
			FROM `users` AS `u`
			INNER JOIN `userstats` AS `us`
			ON `u`.`userid` = `us`.`userid`
			WHERE `u`.`user_level` != 'Admin' AND `u`.`user_level` != 'NPC'
			ORDER BY (`strength` + `agility` + `guard` + `labor` + `IQ`) 
			DESC, `u`.`userid` ASC LIMIT 10");
//Show the top 10 strongest players in the game.
while ($pdata = $db->fetch_row($RankPlayerQuery)) {
    $Rank = $Rank + 1;
    echo "{$Rank}) <a href='profile.php?user={$pdata['userid']}'>{$pdata['username']}</a> (Level {$pdata['level']})<br />";
}
echo "
		</div>
	</div>
</div>
</div>";
//referral link.
echo "	<div class='row'>
			<div class='col-md-12'>
				Share your referral link to gain 25 Secondary Currency every time a friend joins!<br />
				<code>{$domain}/register.php?REF={$userid}</code>
			</div>
		</div>";
$h->endpage();