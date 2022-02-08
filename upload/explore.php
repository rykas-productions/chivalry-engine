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
if (isset($_POST['sc_shortcut'])) {
    $sc = (isset($_POST['sc_shortcut'])) ? $db->escape(strip_tags(stripslashes($_POST['sc_shortcut']))) : '';
    $name = (isset($_POST['sc_name'])) ? $db->escape(strip_tags(stripslashes($_POST['sc_name']))) : '';
    $file = strstr($sc, '.php', true);
    
    if ((empty($sc)) || (empty($name))) {
        alert('danger', "Uh Oh!", "Missing one ore more required inputs.", false);
    } elseif (!file_exists("{$file}.php")) {
        alert('danger', "Uh Oh!", "Web-page does not exist.", false);
    } else {
        $db->query("INSERT INTO `shortcut` (`sc_link`, `sc_name`, `sc_userid`) VALUES ('{$sc}', '{$name}', '{$userid}')");
        alert('success', "Success!", "Shortcut added successfully.", false);
    }
}
if (isset($_GET['delete'])) {
    $_GET['delete'] = (isset($_GET['delete']) && is_numeric($_GET['delete'])) ? abs($_GET['delete']) : '';
    if (!empty($_GET['delete'])) {
        $db->query("DELETE FROM `shortcut` WHERE `sc_id` = {$_GET['delete']} AND `sc_userid` = {$userid}");
        alert('success', "Success!", "Shortcut deleted successfully.", false);
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
$bank = ($ir['bank'] > -1) ? shortNumberParse($ir['bank']) : "N/A";
$bigbank = ($ir['bigbank'] > -1) ? shortNumberParse($ir['bigbank']) : "N/A";
$vaultbank = ($ir['vaultbank'] > -1) ? shortNumberParse($ir['vaultbank']) : "N/A";
$tbank = ($ir['tokenbank'] > -1) ? shortNumberParse($ir['tokenbank']) : "N/A";
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
						<a href='shops.php' class='{$txtClass}'>" . loadImageAsset("explore/shop.svg") . " Local Shops</a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='itemmarket.php'>" . loadImageAsset("explore/item_market.svg") . " Item Market <span class='badge badge-pill badge-primary'>{$market}</span></a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='itemrequest.php'>" . loadImageAsset("explore/item_request.svg") . " Item Request <span class='badge badge-pill badge-primary'>{$rmarket}</span></a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='secmarket.php'>" . loadImageAsset("explore/token_market.svg") . " Token Market <span class='badge badge-pill badge-primary'>{$secmarket}</span></a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='itemweekshop.php' class='{$txtClass}'>" . loadImageAsset("explore/item_of_week.svg") . " Item of the Week</a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='votestore.php'>" . loadImageAsset("explore/vote_store.svg") . " Vote Point Store <span class='badge badge-pill badge-primary'>" . number_format($ir['vote_points']) . "</span></a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='vipmarket.php'>" . loadImageAsset("explore/vip_store.svg") . " VIP Days Market <span class='badge badge-pill badge-primary'>" . number_format($vipMarket) . "</span></a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='estate_management.php?action=estateMarket'>" . loadImageAsset("explore/estate_market.svg") . " Estate Market <span class='badge badge-pill badge-primary'>" . number_format($estates) . "</span></a>
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
						<a href='job.php' class='{$txtClass}'>" . loadImageAsset("explore/work_center.svg") . " Work Center</a>
					</div>
					<div class='col-12 col-sm-7 col-md-6 col-lg-12 col-xxxl-6'>
						<a href='bank.php' class='{$txtClass}'>" . loadImageAsset("explore/city_bank.svg") . " City Bank <span class='badge badge-pill badge-primary'>{$bank}</span></a>
					</div>";
					if ($ir['level'] > 74) 
					{
						echo "
					<div class='col-12 col-md-6 col-lg-12 col-xxxl-6'>
						<a href='bigbank.php' class='{$txtClass}'>" . loadImageAsset("explore/fed_bank.svg") . " Federal Bank <span class='badge badge-pill badge-primary'>{$bigbank}</span></a>
					</div>";
					}
					if ($ir['level'] > 174) 
					{
						echo "
					<div class='col-12 col-md-6 col-lg-12 col-xxxl-6'>
						<a href='vaultbank.php' class='{$txtClass}'>" . loadImageAsset("explore/vault_bank.svg") . " Vault Bank <span class='badge badge-pill badge-primary'>{$vaultbank}</span></a>
					</div>";
					}
					echo "
					<div class='col-12 col-sm-6 col-md-6 col-lg-12 col-xxxl-6'>
						<a href='tokenbank.php' class='{$txtClass}'>" . loadImageAsset("explore/token_bank.svg") . " Token Bank <span class='badge badge-pill badge-primary'>{$tbank}</span></a>
					</div>
					<div class='col-12 col-sm-6 col-md-6 col-lg-6 col-xl-12 col-xxxl-6'>
						<a href='estate_management.php' class='{$txtClass}'>" . loadImageAsset("explore/estate_manage.svg") . " Estate Agent</a>
					</div>
					<div class='col-12 col-sm-6 col-md-6 col-lg-6 col-xl-12 col-xxxl-6'>
						<a href='travel.php' class='{$txtClass}'>" . loadImageAsset("explore/travel_agent.svg") . " Travel Agent</a>
					</div>
					<div class='col-12 col-sm-6 col-md-6 col-lg-12 col-xxxl-6'>
						<a href='temple.php' class='{$txtClass}'>" . loadImageAsset("explore/temple_fortune.svg") . " Temple of Fortune</a>
					</div>
                    <div class='col-12 col-sm-6 col-md-6 col-lg-12 col-xxxl-6'>
						<a href='investmarket.php' class='{$txtClass}'>Asset Investment</a>
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
						<a href='mine.php' class='{$txtClass}'>" . loadImageAsset("explore/mine.svg") . " Dangerous Mines <span class='badge badge-pill badge-primary'>{$miningenergy}%</span></a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='smelt.php' class='{$txtClass}'>" . loadImageAsset("explore/blacksmith.svg") . " Blacksmith's Smeltery</a>
					</div>
					<div class='col-12 col-sm-4 col-md-3 col-lg-6 col-xxxl-6'>
						<a href='farm.php' class='{$txtClass}'>" . loadImageAsset("explore/farming.svg") . "Farming</a>
					</div>
					<div class='col-12 col-sm-3 col-md-4 col-lg-6 col-xxxl-6'>
						<a href='gym.php' class='{$txtClass}'>" . loadImageAsset("explore/gym.svg") . " The Gym</a>
					</div>
					<div class='col-12 col-sm-5 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='bottent.php' class='{$txtClass}'>" . loadImageAsset("explore/npc_list.svg") . " NPC Battle List</a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='chivalry_gym.php' class='{$txtClass}'>" . loadImageAsset("explore/gym_chiv.svg") . " Chivalry Gym</a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='criminal.php' class='{$txtClass}'>" . loadImageAsset("explore/crime_center.svg") . " Criminal Center</a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='streetbum.php' class='{$txtClass}'>" . loadImageAsset("explore/street_beg.svg") . " Street Begging <span class='badge badge-pill badge-primary'>" . number_format($ir['searchtown']) . "</span></a>
					</div>";
						if ($ir['autobum'] > 0)
						{
							echo "
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='autobum.php' class='{$txtClass}'>" . loadImageAsset("explore/auto_street_beg.svg") . " Auto Street Beg <span class='badge badge-pill badge-primary'>" . shortNumberParse($ir['autobum']) . "</span></a>
					</div>";
						}
					echo"
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='academy.php' class='{$txtClass}'>" . loadImageAsset("explore/academy.svg") . " Local Academy</a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='achievements.php'>" . loadImageAsset("explore/achievements.svg") . "  Achievements</a>
					</div>
                    <div class='col-12 col-sm-6 col-md-6 col-lg-12 col-xxxl-6'>
						<a href='woodcut.php' class='{$txtClass}'>Wood Cutter</a>
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
						<a href='users.php'>" . loadImageAsset("explore/user_list.svg") . "  Players List <span class='badge badge-pill badge-primary'>{$users}</span></a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='usersonline.php'>" . loadImageAsset("explore/players_online.svg") . " Players Online <span class='badge badge-pill badge-primary'>{$userson}</span></a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='userstown.php'>" . loadImageAsset("explore/town_list.svg") . " Players In Town <span class='badge badge-pill badge-primary'>{$userstown}</span></a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-6'>
						<a href='staff.php'>" . loadImageAsset("explore/staff_list.svg") . " CID Staff</a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='fedjail.php'>" . loadImageAsset("explore/fed_dungeon.svg") . " Federal Dungeon</a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='stats.php'>" . loadImageAsset("explore/game_stats.svg") . " Game Statistics</a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='playerreport.php'>" . loadImageAsset("explore/player_report.svg") . " Player Report</a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='announcements.php'>" . loadImageAsset("explore/announcement.svg") . " Announcements <span class='badge badge-pill badge-primary'>" . number_format($ir['announcements']) . "</span></a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='itemappendix.php'>" . loadImageAsset("explore/item_list.svg") . " Item Appendix</a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='milestones.php'>" . loadImageAsset("explore/milestone.svg") . " Milestones</a>
					</div>
                    <div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='promo.php'> Promo Codes</a>
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
						<a href='russianroulette.php' class='{$txtClass}'>" . loadImageAsset("explore/russian_roulette.svg") . " Russian Roulette <span class='badge badge-pill badge-primary'>{$rr}</span></a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-6'>
						<a href='roulette.php?tresde={$tresder}' class='{$txtClass}'>" . loadImageAsset("explore/roulette.svg") . " Roulette</a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-6'>
						<a href='slots.php?tresde={$tresder}' class='{$txtClass}'>" . loadImageAsset("explore/slots.svg") . " Slots</a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='hexbags.php' class='{$txtClass}'>" . loadImageAsset("explore/hexbags.svg") . " Hexbags <span class='badge badge-pill badge-primary'>" . number_format($ir['hexbags']) . "</span></a>
					</div>";
					if ($ir['autohex'] > 0)
					{
						echo"
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='autohex.php' class='{$txtClass}'>" . loadImageAsset("explore/auto_hexbag.svg") . " Auto Hexbags <span class='badge badge-pill badge-primary'>" . shortNumberParse($ir['autohex']) . "</span></a>
					</div>";
					}
					echo"
					<div class='col-12 col-sm-6 col-lg-12 col-xxxl-6'>
						<a href='raffle.php' class='{$txtClass}'>" . loadImageAsset("explore/cid_raffle.svg") . " CID Raffle <span class='badge badge-pill badge-primary'>" . shortNumberParse($set['lotterycash']) . "</span></a>
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
						<a href='viewguild.php'>" . loadImageAsset("explore/your_guild.svg") . " Visit Your Guild</a>
					</div>";
					}
					echo"
					<div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xl-12 col-xxxl-6'>
						<a href='guilds.php'>" . loadImageAsset("explore/guild_list.svg") . " Guilds <span class='badge badge-pill badge-primary'>{$guildcount}</span></a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xl-12 col-xxxl-6'>
						<a href='guild_district.php'>" . loadImageAsset("explore/guild_district.svg") . " Guild Districts</a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='guilds.php?action=wars'>" . loadImageAsset("explore/guild_war.svg") . " Guild Wars</a> <span class='badge badge-pill badge-danger'>{$wars}</span>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='bounty.php' class='{$txtClass}'>" . loadImageAsset("explore/bounty_hunter.svg") . " Bounty Hunter <span class='badge badge-pill badge-primary'>{$bounty_count}</span></a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-6'>
						<a href='missions.php' class='{$txtClass}'>" . loadImageAsset("explore/mission.svg") . " Missions</a>
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
						<a href='dungeon.php'>" . loadImageAsset("explore/dungeon.svg") . " Dungeon <span class='badge badge-pill badge-primary'>{$dung_count}</span></a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='infirmary.php'>" . loadImageAsset("explore/infirmary.svg") . " Infirmary <span class='badge badge-pill badge-primary'>{$infirm_count}</span></a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xl-12 col-xxxl-6'>
						<a href='forums.php'>" . loadImageAsset("explore/forums.svg") . " Forums <span class='badge badge-pill badge-primary'>{$forumposts}</span></a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xl-12 col-xxxl-6'>
						<a href='/chat/?userName={$ir['username']}'>" . loadImageAsset("explore/forum.svg") . " CID Chat</a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='newspaper.php'>" . loadImageAsset("explore/cid_newspaper.svg") . " Newspaper <span class='badge badge-pill badge-primary'>{$paperads}</span></a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='polling.php'>" . loadImageAsset("explore/polling_center.svg") . " Polling Center</a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='halloffame.php'>" . loadImageAsset("explore/hof.svg") . " Hall of Fame</a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='marriage.php'>" . loadImageAsset("explore/marriage_center.svg") . " Marriage Center</a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='tutorial.php'>" . loadImageAsset("explore/tutorial.svg") . " CID Tutorial</a>
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
						<a href='referallist.php'>" . loadImageAsset("explore/refferal.svg") . " Your Referrals</a>
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
    <div class='col-12 col-lg-6 col-xl-4'>
		<div class='card'>
			<div class='card-header'>
                Shortcuts
			</div>
			<div class='card-body'>
				<div class='row'>";
                	$q = $db->query("/*qc=on*/SELECT * FROM `shortcut` WHERE `sc_userid` = {$userid}");
                	while ($r = $db->fetch_row($q)) {
                	      echo "
                        <div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
    						<a href='{$r['sc_link']}'>{$r['sc_name']}</a> [<a href='?delete={$r['sc_id']}'>&times;</a>]
    					</div>";
                	}
                	echo "<hr />
                    <div class='col-12'>
                        <a href='#' data-toggle='modal' class='btn btn-primary btn-block' data-target='#addShortcut'>Add Shortcut</a>
                    </div>
				</div>
			</div>
		</div>
	</div>
	</div>";
//referral link.
echo "	<div class='row'>
			<div class='col-md-12'>
				Share your referral link to gain 10 CID Admin Gym Scrolls and 3 VIP Days every time a friend joins!<br />
				<code>https://www.chivalryisdeadgame.com/register.php?REF={$userid}</code>
			</div>
		</div>";
include('explore_shortcut.php');
$h->endpage();