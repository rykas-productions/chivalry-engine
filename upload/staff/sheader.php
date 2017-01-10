<?php
/*
	File: staff/sheader.php
	Created: 6/1/2016 at 6:06PM Eastern Time
	Info: Loads the template, CSS, JS, etc. inside the staff panel.
	Author: TheMasterGeneral
	Website: http://mastergeneral156.pcriot.com/
*/
class headers
{

    function startheaders()
    {
		global $ir, $set, $lang, $db;
		?>
<!DOCTYPE html>
<html lang="en">
	<head>
			<?php echo "<title>{$set['WebsiteName']}</title>"; ?>
			<center>
			<meta charset="utf-8">
			<meta http-equiv="X-UA-Compatible" content="IE=edge">
			<meta charset="utf-8">
			<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

			<!-- Bootstrap CSS -->
			<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
			<!-- Custom CSS -->
			<style>
			body 
			{
				padding-top: 70px;
				/* Required padding for .navbar-fixed-top. Remove if using .navbar-static-top. Change if height of navigation changes. */
			}
			</style>
		</head>
		<body>
			<nav class="navbar navbar-toggleable-md navbar-light bg-faded fixed-top">
				<button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#gamedropdown" aria-controls="gamedropdown" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>
				<a class="navbar-brand" href="index.php"><?php echo $set['WebsiteName']; ?></a>
				<div class="collapse navbar-collapse" id="gamedropdown">
					<ul class="navbar-nav">
						<li class="nav-item">
							<a class="nav-link" href="../explore.php"><?php echo $lang['MENU_EXPLORE']; ?></a>
						</li>
						<li class="nav-item">
							<?php
								$ir['mail']=$db->fetch_single($db->query("SELECT COUNT(`mail_id`) FROM `mail` WHERE `mail_to` = {$ir['userid']} AND `mail_status` = 'unread'"));
								$ir['notifications']=$db->fetch_single($db->query("SELECT COUNT(`notif_id`) FROM `notifications` WHERE `notif_user` = {$ir['userid']} AND `notif_status` = 'unread'"));
								?>
							<a class="nav-link" href="../inbox.php"><?php echo $lang['MENU_MAIL']; ?> (<?php echo $ir['mail']; ?>)</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="../notifications.php"><?php echo $lang['MENU_EVENT']; ?> (<?php echo $ir['notifications']; ?>)</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="../inventory.php"><?php echo $lang['MENU_INVENTORY']; ?></a>
						</li>
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle" href="#" id="userinfo" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<?php echo" {$lang['GEN_GREETING']}, {$ir['username']}"; ?>
							</a>
							<div class="dropdown-menu" aria-labelledby="userinfo">
								<a class="dropdown-item" href="../profile.php?user=<?php echo $ir['userid']; ?>"><?php echo $lang['MENU_PROFILE']; ?></a>
								<a class="dropdown-item" href="../preferences.php?action=menu"><?php echo $lang['MENU_SETTINGS']; ?></a>
								<?php
									if (in_array($ir['user_level'], array('Admin', 'Forum Moderator', 'Web Developer', 'Assistant')))
									{
										?><div class="dropdown-divider"></div>
										<a class="dropdown-item" href="../staff/"><i class="fa fa-fw fa fa-terminal"></i> <?php echo $lang['MENU_STAFF']; ?></a><?php
									}
									?>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item" href="../gamerules.php"><?php echo $lang['MENU_RULES']; ?></a>
								<a class="dropdown-item" href="../logout.php"><?php echo $lang['MENU_LOGOUT']; ?></a>
							</div>
						</li>
					</ul>
				</div>
			</nav>
			<!-- Page Content -->
    <div class="container">

        <div class="row">
            <div class="col-lg-12 text-center">
		<?php
		date_default_timezone_set($ir['timezone']); 
		if ($ir['mail'] > 0)
		{
			alert('info',"{$lang['MENU_UNREADMAIL1']}","{$lang['MENU_UNREADMAIL2']} {$ir['mail']} {$lang['MENU_UNREADMAIL3']} <a href='../inbox.php'>{$lang["GEN_HERE"]}</a> {$lang['MENU_UNREADMAIL4']}");
		}
		if ($ir['notifications'] > 0)
		{
			alert('info',"{$lang['MENU_UNREADNOTIF']}","{$lang['MENU_UNREADMAIL2']} {$ir['notifications']} {$lang['MENU_UNREADNOTIF1']} <a href='../notifications.php'>{$lang["GEN_HERE"]}</a> {$lang['MENU_UNREADMAIL4']}");
		}
		if ($ir['announcements'] > 0)
		{
			alert('info',"{$lang['MENU_UNREADANNONCE']}","{$lang['MENU_UNREADANNONCE1']} {$ir['announcements']} {$lang['MENU_UNREADANNONCE2']} <a href='../announcements.php'>{$lang["GEN_HERE"]}</a>.");
		}
		if (user_infirmary($ir['userid']) == true)
		{
			$InfirmaryOut=$db->fetch_single($db->query("SELECT `infirmary_out` FROM `infirmary` WHERE `infirmary_user` = {$ir['userid']}"));
			$InfirmaryRemain=TimeUntil_Parse($InfirmaryOut);
			alert('info',"{$lang['GEN_INFIRM']}","{$lang['MENU_INFIRMARY1']} {$InfirmaryRemain}.");
		}
		if (user_dungeon($ir['userid']) == true)
		{
			$DungeonOut=$db->fetch_single($db->query("SELECT `dungeon_out` FROM `dungeon` WHERE `dungeon_user` = {$ir['userid']}"));
			$DungeonRemain=TimeUntil_Parse($DungeonOut);
			alert('info',"{$lang["GEN_DUNG"]}","{$lang['MENU_DUNGEON1']} {$DungeonRemain}.");
		}
		
	}
	function userdata($ir, $lv, $fm, $cm, $dosessh = 1)
    {
		global $db, $c, $userid, $set;
		$IP = $db->escape($_SERVER['REMOTE_ADDR']);
		$db->query(
                "UPDATE `users`
                 SET `laston` = {$_SERVER['REQUEST_TIME']}, `lastip` = '$IP'
                 WHERE `userid` = $userid");
		if (!$ir['email'])
        {
            global $domain;
            die(
                    "<body>Your account may be broken. Please mail help@{$domain} stating your username and player ID.");
        }
        if (!isset($_SESSION['attacking']))
        {
            $_SESSION['attacking'] = 0;
        }
        if ($dosessh && ($_SESSION['attacking'] || $ir['attacking']))
        {
           alert("warning","{$lang['ERROR_GENERIC']}","{$lang['MENU_XPLOST']}");
            $db->query("UPDATE `users` SET `xp` = 0, `attacking` = 0 WHERE `userid` = $userid");
            $_SESSION['attacking'] = 0;
        }
		$enperc = min((int) ($ir['energy'] / $ir['maxenergy'] * 100), 100);
        $wiperc = min((int) ($ir['will'] / $ir['maxwill'] * 100), 100);
        $experc = min((int) ($ir['xp'] / $ir['xp_needed'] * 100), 100);
        $brperc = min((int) ($ir['brave'] / $ir['maxbrave'] * 100), 100);
        $hpperc = min((int) ($ir['hp'] / $ir['maxhp'] * 100), 100);
        $enopp = 100 - $enperc;
        $wiopp = 100 - $wiperc;
        $exopp = 100 - $experc;
        $bropp = 100 - $brperc;
        $hpopp = 100 - $hpperc;
        $d = "";
        $u = $ir['username'];
        if ($ir['vip_days'])
        {
            $u = "<span style='color: red;'>{$ir['username']}</span>";
            $d =
                    "<img src='donator.gif'
                     alt='VIP: {$ir['vip_days']} Days Left'
                     title='VIP: {$ir['vip_days']} Days Left' />";
        }

        $gn = "";
        global $staffpage;

        $bgcolor = 'FFFFFF';
	}
	function endpage()
    {
        global $db, $ir, $lang;
        $query_extra = '';
        if (isset($_GET['mysqldebug']) && $ir['user_level'] == 'Admin')
        {
            $query_extra = '<br />' . implode('<br />', $db->queries);
        }
		?>
		</div>
			</div>
        <!-- /.row -->

			</div>
			<!-- /.container -->

			<!-- jQuery first, then Tether, then Bootstrap JS. -->
			<script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
			<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
			
			<!-- Other JavaScript -->
			<script src="js/register.js"></script>
			<script src="js/game.js"></script>
			<script src='https://www.google.com/recaptcha/api.js'></script>
			<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
		</body>
			<footer>
				<p>
					<?php 
					echo "<hr />
					{$lang['MENU_TIN']}  
						" . date('F j, Y') . " " . date('g:i:s a') . "<br />
					{$lang['MENU_OUT']}";
					?>
					&copy; <?php echo date("Y");
					echo"<br/>{$db->num_queries} {$lang['MENU_QE']}.{$query_extra}";
					?>
				</p>
			</footer>
		</html>
			   <?php
			}
	
}