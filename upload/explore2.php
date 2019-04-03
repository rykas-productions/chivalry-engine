<?php
/*
	File:		explore.php
	Created: 	4/5/2016 at 12:00AM Eastern Time
	Info: 		Gateway to many things around the game.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require("globals.php");
include('facebook.php');
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
//Anti-refresh RNG.
$tresder = (Random(100, 999));
$time = time();
$last15 = $time - 900;
//Select users in infirmary and dungeon to list later on the page.
$dung_count = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`dungeon_user`) FROM `dungeon` WHERE `dungeon_out` > {$time}"));
$bounty_count = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`bh_id`) FROM `bounty_hunter`"));
$infirm_count = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`infirmary_user`) FROM `infirmary` WHERE `infirmary_out` > {$time}"));
$market = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`imID`) FROM `itemmarket`"));
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
<h4>You begin exploring {$api->SystemTownIDtoName($ir['location'])}. You find a few things that could keep you occupied.</h4></div>
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
				<i class='fas fa-dollar-sign'></i> 
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
		</li>";
if ($ir['vip_days']) {
    echo "
			<li class='nav-item'>
				<a class='nav-link' data-toggle='tab' href='#VIP'>
					<i class='fas fa-shield-alt'></i>
					VIP District
				</a>
			</li>";
}
echo "
	</ul>
</div>
<div class='col-md-4'>
	<div class='tab-content'>
		<div id='SHOPS' class='tab-pane'>
			<div class='card' align='left'>
				<div class='card-body'>
					<a href='shops.php'><i class='game-icon game-icon-shop'></i> Local Shops</a><br />
					<a href='itemmarket.php'><i class='game-icon game-icon-trade'></i> Item Market <span class='badge badge-pill badge-primary'>{$market}</span></a><br />
					<a href='itemrequest.php'><i class='game-icon game-icon-trade'></i> Item Request <span class='badge badge-pill badge-primary'>{$rmarket}</span></a><br />
					<a href='secmarket.php'><i class='game-icon game-icon-cash'></i> Chivalry Tokens Market <span class='badge badge-pill badge-primary'>{$secmarket}</span></a><br />
                    <a href='itemweekshop.php'>Item of the Week</a><br />
                    <a href='votestore.php'>Vote Point Store</a>";
					echo"
				</div>
			</div>
		</div>
		<div id='FD' class='tab-pane'>
			<div class='card' align='left'>
				<div class='card-body'>
				    <a href='job.php'><i class='game-icon game-icon-push'></i> Work Center</a><br />
					<a href='bank.php'><i class='game-icon game-icon-bank'></i> City Bank <span class='badge badge-pill badge-primary'>{$bank}</span></a><br />";
if ($ir['level'] > 74) {
    echo "<a href='bigbank.php'><i class='game-icon game-icon-bank'></i> Federal Bank <span class='badge badge-pill badge-primary'>{$bigbank}</span></a><br />";
}
if ($ir['level'] > 174) {
    echo "<a href='vaultbank.php'><i class='game-icon game-icon-bank'></i> Vault Bank <span class='badge badge-pill badge-primary'>{$vaultbank}</span></a><br />";
}
echo "
					<a href='tokenbank.php'><i class='game-icon game-icon-chest'></i> Chivalry Token Bank <span class='badge badge-pill badge-primary'>{$tbank}</span></a><br />
					<a href='estates.php'><i class='game-icon game-icon-house'></i> Estate Agent</a><br />
					<a href='travel.php'><i class='game-icon game-icon-horseshoe'></i> Travel Agent</a><br />
					<a href='temple.php'><i class='game-icon game-icon-mayan-pyramid'></i> Temple of Fortune</a><br />
				</div>
			</div>
		</div>
		<div id='HL' class='tab-pane'>
			<div class='card' align='left'>
				<div class='card-body'>
					<a href='mine.php'><i class='game-icon game-icon-mining'></i> Dangerous Mines <span class='badge badge-pill badge-primary'>Power: {$miningenergy}%</span></a><br />
					<a href='smelt.php'><i class='game-icon game-icon-anvil'></i> Blacksmith's Smeltery</a><br />
					<a href='bottent.php'><i class='game-icon game-icon-guards'></i> NPC Battle List</a><br />
					<a href='gym.php'><i class='game-icon game-icon-weight-lifting-down'></i> The Gym</a><br />
					<a href='chivalry_gym.php'><i class='game-icon game-icon-weight-lifting-up'></i> Chivalry Gym</a><br />
					<a href='criminal.php'><i class='game-icon game-icon-robber'></i> Criminal Center</a><br />
					<a href='academy.php'><i class='game-icon game-icon-diploma'></i> Local Academy</a><br />
					<a href='achievements.php'><i class='game-icon game-icon-achievement'></i> Achievements</a><br />
                    <a href='bounty.php'><i class='game-icon game-icon-game-icon game-icon-shadow-grasp'></i> Bounty Hunter <span class='badge badge-pill badge-primary'>{$bounty_count}</span></a><br />
					<a href='missions.php'><i class='game-icon game-icon-game-icon game-icon-stabbed-note'></i> Missions</a><br />
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
					<a href='russianroulette.php'><i class='game-icon game-icon-revolver'></i> Russian Roulette <span class='badge badge-pill badge-primary'>{$rr}</span></a><br />
					<a href='roulette.php?tresde={$tresder}'><i class='game-icon game-icon-table'></i> Roulette Table</a><br />
					<a href='slots.php?tresde={$tresder}'><i class='game-icon game-icon-pokecog spinner'></i> Slot Machines</a><br />";
if ($ir['level'] > 49)
    echo "<a href='bigslots.php?tresde={$tresder}'><i class='game-icon game-icon-pokecog'></i> Federal Slots</a><br />";
echo "
					<a href='hexbags.php'><i class='game-icon game-icon-open-treasure-chest'></i> Hexbags <span class='badge badge-pill badge-primary'>{$ir['hexbags']}</span></a><br />";
if ($ir['autohex'] > 0)
    echo "<a href='autohex.php'><i class='game-icon game-icon-open-treasure-chest'></i> Auto Hexbags <span class='badge badge-pill badge-primary'>{$ir['autohex']}</span></a><br />";
echo "
					<a href='raffle.php'><i class='fas fa-ticket-alt'></i> CID Raffle <span class='badge badge-pill badge-primary'>" . number_format($set['lotterycash']) . "</span></a><br />
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
					<a href='guilds.php'><i class='game-icon game-icon-dozen'></i> Guild Listing <span class='badge badge-pill badge-primary'>{$guildcount}</span></a><br />
					<a href='guilds.php?action=wars'><i class='game-icon game-icon-mounted-knight'></i> Guild Wars</a> <span class='badge badge-pill badge-danger'>{$wars}</span><br />
				</div>
			</div>
		</div>
		<div id='PINTER' class='tab-pane'>
			<div class='card' align='left'>
				<div class='card-body'>
				    <a href='dungeon.php'><i class='game-icon game-icon-cage'></i> Dungeon <span class='badge badge-pill badge-primary'>{$dung_count}</span></a><br />
					<a href='infirmary.php'><i class='game-icon game-icon-hospital-cross'></i> Infirmary <span class='badge badge-pill badge-primary'>{$infirm_count}</span></a><br />
					<a href='forums.php'><i class='far fa-comments'></i> CID Forums <span class='badge badge-pill badge-primary'>{$forumposts}</span></a><br />
					<a href='newspaper.php'><i class='game-icon game-icon-scroll-unfurled'></i> CID Newspaper <span class='badge badge-pill badge-primary'>{$paperads}</span></a><br />
					<a href='polling.php'><i class='game-icon game-icon-vote'></i> Polling Center</a><br />
					<a href='halloffame.php'><i class='game-icon game-icon-crown'></i> Hall of Fame</a><br />
					<a href='marriage.php'><i class='game-icon game-icon-linked-rings'></i> Marriage Center</a><br />
					<a href='tutorial.php'><i class='far fa-question-circle'></i> CID Tutorial</a><br />
					<a href='referallist.php'><i class='game-icon game-icon-minions'></i> Your Referrals</a><br />
				</div>
			</div>
		</div>";
if ($ir['vip_days']) {
    echo "
			<div id='VIP' class='tab-pane'>
			<div class='card' align='left'>
				<div class='card-body'>
				    <a href='friends.php'><i class='far fa-fw fa-smile'></i> Friends</a><br />
					<a href='enemy.php'><i class='far fa-fw fa-frown'></i> Enemies</a><br />
					<a href='userlogs.php'><i class='fas fa-book fa-fw'></i> VIP Logs</a><br />
				</div>
			</div>
		</div>";
}
echo "</div>
</div><div class='col-sm-4'>
        <div class='card'>
            <div class='card-header'>
                Your Shortcuts [<a href='#' data-toggle='modal' data-target='#addShortcut'>Add Shortcut</a>]
            </div>
            <div class='card-body' align='left'>";
$q = $db->query("/*qc=on*/SELECT * FROM `shortcut` WHERE `sc_userid` = {$userid}");
while ($r = $db->fetch_row($q)) {
    echo "<a href='{$r['sc_link']}'>{$r['sc_name']}</a> [<a href='?delete={$r['sc_id']}'>&times;</a>]<br />";
}
echo "</div>
        </div>
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
include('explore_shortcut.php');
$h->endpage();