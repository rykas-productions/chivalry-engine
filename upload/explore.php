<?php
require('globals.php');
include('facebook.php');
$blockAccess = false;
if (isMobile())
{
	header("Location: explore2.php");
    exit;
}
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
$miningenergy = min(round($MUS['miningpower'] / $MUS['max_miningpower'] * 100), 100);
if (empty($dung_count)) {
    $dung_count = 0;
}
if (empty($infirm_count)) {
    $infirm_count = 0;
}
if ($paperads == 0)
{
	$news="Welcome to Chivalry is Dead.";
}
else
{
	$news='';
	$npq=$db->query("/*qc=on*/SELECT * FROM `newspaper_ads` WHERE `news_end` > {$time} ORDER BY `news_cost` ASC");
	while ($par=$db->fetch_row($npq))
		{
			$phrase = " " . parseUsername($par['news_owner']) . " [{$par['news_owner']}]: {$par['news_text']} //";
			$news.="{$phrase}";
		}
	$news.="//END";
}
echo "
<div class='marquee'>
	<div class='text'>{$news}</div>
</div>
<h4>You begin exploring {$api->SystemTownIDtoName($ir['location'])}. You find a few things that could keep you occupied.</h4>
<div class='row'>
	<div class='col-sm'>
		<u><b>Shopping District</b></u><br />
		<a href='shops.php' class='{$txtClass}'><i class='game-icon game-icon-shop'></i> Local Shops</a><br />
		<a href='itemmarket.php'><i class='game-icon game-icon-trade'></i> Item Market <span class='badge badge-pill badge-primary'>{$market}</span></a><br />
				<a href='itemrequest.php'><i class='game-icon game-icon-trade'></i> Item Request <span class='badge badge-pill badge-primary'>{$rmarket}</span></a><br />
				<a href='secmarket.php'><i class='game-icon game-icon-cash'></i> Chivalry Tokens Market <span class='badge badge-pill badge-primary'>{$secmarket}</span></a><br />
				<a href='itemweekshop.php' class='{$txtClass}'>Item of the Week</a><br />
				<a href='votestore.php'>Vote Point Store <span class='badge badge-pill badge-primary'>" . number_format($ir['vote_points']) . "</span></a><br />
				<a href='vipmarket.php'>VIP Days Market <span class='badge badge-pill badge-primary'>" . number_format($vipMarket) . "</span></a><br />
	</div>
	<div class='col-sm'>
		<u><b>Financial District</b></u><br />
		<a href='job.php' class='{$txtClass}'><i class='game-icon game-icon-push'></i> Work Center</a><br />
				<a href='bank.php' class='{$txtClass}'><i class='game-icon game-icon-bank'></i> City Bank <span class='badge badge-pill badge-primary'>{$bank}</span></a><br />";
				if ($ir['level'] > 74) {
					echo "<a href='bigbank.php' class='{$txtClass}'><i class='game-icon game-icon-bank'></i> Federal Bank <span class='badge badge-pill badge-primary'>{$bigbank}</span></a><br />";
				}
				if ($ir['level'] > 174) {
					echo "<a href='vaultbank.php' class='{$txtClass}'><i class='game-icon game-icon-bank'></i> Vault Bank <span class='badge badge-pill badge-primary'>{$vaultbank}</span></a><br />";
				}
				echo "
				<a href='tokenbank.php' class='{$txtClass}'><i class='game-icon game-icon-chest'></i> Chivalry Token Bank <span class='badge badge-pill badge-primary'>{$tbank}</span></a><br />
				<a href='estates.php' class='{$txtClass}'><i class='game-icon game-icon-house'></i> Estate Agent</a><br />
				<a href='travel.php' class='{$txtClass}'><i class='game-icon game-icon-horseshoe'></i> Travel Agent</a><br />
				<a href='temple.php' class='{$txtClass}'><i class='game-icon game-icon-mayan-pyramid'></i> Temple of Fortune</a><br />
	</div>
	<div class='col-sm'>
		<u><b>Working District</b></u><br />
		<a href='mine.php' class='{$txtClass}'><i class='game-icon game-icon-mining'></i> Dangerous Mines <span class='badge badge-pill badge-primary'>Power: {$miningenergy}%</span></a><br />
				<a href='smelt.php' class='{$txtClass}'><i class='game-icon game-icon-anvil'></i> Blacksmith's Smeltery</a><br />
				<a href='farm.php' class='{$txtClass}'>Farming</a><br />
				<a href='bottent.php' class='{$txtClass}'><i class='game-icon game-icon-guards'></i> NPC Battle List</a><br />
				<a href='gym.php' class='{$txtClass}'><i class='game-icon game-icon-weight-lifting-down'></i> The Gym</a><br />
				<a href='chivalry_gym.php' class='{$txtClass}'> <i class='game-icon game-icon-weight-lifting-up'></i> Chivalry Gym</a><br />
				<a href='criminal.php' class='{$txtClass}'><i class='game-icon game-icon-robber'></i> Criminal Center</a><br />
				<a href='streetbum.php' class='{$txtClass}'> Street Begging <span class='badge badge-pill badge-primary'>{$ir['searchtown']}</span></a><br />
				<a href='academy.php' class='{$txtClass}'><i class='game-icon game-icon-diploma'></i> Local Academy</a><br />
				<a href='achievements.php'><i class='game-icon game-icon-achievement'></i> Achievements</a><br />
	</div>
</div>
<div class='row'>
	<div class='col-sm'>
		<u><b>Administration District</b></u><br />
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
	<div class='col-sm'>
		<u><b>Gambling District</b></u><br />
			<a href='russianroulette.php' class='{$txtClass}'><i class='game-icon game-icon-revolver'></i> Russian Roulette <span class='badge badge-pill badge-primary'>{$rr}</span></a><br />
			<a href='roulette.php?tresde={$tresder}' class='{$txtClass}'><i class='game-icon game-icon-table'></i> Roulette Table</a><br />
			<a href='slots.php?tresde={$tresder}' class='{$txtClass}'><i class='game-icon game-icon-pokecog spinner'></i> Slot Machines</a><br />";
			if ($ir['level'] > 49)
				echo "<a href='bigslots.php?tresde={$tresder}' class='{$txtClass}'><i class='game-icon game-icon-pokecog'></i> Federal Slots</a><br />";
			echo "<a href='hexbags.php' class='{$txtClass}'><i class='game-icon game-icon-open-treasure-chest'></i> Hexbags <span class='badge badge-pill badge-primary'>{$ir['hexbags']}</span></a><br />";
			if ($ir['autohex'] > 0)
				echo "<a href='autohex.php' class='{$txtClass}'><i class='game-icon game-icon-open-treasure-chest'></i> Auto Hexbags <span class='badge badge-pill badge-primary'>{$ir['autohex']}</span></a><br />";
			echo "
			<a href='raffle.php' class='{$txtClass}'><i class='fas fa-ticket-alt'></i> CID Raffle <span class='badge badge-pill badge-primary'>" . number_format($set['lotterycash']) . "</span></a><br />
	</div>
	<div class='col-sm'>
		<u><b>Danger District</b></u><br />";
				//User is in a guild.
				if ($ir['guild'] > 0) {
					echo "<a href='viewguild.php'><i class='game-icon game-icon-minions'></i> Visit Your Guild</a><br />";
				}
				echo "
				<a href='guilds.php'><i class='game-icon game-icon-dozen'></i> Guild Listing <span class='badge badge-pill badge-primary'>{$guildcount}</span></a><br />
				<a href='guild_district.php'> Guild Districts</a><br />
				<a href='guilds.php?action=wars'><i class='game-icon game-icon-mounted-knight'></i> Guild Wars</a> <span class='badge badge-pill badge-danger'>{$wars}</span><br />
				<a href='bounty.php' class='{$txtClass}'><i class='game-icon game-icon-game-icon game-icon-shadow-grasp'></i> Bounty Hunter <span class='badge badge-pill badge-primary'>{$bounty_count}</span></a><br />
				<a href='missions.php' class='{$txtClass}'><i class='game-icon game-icon-game-icon game-icon-stabbed-note'></i> Missions</a><br />
	</div>
</div>
<div class='row'>
	<div class='col-sm'>
		<u><b>Social District</b></u><br />
		<b><a href='2020bigbang.php'>2020 Big Bang Event</a></b><br />
		<a href='dungeon.php'><i class='game-icon game-icon-cage'></i> Dungeon <span class='badge badge-pill badge-primary'>{$dung_count}</span></a><br />
		<a href='infirmary.php'><i class='game-icon game-icon-hospital-cross'></i> Infirmary <span class='badge badge-pill badge-primary'>{$infirm_count}</span></a><br />
		<a href='forums.php'><i class='far fa-comments'></i> CID Forums <span class='badge badge-pill badge-primary'>{$forumposts}</span></a><br />
		<a href='/chat/?userName={$ir['username']}'><i class='fas fa-comment-dots'></i> CID Chat</a><br />
		<a href='newspaper.php'><i class='game-icon game-icon-scroll-unfurled'></i> CID Newspaper <span class='badge badge-pill badge-primary'>{$paperads}</span></a><br />
		<a href='polling.php'><i class='game-icon game-icon-vote'></i> Polling Center</a><br />
		<a href='halloffame.php'><i class='game-icon game-icon-crown'></i> Hall of Fame</a><br />
		<a href='marriage.php'><i class='game-icon game-icon-linked-rings'></i> Marriage Center</a><br />
		<a href='tutorial.php'><i class='far fa-question-circle'></i> CID Tutorial</a><br />
		<a href='referallist.php'><i class='game-icon game-icon-minions'></i> Your Referrals</a><br />
	</div>
	<div class='col-sm'>
		<u><b>VIP District</b></u><br />
		<a href='friends.php'><i class='far fa-fw fa-smile'></i> Friends</a><br />
				<a href='enemy.php'><i class='far fa-fw fa-frown'></i> Enemies</a><br />
				<a href='userlogs.php'><i class='fas fa-book fa-fw'></i> VIP Logs</a><br />
	</div>
</div>";
//referral link.
echo "	<div class='row'>
			<div class='col-md-12'>
				Share your referral link to gain 10 Chivalry Gym Scrolls every time a friend joins!<br />
				<code>chivalryisdeadgame.com/register.php?REF={$userid}</code><br />
				<div class='fb-like' data-href='https://www.facebook.com/officialcidgame' data-layout='button' data-action='like' data-size='large' data-show-faces='false' data-share='true'></div><br />
                <a href='https://twitter.com/cidgame?ref_src=twsrc%5Etfw' class='twitter-follow-button' data-size='large' data-dnt='true' data-show-count='false'>Follow @cidgame</a><script async src='https://platform.twitter.com/widgets.js' charset='utf-8'></script>
			</div>
		</div>";
$h->endpage();