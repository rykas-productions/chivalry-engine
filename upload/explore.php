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
$last15=$time-900;
//Select users in infirmary and dungeon to list later on the page.
$dung_count = $db->fetch_single($db->query("SELECT COUNT(`dungeon_user`) FROM `dungeon` WHERE `dungeon_out` > {$time}"));
$infirm_count = $db->fetch_single($db->query("SELECT COUNT(`infirmary_user`) FROM `infirmary` WHERE `infirmary_out` > {$time}"));
$market = $db->fetch_single($db->query("SELECT COUNT(`imID`) FROM `itemmarket`"));
$secmarket = $db->fetch_single($db->query("SELECT COUNT(`sec_id`) FROM `sec_market`"));
$forumposts = $db->fetch_single($db->query("SELECT COUNT(`fp_id`) FROM `forum_posts` WHERE `fp_time` > {$last15}"));
$chat = $db->fetch_single($db->query("SELECT COUNT(`chat_id`) FROM `chat` WHERE `chat_time` > {$last15}"));
$wars = $db->fetch_single($db->query("SELECT COUNT(`gw_id`) FROM `guild_wars` WHERE `gw_end` > {$time}"));
$users = number_format($db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `users`")));
$userson = number_format($db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `users` WHERE `laston` > {$last15}")));
$userstown = number_format($db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `users` WHERE `location` = {$ir['location']}")));
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
			<a class='nav-link' data-toggle='tab' href='#SHOPS'>
				<i class='fa fa-shopping-cart'></i> 
				Shopping District
			</a>
		</li>
		<li class='nav-item'>
			<a class='nav-link' data-toggle='tab' href='#FD'>
				<i class='fa fa-diamond'></i> 
				Financial District
			</a>
		</li>
		<li class='nav-item'>
			<a class='nav-link' data-toggle='tab' href='#HL'>
				<i class='fa fa-briefcase'></i> 
				Working District
			</a>
		</li>
		<li class='nav-item'>
			<a class='nav-link' data-toggle='tab' href='#ADMIN'>
				<i class='fa fa-legal'></i> 
				Administration District
			</a>
		</li>
		<li class='nav-item'>
			<a class='nav-link' data-toggle='tab' href='#GAMES'>
				<i class='fa fa-money'></i> 
				Gambling District
			</a>
		</li>
		<li class='nav-item'>
			<a class='nav-link' data-toggle='tab' href='#GUILDS'>
				<i class='fa fa-group'></i> 
				Guilds District
			</a>
		</li>
		<li class='nav-item'>
			<a class='nav-link' data-toggle='tab' href='#PINTER'>
				<i class='fa fa-comments-o'></i> 
				Social District
			</a>
		</li>
	</ul>
</div>
<div class='col-md-4'>
	<div class='tab-content'>
		<div id='SHOPS' class='tab-pane'>
			<div class='card'>
				<div class='card-body'>
					<a href='shops.php'>Local Shops</a><br />
					<a href='itemmarket.php'>Item Market <span class='badge badge-pill badge-primary'>{$market}</span></a><br />
					<a href='secmarket.php'>Chivalry Tokens Market <span class='badge badge-pill badge-primary'>{$secmarket}</span></a><br />
				</div>
			</div>
		</div>
		<div id='FD' class='tab-pane'>
			<div class='card'>
				<div class='card-body'>
				    <a href='job.php'>Work Center</a><br />
					<a href='bank.php'>City Bank</a><br />
					<a href='tokenbank.php'>Chivalry Token Bank</a><br />
					<a href='estates.php'>Estate Agent</a><br />
					<a href='travel.php'><i class='ra ra-horseshoe'></i> Travel Agent</a><br />
					<a href='temple.php'>Temple of Fortune</a><br />
				</div>
			</div>
		</div>
		<div id='HL' class='tab-pane'>
			<div class='card'>
				<div class='card-body'>
					<a href='mine.php'><i class='ra ra-mining-diamonds'></i> Dangerous Mines</a><br />
					<a href='smelt.php'><i class='ra ra-forging'></i> Blacksmith's Smeltery</a><br />
					<a href='bottent.php'>NPC Battle List</a><br />
					<a href='gym.php'>The Gym</a><br />
					<a href='chivalry_gym.php'>The Chivalry Gym</a><br />
					<a href='criminal.php'>Criminal Center</a><br />
					<a href='academy.php'>Learning Academy</a><br />
					<a href='achievements.php'>Achievements</a><br />
				</div>
			</div>
		</div>
		<div id='ADMIN' class='tab-pane'>
			<div class='card'>
				<div class='card-body'>
					<a href='users.php'>Players List <span class='badge badge-pill badge-primary'>{$users}</span></a><br />
					<a href='usersonline.php'>Players Online <span class='badge badge-pill badge-primary'>{$userson}</span></a><br />
					<a href='userstown.php'>Players In Town <span class='badge badge-pill badge-primary'>{$userstown}</span></a><br />
					<a href='staff.php'>CID Staff</a><br />
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
					<a href='roulette.php?tresde={$tresder}'>Roulette Table</a><br />
					<a href='slots.php?tresde={$tresder}'>Slot Machines</a><br />
					<a href='hexbags.php?tresde={$tresder}'>Hexbags <span class='badge badge-pill badge-primary'>{$ir['hexbags']}</span></a><br />";
					if ($ir['autohex'] > 0)
						echo "<a href='autohex.php'>Auto Hexbags <span class='badge badge-pill badge-primary'>{$ir['autohex']}</span></a><br />";
					echo"
					<a href='raffle.php'>CID Raffle</a><br />
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
					<a href='guilds.php?action=wars'>Guild Wars</a> <span class='badge badge-pill badge-danger'>{$wars}</span><br />
				</div>
			</div>
		</div>
		<div id='PINTER' class='tab-pane'>
			<div class='card'>
				<div class='card-body'>
				    <a href='dungeon.php'>Dungeon <span class='badge badge-pill badge-primary'>{$dung_count}</span></a><br />
					<a href='infirmary.php'>Infirmary <span class='badge badge-pill badge-primary'>{$infirm_count}</span></a><br />
					<a href='forums.php'>CID Forums <span class='badge badge-pill badge-primary'>{$forumposts}</span></a><br />
					<a href='newspaper.php'><i class='ra ra-scroll-unfurled'></i> CID Newspaper</a><br />
					<a href='polling.php'>Polling Center</a><br />
					<a href='halloffame.php'>Hall of Fame</a><br />
					<a href='marriage.php'>Marriage Center</a><br />
					<a href='tutorial.php'>CID Tutorial</a><br />
					<a href='chat.php?tresde={$tresder}' target='_blank'>CID Chat <span class='badge badge-pill badge-primary'>{$chat}</span></a><br />
					<a href='referallist.php'>Your Referrals</a><br />
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
				Share your referral link to gain {$set['ReferalKickback']} Chivalry Tokens every time a friend joins!<br />
				<code>chivalryisdeadgame.com/register.php?REF={$userid}</code><br />
				[<a href='http://fb.me/officialcidgame'>CID Facebook Page</a>] [<a href='https://twitter.com/cidgame'>CID Twitter Page</a>]
			</div>
		</div>";
$h->endpage();