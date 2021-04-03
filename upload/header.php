<?php

/*
	File:		header.php
	Created: 	4/5/2016 at 12:05AM Eastern Time
	Info: 		Class file to load the template in-game.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/

class headers
{
	function extraData()
	{
		global $db, $ir, $userid, $api;
		$time = time();
		$last15 = $time - 900;
		$extras = array();
		$extras['dung_count'] = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`dungeon_user`) FROM `dungeon` WHERE `dungeon_out` > {$time}"));
		$extras['infirm_count'] = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`infirmary_user`) FROM `infirmary` WHERE `infirmary_out` > {$time}"));
		$extras['forum_count'] = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`fp_id`) FROM `forum_posts` WHERE `fp_time` > {$last15}"));
		$extras['newspaper_count'] = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`news_id`) FROM `newspaper_ads` WHERE `news_end` > {$time}"));
		$extras['announce_class'] = ($ir['announcements'] == 0) ? "" : "text-danger font-weight-bold";
		$extras['news_class'] = ($extras['newspaper_count'] == 0) ? "" : "text-warning";
		$extras['forum_class'] = ($extras['forum_count'] == 0) ? "" : "text-info font-weight-bold";
		$extras['infirm_class'] = (!$api->UserStatus($ir['userid'], 'infirmary')) ? "" : "text-danger font-weight-bold";
		$extras['dung_class'] = (!$api->UserStatus($ir['userid'], 'dungeon')) ? "" : "text-danger font-weight-bold";
		return $extras;
	}
	
    function startheaders()
    {
        global $ir, $set, $h, $db, $menuhide, $userid, $macropage, $api, $time, $sound;
		cslog('log',"Loading headers for {$set['WebsiteName']}");
		date_default_timezone_set($set['game_time']);
		$extras = $this->extraData();
        //Load the meta headers.
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
                <?php
                //Select count of user's unread messages.
                $ir['mail'] = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`mail_id`) FROM `mail` WHERE `mail_to` = {$ir['userid']} AND `mail_status` = 'unread'"));
                //Select count of user's unread notifications.
                $ir['notifications'] = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`notif_id`) FROM `notifications` WHERE `notif_user` = {$ir['userid']} AND `notif_status` = 'unread'"));
                $title = "{$set['WebsiteName']} - {$ir['username']}";
                echo "<title>{$title}</title>";
				if ($ir['disable_alerts'] == 0)
					$notificon = "fas fa-bell";
				else
					$notificon = "fas fa-bell-slash";
				$this->loadEssentialAssets();
				$this->loadUserTheme($ir['theme']);
				$this->returnMetadata();
				$hdr=$this->getThemeNavbarColor($ir['theme']);
				$sound->loadSystem();
				cslog('warn',"Main assets have loaded successfully. Log entries after this point were created by the game or related modules, not the base engine.");
				include('ads/ad_all.php');
				?>
				</head>
    <?php
    //If the called script wants the menu hidden.
    if (empty($menuhide))
    {
			$energy = $api->UserInfoGet($userid, 'energy', true);
			$brave = $api->UserInfoGet($userid, 'brave', true);
			$will = $api->UserInfoGet($userid, 'will', true);
			$xp = round($ir['xp'] / $ir['xp_needed'] * 100);
			$hp = $api->UserInfoGet($userid, 'hp', true);
			if ($ir['sidemenu'] == 0)
				$toggle='toggled';
			else
				$toggle='';
		echo "
        <body>
				<div class='page-wrapper default-theme sidebar-bg {$toggle}'>
				<div id='show-sidebar' class='btn btn-md btn-dark'>
					<i class='fas fa-bars'></i>
				</div>
				<nav id='sidebar' class='sidebar-wrapper'>
					<div class='sidebar-content'>
						<!-- sidebar-brand  -->
						<div class='sidebar-item sidebar-brand'>
							<a href='index.php' class='updateHoverBtn'>{$set['WebsiteName']}</a>
							<div id='close-sidebar'>
								<i class='fas fa-times'></i>
							</div>
						</div>
						<!-- sidebar-menu  -->
						<div class=' sidebar-item sidebar-menu'>
							<ul>
								<li class='header-menu'>
									<span>General</span>
								</li>
								<li>
									<a href='#' class='updateHoverBtn' data-toggle='modal' data-target='#userInfo'>
										<span class='menu-text'><i class='fas fa-user-cog'></i> {$ir['username']} <span class='badge badge-pill badge-primary'>{$userid}</span></span>
									</a>
								</li>
								<li>
									<a href='inventory.php' class='updateHoverBtn'>
										<span class='menu-text'><i class='fas fa-box'></i> Inventory</span>
									</a>
								</li>
								<li>
									<a href='explore.php' class='updateHoverBtn'>
										<span class='menu-text'><i class='fas fa-map-signs'></i> Explore</span>
									</a>
								</li>
								<li class='header-menu'>
									<span>Activities</span>
								</li>
								<li>
									<a href='gym.php' class='updateHoverBtn'>
										<span class='menu-text'>" . loadImageAsset("explore/gym.svg") . " Gym</span>
									</a>
								</li>
								<li>
									<a href='criminal.php' class='updateHoverBtn'>
										<span class='menu-text'>" . loadImageAsset("explore/crime_center.svg") . " Crimes</span>
									</a>
								</li>
								<li>
									<a href='academy.php' class='updateHoverBtn'>
										<span class='menu-text'>" . loadImageAsset("explore/academy.svg") . " Academy</span>
									</a>
								</li>
								<li>
									<a href='dungeon.php' class='updateHoverBtn {$extras['dung_class']}'>
										<span class='menu-text'>" . loadImageAsset("explore/dungeon.svg") . " Dungeon <span class='badge badge-pill badge-primary'>" . number_format($extras['dung_count']) . "</span></span>
									</a>
								</li>
								<li>
									<a href='infirmary.php' class='updateHoverBtn {$extras['infirm_class']}'>
										<span class='menu-text'>" . loadImageAsset("explore/infirmary.svg") . " Infirmary <span class='badge badge-pill badge-primary'>" . number_format($extras['infirm_count']) . "</span></span>
									</a>
								</li>
								<li class='header-menu'>
									<span>Social</span>
								</li>
								<li>
									<a href='forums.php' class='updateHoverBtn {$extras['forum_class']}'>
										<span class='menu-text'>" . loadImageAsset("explore/forums.svg") . " Forums <span class='badge badge-pill badge-primary'>" . number_format($extras['forum_count']) . "</span></span>
									</a>
								</li>
								<li>
									<a href='newspaper.php' class='updateHoverBtn {$extras['news_class']}'>
										<span class='menu-text'>" . loadImageAsset("explore/cid_newspaper.svg") . " Newspaper <span class='badge badge-pill badge-primary'>" . number_format($extras['newspaper_count']) . "</span></span>
									</a>
								</li>
								<li>
									<a href='profile.php?user={$userid}' class='updateHoverBtn'>
										<span class='menu-text'><i class='fas fa-user-circle'></i> Your Profile</span>
									</a>
								</li>
								<li>
									<a href='announcements.php' class='updateHoverBtn {$extras['announce_class']}'>
										<span class='menu-text'>" . loadImageAsset("explore/announcement.svg") . " Announcements <span class='badge badge-pill badge-primary' id='ui_announce'>" . number_format($ir['announcements']) . "</span></span>
									</a>
								</li>";
								if ($ir['guild'] > 0) 
								{
									echo "
									<li>
										<a href='viewguild.php' class='updateHoverBtn'>
											<span class='menu-text'>" . loadImageAsset("explore/your_guild.svg") . " Your Guild</span></span>
										</a>
									</li>";
								}
								if ($api->UserMemberLevelGet($userid, 'forum moderator'))
								{
								    echo "
                                    <li class='header-menu'>
									   <span>Staff</span>
								    </li>
    								<li>
    									<a href='staff/index.php' class='updateHoverBtn'>
    										<span class='menu-text'><i class='fas fa-users-cog'></i> Staff Panel</span>
    									</a>
    								</li>
                                    <li class='header-menu'>
									   <span>Staff Online</span>
								    </li>";
								    $online_cutoff = time() - 900;
								    $q =
								    $db->query(
								        "SELECT `userid`, `username`, `laston`
                                         FROM `users`
                                         WHERE `laston` > ({$online_cutoff})
                                         AND `user_level` != 'NPC'
                                         AND `user_level` != 'Member'
                                         ORDER BY `userid` ASC");
								    while ($r = $db->fetch_row($q))
								    {
								        echo "<li>
    									<a href='profile.php?user={$r['userid']}' class='updateHoverBtn'>
    										  <span class='menu-text'>{$r['username']} <span class='badge badge-pill badge-primary'>{$r['userid']}</span></span>
        									</a>
        								</li>";
								    }
								} 
								?>
								<li class="header-menu">
									<span id='ui_time'><?php echo date('F j, Y') . " " . date('g:i:s a'); ?></span>
								</li>
							</ul>
						</div>
						<!-- sidebar-menu  -->
					</div>
					<!-- sidebar-footer  -->
					<div class="sidebar-footer">
						<div class="dropdown">
							<a href="notifications.php" class="updateHoverBtn">
								<i class="fa fa-bell"></i>
								<span class="badge badge-pill badge-success notification" id="ui_notif"><?php echo $ir['notifications']; ?></span>
							</a>
						</div>
						<div class="dropdown">
							<a href="inbox.php" class="updateHoverBtn">
								<i class="fa fa-envelope"></i>
								<span class="badge badge-pill badge-success notification" id="ui_mail"><?php echo $ir['mail']; ?></span>
							</a>
						</div>
						<div class="dropdown">
							<a href="preferences.php" class="updateHoverBtn">
								<i class="fa fa-cog"></i>
							</a>
						</div>
						<div>
							<a href="logout.php" class="updateHoverBtn">
								<i class="fa fa-power-off"></i>
							</a>
						</div>
						<div class="pinned-footer">
							<a href="#">
								<i class="fas fa-ellipsis-h"></i>
							</a>
						</div>
					</div>
				</nav>

				<!-- Page Content -->
				<main class="page-content pt-2">
					<div id="overlay" class="overlay"></div>
					<div class="container-fluid p-5">
        <noscript>
            <?php
            //User doesn't have javascript turned on, so lets tell them.
            alert('warning', "", "We highly recommend you enable Javascript! Many of the game's functions will not work without it.", false);
            ?>
        </noscript>
    <?php
    date_default_timezone_set($set['game_time']);
    $IP = $db->escape($_SERVER['REMOTE_ADDR']);
    $ipq = $db->query("/*qc=on*/SELECT `ip_id` FROM `ipban` WHERE `ip_ip` = '{$IP}'");
    //User's IP is banned, so lets stop access.
    if ($db->num_rows($ipq) > 0) {
        alert('danger', "Uh Oh!", "You have been IP banned. There is no way around this.", false);
        die($h->endpage());
    }
    $fed = $db->fetch_row($db->query("/*qc=on*/SELECT * FROM `fedjail` WHERE `fed_userid` = {$userid}"));
    $votecount=$db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`voted`) FROM `votes` WHERE `userid` = {$userid}"));
    if ($votecount < 5) 
        echo "<b><a href='vote.php' class='text-success updateHoverBtn'>[Vote for {$set['WebsiteName']}<span class='hidden-sm-down'> at various Voting Websites and be rewarded</span>.]</a></b><br />";
	echo "<b><a href='donator.php' class='text-danger updateHoverBtn'>[Donate to {$set['WebsiteName']}.<span class='hidden-sm-down'> Packs start at $1 and you receive tons of benefits.</span>]</a></b><br />";
    $this->loadNewsScroller();
    if (userHasEffect($userid, "sleep"))
    {
        $protDone = returnEffectDone($userid, "sleep");
        if (isset($_GET['wakeup']))
        {
            userRemoveEffect($userid, "sleep");
            alert("success","Rise and Shine!", "You've successfully woken up. Nothing like the smell of the blood of your fallen prey in the morning.", true, 'index.php');
        }
        else
        {
            alert("info","Nighty night!", "You're currently sleeping and will wake up in " . TimeUntil_Parse($protDone) . ". Your stats will replenish each minute you sleep.", true, '?wakeup', "Wake Up");
            die($h->endpage());
        }
    }
	if (userHasEffect($userid, "basic_protection"))
	{
		$protDone = returnEffectDone($userid, "basic_protection");
		echo "<b><span class='text-info'>You have protection active for the next " . TimeUntil_Parse($protDone) . ".</span></b><br />";
	}
    if ($ir['invis'] > time())
	{
		echo "<b><span class='text-info'>You have invisibility active for the next " . TimeUntil_Parse($ir['invis']) . ".</span></b><br />";
	}
	if ($ir['will_overcharge'] > time())
	{
		echo "<b><span class='text-info'>You have Will Overcharge active for the next " . TimeUntil_Parse($ir['will_overcharge']) . ".</span></b><br />";
	}
    if (getCurrentUserPref('tutorialToggle', 'true') == 'true')
    {
        $page = $db->escape(strip_tags(stripslashes(basename($_SERVER['PHP_SELF']))));
        $tq=$db->query("/*qc=on*/SELECT * FROM `tutorial` WHERE `page` = '{$page}'");
        if ($db->num_rows($tq) > 0)
        {
            $tr=$db->fetch_row($tq);
            alert('info',"Tutorial!",$tr['tutorial'],true,'preferences.php?action=tuttoggle',"Disable Tutorial");
        }
    }

	
	//User's federal jail sentence is completed. Let them play again.
    if ($fed['fed_out'] < $time) {
        $db->query("UPDATE `users` SET `fedjail` = 0 WHERE `userid` = {$userid}");
        $db->query("DELETE FROM `fedjail` WHERE `fed_userid` = {$userid}");
    }
    //User is in federal jail. Stop their access.
    if ($ir['fedjail'] > 0) {
		$lasthour=time()-3600;
		$fq2=$db->query("/*qc=on*/SELECT * FROM `fedjail_appeals` WHERE `fja_user` = {$userid} AND `fja_time` >= {$lasthour} LIMIT 1");
		if (isset($_POST['fedappeal']))
		{
			$msg = $db->escape(stripslashes($_POST['fedappeal']));
			$time=time();
			if ($db->num_rows($fq2) != 0)
			{
				echo "<b>You can only submit an appeal once per hour...</b>";
			}
			else
			{
				echo "<b>Response posted. Come back later for a response.</b>";
				$db->query("INSERT INTO `fedjail_appeals` (`fja_user`, `fja_responder`, `fja_text`, `fja_time`) VALUES ('{$userid}', '{$userid}', '{$msg}', '{$time}')");
			}
		}
        alert('info', "Federal Dungeon!", "You are locked away in Federal Dungeon for the next
					    " . TimeUntil_Parse($fed['fed_out']) . ". You were placed in here for <b>{$fed['fed_reason']}</b>.", false);
		$fq=$db->query("/*qc=on*/SELECT * FROM `fedjail_appeals` WHERE `fja_user` = {$userid} ORDER BY `fja_time` ASC");
		echo "<table class='table table-bordered'>";
		while ($fr = $db->fetch_row($fq))
		{
			echo "<tr>
			<th width='33%'>
				{$api->SystemUserIDtoName($fr['fja_responder'])} [{$fr['fja_responder']}]<br />
				" . DateTime_Parse($fr['fja_time']) . "
			</th>
			<td>
				{$fr['fja_text']}
			</td>
			</tr>";
		}
		echo "
		<tr>
			<td colspan='2'>
				<form method='post'>
					Submitting your appeal. You can only respond once an hour, so give as much information as you can. Honesty may be rewarded with a lesser sentence.
					<textarea name='fedappeal' class='form-control'></textarea>
					<input type='submit' value='Submit Appeal' class='btn btn-primary'>
				</form>
			</td>
		</tr>
		</table>";
        die($h->endpage());
    }
	$this->showSocialAlerts();
	$this->showStatusAlerts();
	$this->doArtifactRNG();
	$this->doLuckRNG();
    //User needs to reverify with reCaptcha
    if (($ir['last_verified'] < ($time - $set['Revalidate_Time'])) || ($ir['need_verify'] == 1))
    {
		//Script calls for reCaptcha to be loaded.
		if (isset($macropage))
		{
			//Set User to need verified.
			$db->query("UPDATE `users` SET `need_verify` = 1 WHERE `userid` = {$userid}");
			echo "This is a needed evil. Please confirm you are not a bot. Please be sure Javascript is enabled.<br />"; ?>
            <script src='https://www.google.com/recaptcha/api.js' async defer></script>
			<noscript>
            <?php
            //User doesn't have javascript turned on, so lets tell them.
				alert('warning', "", "{$set['WebsiteName']}'s reCaptcha system needs you to enable Javascript to continue.", false);
            ?>
			</noscript>
            <form action='macro.php' method='post'>
				<div class='g-recaptcha' data-theme='light' data-sitekey='<?php echo $set['reCaptcha_public']; ?>' data-callback='enableBtn'></div>
                <input type='hidden' value='<?php echo $macropage; ?>' name='page'>
                <input type='submit' value="<?php echo "Confirm"; ?>" class="btn btn-primary" id="recaptchabtn" disabled="disabled">
            </form>
            <?php
            die($h->endpage());
		}
    }
	include('rickroll.php');
    }
    }
	
	function doLuckRNG()
	{
		global $db, $ir, $api, $userid;
		$luckrng=Random(1,200);
		//$luckrng=69;
		if (!isset($_SESSION['lucked_out']))
			$_SESSION['lucked_out']=0;
		//if (isset($_SESSION['lucked_out']))
		if ($_SESSION['lucked_out'] < time())
		{
			if ($luckrng == 160)
			{
				$thisrng=0;
				if (($ir['luck'] > 50) && ($ir['luck'] < 150))
				{
					$minimumluck=1;
					//Lucky Day
					$specialnumber=((getSkillLevel($userid,26)*1)/100);
					$minimumluck=$minimumluck+$specialnumber;
					while ($thisrng == 0)
					{
						$thisrng=Random($minimumluck,7);
					}
					$_SESSION['lucked_out']=time()+3600;
					//alert('info','Lucked Out!',"While walking around the kingdom, your luck has changed by {$thisrng}%.",false);
					toast("Lucky Day!","While walking around the kingdom, your luck has changed by {$thisrng}%.");
					$db->query("UPDATE `userstats` SET `luck` = `luck` + ({$thisrng}) WHERE `userid` = {$userid}");
				}
			}
			if ($luckrng == 69)
			{
			    //easter 2021 (april 4th)
			    if (date('n') == 4 && date('j') == 4)
			    {
			        $effct = constant("holiday_mining_energy");
			        if (returnEffectMultiplier($userid, $effct) < 4)
			        {
			            $_SESSION['lucked_out']=time()+Random(1800,2400);
			            alert('info','',"Your holiday mining energy requirement has been reduced by another 20%.",false);
			            $db->query("UPDATE `users_effects` SET `effectMulti` = `effectMulti` + 1 WHERE `userid` = {$userid} AND `effectName` = '{$effct}'");
			        }
			    }
			}
		}
	}
	
	function doArtifactRNG()
	{
		global $db, $ir, $api, $userid;
		//RNG for experience token? O.o
		$xprng=Random(1,100);
		if ($xprng == 56 && $ir['artifacts'] != 4)
		{
			if (($ir['artifact_time']) < time() - Random(270,360))
			{
				alert("info","","While wondering around, you find a small artifact laying on the ground. Maybe you should take it to the Blacksmith Smeltery to find out what you can do with it?",false);
				$api->UserGiveItem($userid,94,1);
				$db->query("UPDATE `user_settings` SET `artifacts` = `artifacts` + 1, `artifact_time` = " . time() . " WHERE `userid` = {$userid}");
			}
		}
	}
	
	function showSocialAlerts()
	{
		global $ir;
		echo "<div class='row' id='socialRow'>";
		if ($ir['mail'] > 0) 
		{
			echo "<div class='col-lg'>";
				alert('info', "", "You have " . number_format($ir['mail']) . " unread messages.", true, 'inbox.php', "View");
			echo "</div>";
        }
        //Tell user they have unread notifcations when they do.
        if ($ir['notifications'] > 0) 
		{
			echo "<div class='col-lg'>";
				alert('info', "", "You have " . number_format($ir['notifications']) . " unread notifications.", true, 'notifications.php', "View");
			echo "</div>";
        }
		//Tell user they have unread game announcements when they do.
		if ($ir['announcements'] > 0) 
		{
			echo "<div class='col-lg'>";
				alert('info', "", "You have " . number_format($ir['announcements']) . " unread announcements.", true, 'announcements.php', "View");
			echo "</div>";
		}
		echo "</div>";
		echo "
		<div class='row' id='socialRow2'>
		</div>";
	}
	function showStatusAlerts()
	{
		global $ir, $api, $db;
		echo "<div class='row'>";
		if ($api->UserStatus($ir['userid'], 'infirmary')) 
		{
			$InfirmaryOut = $db->fetch_single($db->query("/*qc=on*/SELECT `infirmary_out` FROM `infirmary` WHERE `infirmary_user` = {$ir['userid']}"));
			$InfirmaryRemain = TimeUntil_Parse($InfirmaryOut);
			echo "<div class='col-md'>";
				alert('info', "", "You are in the Infirmary for {$InfirmaryRemain}.", true, "quickuse.php?infirmary", "Use " . parseInfirmaryItemName($ir['iitem']));
			echo "</div>";
		}
		//User is in the dungeon, tell them how long.
		if ($api->UserStatus($ir['userid'], 'dungeon')) 
		{
			$DungeonOut = $db->fetch_single($db->query("/*qc=on*/SELECT `dungeon_out` FROM `dungeon` WHERE `dungeon_user` = {$ir['userid']}"));
			$DungeonRemain = TimeUntil_Parse($DungeonOut);
			echo "<div class='col-md'>";
				alert('info', "", "You are in the dungeon for {$DungeonRemain}.", true, "quickuse.php?dungeon", "Use " . parseDungeonItemName($ir['ditem']));
			echo "</div>";
		}
		echo "</div>";
	}
	
	function loadUserTheme($themeID)
	{
		global $set;
		cslog('log',"User Theme ID: {$themeID}.");
		echo "<link rel='stylesheet' href='css/sidebar-themes.css'>";
		if ($themeID == 1)
		{
			echo "
			<link rel='stylesheet' href='css/themes/default-21.2.2.css'>
			<meta name='theme-color' content='#333'>
			<style>
			.default-theme .sidebar-wrapper {
				background-color: #333; 
			}
			</style>";
		}
		if ($themeID == 2)
		{
			echo "
			<link rel='stylesheet' href='css/themes/darkly-21.2.2.css'>
			<meta name='theme-color' content='#303030'>";
		}
		if ($themeID == 3)
		{
			echo "
			<link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootswatch/{$set['bootstrap_version']}/slate/bootstrap.min.css'>
			<meta name='theme-color' content='#272B30'>
			<style>
			.default-theme .sidebar-wrapper {
				background-color: #272B30; 
			}
			</style>";
		}
		if ($themeID == 4)
		{
			echo "
			<link rel='stylesheet' href='css/themes/cyborg-21.2.2.css'>
			<meta name='theme-color' content='#060606'>";
		}
		if ($themeID == 5)
		{
			echo "
			<link rel='stylesheet' href='css/themes/united-21.2.2.css'>
			<meta name='theme-color' content='#772953'>";
		}
		if ($themeID == 6)
		{
			echo "
			<link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootswatch/{$set['bootstrap_version']}/cerulean/bootstrap.min.css'>
			<meta name='theme-color' content='#04519b'>
			<style>
			.default-theme .sidebar-wrapper {
				background-color: #04519b; 
			}
			</style>";
		}
		if ($themeID == 7)
		{
			echo "
			<link rel='stylesheet' href='css/themes/castle-21.2.1.css'>
			<meta name='theme-color' content='rgba(0, 0, 0, 0.8)'>";
		}
		if ($themeID == 8)
		{
			echo "
			<link rel='stylesheet' href='css/themes/sunset-21.2.1.css'>
			<meta name='theme-color' content='rgba(64, 0, 0, 0.8)'>";
		}
	}
	
	function getThemeNavbarColor($themeID)
	{
		if ($themeID == 2)
			return 'navbar-light bg-light';
		else
			return 'navbar-dark bg-dark';
	}
	
	function loadEssentialAssets()
	{
		cslog('log',"Essential assets loading now.");
		$this->loadCSS();
		$this->loadEarlyJS();
		cslog('log',"Essential assets loaded successfully.");
		
	}
	
	function loadCSS()
	{
		global $set;
		cslog('log',"CSS is loading.");
		echo "<link rel='stylesheet' href='css/game-{$set['game_css_version']}.css'>
				<link rel='stylesheet' href='https://seiyria.com/gameicons-font/css/game-icons.css'>
				<link rel='stylesheet' href='//malihu.github.io/custom-scrollbar/jquery.mCustomScrollbar.min.css'>";
		
	}
	
	function loadEarlyJS()
	{
		global $set;
		cslog('log',"Essential JS scripts are loading.");
		echo "<script src='https://ajax.googleapis.com/ajax/libs/jquery/{$set['jquery_version']}/jquery.min.js'></script>
		<script src='js/game-v{$set['game_js_version']}.js' async></script>";
	}
	
	function loadJS()
	{
		global $ir, $set;
		cslog('log',"JS is loading.");
		echo "<script src='https://cdn.jsdelivr.net/npm/popper.js@{$set['popper_version']}/dist/umd/popper.min.js'></script>
        <script src='https://stackpath.bootstrapcdn.com/bootstrap/{$set['bootstrap_version']}/js/bootstrap.min.js'></script>
		<script src='https://cdn.jsdelivr.net/gh/MasterGeneral156/chivalry-is-dead-game-cdn@1/js/register.min.js' defer></script>
		<script src='https://use.fontawesome.com/releases/v{$set['fontawesome_version']}/js/all.js'></script>
		<script src='js/underscore-min.js' async defer></script>
        <script src='https://cdn.rawgit.com/tonystar/bootstrap-hover-tabs/v{$set['bshover_tabs_version']}/bootstrap-hover-tabs.js'></script>
		<script async src='https://www.googletagmanager.com/gtag/js?id=UA-69718211-1'></script>
		<script>
		  window.dataLayer = window.dataLayer || [];
		  function gtag(){dataLayer.push(arguments);}
		  gtag('js', new Date());

		  gtag('config', 'UA-69718211-1');
		</script>";
		?>
		<script src="js/sidemenu.js"></script>
		<script src="https://malihu.github.io/custom-scrollbar/jquery.mCustomScrollbar.concat.min.js"></script>
		<script type="text/javascript">
            jQuery(function ($) {
            $("#close-sidebar").click(function() {
              $(".page-wrapper").removeClass("toggled");
				$.post('js/script/menu.php', { value: 1}, 
					function(returnedData){
						 console.log("Disabled sidebar.");
				});
			});
			$("#overlay").click(function() {
              $(".page-wrapper").removeClass("toggled");
				$.post('js/script/menu.php', { value: 1}, 
					function(returnedData){
						 console.log("Disabled sidebar via overlay.");
				});
			});
            $("#show-sidebar").click(function() {
              $(".page-wrapper").addClass("toggled");
			  $.post('js/script/menu.php', { value: 0}, 
					function(returnedData){
						 console.log("Enabled sidebar.");
				});
            });
        });	
        </script>
		<?php
	}
	
	function returnMetadata()
	{
		global $set;
		cslog('log',"Setting website metadata.");
		echo "<meta charset='utf-8'>
                <meta http-equiv='X-UA-Compatible' content='IE=edge'>
                <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>
				<meta name='author' content='{$set['WebsiteOwner']}'>
                <meta name='description' content='{$set['Website_Description']}'>
                <meta name='keywords' content='medieval europe, mmorpg, text rpg, rpg, multiplayer, game, video game, no download, mobile, free, chivalry is dead, cid'>
                <meta property='og:title' content='{$set['WebsiteName']}'/>
                <meta property='og:description' content='{$set['Website_Description']}'/>
                <meta property='og:image' content='https://res.cloudinary.com/dydidizue/image/upload/c_scale,h_512/v1520819749/logo.png'/>
                <link rel='shortcut icon' href='https://res.cloudinary.com/dydidizue/image/upload/c_scale,h_192/v1520819749/logo.png' type='image/x-icon'/>
				<!-- generics -->
				<link rel='icon' href='https://res.cloudinary.com/dydidizue/image/upload/c_scale,h_32/v1520819749/logo.png' sizes='32x32'>
				<link rel='icon' href='https://res.cloudinary.com/dydidizue/image/upload/c_scale,h_57/v1520819749/logo.png' sizes='57x57'>
				<link rel='icon' href='https://res.cloudinary.com/dydidizue/image/upload/c_scale,h_76/v1520819749/logo.png' sizes='76x76'>
				<link rel='icon' href='https://res.cloudinary.com/dydidizue/image/upload/c_scale,h_96/v1520819749/logo.png' sizes='96x96'>
				<link rel='icon' href='https://res.cloudinary.com/dydidizue/image/upload/c_scale,h_128/v1520819749/logo.png' sizes='128x128'>
				<link rel='icon' href='https://res.cloudinary.com/dydidizue/image/upload/c_scale,h_192/v1520819749/logo.png' sizes='192x192'>
				<link rel='icon' href='https://res.cloudinary.com/dydidizue/image/upload/c_scale,h_228/v1520819749/logo.png' sizes='228x228'>
				
				<!-- Android -->
				<link rel='shortcut icon' sizes='196x196' href=“https://res.cloudinary.com/dydidizue/image/upload/c_scale,h_196/v1520819749/logo.png'>

				<!-- iOS -->
				<link rel='apple-touch-icon' href='https://res.cloudinary.com/dydidizue/image/upload/c_scale,h_120/v1520819749/logo.png' sizes='120x120'>
				<link rel='apple-touch-icon' href='https://res.cloudinary.com/dydidizue/image/upload/c_scale,h_152/v1520819749/logo.png' sizes='152x152'>
				<link rel='apple-touch-icon' href='https://res.cloudinary.com/dydidizue/image/upload/c_scale,h_180/v1520819749/logo.png' sizes='180x180'>

				<!-- Windows 8 IE 10-->
				<meta name='msapplication-TileColor' content='#FFFFFF'>
				<meta name='msapplication-TileImage' content='https://res.cloudinary.com/dydidizue/image/upload/c_scale,h_144/v1520819749/logo.png'>

				<!— Windows 8.1 + IE11 and above —>
				<meta name='msapplication-config' content='assets/browserconfig.xml' />";
	}
	
	function loadNewsScroller()
	{
		global $db;
		$time = time();
		$paperads = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`news_id`) FROM `newspaper_ads` WHERE `news_end` > {$time}"));
		if ($paperads == 0)
		{
			$news="Welcome to Chivalry is Dead. <b>//</b> You may post an ad by clicking <a href='newspaper.php?action=buyad'>here</a>.";
		}
		else
		{
			$news='';
			$npq=$db->query("/*qc=on*/SELECT * FROM `newspaper_ads` WHERE `news_end` > {$time} ORDER BY `news_cost` ASC");
			while ($par=$db->fetch_row($npq))
				{
				    $par['news_text'] = str_replace("<br />", " ", $par['news_text']);
					$phrase = " " . parseUsername($par['news_owner']) . " [{$par['news_owner']}]: {$par['news_text']} <b>//</b>";
					$news.="{$phrase}";
				}
			$news.="<b>//END</b>";
		}
		echo "
		<div class='marquee'>
			<div class='text'>{$news}</div>
		</div><br />";
	}

    function userdata($ir, $dosessh = 1)
    {
        global $db, $userid, $api, $ir;
        $IP = $db->escape($_SERVER['REMOTE_ADDR']);
		$time=time();
		if ($ir['invis'] < time())
		{
			//Update the user as they browse the game.
			$db->query("UPDATE `users`
                    SET `laston` = {$_SERVER['REQUEST_TIME']}, 
                    `lastip` = '{$IP}' 
                    WHERE `userid` = {$userid}");
		}
		else
		{
			//Update the user as they browse the game.
			$db->query("UPDATE `users`
                    SET `lastip` = '{$IP}' 
                    WHERE `userid` = {$userid}");
		}
        
        //User's account does not have an email address.
        if (!$ir['email']) {
            global $domain;
            alert('info',"Incomplete Setup","Please be sure to add an email address to your account by clicking <a href='preferences.php?action=changeemail'>here</a>.",false);
        }
        //If the user's attacking is not stored in session.
        if (!isset($_SESSION['attacking'])) {
            $_SESSION['attacking'] = 0;
        }
        //If user does not end a fight correctly, take their XP and warn them.
        if ($dosessh && ($_SESSION['attacking'] || $ir['attacking'])) {
            $hosptime = Random(10, 20) + floor($ir['level'] / 2);
            $api->UserStatusSet($userid, 'infirmary', $hosptime, "Ran from a fight");
            alert("warning", "Uh Oh!", "For leaving your previous fight, you were placed in the Infirmary for {$hosptime}
            minutes, and lost all your experience.", false);
            $db->query("UPDATE `users` SET `xp` = 0, `attacking` = 0 WHERE `userid` = $userid");
            $_SESSION['attacking'] = 0;
			$_SESSION['attack_scroll'] = 0;
        }
    }

    function endpage()
    {
        global $db, $ir, $set, $userid, $api, $start;
        $query_extra = '';
        include('userinfo.php');
        include('marriage_perks.php');
		$this->loadJS();
		cslog('warn',"Main script has finished executing. Wrapping up now.");
        //Set mysqldebug in the URL to get query debugging as an admin.
    if (isset($_GET['mysqldebug']) && $ir['user_level'] == 'Admin')
    {
        ?>
        <pre class='pre-scrollable'>
                  <?php
                  var_dump($db->queries)
                  ?>
              </pre>
    <?php
    }
    ?>
        </div>
        </div>
        </div>
        <!-- /.container -->
        <br />
		</body>
        </html>
    <?php
    }
}
