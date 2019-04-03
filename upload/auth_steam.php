<?php
$menuhide = true;
require_once('globals_nonauth.php');
include ('lib/steamauth/userInfo.php');
$IP = $db->escape($_SERVER['REMOTE_ADDR']);
$CurrentTime = time();
$email = (array_key_exists('email', $_POST) && is_string($_POST['email'])) ? $_POST['email'] : $steamprofile['steamid'];
$password = (array_key_exists('password', $_POST) && is_string($_POST['password'])) ? $_POST['password'] : randomizer();
$form_email = $db->escape(stripslashes($email));
$raw_password = stripslashes($password);
$uq = $db->query("/*qc=on*/SELECT * FROM `steam_account_link` WHERE `steam_id` = '{$form_email}'");
if ($db->num_rows($uq) == 0) {
    $username=$steamprofile['steamid'];
    //Register the user
    $db->query("INSERT INTO `users`
					(`username`,`email`,`password`,`level`,`gender`,`class`,
					`lastip`,`registerip`,`registertime`,`loginip`,`display_pic`,`vip_days`)
					VALUES ('{$username}','','','1','{$e_gender}',
					'Warrior','{$IP}','{$IP}','{$CurrentTime}', '{$IP}', 
					'{$steamprofile['avatarfull']}','3')");
    $i = $db->insert_id();
    $db->query("UPDATE `users` SET `brave`='10',`maxbrave`='10',`hp`='100',
					`maxhp`='100',`maxwill`='100',`will`='100',`energy`='24',
					`maxenergy`='24' WHERE `userid`={$i}");
    $db->query("INSERT INTO `userstats` VALUES({$i}, 1100, 1000, 900, 1000, 1000, 100)");
    $db->query("INSERT INTO `infirmary`
			(`infirmary_user`, `infirmary_reason`, `infirmary_in`, `infirmary_out`) 
			VALUES ('{$i}', 'N/A', '0', '0');");
    $db->query("INSERT INTO `dungeon`
			(`dungeon_user`, `dungeon_reason`, `dungeon_in`, `dungeon_out`) 
			VALUES ('{$i}', 'N/A', '0', '0');");
    $db->query("INSERT INTO `steam_account_link` (`steam_linked`, `steam_id`) VALUES ('{$i}', '{$form_email}')");
    $api->UserGiveItem($i,6,50);
    $api->UserGiveItem($i,30,50);
    $api->UserGiveItem($i,33,250);
    $api->UserGiveCurrency($i,'primary',10000);
    $api->UserGiveCurrency($i,'secondary',50);
    $mail="Welcome to Chivalry is Dead, {$username}. We hope you stay a while and hang out. To get started,
    check out the Explore page and visit the [url=hexbags.php]Hexbags[/url] under the Gambling tab. Here you will gain many awesome starter items. Should
    your fortune be unkind, your inventory has 50 Dungeon Keys and 50 Linen Wraps to get you out of the Infirmary and Dungeon when needed.";
    session_regenerate_id();
    $_SESSION['loggedin'] = 1;
    $_SESSION['userid'] = $i;
    $_SESSION['last_login'] = time();
    $db->query("INSERT INTO `user_settings` (`userid`) VALUES ('{$_SESSION['userid']}')");
    $randophrase=randomizer();
    $db->query("UPDATE `user_settings` SET `security_key` = '{$randophrase}' WHERE `userid` = {$_SESSION['userid']}");
    $api->SystemLogsAdd($_SESSION['userid'], 'login', "Successfully logged in.");
    $db->query("UPDATE `users` SET `loginip` = '$IP', `last_login` = '{$CurrentTime}', `laston` = '{$CurrentTime}' WHERE `userid` = {$i}");
    $WelcomeMSGEmail="Welcome to Chivalry is Dead, {$username}!<br />We hope you enjoy our lovely game and stick around for a while! If you have any questions or concerns, please contact a staff member in-game!<br />Thank you!<br /> -{$set['WebsiteName']}<br /><a href='https://{$url}'>https://{$url}</a>";
    $api->SystemSendEmail($e_email,$WelcomeMSGEmail,"{$set['WebsiteName']} Registration",$set['sending_email']);
    $api->GameAddMail($i,"Welcome to Chivalry is Dead",$mail,1);
    header("Location: explore.php");
}
else
{
    session_regenerate_id();
    $mem = $db->fetch_row($uq);
    $mem['userid']=$mem['steam_linked'];
    $_SESSION['userid'] = $mem['userid'];
	$uade=$db->query("/*qc=on*/SELECT * FROM `user_settings` WHERE `userid` = {$mem['userid']}");
	if ($db->num_rows($uade) == 0)
	{
		$db->query("INSERT INTO `user_settings` (`userid`) VALUES ('{$mem['userid']}')");
	}
	$uadr=$db->fetch_row($uade);
	$_SESSION['loggedin'] = 1;
	$_SESSION['last_login'] = time();
	setcookie('login_expire', time() + 604800, time() + 604800);
    $invis=$db->fetch_single($db->query("/*qc=on*/SELECT `invis` FROM `user_settings` WHERE `userid` = {$mem['userid']}"));
    if ($invis < time())
    {
        $db->query("UPDATE `users`
              SET `loginip` = '{$IP}',
              `last_login` = '{$CurrentTime}',
              `laston` = '{$CurrentTime}'
               WHERE `userid` = {$mem['userid']}");
    }
    else
    {
        $db->query("UPDATE `users`
              SET `loginip` = '{$IP}'
               WHERE `userid` = {$mem['userid']}");
    }
    $db->query("DELETE FROM `login_attempts` WHERE `userid` = {$_SESSION['userid']}");
    $loggedin_url = 'explore.php';
    //Log that the user logged in successfully.
    $api->SystemLogsAdd($_SESSION['userid'], 'login', "Successfully logged in.");
    //Delete password recovery attempts from DB if they exist for this user.
    $db->query("DELETE FROM `pw_recovery` WHERE `pwr_email` = '{$form_email}'");
    header("Location: {$loggedin_url}");
    exit;
}