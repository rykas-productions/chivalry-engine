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
$blockAccess = false;
$txtClass='';
$month = date('n');
$day = date('j');
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
$vipMarket = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`vip_id`) FROM `vip_market`"));
$rmarket = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`irID`) FROM `itemrequest`"));
$secmarket = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`sec_id`) FROM `sec_market`"));
$forumposts = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`fp_id`) FROM `forum_posts` WHERE `fp_time` > {$last15}"));
$wars = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`gw_id`) FROM `guild_wars` WHERE `gw_end` > {$time}"));
$users = shortNumberParse($db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `users`")));
$userson = shortNumberParse($db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `users` WHERE `laston` > {$last15}")));
$userstown = shortNumberParse($db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `users` WHERE `location` = {$ir['location']}")));
$paperads = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`news_id`) FROM `newspaper_ads` WHERE `news_end` > {$time}"));
$rr = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`challenger`) FROM `russian_roulette` WHERE `challengee` = {$userid}"));
$bank = ($ir['bank'] > -1) ? shortNumberParse($ir['bank']) : "N/A";
$bigbank = ($ir['bigbank'] > -1) ? shortNumberParse($ir['bigbank']) : "N/A";
$vaultbank = ($ir['vaultbank'] > -1) ? shortNumberParse($ir['vaultbank']) : "N/A";
$storebank = (getCurrentUserPref("storageAcc{$ir['location']}", -1) > -1) ? shortNumberParse(getCurrentUserPref("storageAcc{$ir['location']}", -1)) : "N/A";
$tbank = ($ir['tokenbank'] > -1) ? shortNumberParse($ir['tokenbank']) : "N/A";
$guildcount = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`guild_id`) FROM `guild`"));
$MUS = ($db->fetch_row($db->query("/*qc=on*/SELECT * FROM `mining` WHERE `userid` = {$userid} LIMIT 1")));
$estates = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`em_id`) FROM `estate_market`"));
$miningenergy = min(round($MUS['miningpower'] / $MUS['max_miningpower'] * 100), 100);
$towndesc = $db->fetch_single($db->query("SELECT `town_desc` FROM `town` WHERE `town_id` = {$ir['location']}"));
$npccount = shortNumberParse($db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`botid`) FROM `botlist`")));
if ($ir['course'] > 0)
{
    
    $cd =
    $db->query(
        "/*qc=on*/SELECT `ac_days`
    				 FROM `academy`
    				 WHERE `ac_id` = {$ir['course']}");
    $coud = $db->fetch_row($cd);
    
    $daystoseconds=getCourseTime($coud['ac_days'])*86400;
    $actualReset = $ir['reset'] - 1;
    if ($actualReset > 0)
        $daystoseconds = $daystoseconds - ($daystoseconds * ($actualReset * 0.08));
        $starttime=time()-($ir['course_complete']-$daystoseconds);
        $academy = round(($starttime/$daystoseconds)*100) . "%";
}
else
{
    $courseTotal = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`ac_id`) FROM `academy`"));
    $courseDone = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `academy_done` WHERE `userid` = {$userid}"));
    $academy = $courseTotal - $courseDone;
}
if (empty($dung_count)) {
    $dung_count = 0;
}
if (empty($infirm_count)) {
    $infirm_count = 0;
}
echo "
<h4>You begin exploring {$api->SystemTownIDtoName($ir['location'])}. You find a few things that could keep you occupied.</h4></div>
<div class='row'>
<div class='col-12 col-xl-4 col-xxl-3 col-xxxl-2' align='left'>
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
<div class='col-12 col-xl-4 col-xxl-5 col-xxxl-6'>
	<div class='tab-content'>
		<div id='SHOPS' class='tab-pane'>
			<div class='card' align='left'>
				<div class='card-body'>
                    <div class='row'>
                        <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
                            <a href='shops.php' class='{$txtClass}'>" . loadImageAsset("explore/shop.svg") . " Local Shops</a>
                        </div>
                        <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
                            <a href='itemmarket.php'>" . loadImageAsset("explore/item_market.svg") . " Item Market <span class='badge badge-pill badge-primary'>{$market}</span></a>
                        </div>
                        <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
                            <a href='itemrequest.php'>" . loadImageAsset("explore/item_request.svg") . " Item Request <span class='badge badge-pill badge-primary'>{$rmarket}</span></a>
                        </div>
                        <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
                            <a href='secmarket.php'>" . loadImageAsset("explore/token_market.svg") . " Token Market <span class='badge badge-pill badge-primary'>{$secmarket}</span></a>
                        </div>
                        <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
                            <a href='votestore.php'>" . loadImageAsset("explore/vote_store.svg") . " Vote Point Store <span class='badge badge-pill badge-primary'>" . shortNumberParse($ir['vote_points']) . "</span></a>
                        </div>
                        <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
                            <a href='vipmarket.php'>" . loadImageAsset("explore/vip_store.svg") . " VIP Days Market <span class='badge badge-pill badge-primary'>" . shortNumberParse($vipMarket) . "</span></a>
                        </div>
                        <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
                            <a href='estate_management.php?action=estateMarket'>" . loadImageAsset("explore/estate_market.svg") . " Estate Market <span class='badge badge-pill badge-primary'>" . shortNumberParse($estates) . "</span></a>
                        </div>";
                        if ($month == 10)
                        {
                            echo"
                            <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
                                <a href='halloween.php?action=chuck'>Pumpkin Chuck</a>
                            </div>";
                        }
                        if ($month == 11)
                        {
                            echo"
                            <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
                                <a href='attack.php?user=21'>Participate in Turkey Hunt</a>
                            </div>";
                        }
                        if ($month == 12)
                        {
                            echo"
                            <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
                                <a href='adventcalender.php'>CID Advent Calendar</a>
                            </div>
                            <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
                                <a href='xmastree.php'>CID Christmas Tree</a>
                            </div>
                            <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
                                <a href='xmastree.php?action=wish'>Christmas Wish</a>
                            </div>";
                        }
                        echo"
                    </div>
				</div>
			</div>
		</div>
		<div id='FD' class='tab-pane'>
			<div class='card' align='left'>
				<div class='card-body'>
                    <div class='row'>
                        <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
                            <a href='job.php' class='{$txtClass}'>" . loadImageAsset("explore/work_center.svg") . " Work Center</a>
                        </div>
                        <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
                            <a href='bank.php' class='{$txtClass}'>" . loadImageAsset("explore/city_bank.svg") . " City Bank <span class='badge badge-pill badge-primary'>{$bank}</span></a>
                        </div>";
                        if ($ir['level'] >= 75) {
                            echo "
                            <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
                                <a href='bigbank.php' class='{$txtClass}'>" . loadImageAsset("explore/fed_bank.svg") . " Federal Bank <span class='badge badge-pill badge-primary'>{$bigbank}</span></a>
                            </div>";
                        }
                        if ($ir['level'] >= 175) {
                            echo "
                            <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
                                <a href='vaultbank.php' class='{$txtClass}'>" . loadImageAsset("explore/vault_bank.svg") . " Vault Bank <span class='badge badge-pill badge-primary'>{$vaultbank}</span></a>
                            </div>";
                        }
                        if ($ir['level'] >= 325) {
                            echo "
                            <div class='col-auto col-xxxl-4'>
                                <a href='bankstore.php' class='{$txtClass}'>" . loadImageAsset("explore/city_bank.svg") . " {$api->SystemTownIDtoName($ir['location'])} Storage <span class='badge badge-pill badge-primary'>{$storebank}</span></a>
                            </div>";
                        }
                        echo "
                        <div class='col-auto'>
                            <a href='tokenbank.php' class='{$txtClass}'>" . loadImageAsset("explore/token_bank.svg") . " Chiv Token Bank <span class='badge badge-pill badge-primary'>{$tbank}</span></a>
                        </div>
                        <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
                            <a href='estate_management.php' class='{$txtClass}'>" . loadImageAsset("explore/estate_manage.svg") . " Estate Agent</a></a>
                        </div>
                        <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
                            <a href='travel.php' class='{$txtClass}'>" . loadImageAsset("explore/travel_agent.svg") . " Travel Agent</a>
                        </div>
                        <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
                            <a href='temple.php' class='{$txtClass}'>" . loadImageAsset("explore/temple_fortune.svg") . " Temple of Fortune</a>
                        </div>
                        <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
                            <a href='investmarket.php' class='{$txtClass}'>Asset Investment</a>
                        </div>
                    </div>
				</div>
			</div>
		</div>
		<div id='HL' class='tab-pane'>
			<div class='card' align='left'>
				<div class='card-body'>
                    <div class='row'>
                        <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
                            <a href='mine.php' class='{$txtClass}'>" . loadImageAsset("explore/mine.svg") . " Dangerous Mines <span class='badge badge-pill badge-primary'>{$miningenergy}%</span></a>
                        </div>
                        <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
                            <a href='smelt.php' class='{$txtClass}'>" . loadImageAsset("explore/blacksmith.svg") . " Blacksmith's Smeltery</a>
                        </div>
                        <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
                            <a href='farm.php' class='{$txtClass}'>" . loadImageAsset("explore/farming.svg") . " Farming</a>
                        </div>
                        <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
                            <a href='bottent.php' class='{$txtClass}'>" . loadImageAsset("explore/npc_list.svg") . " NPC Battle List <span class='badge badge-pill badge-primary'>{$npccount}</span></a>
                        </div>
                        <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
                            <a href='gym.php' class='{$txtClass}'>" . loadImageAsset("explore/gym.svg") . "The Gym</a>
                        </div>
                        <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
                            <a href='chivalry_gym.php' class='{$txtClass}'>" . loadImageAsset("explore/gym_chiv.svg") . " Chivalry Gym</a>
                        </div>
                        <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
                            <a href='criminal.php' class='{$txtClass}'>" . loadImageAsset("explore/crime_center.svg") . " Criminal Center</a>
                        </div>
                        <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
                            <a href='streetbum.php' class='{$txtClass}'>" . loadImageAsset("explore/street_beg.svg") . " Street Begging <span class='badge badge-pill badge-primary'>" . shortNumberParse($ir['searchtown']) . "</span></a>
                        </div>";
                        if ($ir['autobum'] > 0)
                        {
                            echo "<div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
                            <a href='autobum.php' class='{$txtClass}'>" . loadImageAsset("explore/auto_street_beg.svg") . " Auto Street Bum <span class='badge badge-pill badge-primary'>" . shortNumberParse($ir['autobum']) . "</span></a>
                        </div>";
                        }
echo"               <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
                            <a href='academy.php' class='{$txtClass}'>" . loadImageAsset("explore/academy.svg") . " Local Academy <span class='badge badge-pill badge-primary'>{$academy}</span></a>
                        </div>
                        <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
                            <a href='achievements.php'>" . loadImageAsset("explore/achievements.svg") . " Achievements</a>
                        </div>
                        <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
                            <a href='bounty.php' class='{$txtClass}'>" . loadImageAsset("explore/bounty_hunter.svg") . " Bounty Hunter <span class='badge badge-pill badge-primary'>{$bounty_count}</span></a>
                        </div>
                        <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
                            <a href='missions.php' class='{$txtClass}'>" . loadImageAsset("explore/mission.svg") . " Missions</a>
                        </div>
                        <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
                            <a href='woodcut.php' class='{$txtClass}'>" . loadImageAsset("explore/woodcutter.svg") . "Wood Cutter</a>
                        </div>
                    </div>
				</div>
			</div>
		</div>
		<div id='ADMIN' class='tab-pane'>
			<div class='card' align='left'>
				<div class='card-body'>
                    <div class='row'>
                        <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
                            <a href='users.php'>" . loadImageAsset("explore/user_list.svg") . "  Players List <span class='badge badge-pill badge-primary'>{$users}</span></a>
                        </div>
                        <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
                            <a href='usersonline.php'>" . loadImageAsset("explore/players_online.svg") . " Players Online <span class='badge badge-pill badge-primary'>{$userson}</span></a>
                        </div>
                        <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
                            <a href='userstown.php'>" . loadImageAsset("explore/town_list.svg") . " Players In Town <span class='badge badge-pill badge-primary'>{$userstown}</span></a>
                        </div>
                        <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
                            <a href='staff.php'>" . loadImageAsset("explore/staff_list.svg") . " CID Staff</a>
                        </div>
                        <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
                            <a href='fedjail.php'>" . loadImageAsset("explore/fed_dungeon.svg") . " Federal Dungeon</a>
                        </div>
                        <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
                            <a href='stats.php'>" . loadImageAsset("explore/game_stats.svg") . " Game Statistics</a>
                        </div>
                        <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
                            <a href='playerreport.php'>" . loadImageAsset("explore/player_report.svg") . " Player Report</a>
                        </div>
                        <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
                            <a href='announcements.php'>" . loadImageAsset("explore/announcement.svg") . " Announcements <span class='badge badge-pill badge-primary'>" . shortNumberParse($ir['announcements']) . "</span></a>
                        </div>
                        <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
                            <a href='itemappendix.php'>" . loadImageAsset("explore/item_list.svg") . " Item Appendix</a>
                        </div>
                        <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
                            <a href='milestones.php'>" . loadImageAsset("explore/milestone.svg") . " Milestones</a>
                        </div>
                        <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
                            <a href='promo.php'>Promo Codes</a>
                        </div>
                    </div>
				</div>
			</div>
		</div>
		<div id='GAMES' class='tab-pane'>
			<div class='card' align='left'>
				<div class='card-body'>
                    <div class='row'>
                        <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
                            <a href='russianroulette.php' class='{$txtClass}'>" . loadImageAsset("explore/russian_roulette.svg") . " Russian Roulette <span class='badge badge-pill badge-primary'>{$rr}</span></a>
                        </div>
                        <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
                            <a href='roulette.php?tresde={$tresder}' class='{$txtClass}'>" . loadImageAsset("explore/roulette.svg") . " Roulette Table</a>
                        </div>
                        <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
                            <a href='slots.php?tresde={$tresder}' class='{$txtClass}'>" . loadImageAsset("explore/slots.svg") . " Slot Machines</a>
                        </div>
                        <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
                            <a href='hexbags.php' class='{$txtClass}'>" . loadImageAsset("explore/hexbags.svg") . " Hexbags <span class='badge badge-pill badge-primary'>" . shortNumberParse($ir['hexbags']) . "</span></a>
                        </div>";
                        if ($ir['autohex'] > 0)
                        {
                            echo "<div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
                            <a href='autohex.php' class='{$txtClass}'>" . loadImageAsset("explore/auto_hexbag.svg") . " Auto Hexbags <span class='badge badge-pill badge-primary'>" . shortNumberParse($ir['autohex']) . "</span></a>
                        </div>";
                        }

                    echo"
                        <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
                            <a href='raffle.php' class='{$txtClass}'>" . loadImageAsset("explore/cid_raffle.svg") . " CID Raffle <span class='badge badge-pill badge-primary'>" . shortNumberParse($set['lotterycash']) . "</span></a>
                        </div>
                        <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
                            <a href='hilow.php?tresde={$tresder}'>High/Low</a>
                        </div>
                    </div>
				</div>
			</div>
		</div>
		<div id='GUILDS' class='tab-pane'>
			<div class='card' align='left'>
				<div class='card-body'>
                    <div class='row'>";
					if ($ir['guild'] > 0) {
						echo "
					    <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
					       <a href='viewguild.php'>" . loadImageAsset("explore/your_guild.svg") . " Visit Your Guild</a>
					    </div>";
					}
					echo"
					    <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
					       <a href='guilds.php'>" . loadImageAsset("explore/guild_list.svg") . " Guilds <span class='badge badge-pill badge-primary'>{$guildcount}</span></a>
					    </div>
					    <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
					       <a href='guild_district.php'>" . loadImageAsset("explore/guild_district.svg") . " Guild Districts</a>
					    </div>
					    <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
					       <a href='guilds.php?action=wars'>" . loadImageAsset("explore/guild_war.svg") . " Guild Wars</a> <span class='badge badge-pill badge-danger'>{$wars}</span>
					    </div>
                    </div>
				</div>
			</div>
		</div>
		<div id='PINTER' class='tab-pane'>
			<div class='card' align='left'>
				<div class='card-body'>
                    <div class='row'>
                        <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
					       <a href='dungeon.php'>" . loadImageAsset("explore/dungeon.svg") . " Dungeon <span class='badge badge-pill badge-primary'>{$dung_count}</span></a>
					    </div>
                        <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
					       <a href='infirmary.php'>" . loadImageAsset("explore/infirmary.svg") . " Infirmary <span class='badge badge-pill badge-primary'>{$infirm_count}</span></a>
					    </div>
                        <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
					       <a href='forums.php'>" . loadImageAsset("explore/forums.svg") . " CID Forums <span class='badge badge-pill badge-primary'>{$forumposts}</span></a></a>
					    </div>
                        <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
					       <a href='newspaper.php'>" . loadImageAsset("explore/cid_newspaper.svg") . " CID Newspaper <span class='badge badge-pill badge-primary'>{$paperads}</span></a>
					    </div>
                        <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
					       <a href='polling.php'>" . loadImageAsset("explore/polling_center.svg") . " Polling Center</a>
					    </div>
                        <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
					       <a href='halloffame.php'>" . loadImageAsset("explore/hof.svg") . " Hall of Fame</a>
					    </div>
                        <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
					       <a href='marriage.php'>" . loadImageAsset("explore/marriage_center.svg") . " Marriage Center</a>
					    </div>
                        <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
					       <a href='tutorial.php'>" . loadImageAsset("explore/tutorial.svg") . " CID Tutorial</a>
					    </div>
                        <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
					       <a href='referallist.php'>" . loadImageAsset("explore/refferal.svg") . " Your Referrals</a>
					    </div>
                    </div>
				</div>
			</div>
		</div>";
if ($ir['vip_days']) {
    echo "
			<div id='VIP' class='tab-pane'>
			<div class='card' align='left'>
				<div class='card-body'>
				    <div class='row'>
    					<div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
    						<a href='friends.php'>" . loadImageAsset("explore/friendslist.svg") . " Friends</a>
    					</div>
    					<div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
    						<a href='enemy.php'>" . loadImageAsset("explore/enemylist.svg") . " Enemies</a>
    					</div>
    					<div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
    						<a href='userlogs.php'>" . loadImageAsset("explore/vip-logs.svg") . " VIP Logs</a>
    					</div>
    				</div>
				</div>
			</div>
		</div>";
}
echo "</div>
</div>
    <div class='col-12 col-xl-4'>
            <div class='card'>
                <div class='card-header'>
                    Your Shortcuts [<a href='#' data-toggle='modal' data-target='#addShortcut'>Add Shortcut</a>]
                </div>
                <div class='card-body' align='left'><div class='row'>";
    $q = $db->query("/*qc=on*/SELECT * FROM `shortcut` WHERE `sc_userid` = {$userid}");
    while ($r = $db->fetch_row($q)) {
        echo "  <div class='col-auto col-md-4 col-xl-auto col-xxl-6 col-xxxl-4'>
                    <a href='{$r['sc_link']}'>{$r['sc_name']}</a> 
                    <a class='btn btn-sm btn-danger' href='?delete={$r['sc_id']}'><i class='fas fa-trash-alt'></i></a>
                </div>";
    }
    echo "      </div>
            </div>
        </div>
    </div>
    <div class='col-12'>
        <div class='card'>
            <div class='card-header'>
                Want free stuff in {$set['WebsiteName']}?
            </div>
            <div class='card-body'>
                <div class='row'>
                    <div class='col-12'>
                        Share your referral link to gain 10 CID Admin Gym Scrolls and 3 VIP Days every time a friend joins!
                    </div>
                    <div class='col-12'>
                        <code><u>https://chivalryisdeadgame.com/register.php?REF={$userid}</u></code>
                    </div>
                    <div class='col-12'>
                        <div class='row'>
                            <div class='col-auto'>
                                <div class='fb-like' data-href='https://www.facebook.com/officialcidgame' data-layout='button' data-action='like' data-size='large' data-show-faces='false' data-share='true'></div>
                            </div>
                            <div class='col-auto'>
                                <a href='https://twitter.com/cidgame?ref_src=twsrc%5Etfw' class='twitter-follow-button' data-size='large' data-dnt='true' data-show-count='false'>Follow @cidgame</a>
                                <script async src='https://platform.twitter.com/widgets.js' charset='utf-8'></script>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>";
include('explore_shortcut.php');
$h->endpage();