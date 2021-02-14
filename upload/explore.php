<?php
require('globals.php');
$userUI=getCurrentUserPref('oldUI',0);
if ($userUI == 1)
{
	header("Location: explore2.php");
	exit;
}
$blockAccess = false;
$txtClass='';
if ($blockAccess)
{
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
}
if ($api->UserStatus($userid,'dungeon') || $api->UserStatus($userid,'infirmary'))
	$txtClass='text-muted strike-through';
//Anti-refresh RNG.
$tresder = (Random(100, 999));
$time = time();
$last15 = $time - 900;
//Select users in infirmary and dungeon to list later on the page.
$dung_count = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`dungeon_user`) FROM `dungeon` WHERE `dungeon_out` > {$time}"));
$bounty_count = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`bh_id`) FROM `bounty_hunter`"));
$infirm_count = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`infirmary_user`) FROM `infirmary` WHERE `infirmary_out` > {$time}"));
$market = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`imID`) FROM `itemmarket`"));
$vipMarket = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`vip_id`) FROM `vip_market`"));
$rmarket = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`irID`) FROM `itemrequest`"));
$secmarket = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`sec_id`) FROM `sec_market`"));
$forumposts = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`fp_id`) FROM `forum_posts` WHERE `fp_time` > {$last15}"));
$wars = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`gw_id`) FROM `guild_wars` WHERE `gw_end` > {$time}"));
$users = number_format($db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `users`")));
$userson = number_format($db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `users` WHERE `laston` > {$last15}")));
$userstown = number_format($db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `users` WHERE `location` = {$ir['location']}")));
$paperads = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`news_id`) FROM `newspaper_ads` WHERE `news_end` > {$time}"));
$rr = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`challenger`) FROM `russian_roulette` WHERE `challengee` = {$userid}"));
$bank = ($ir['bank'] > -1) ? number_format($ir['bank']) : "N/A";
$bigbank = ($ir['bigbank'] > -1) ? number_format($ir['bigbank']) : "N/A";
$vaultbank = ($ir['vaultbank'] > -1) ? number_format($ir['vaultbank']) : "N/A";
$tbank = ($ir['tokenbank'] > -1) ? number_format($ir['tokenbank']) : "N/A";
$guildcount = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`guild_id`) FROM `guild`"));
$MUS = ($db->fetch_row($db->query("/*qc=on*/SELECT * FROM `mining` WHERE `userid` = {$userid} LIMIT 1")));
$estates = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`em_id`) FROM `estate_market`"));
$miningenergy = min(round($MUS['miningpower'] / $MUS['max_miningpower'] * 100), 100);
if (empty($dung_count)) {
    $dung_count = 0;
}
if (empty($infirm_count)) {
    $infirm_count = 0;
}
if ($ir['location'] == 1)
	if (Random(1,100) == 56)
		alert('warning',"Mysterious Vibration!","You feel the ground shake underneathe you...",false);
echo"
<h4>You begin exploring {$api->SystemTownIDtoName($ir['location'])}. You find a few things that could keep you occupied.</h4>
<div class='row'>
	<div class='col-12 col-lg-6 col-xl-4'>
		<div class='card'>
			<div class='card-header'>
				<b>Shopping District</b>
			</div>
			<div class='card-body'>
				<div class='row'>
					<div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xl-12 col-xxxl-6'>
						<a href='shops.php' class='{$txtClass}'><i class='game-icon game-icon-shop'></i> Local Shops</a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='itemmarket.php'><i class='game-icon game-icon-trade'></i> Item Market <span class='badge badge-pill badge-primary'>{$market}</span></a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='itemrequest.php'><i class='game-icon game-icon-trade'></i> Item Request <span class='badge badge-pill badge-primary'>{$rmarket}</span></a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='secmarket.php'><i class='game-icon game-icon-cash'></i> Token Market <span class='badge badge-pill badge-primary'>{$secmarket}</span></a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='itemweekshop.php' class='{$txtClass}'>Item of the Week</a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='votestore.php'>Vote Point Store <span class='badge badge-pill badge-primary'>" . number_format($ir['vote_points']) . "</span></a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='vipmarket.php'>VIP Days Market <span class='badge badge-pill badge-primary'>" . number_format($vipMarket) . "</span></a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='estate_management.php?action=estateMarket'>Estate Market <span class='badge badge-pill badge-primary'>" . number_format($estates) . "</span></a>
					</div>";
						$bossq=$db->query("
							SELECT `boss_user`,`location` 
							FROM `activeBosses` `ab`
							LEFT JOIN `users` AS `u`
							ON `u`.`userid` = `ab`.`boss_user`
							WHERE `u`.`location` = {$ir['location']}");
						if ($db->num_rows($bossq) > 0)
						{
							$br=$db->fetch_row($bossq);
							echo "
							<div class='col-12 col-sm-6 col-md-4 col-lg-6'>
								<b><a href='attack.php?user={$br['boss_user']}' class='text-danger'>Slay Boss!</a></b>
							</div>";
						}
					echo"
				</div>
			</div>
		</div>
	</div>
	<div class='col-12 col-lg-6 col-xl-4'>
		<div class='card'>
			<div class='card-header'>
				<b>Financial District</b>
			</div>
			<div class='card-body'>
				<div class='row'>
					<div class='col-12 col-sm-5 col-md-6 col-lg-6 col-xl-12 col-xxxl-6'>
						<a href='job.php' class='{$txtClass}'><i class='game-icon game-icon-push'></i> Work Center</a>
					</div>
					<div class='col-12 col-sm-7 col-md-6 col-lg-12 col-xxxl-6'>
						<a href='bank.php' class='{$txtClass}'><i class='game-icon game-icon-bank'></i> City Bank <span class='badge badge-pill badge-primary'>{$bank}</span></a>
					</div>";
					if ($ir['level'] > 74) 
					{
						echo "
					<div class='col-12 col-md-6 col-lg-12 col-xxxl-6'>
						<a href='bigbank.php' class='{$txtClass}'><i class='game-icon game-icon-bank'></i> Federal Bank <span class='badge badge-pill badge-primary'>{$bigbank}</span></a>
					</div>";
					}
					if ($ir['level'] > 174) 
					{
						echo "
					<div class='col-12 col-md-6 col-lg-12 col-xxxl-6'>
						<a href='vaultbank.php' class='{$txtClass}'><i class='game-icon game-icon-bank'></i> Vault Bank <span class='badge badge-pill badge-primary'>{$vaultbank}</span></a>
					</div>";
					}
					echo "
					<div class='col-12 col-sm-6 col-md-6 col-lg-12 col-xxxl-6'>
						<a href='tokenbank.php' class='{$txtClass}'><i class='game-icon game-icon-chest'></i> Token Bank <span class='badge badge-pill badge-primary'>{$tbank}</span></a>
					</div>
					<div class='col-12 col-sm-6 col-md-6 col-lg-6 col-xl-12 col-xxxl-6'>
						<a href='estate_management.php' class='{$txtClass}'><i class='game-icon game-icon-house'></i> Estate Agent</a>
					</div>
					<div class='col-12 col-sm-6 col-md-6 col-lg-6 col-xl-12 col-xxxl-6'>
						<a href='travel.php' class='{$txtClass}'><i class='game-icon game-icon-horseshoe'></i> Travel Agent</a>
					</div>
					<div class='col-12 col-sm-6 col-md-6 col-lg-12 col-xxxl-6'>
						<a href='temple.php' class='{$txtClass}'><i class='game-icon game-icon-mayan-pyramid'></i> Temple of Fortune</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class='col-12 col-lg-6 col-xl-4'>
		<div class='card'>
			<div class='card-header'>
				<b>Working District</b>
			</div>
			<div class='card-body'>
				<div class='row'>
					<div class='col-12 col-sm-6 col-md-5 col-lg-12 col-xxxl-6'>
						<a href='mine.php' class='{$txtClass}'><i class='game-icon game-icon-mining'></i> Dangerous Mines <span class='badge badge-pill badge-primary'>{$miningenergy}%</span></a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='smelt.php' class='{$txtClass}'><i class='game-icon game-icon-anvil'></i> Blacksmith's Smeltery</a>
					</div>
					<div class='col-12 col-sm-4 col-md-3 col-lg-6 col-xxxl-6'>
						<a href='farm.php' class='{$txtClass}'>Farming</a>
					</div>
					<div class='col-12 col-sm-3 col-md-4 col-lg-6 col-xxxl-6'>
						<a href='gym.php' class='{$txtClass}'><i class='game-icon game-icon-weight-lifting-down'></i> The Gym</a>
					</div>
					<div class='col-12 col-sm-5 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='bottent.php' class='{$txtClass}'><i class='game-icon game-icon-guards'></i> NPC Battle List</a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='chivalry_gym.php' class='{$txtClass}'> <i class='game-icon game-icon-weight-lifting-up'></i> Chivalry Gym</a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='criminal.php' class='{$txtClass}'><i class='game-icon game-icon-robber'></i> Criminal Center</a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='streetbum.php' class='{$txtClass}'> Street Begging <span class='badge badge-pill badge-primary'>" . number_format($ir['searchtown']) . "</span></a>
					</div>";
						if ($ir['autobum'] > 0)
						{
							echo "
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='autobum.php' class='{$txtClass}'> Auto Street Beg <span class='badge badge-pill badge-primary'>" . number_format($ir['autobum']) . "</span></a>
					</div>";
						}
					echo"
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='academy.php' class='{$txtClass}'><i class='game-icon game-icon-diploma'></i> Local Academy</a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='achievements.php'><i class='game-icon game-icon-achievement'></i> Achievements</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class='col-12 col-lg-6 col-xl-4'>
		<div class='card'>
			<div class='card-header'>
				<b>Federal District</b>
			</div>
			<div class='card-body'>
				<div class='row'>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='users.php'><i class='fas fa-users'></i>  Players List <span class='badge badge-pill badge-primary'>{$users}</span></a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='usersonline.php'><i class='fas fa-toggle-on'></i> Players Online <span class='badge badge-pill badge-primary'>{$userson}</span></a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='userstown.php'><i class='game-icon game-icon-village'></i> Players In Town <span class='badge badge-pill badge-primary'>{$userstown}</span></a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-6'>
						<a href='staff.php'><i class='game-icon game-icon-embrassed-energy'></i> CID Staff</a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='fedjail.php'><i class='game-icon game-icon-closed-doors'></i> Federal Dungeon</a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='stats.php'><i class='fas fa-chart-bar'></i> Game Statistics</a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='playerreport.php'><i class='far fa-flag'></i> Player Report</a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='announcements.php'><i class='fas fa-bullhorn'></i> Announcements <span class='badge badge-pill badge-primary'>" . number_format($ir['announcements']) . "</span></a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='itemappendix.php'><i class='fas fa-list'></i> Item Appendix</a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='milestones.php'>Milestones</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class='col-12 col-lg-6 col-xl-4'>
		<div class='card'>
			<div class='card-header'>
				<b>Gambling District</b>
			</div>
			<div class='card-body'>
				<div class='row'>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='russianroulette.php' class='{$txtClass}'><i class='game-icon game-icon-revolver'></i> Russian Roulette <span class='badge badge-pill badge-primary'>{$rr}</span></a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-6'>
						<a href='roulette.php?tresde={$tresder}' class='{$txtClass}'><i class='game-icon game-icon-table'></i> Roulette</a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-6'>
						<a href='slots.php?tresde={$tresder}' class='{$txtClass}'><i class='game-icon game-icon-pokecog spinner'></i> Slots</a>
					</div>";
					if ($ir['level'] > 49)
					{
						echo"
					<div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xl-12 col-xxxl-6'>
						<a href='bigslots.php?tresde={$tresder}' class='{$txtClass}'><i class='game-icon game-icon-pokecog'></i> Federal Slots</a>
					</div>";
					}
					echo"
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='hexbags.php' class='{$txtClass}'><i class='game-icon game-icon-open-treasure-chest'></i> Hexbags <span class='badge badge-pill badge-primary'>" . number_format($ir['hexbags']) . "</span></a>
					</div>";
					if ($ir['autohex'] > 0)
					{
						echo"
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='autohex.php' class='{$txtClass}'><i class='game-icon game-icon-open-treasure-chest'></i> Auto Hexbags <span class='badge badge-pill badge-primary'>" . number_format($ir['autohex']) . "</span></a>
					</div>";
					}
					echo"
					<div class='col-12 col-sm-6 col-lg-12 col-xxxl-6'>
						<a href='raffle.php' class='{$txtClass}'><i class='fas fa-ticket-alt'></i> CID Raffle <span class='badge badge-pill badge-primary'>" . number_format($set['lotterycash']) . "</span></a>
					</div>
				</div>
			</div>
		</div>
			<br />
	</div>
	<div class='col-12 col-lg-6 col-xl-4'>
		<div class='card'>
			<div class='card-header'>
				<b>Dangerous District</b>
			</div>
			<div class='card-body'>
				<div class='row'>";
					if ($ir['guild'] > 0) {
						echo "
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='viewguild.php'><i class='game-icon game-icon-minions'></i> Visit Your Guild</a>
					</div>";
					}
					echo"
					<div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xl-12 col-xxxl-6'>
						<a href='guilds.php'><i class='game-icon game-icon-dozen'></i> Guilds <span class='badge badge-pill badge-primary'>{$guildcount}</span></a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xl-12 col-xxxl-6'>
						<a href='guild_district.php'> Guild Districts</a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='guilds.php?action=wars'><i class='game-icon game-icon-mounted-knight'></i> Guild Wars</a> <span class='badge badge-pill badge-danger'>{$wars}</span>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='bounty.php' class='{$txtClass}'><i class='game-icon game-icon-game-icon game-icon-shadow-grasp'></i> Bounty Hunter <span class='badge badge-pill badge-primary'>{$bounty_count}</span></a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-6'>
						<a href='missions.php' class='{$txtClass}'><i class='game-icon game-icon-game-icon game-icon-stabbed-note'></i> Missions</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class='col-12 col-lg-6 col-xl-4'>
		<div class='card'>
			<div class='card-header'>
				<b>Social District</b>
			</div>
			<div class='card-body'>
				<div class='row'>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='dungeon.php'><i class='game-icon game-icon-cage'></i> Dungeon <span class='badge badge-pill badge-primary'>{$dung_count}</span></a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='infirmary.php'><i class='game-icon game-icon-hospital-cross'></i> Infirmary <span class='badge badge-pill badge-primary'>{$infirm_count}</span></a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xl-12 col-xxxl-6'>
						<a href='forums.php'><i class='far fa-comments'></i> Forums <span class='badge badge-pill badge-primary'>{$forumposts}</span></a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xl-12 col-xxxl-6'>
						<a href='/chat/?userName={$ir['username']}'><i class='fas fa-comment-dots'></i> CID Chat</a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='newspaper.php'><i class='game-icon game-icon-scroll-unfurled'></i> Newspaper <span class='badge badge-pill badge-primary'>{$paperads}</span></a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='polling.php'><i class='game-icon game-icon-vote'></i> Polling Center</a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='halloffame.php'><i class='game-icon game-icon-crown'></i> Hall of Fame</a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='marriage.php'><i class='game-icon game-icon-linked-rings'></i> Marriage Center</a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='tutorial.php'><i class='far fa-question-circle'></i> CID Tutorial</a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='referallist.php'><i class='game-icon game-icon-minions'></i> Your Referrals</a>
					</div>
				</div>
			</div>
		</div>
	</div>";
	if ($ir['vip_days'] > 0)
	{
		echo "
	<div class='col-12 col-lg-6 col-xl-4'>
		<div class='card'>
			<div class='card-header'>
				<b>VIP District</b>
			</div>
			<div class='card-body'>
				<div class='row'>
					<div class='col-12 col-sm-4 col-lg-6 col-xxxl-4'>
						<a href='friends.php'><i class='far fa-fw fa-smile'></i> Friends</a>
					</div>
					<div class='col-12 col-sm-4 col-lg-6 col-xxxl-4'>
						<a href='enemy.php'><i class='far fa-fw fa-frown'></i> Enemies</a>
					</div>
					<div class='col-12 col-sm-4 col-lg-6 col-xxxl-4'>
						<a href='userlogs.php'><i class='fas fa-book fa-fw'></i> VIP Logs</a>
					</div>
				</div>
			</div>
		</div>
	</div>";
	}
	echo"
	</div>";
//referral link.
echo "	<div class='row'>
			<div class='col-md-12'>
				Share your referral link to gain 10 Chivalry Gym Scrolls every time a friend joins!<br />
				<code>https://www.chivalryisdeadgame.com/register.php?REF={$userid}</code>
			</div>
		</div>";
$h->endpage();