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
<div class='col-md-4' align='left'>
	<ul class='nav flex-column nav-pills'>
		<li class='nav-item'>
			<a class='nav-link' data-toggle='tab' href='#SHOPS'>
				<i class='fa fa-shopping-cart'></i> 
				Shopping District
			</a>
		</li>
		<li class='nav-item'>
			<a class='nav-link' data-toggle='tab' href='#FD'>
				<i class='far fa-gem'></i> 
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
				<i class='fas fa-balance-scale'></i> 
				Administration District
			</a>
		</li>
		<li class='nav-item'>
			<a class='nav-link' data-toggle='tab' href='#GAMES'>
				<i class='fas fa-money-bill-alt'></i> 
				Gambling District
			</a>
		</li>
		<li class='nav-item'>
			<a class='nav-link' data-toggle='tab' href='#GUILDS'>
				<i class='fas fa-users'></i> 
				Guilds District
			</a>
		</li>
		<li class='nav-item'>
			<a class='nav-link' data-toggle='tab' href='#PINTER'>
				<i class='fas fa-comments'></i> 
				Social District
			</a>
		</li>
	</ul>
</div>
<div class='col-md-4'>
	<div class='tab-content'>
		<div id='SHOPS' class='tab-pane'>
			<div class='card' align='left'>
				<div class='card-body'>
					<a href='shops.php'><i class='game-icon game-icon-shop'></i> Local Shops</a><br />
					<a href='itemmarket.php'><i class='game-icon game-icon-trade'></i> Item Market <span class='badge badge-pill badge-primary'>{$market}</span></a><br />
					<a href='secmarket.php'><i class='game-icon game-icon-cash'></i> Chivalry Tokens Market <span class='badge badge-pill badge-primary'>{$secmarket}</span></a><br />
				</div>
			</div>
		</div>
		<div id='FD' class='tab-pane'>
			<div class='card' align='left'>
				<div class='card-body'>
				    <a href='job.php'><i class='game-icon game-icon-push'></i> Work Center</a><br />
					<a href='bank.php'><i class='game-icon game-icon-bank'></i> City Bank</a><br />
					<a href='tokenbank.php'><i class='game-icon game-icon-chest'></i> Chivalry Token Bank</a><br />
					<a href='estates.php'><i class='game-icon game-icon-house'></i> Estate Agent</a><br />
					<a href='travel.php'><i class='game-icon game-icon-horseshoe'></i> Travel Agent</a><br />
					<a href='temple.php'><i class='game-icon game-icon-mayan-pyramid'></i> Temple of Fortune</a><br />
				</div>
			</div>
		</div>
		<div id='HL' class='tab-pane'>
			<div class='card' align='left'>
				<div class='card-body'>
					<a href='mine.php'><i class='game-icon game-icon-mining'></i> Dangerous Mines</a><br />
					<a href='smelt.php'><i class='game-icon game-icon-anvil'></i> Blacksmith's Smeltery</a><br />
					<a href='bottent.php'><i class='game-icon game-icon-guards'></i> NPC Battle List</a><br />
					<a href='gym.php'><i class='game-icon game-icon-weight-lifting-down'></i> The Gym</a><br />
					<a href='chivalry_gym.php'><i class='game-icon game-icon-weight-lifting-up'></i> Chivalry Gym</a><br />
					<a href='criminal.php'><i class='game-icon game-icon-robber'></i> Criminal Center</a><br />
					<a href='academy.php'><i class='game-icon game-icon-diploma'></i> Local Academy</a><br />
					<a href='achievements.php'><i class='game-icon game-icon-achievement'></i> Achievements</a><br />
				</div>
			</div>
		</div>
		<div id='ADMIN' class='tab-pane'>
			<div class='card' align='left'>
				<div class='card-body'>
					<a href='users.php'><i class='fas fa-users'></i>  Players List <span class='badge badge-pill badge-primary'>{$users}</span></a><br />
					<a href='usersonline.php'><i class='fas fa-toggle-on'></i> Players Online <span class='badge badge-pill badge-primary'>{$userson}</span></a><br />
					<a href='userstown.php'><i class='game-icon game-icon-village'></i> Players In Town <span class='badge badge-pill badge-primary'>{$userstown}</span></a><br />
					<a href='staff.php'><i class='game-icon game-icon-embrassed-energy'></i> CID Staff</a><br />
					<a href='fedjail.php'><i class='game-icon game-icon-closed-doors'></i> Federal Dungeon</a><br />
					<a href='stats.php'><i class='fas fa-chart-bar'></i> Game Statistics</a><br />
					<a href='playerreport.php'><i class='far fa-flag'></i> Player Report</a><br />
					<a href='announcements.php'><i class='fas fa-bullhorn'></i> Announcements <span class='badge badge-pill badge-primary'>{$ir['announcements']}</span></a><br />
					<a href='itemappendix.php'><i class='fas fa-list'></i> Item Appendix</a><br />
				</div>
			</div>
		</div>
		<div id='GAMES' class='tab-pane'>
			<div class='card' align='left'>
				<div class='card-body'>
					<a href='russianroulette.php'><i class='game-icon game-icon-revolver'></i> Russian Roulette</a><br />
					<a href='roulette.php?tresde={$tresder}'><i class='game-icon game-icon-table'></i> Roulette Table</a><br />
					<a href='slots.php?tresde={$tresder}'><i class='game-icon game-icon-pokecog'></i> Slot Machines</a><br />
					<a href='hexbags.php?tresde={$tresder}'><i class='game-icon game-icon-open-treasure-chest'></i> Hexbags <span class='badge badge-pill badge-primary'>{$ir['hexbags']}</span></a><br />";
					if ($ir['autohex'] > 0)
						echo "<a href='autohex.php'><i class='game-icon game-icon-open-treasure-chest'></i> Auto Hexbags <span class='badge badge-pill badge-primary'>{$ir['autohex']}</span></a><br />";
					echo"
					<a href='raffle.php'><i class='fas fa-ticket-alt'></i> CID Raffle</a><br />
				</div>
			</div>
		</div>
		<div id='GUILDS' class='tab-pane'>
			<div class='card' align='left'>
				<div class='card-body'>";
					//User is in a guild.
					if ($ir['guild'] > 0) {
						echo "<a href='viewguild.php'><i class='game-icon game-icon-minions'></i> Visit Your Guild</a><br />";
					}
					echo "
					<a href='guilds.php'><i class='game-icon game-icon-dozen'></i> Guild Listing</a><br />
					<a href='guilds.php?action=wars'><i class='game-icon game-icon-mounted-knight'></i> Guild Wars</a> <span class='badge badge-pill badge-danger'>{$wars}</span><br />
				</div>
			</div>
		</div>
		<div id='PINTER' class='tab-pane'>
			<div class='card' align='left'>
				<div class='card-body'>
				    <a href='dungeon.php'><i class='game-icon game-icon-cage'></i> Dungeon <span class='badge badge-pill badge-primary'>{$dung_count}</span></a><br />
					<a href='infirmary.php'><i class='game-icon game-icon-hospital-cross'></i> Infirmary <span class='badge badge-pill badge-primary'>{$infirm_count}</span></a><br />
					<a href='forums.php'><i class='far fa-comment-alt'></i> CID Forums <span class='badge badge-pill badge-primary'>{$forumposts}</span></a><br />
					<a href='newspaper.php'><i class='game-icon game-icon-scroll-unfurled'></i> CID Newspaper</a><br />
					<a href='polling.php'><i class='game-icon game-icon-vote'></i> Polling Center</a><br />
					<a href='halloffame.php'><i class='game-icon game-icon-crown'></i> Hall of Fame</a><br />
					<a href='marriage.php'><i class='game-icon game-icon-lovers'></i> Marriage Center</a><br />
					<a href='tutorial.php'><i class='far fa-question-circle'></i> CID Tutorial</a><br />
					<a href='chat.php?tresde={$tresder}' target='_blank'><i class='far fa-comment'></i> CID Chat <span class='badge badge-pill badge-primary'>{$chat}</span></a><br />
					<a href='referallist.php'><i class='game-icon game-icon-minions'></i> Your Referrals</a><br />
				</div>
			</div>
		</div>
	</div>
</div>
<div class='col-md-4'>
	<div class='card'>
		<div class='card-header'>
			<i class='game-icon game-icon-podium'></i> Top 10 Players
		</div>
		<div class='card-body' align='left'>";
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