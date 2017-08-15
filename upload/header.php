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
		global $ir,$set,$h,$lang,$db,$menuhide,$userid,$macropage,$api,$time;
        //Load the meta headers.
		?>
		<!DOCTYPE html>
		<html lang="en">
		<head>
			<center>
			<meta charset="utf-8">
			<meta http-equiv="X-UA-Compatible" content="IE=edge">
			<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
			<meta name="description" content="<?php echo $set['Website_Description']; ?>">
			<meta property="og:title" content="<?php echo $set['WebsiteName']; ?>"/>
			<meta property="og:description" content="<?php echo $set['Website_Description']; ?>" />
			<meta property="og:image" content="" />
			<link rel="shortcut icon" href="" type="image/x-icon" />
			<meta name="author" content="<?php echo $set['WebsiteOwner']; ?>">
			<?php echo "<title>{$set['WebsiteName']}</title>"; ?>
		</head>
		<?php
        //If the called script wants the menu hidden.
		if (empty($menuhide))
		{
            //Select count of user's unread messages.
			$ir['mail']=$db->fetch_single($db->query("SELECT COUNT(`mail_id`) FROM `mail` WHERE `mail_to` = {$ir['userid']} AND `mail_status` = 'unread'"));
			//Select count of user's unread notifications.
            $ir['notifications']=$db->fetch_single($db->query("SELECT COUNT(`notif_id`) FROM `notifications` WHERE `notif_user` = {$ir['userid']} AND `notif_status` = 'unread'"));
			?>
			<body>
				<!-- Navigation -->
                <nav class="navbar navbar-expand-lg navbar-light bg-light">
                    <a class="navbar-brand" href="index.php"><?php echo $set['WebsiteName']; ?></a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#CENGINENav" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="CENGINENav">
                        <ul class="navbar-nav mr-auto">
                            <li class="nav-item active">
                                <a class="nav-link" href="explore.php"><?php echo $lang['MENU_EXPLORE']; ?></a>
                            </li>
                        </ul>
                        <div class="my-2 my-lg-0">
                            <ul class="navbar-nav mr-auto">
                                <li class="nav-item">
                                    <a class="nav-link" href="inbox.php"><?php echo "{$lang['MENU_MAIL']} <span class='badge badge-pill badge-default'>{$ir['mail']}</span>"; ?></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="notifications.php"><?php echo "{$lang['MENU_EVENT']} <span class='badge badge-pill badge-default'>{$ir['notifications']}</span>"; ?></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="inventory.php"><?php echo $lang['MENU_INVENTORY']; ?></a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <?php
                                        //User has a display picture, lets show it!
                                        if ($ir['display_pic'])
                                        {
                                            echo"<img src='{$ir['display_pic']}' width='24' height='24'>";
                                        }
                                        echo" {$lang['GEN_GREETING']}, {$ir['username']}";
                                        ?>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                                        <a class="dropdown-item" href="profile.php?user=<?php echo "{$ir['userid']}"; ?>"><i class="fa fa-fw fa-user"></i> <?php echo $lang['MENU_PROFILE']; ?></a>
                                        <a class="dropdown-item" href="preferences.php?action=menu"><i class="fa fa-fw fa-gear"></i><?php echo $lang['MENU_SETTINGS']; ?></a>
                                        <?php
                                        //User is a staff member, so lets show the panel's link.
                                        if (in_array($ir['user_level'], array('Admin', 'Forum Moderator', 'Web Developer', 'Assistant')))
                                        {
                                            ?>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="staff/index.php"><i class="fa fa-fw fa fa-terminal"></i> <?php echo $lang['MENU_STAFF']; ?></a>
                                        <?php
                                        }
                                        ?>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="gamerules.php"><i class="fa fa-fw fa-server"></i> <?php echo $lang['MENU_RULES']; ?></a>
                                        <a class="dropdown-item" href="logout.php"><i class="fa fa-fw fa-power-off"></i> <?php echo $lang['MENU_LOGOUT']; ?></a>
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
                        alert('info',$lang['ERROR_INFO'],$lang['HDR_JS'],false);
                    ?>
				</noscript>
				<?php
				$IP=$db->escape($_SERVER['REMOTE_ADDR']);
				$ipq=$db->query("SELECT `ip_id` FROM `ipban` WHERE `ip_ip` = '{$IP}'");
                //User's IP is banned, so lets stop access.
				if ($db->num_rows($ipq) > 0)
				{
					alert('danger',$lang['ERROR_GENERIC'],$lang['HDR_IPREKT'],false);
					die($h->endpage());
				}
				$fed=$db->fetch_row($db->query("SELECT * FROM `fedjail` WHERE `fed_userid` = {$userid}"));
				alert('info',$lang['ERROR_INFO'],"{$lang['MENU_DONATE']} {$set['WebsiteName']}{$lang['MENU_DONATE2']}",false);
				//User's federal jail sentence is completed. Let them play again.
                if ($fed['fed_out'] < $time)
				{
					$db->query("UPDATE `users` SET `fedjail` = 0 WHERE `userid` = {$userid}");
					$db->query("DELETE FROM `fedjail` WHERE `fed_userid` = {$userid}");
				}
                //User is in federal jail. Stop their access.
				if ($ir['fedjail'] > 0)
				{
					alert('info',$lang['MENU_FEDJAIL'],"{$lang['MENU_FEDJAIL1']} " . TimeUntil_Parse($fed['fed_out']) . " {$lang['MENU_FEDJAIL2']} <b>{$fed['fed_reason']}</b>",false);
					die($h->endpage());
				}
                //Tell user when they have unread messages, when they do.
				if ($ir['mail'] > 0)
				{
					alert('info',$lang['MENU_UNREADMAIL1'],"{$lang['MENU_UNREADMAIL2']} {$ir['mail']} {$lang['MENU_UNREADMAIL3']} <a href='inbox.php'>{$lang["GEN_HERE"]}</a> {$lang['MENU_UNREADMAIL4']}",false);
				}
                //Tell user they have unread notifcations when they do.
				if ($ir['notifications'] > 0)
				{
					alert('info',$lang['MENU_UNREADNOTIF'],"{$lang['MENU_UNREADMAIL2']} {$ir['notifications']} {$lang['MENU_UNREADNOTIF1']} <a href='notifications.php'>{$lang["GEN_HERE"]}</a> {$lang['MENU_UNREADMAIL4']}",false);
				}
                //Tell user they have unread game announcements when they do.
				if ($ir['announcements'] > 0)
				{
					alert('info',$lang['MENU_UNREADANNONCE'],"{$lang['MENU_UNREADANNONCE1']} {$ir['announcements']} {$lang['MENU_UNREADANNONCE2']} <a href='announcements.php'>{$lang["GEN_HERE"]}</a>.",false);
				}
                //User is in the infirmary, tell them for how long.
				if ($api->UserStatus($ir['userid'],'infirmary'))
				{
					$InfirmaryOut=$db->fetch_single($db->query("SELECT `infirmary_out` FROM `infirmary` WHERE `infirmary_user` = {$ir['userid']}"));
					$InfirmaryRemain=TimeUntil_Parse($InfirmaryOut);
					alert('info',$lang['GEN_INFIRM'],"{$lang['MENU_INFIRMARY1']} {$InfirmaryRemain}.",false);
				}
                //User is in the dungeon, tell them how long.
				if ($api->UserStatus($ir['userid'],'dungeon'))
				{
					$DungeonOut=$db->fetch_single($db->query("SELECT `dungeon_out` FROM `dungeon` WHERE `dungeon_user` = {$ir['userid']}"));
					$DungeonRemain=TimeUntil_Parse($DungeonOut);
					alert('info',$lang["GEN_DUNG"],"{$lang['MENU_DUNGEON1']} {$DungeonRemain}.",false);
				}
                //User needs to reverify with reCaptcha
				if (($ir['last_verified'] < ($time-$set['Revalidate_Time'])) || ($ir['need_verify'] == 1))
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
						echo "{$lang['RECAPTCHA_INFO']}"; ?>
						<form action='macro.php' method='post'>
							<center>
								<div class='g-recaptcha' data-theme='light' data-sitekey='<?php echo $set['reCaptcha_public']; ?>'></div>
							</center>
							<input type='hidden' value='<?php echo $macropage; ?>' name='page'>
							<input type='submit' value="<?php echo $lang['RECAPTCHA_BTN']; ?>" class="btn btn-secondary" data-dismiss="modal">
						</form>
						<?php
						die($h->endpage());
					}
				}
        //Set user's timezone.
		date_default_timezone_set($ir['timezone']);  
	}
}
	function userdata($ir, $lv, $fm, $cm, $dosessh = 1)
    {
		global $db, $c, $userid, $set, $lang, $api;
		$IP = $db->escape($_SERVER['REMOTE_ADDR']);
        //Update the user as they browse the game.
		$db->query("UPDATE `users` 
                    SET `laston` = {$_SERVER['REQUEST_TIME']}, 
                    `lastip` = '{$IP}' 
                    WHERE `userid` = {$userid}");
		//User's account does not have an email address.
        if (!$ir['email'])
        {
            global $domain;
            die("<body>{$lang['HDR_REKT']}{$domain} {$lang['HDR_REKT1']}");
        }
        //If the user's attacking is not stored in session.
        if (!isset($_SESSION['attacking']))
        {
            $_SESSION['attacking'] = 0;
        }
        //If user does not end a fight correctly, take their XP and warn them.
        if ($dosessh && ($_SESSION['attacking'] || $ir['attacking']))
        {
           alert("warning",$lang['ERROR_GENERIC'],$lang['MENU_XPLOST'],false);
            $db->query("UPDATE `users` SET `xp` = 0, `attacking` = 0 WHERE `userid` = $userid");
            $_SESSION['attacking'] = 0;
        }
		$townguild = $db->fetch_single($db->query("SELECT `town_guild_owner` FROM `town` WHERE `town_id` = {$ir['location']}"));
		//User is in a guild, and the guild has control of the current town.
        if (($townguild == $ir['guild']) && ($townguild > 0) && ($ir['guild'] > 0))
		{
			$encounterchance=Random(1,1000);
            //User gets robbed!
			if ($encounterchance == 1)
			{
				$result=Random(1,2);
				if ($result == 1)
				{
					$infirmtime=Random(20,60);
					$api->UserStatusSet($userid,"infirmary",$infirmtime,"Attacked by Bandits");
					$api->GameAddNotification($userid,"While randomly walking about in this town, you were attacked by a group of bandits as a message to your guild leader.");
				}
				if ($result == 2)
				{
					$api->GameAddNotification($userid,"While randomly walking about in this town, you successfully fended off a group of bandits.");
				}
				if ($result == 3)
				{
					//Get saved... API call not done yet so meh.
				}
			}
		}
        $d = "";
        $u = $ir['username'];
        if ($ir['vip_days'])
        {
            $u = "<span style='color: red;'>{$ir['username']}</span>";
            $d = "<img src='donator.gif' alt='VIP: {$ir['vip_days']} Days Left' title='VIP: {$ir['vip_days']} Days Left' />";
        }
        global $staffpage;
	}
	function endpage()
    {
        global $db, $ir, $lang, $StartTime;
        $query_extra = '';
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
			<!-- CSS -->
			<?php
            //User has chosen the day theme.
			if ($ir['theme'] == 1)
			{
				?>
                    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css">
                    <meta name="theme-color" content="#e7e7e7">
				<?php
			}
            //User has chosen the night theme.
			else
			{
				?> 
					<link rel="stylesheet" href="css/bootstrap-purple-min.css">
					<meta name="theme-color" content="#2d135d">
				<?php
			}
			?>
            <link rel="stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.min.css">
            <link rel="stylesheet" href="css/bs2.css">
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
            <!-- jQuery Version 3.2.1 -->
            <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>

            <!-- Bootstrap Core JavaScript -->
            <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
            <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js"></script>
			
			<!-- Other JavaScript -->
			<script src="js/game.js" async defer></script>
			<script src='https://www.google.com/recaptcha/api.js' async defer></script>
			<script src="https://cdn.rawgit.com/tonystar/bootstrap-hover-tabs/v3.1.1/bootstrap-hover-tabs.js" async defer></script>
		</body>
			<footer>
				<p>
					<br />
					<?php 
                    //Print copyright info, Chivalry Engine info, and current time.
					echo "<hr />
					{$lang['MENU_TIN']}  
						" . date('F j, Y') . " " . date('g:i:s a') . "<br />
					{$lang['MENU_OUT']}";
					?>
					&copy; <?php echo date("Y");
					echo"<br/>{$db->num_queries} {$lang['MENU_QE']}.{$query_extra}<br />";
					//Profile page loading putting profile in the URL GET.
					if (isset($_GET['profile']))
					{
						$ms=microtime()-$StartTime;
						echo "{$lang['MENU_SCRIPTTIME']} {$ms} {$lang['MENU_SCRIPTTIME1']}";
					}
					?>
				</p>
			</footer>
		</html>
		<?php
	}
}
