<?php

/*
	File: staff/sheader.php
	Created: 6/1/2016 at 6:06PM Eastern Time
	Info: Loads the template, CSS, JS, etc. inside the staff panel.
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine/
*/

class headers
{
    function startheaders()
    {
        global $ir, $set, $h, $db, $menuhide, $userid, $api, $time, $sound;
		cslog('log',"Loading headers for {$set['WebsiteName']}");
        //Load the meta headers.
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
                <center>
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
				$this->returnMetadata();
				$this->loadUserTheme($ir['theme']);
				$hdr=$this->getThemeNavbarColor($ir['theme']);
				$sound->loadSystem();
				cslog('warn',"Main assets have loaded successfully. Log entries after this point were created by the game or related modules, not the base engine.");
				?>
				</head>
        <?php
        if (empty($menuhide)) {
            $ir['mail'] = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`mail_id`) FROM `mail` WHERE `mail_to` = {$ir['userid']} AND `mail_status` = 'unread'"));
            $ir['notifications'] = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`notif_id`) FROM `notifications` WHERE `notif_user` = {$ir['userid']} AND `notif_status` = 'unread'"));
            
			?>
            <body>
            <!-- Navigation -->
            <nav class="navbar navbar-expand-lg fixed-top <?php echo $hdr; ?>">
                <a class="navbar-brand" href="index.php">
					<?php 
						echo "<img src='https://res.cloudinary.com/dydidizue/image/upload/v1520819511/logo-optimized.png' width='30' height='30' alt=''>
						{$set['WebsiteName']}"; 
					?>
				</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#CENGINENav"
                        aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="CENGINENav">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="../index.php"><?php echo "Back to Game"; ?></a>
                        </li>
                    </ul>
                    <div class="my-2 my-lg-0">
                        <ul class="navbar-nav mr-auto">
                            <li class="nav-item">
                                <a class="nav-link"
                                   href="../inbox.php"><?php echo "<i
                                        class='fa fa-fw fa-inbox'></i> Inbox <span class='badge badge-pill badge-primary'>{$ir['mail']}</span>"; ?></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link"
                                   href="../notifications.php"><?php echo "<i
                                        class='fas fa-fw fa-bell'></i> Notifications <span class='badge badge-pill badge-primary'>{$ir['notifications']}</span>"; ?></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="../inventory.php"><?php echo "<i
                                        class='fas fa-fw fa-briefcase'></i> Inventory"; ?></a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink"
                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <?php
                                    //User has a display picture, lets show it!
                                    if ($ir['display_pic']) {
                                        echo "<img src='{$ir['display_pic']}' width='30' height='30'>";
                                    }
                                    echo " Hello, {$ir['username']}!";
                                    ?>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                                    <a class="dropdown-item"
                                       href="../profile.php?user=<?php echo "{$ir['userid']}"; ?>"><i
                                            class="fa fa-fw fa-user"></i> <?php echo "Profile"; ?></a>
                                    <a class="dropdown-item" href="../preferences.php?action=menu"><i
                                            class="fas fa-spin fa-fw fa-cog"></i><?php echo "Preferences"; ?></a>
                                    <?php
                                    //User is a staff member, so lets show the panel's link.
                                    if (in_array($ir['user_level'], array('Admin', 'Forum Moderator', 'Web Developer', 'Assistant'))) {
                                        ?>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="index.php"><i
                                                class="fa fa-fw fa fa-terminal"></i> <?php echo "Staff Panel"; ?></a>
                                    <?php
                                    }
                                    ?>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="../gamerules.php"><i
                                            class="fa fa-fw fa-server"></i> <?php echo "Game Rules"; ?></a>
                                    <a class="dropdown-item" href="../logout.php"><i
                                            class="fas fa-sign-out-alt"></i> <?php echo "Log Out"; ?></a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <div class="container">
            <div class="row">
            <div class="col-sm-12 text-center">
            <noscript>
                <?php alert('info', "Information!", "Please enable Javascript.", false); ?>
            </noscript>
            <?php
			date_default_timezone_set($set['game_time']);
            $IP = $db->escape($_SERVER['REMOTE_ADDR']);
            $ipq = $db->query("/*qc=on*/SELECT `ip_id` FROM `ipban` WHERE `ip_ip` = '{$IP}'");
            if ($db->num_rows($ipq) > 0) {
                alert('danger', "Uh Oh!", "You have been IP Banned. Please contact support.", false);
                die($h->endpage());
            }
            $fed = $db->fetch_row($db->query("/*qc=on*/SELECT * FROM `fedjail` WHERE `fed_userid` = {$userid}"));
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
        }
    }
	
	function showSocialAlerts()
	{
		global $ir;
		echo "<div class='row'>";
		if ($ir['mail'] > 0) 
		{
			echo "<div class='col-sm'>";
				alert('info', "", "You have {$ir['mail']} unread messages.", true, 'inbox.php', "View");
			echo "</div>";
        }
        //Tell user they have unread notifcations when they do.
        if ($ir['notifications'] > 0) 
		{
			echo "<div class='col-sm'>";
				alert('info', "", "You have {$ir['notifications']} unread notifications.", true, 'notifications.php', "View");
			echo "</div>";
        }
		//Tell user they have unread game announcements when they do.
		if ($ir['announcements'] > 0) 
		{
			echo "<div class='col-sm'>";
				alert('info', "", "You have {$ir['announcements']} unread announcements.", true, 'announcements.php', "View");
			echo "</div>";
		}
		echo "</div>";
	}
	function showStatusAlerts()
	{
		global $ir, $api, $db;
		echo "<div class='row'>";
		if ($api->UserStatus($ir['userid'], 'infirmary')) 
		{
			$InfirmaryOut = $db->fetch_single($db->query("/*qc=on*/SELECT `infirmary_out` FROM `infirmary` WHERE `infirmary_user` = {$ir['userid']}"));
			$InfirmaryRemain = TimeUntil_Parse($InfirmaryOut);
			echo "<div class='col-sm'>";
				alert('info', "", "You are in the Infirmary for {$InfirmaryRemain}.", true, "quickuse.php?infirmary", "Use " . parseInfirmaryItemName($ir['iitem']));
			echo "</div>";
		}
		//User is in the dungeon, tell them how long.
		if ($api->UserStatus($ir['userid'], 'dungeon')) 
		{
			$DungeonOut = $db->fetch_single($db->query("/*qc=on*/SELECT `dungeon_out` FROM `dungeon` WHERE `dungeon_user` = {$ir['userid']}"));
			$DungeonRemain = TimeUntil_Parse($DungeonOut);
			echo "<div class='col-sm'>";
				alert('info', "", "You are in the dungeon for {$DungeonRemain}.", true, "quickuse.php?dungeon", "Use " . parseDungeonItemName($ir['ditem']));
			echo "</div>";
		}
		echo "</div>";
	}
	
	function loadUserTheme($themeID)
	{
		global $set;
		cslog('log',"User Theme ID: {$themeID}.");
		if ($themeID == 1)
		{
			echo "
			<link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/{$set['bootstrap_version']}/css/bootstrap.min.css'>
			<meta name='theme-color' content='#333'>";
		}
		if ($themeID == 2)
		{
			echo "
			<link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootswatch/{$set['bootstrap_version']}/darkly/bootstrap.min.css'>
			<meta name='theme-color' content='#303030'>";
		}
		if ($themeID == 3)
		{
			echo "
			<link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootswatch/{$set['bootstrap_version']}/slate/bootstrap.min.css'>
			<meta name='theme-color' content='#272B30'>";
		}
		if ($themeID == 4)
		{
			echo "
			<link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootswatch/{$set['bootstrap_version']}/cyborg/bootstrap.min.css'>
			<meta name='theme-color' content='#060606'>";
		}
		if ($themeID == 5)
		{
			echo "
			<link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootswatch/{$set['bootstrap_version']}/united/bootstrap.min.css'>
			<meta name='theme-color' content='#772953'>";
		}
		if ($themeID == 6)
		{
			echo "
			<link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootswatch/{$set['bootstrap_version']}/cerulean/bootstrap.min.css'>
			<meta name='theme-color' content='#04519b'>";
		}
		if ($themeID == 7)
		{
			echo "
			<link rel='stylesheet' href='../css/castle.css'>
			<meta name='theme-color' content='rgba(0, 0, 0, .8)'>";
		}
		if ($themeID == 8)
		{
			echo "
			<link rel='stylesheet' href='../css/bright-castle.css'>
			<meta name='theme-color' content='rgba(0, 0, 0, .8)'>";
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
		global $ir, $set;
		cslog('log',"Essential assets loading now.");
		$this->loadCSS();
		$this->loadEarlyJS();
		cslog('log',"Essential assets loaded successfully.");
		
	}
	
	function loadCSS()
	{
		global $set;
		cslog('log',"CSS is loading.");
		echo "<link rel='stylesheet' href='../css/game-{$set['game_css_version']}.css'>
				<link rel='stylesheet' href='https://seiyria.com/gameicons-font/css/game-icons.css'>";
		
	}
	
	function loadEarlyJS()
	{
		global $set;
		cslog('log',"Essential JS scripts are loading.");
		echo "<script src='https://ajax.googleapis.com/ajax/libs/jquery/{$set['jquery_version']}/jquery.min.js'></script>
		<script src='../js/game-v{$set['game_js_version']}.js' async></script>";
	}
	
	function loadJS()
	{
		global $ir, $set;
		cslog('log',"JS is loading.");
		echo "<script src='https://cdn.jsdelivr.net/npm/popper.js@{$set['popper_version']}/dist/umd/popper.min.js'></script>
        <script src='https://stackpath.bootstrapcdn.com/bootstrap/{$set['bootstrap_version']}/js/bootstrap.min.js'></script>
		<script src='https://cdn.jsdelivr.net/gh/MasterGeneral156/chivalry-is-dead-game-cdn@1/js/register.min.js' defer></script>
		<script defer src='https://use.fontawesome.com/releases/v{$set['fontawesome_version']}/js/all.js'></script>
        <script src='https://cdn.rawgit.com/tonystar/bootstrap-hover-tabs/v{$set['bshover_tabs_version']}/bootstrap-hover-tabs.js' async defer></script>";
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
                <meta property='og:title' content='{$set['WebsiteName']}'/>
                <meta property='og:description' content='{$set['Website_Description']}'/>
                <meta property='og:image' content='https://res.cloudinary.com/dydidizue/image/upload/c_scale,h_192/v1520819749/logo.png'/>
                <link rel='shortcut icon' href='https://res.cloudinary.com/dydidizue/image/upload/c_scale,h_192/v1520819749/logo.png' type='image/x-icon'/>
				<link rel='icon' sizes='192x192' href='https://res.cloudinary.com/dydidizue/image/upload/c_scale,h_192/v1520819749/logo.png'>
				<link rel='icon' sizes='128x128' href='https://res.cloudinary.com/dydidizue/image/upload/c_scale,h_128/v1520819749/logo.png'>";
	}

    function userdata($ir, $dosessh = 1)
    {
        global $db, $userid;;
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
        if (!$ir['email']) {
            global $domain;
            die("<body>Your account is likely broken. Please contact admin@{$domain} and include your User ID.");
        }
        if (!isset($_SESSION['attacking'])) {
            $_SESSION['attacking'] = 0;
        }
        if ($dosessh && ($_SESSION['attacking'] || $ir['attacking'])) {
            $hosptime = Random(10, 50);
            $api->UserStatusSet($userid, 'infirmary', $hosptime, "Ran from a fight");
            alert("warning", "Uh Oh!", "For leaving your previous fight, you were placed in the Infirmary for {$hosptime}
            minutes, and lost all your experience.", false);
            $db->query("UPDATE `users` SET `xp` = 0, `attacking` = 0 WHERE `userid` = $userid");
            $_SESSION['attacking'] = 0;
        }
    }

    function endpage()
    {
        global $db, $ir, $set, $userid, $start;
        $query_extra = '';
		if (isset($_GET['mysqldebug']) && $ir['user_level'] == 'Admin')
		{
			?>
			<pre class='pre-scrollable'> <?php var_dump($db->queries) ?> </pre> <?php
		}
    ?>
        </div>
        </div>
        <!-- /.row -->

        </div>
        <!-- /.container -->
        <!-- jQuery Version 3.3.1 -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

        <!-- Bootstrap Core JavaScript -->
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>

        <!-- Other JavaScript -->
        <script src="https://cdn.jsdelivr.net/gh/MasterGeneral156/chivalry-is-dead-game-cdn@1/js/game-v1.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/gh/MasterGeneral156/chivalry-is-dead-game-cdn@1/js/register.min.js" async defer></script>
		<script defer src="https://use.fontawesome.com/releases/v5.0.10/js/all.js"></script>
        <script src='https://www.google.com/recaptcha/api.js' async defer></script>
        <script src="https://cdn.rawgit.com/tonystar/bootstrap-hover-tabs/v3.1.1/bootstrap-hover-tabs.js" async defer></script>
		<script src="https://cdn.jsdelivr.net/gh/MasterGeneral156/chivalry-is-dead-game-cdn@1/js/clock.min.js"></script>
        <footer class='footer'>
            <div class='container'>
				<span>
                <?php
                //Print copyright info, Chivalry Engine info, and current time.
                echo "<hr />
					Time is now " . date('l, F j, Y g:i:s a') . "<br />
					{$set['WebsiteName']} &copy; " . date("Y") . " {$set['WebsiteOwner']}. Game source viewable on <a href='https://github.com/MasterGeneral156/chivalry-engine/tree/chivalry-is-dead-game'>Github</a>.<br />";
                if ($ir['user_level'] == 'Admin' || $ir['user_level'] == 'Web Developer')
                    echo "{$db->num_queries} Queries Executed.{$query_extra}<br />";
				if ($ir['vip_days'] == 0)
				{
					include('../ads/ad_header.php');
				}
				include('../forms/include_end.php');
                ?>
				</span>
            </div>
        </footer>
		</body>
        </html>
    <?php
    }
}
