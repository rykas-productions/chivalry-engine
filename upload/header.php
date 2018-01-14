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
    function startheaders()
    {
        global $ir, $set, $h, $db, $menuhide, $userid, $macropage, $api, $time;
        //Load the meta headers.
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <center>
				<script src="https://use.fontawesome.com/releases/v5.0.4/js/all.js"></script>
                <meta charset="utf-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
                <meta name="description" content="<?php echo $set['Website_Description']; ?>">
                <meta property="og:title" content="<?php echo $set['WebsiteName']; ?>"/>
                <meta property="og:description" content="<?php echo $set['Website_Description']; ?>"/>
                <meta property="og:image" content="assets/img/logo.png"/>
                <link rel="shortcut icon" href="assets/img/logo.png" type="image/x-icon"/>
                <meta name="author" content="<?php echo $set['WebsiteOwner']; ?>">
                <?php 
					echo "<title>{$set['WebsiteName']}</title>";
				?>
				<link rel="stylesheet" href="css/game-v1.2.min.css">
				<link rel="stylesheet" href="css/game-icons.css">
				<?php
				if ($ir['theme'] == 1)
				{
					?>
					<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/css/bootstrap.min.css">
					<meta name="theme-color" content="#343a40">
					<?php
					$hdr='navbar-dark bg-dark';
				}
				if ($ir['theme'] == 2)
				{
					?>
					<link rel="stylesheet" href="css/darkly.bs4.b2.min.css">
					<meta name="theme-color" content="#303030">
					<?php
					$hdr='navbar-light bg-light';
				}
				if ($ir['theme'] == 3)
				{
					?>
					<link rel="stylesheet" href="https://bootswatch.com/4/superhero/bootstrap.min.css">
					<meta name="theme-color" content="#4E5D6C">
					<?php
					$hdr='navbar-dark bg-dark';
				}
				if ($ir['theme'] == 4)
				{
					?>
					<link rel="stylesheet" href="https://bootswatch.com/4/slate/bootstrap.min.css">
					<meta name="theme-color" content="#272B30">
					<?php
					$hdr='navbar-dark bg-dark';
				}
				if ($ir['theme'] == 5)
				{
					?>
					<link rel="stylesheet" href="https://bootswatch.com/4/cerulean/bootstrap.min.css">
					<meta name="theme-color" content="#04519b">
					<?php
					$hdr='navbar-dark bg-dark';
				}
				if ($ir['theme'] == 6)
				{
					?>
					<link rel="stylesheet" href="https://bootswatch.com/4/minty/bootstrap.min.css">
					<meta name="theme-color" content="#78C2AD">
					<?php
					$hdr='navbar-dark bg-primary';
				}
				if ($ir['theme'] == 7)
				{
					?>
					<link rel="stylesheet" href="https://bootswatch.com/4/united/bootstrap.min.css">
					<meta name="theme-color" content="#772953">
					<?php
					$hdr='navbar-dark bg-dark';
				}
				if ($ir['theme'] == 8)
				{
					?>
					<link rel="stylesheet" href="https://bootswatch.com/4/cyborg/bootstrap.min.css">
					<meta name="theme-color" content="#060606">
					<?php
					$hdr='navbar-dark bg-dark';
				}
				setcookie('theme', $ir['theme']);
				?>
				</head>
    <?php
    //If the called script wants the menu hidden.
    if (empty($menuhide))
    {
    //Select count of user's unread messages.
    $ir['mail'] = $db->fetch_single($db->query("SELECT COUNT(`mail_id`) FROM `mail` WHERE `mail_to` = {$ir['userid']} AND `mail_status` = 'unread'"));
    //Select count of user's unread notifications.
    $ir['notifications'] = $db->fetch_single($db->query("SELECT COUNT(`notif_id`) FROM `notifications` WHERE `notif_user` = {$ir['userid']} AND `notif_status` = 'unread'"));
    ?>
        <body>
        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg fixed-top <?php echo $hdr; ?>">
            <a class="navbar-brand" href="index.php">
					<?php 
						echo "<img src='assets/img/logo-optimized.png' width='30' height='30' alt=''>
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
                        <?php
                        if ($api->UserStatus($userid,'dungeon') || $api->UserStatus($userid,'infirmary'))
                        {
                            ?><a class="nav-link" href="forums.php"><?php echo "Forums"; ?></a><?php
                        }
                        else
                        {
                            ?><a class="nav-link" href="explore.php"><?php echo "<i
                                        class='fa fa-fw fa-compass fa-spin'></i> Explore"; ?></a><?php
                        }
                        ?>
                    </li>
                </ul>
                <div class="my-2 my-lg-0">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item">
                            <a class="nav-link"
                               href="inbox.php"><?php echo "<i
                                        class='fas fa-fw fa-inbox'></i> Inbox <span class='badge badge-pill badge-primary'>{$ir['mail']}</span>"; ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link"
                               href="notifications.php"><?php echo "<i
                                        class='fas fa-fw fa-globe'></i> Notifications <span class='badge badge-pill badge-primary'>{$ir['notifications']}</span>"; ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="inventory.php"><?php echo "<i
                                        class='game-icon game-icon-knapsack'></i> Inventory"; ?></a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <?php
                                //User has a display picture, lets show it!
                                if ($ir['display_pic']) {
                                    echo "<img src='{$ir['display_pic']}' width='24' height='24'>";
                                }
                                echo " Hello, {$ir['username']}!";
                                ?>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                                <a class="dropdown-item" href="profile.php?user=<?php echo "{$ir['userid']}"; ?>"><i
                                        class="fas fa-fw fa-user"></i> <?php echo "Profile"; ?></a>
                                <a class="dropdown-item" href="preferences.php?action=menu"><i
                                        class="fas fa-spin fa-fw fa-cog"></i><?php echo "Preferences"; ?></a>
                                <?php
                                //User is a staff member, so lets show the panel's link.
                                if (in_array($ir['user_level'], array('Admin', 'Forum Moderator', 'Web Developer', 'Assistant'))) {
                                    ?>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="staff/index.php"><i
                                            class="fas fa-fw fa fa-terminal"></i> <?php echo "Staff Panel"; ?></a>
                                <?php
                                }
								if ($ir['vip_days'] > 0)
								{
									?>
										<div class="dropdown-divider"></div>
										<a class="dropdown-item" href="friends.php"><i
											class="far fa-fw fa-smile"></i> <?php echo "Friends"; ?></a>
										<a class="dropdown-item" href="enemy.php"><i
											class="far fa-fw fa-frown"></i> <?php echo "Enemies"; ?></a>
									<?php
								}
                                ?>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="gamerules.php"><i
                                        class="fas fa-fw fa-server"></i> <?php echo "Game Rules"; ?></a>
                                <a class="dropdown-item" href="logout.php"><i
                                        class="fas fa-fw fa-power-off"></i> <?php echo "Logout"; ?></a>
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
            <?php
            //User doesn't have javascript turned on, so lets tell them.
            alert('info', "Uh Oh!", "Please enable Javascript. Many features of the game will not work without it.", false);
            ?>
        </noscript>
    <?php
    $IP = $db->escape($_SERVER['REMOTE_ADDR']);
    $ipq = $db->query("SELECT `ip_id` FROM `ipban` WHERE `ip_ip` = '{$IP}'");
    //User's IP is banned, so lets stop access.
    if ($db->num_rows($ipq) > 0) {
        alert('danger', "Uh Oh!", "You have been IP banned.", false);
        die($h->endpage());
    }
    $fed = $db->fetch_row($db->query("SELECT * FROM `fedjail` WHERE `fed_userid` = {$userid}"));
    $votecount=$db->fetch_single($db->query("SELECT COUNT(`voted`) FROM `votes` WHERE `userid` = {$userid}"));
    if ($votecount < 4) {
        echo "<b><a href='vote.php' class='text-success'>[Vote for {$set['WebsiteName']}<span class='hidden-sm-down'> at various Voting Websites and be rewarded</span>.]</a></b><br />";
    }
    echo "<b><a href='donator.php' class='text-danger'>[Donate to {$set['WebsiteName']}.<span class='hidden-sm-down'> Packs start at $1 and you receive tons of benefits.</span>]</a></b><br />";
    if ($ir['protection'] > time())
	{
		echo "<b><span class='text-info'>You have protection for the next " . TimeUntil_Parse($ir['protection']) . ".</span></b><br />";
	}

	
	//User's federal jail sentence is completed. Let them play again.
    if ($fed['fed_out'] < $time) {
        $db->query("UPDATE `users` SET `fedjail` = 0 WHERE `userid` = {$userid}");
        $db->query("DELETE FROM `fedjail` WHERE `fed_userid` = {$userid}");
    }
    //User is in federal jail. Stop their access.
    if ($ir['fedjail'] > 0) {
		$lasthour=time()-3600;
		$fq2=$db->query("SELECT * FROM `fedjail_appeals` WHERE `fja_user` = {$userid} AND `fja_time` >= {$lasthour} LIMIT 1");
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
		$fq=$db->query("SELECT * FROM `fedjail_appeals` WHERE `fja_user` = {$userid} ORDER BY `fja_time` ASC");
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
    if ($ir['disable_alerts'] == 0) {
        //Tell user when they have unread messages, when they do.
        if ($ir['mail'] > 0) {
            alert('info', "New Mail!", "You have {$ir['mail']} unread messages.", true, 'inbox.php', "View Inbox");
        }
        //Tell user they have unread notifcations when they do.
        if ($ir['notifications'] > 0) {
            alert('info', "New Notifications!", "You have {$ir['notifications']} unread notifications.", true, 'notifications.php', "View Notifications");
        }
    }
    //Tell user they have unread game announcements when they do.
    if ($ir['announcements'] > 0) {
        alert('info', "New Announcements!", "You have {$ir['announcements']} unread announcements.", true, 'announcements.php', "View Announcements");
    }
    //User is in the infirmary, tell them for how long.
    if ($api->UserStatus($ir['userid'], 'infirmary')) {
        $InfirmaryOut = $db->fetch_single($db->query("SELECT `infirmary_out` FROM `infirmary` WHERE `infirmary_user` = {$ir['userid']}"));
        $InfirmaryRemain = TimeUntil_Parse($InfirmaryOut);
        alert('info', "Unconscious!", "You are in the Infirmary for the next {$InfirmaryRemain}.", true, "quickuse.php?infirmary", "Use Item");
    }
    //User is in the dungeon, tell them how long.
    if ($api->UserStatus($ir['userid'], 'dungeon')) {
        $DungeonOut = $db->fetch_single($db->query("SELECT `dungeon_out` FROM `dungeon` WHERE `dungeon_user` = {$ir['userid']}"));
        $DungeonRemain = TimeUntil_Parse($DungeonOut);
        alert('info', "Locked Up!", "You are in the dungeon for the next {$DungeonRemain}.", true, "quickuse.php?dungeon", "Use Item");
    }
	//RNG for experience token? O.o
	$xprng=Random(1,100);
	if ($xprng == 56 && $ir['artifacts'] != 4)
	{
		if (($ir['artifact_time']) < time() - Random(270,360))
		{
			alert("info","Artifact!","While wondering around, you find a small artifact laying on the ground. Maybe you should take it to the Blacksmith Smeltery to find out what you can do with it?",false);
			$api->UserGiveItem($userid,94,1);
			$db->query("UPDATE `user_settings` SET `artifacts` = `artifacts` + 1, `artifact_time` = " . time() . " WHERE `userid` = {$userid}");
		}
	}
	$luckrng=Random(1,250);
	if ($luckrng == 160)
	{
		if (($ir['luck'] > 50) && ($ir['luck'] < 150))
		{
			$thisrng=Random(-5,5);
			$db->query("UPDATE `userstats` SET `luck` = `luck` + ({$thisrng}) WHERE `userid` = {$userid}");
			$api->GameAddNotification($userid,"While walking around the kingdom, your luck has changed by {$thisrng}%.");
		}
	}
    //User needs to reverify with reCaptcha
    if (($ir['last_verified'] < ($time - $set['Revalidate_Time'])) || ($ir['need_verify'] == 1))
    {
		//ReCaptcha public or private key(s) are unspecifed in the game settings.
		if (empty($set['reCaptcha_public']) || empty($set['reCaptcha_private']))
		{
		?>
			<script>alert('Please add the reCaptcha private and public keys.');</script>
		<?php
		die($h->endpage());
		}
    //Script calls for reCaptcha to be loaded.
    if (isset($macropage))
    {
    //Set User to need verified.
    $db->query("UPDATE `users` SET `need_verify` = 1 WHERE `userid` = {$userid}");
    echo "This is a needed evil. Please confirm you are not a bot."; ?>
        <form action='macro.php' method='post'>
            <center>
                <div class='g-recaptcha' data-theme='light'
                     data-sitekey='<?php echo $set['reCaptcha_public']; ?>'></div>
            </center>
            <input type='hidden' value='<?php echo $macropage; ?>' name='page'>
            <input type='submit' value="<?php echo "Confirm"; ?>" class="btn btn-primary" data-dismiss="modal">
        </form>
        <?php
        die($h->endpage());
    }
    }
        //Set user's timezone.
        date_default_timezone_set("America/New_York");
    }
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
            die("<body>Your account is broken. Please contact admin@{$domain} for assistance.");
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
        global $db, $ir, $StartTime, $set;
        $query_extra = '';
		if ($ir['analytics'] == 1)
			include('analytics.php');
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
        <!-- /.row -->

        </div>
        <!-- /.container -->
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.min.css">
        
        <!-- jQuery Version 3.2.1 -->
        <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>

        <!-- Bootstrap Core JavaScript -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/js/bootstrap.min.js"></script>

        <!-- Other JavaScript -->
        <script src="js/game.js"></script>
        <script src='https://www.google.com/recaptcha/api.js' async defer></script>
        <script src="https://cdn.rawgit.com/tonystar/bootstrap-hover-tabs/v3.1.1/bootstrap-hover-tabs.js" async defer></script>
		<script type="text/javascript" src="js/clock.min.js"></script>
		<script type="text/javascript"> 
		  $(document).ready(function(){ 
			customtimestamp = parseInt($("#jqclock").data("time"));
			$("#jqclock").clock({"langSet":"en","timestamp":customtimestamp,"timeFormat":" g:i:s a"}); 
		  }); 
		</script> 
        
        <footer class='footer'>
            <div class='container'>
				<span>
                <?php
				$timestamp=time()-18000;
                //Print copyright info, Chivalry Engine info, and current time.
                echo "<hr />
					Time is now <span id='jqclock' class='jqclock' data-time='{$timestamp}'>" . date('l, F j, Y g:i:s a') . "</span><br />
					{$set['WebsiteName']} &copy; " . date("Y") . " {$set['WebsiteOwner']}.<br />";
                if ($ir['user_level'] == 'Admin' || $ir['user_level'] == 'Web Developer')
                    echo "{$db->num_queries} Queries Executed.{$query_extra}<br />";
                //Profile page loading putting profile in the URL GET.
                if (isset($_GET['profile'])) {
                    $ms = microtime() - $StartTime;
                    echo "Page loaded in {$ms} miliseconds.";
                }
				if ($ir['vip_days'] == 0)
				{
					include('ads/ad_header.php');
				}
                ?>
				</span>
            </div>
        </footer>
		</body>
        </html>
    <?php
    }
}
