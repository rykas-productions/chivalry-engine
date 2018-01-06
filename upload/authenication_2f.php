<?php
include_once("lib/PHPGangsta/GoogleAuthenticator.php");
$ga = new PHPGangsta_GoogleAuthenticator();
$menuhide = true;
require_once('globals_nonauth.php');
$IP = $db->escape($_SERVER['REMOTE_ADDR']);
$CurrentTime = time();
$uade=$db->query("SELECT * FROM `user_settings` WHERE `userid` = {$_SESSION['userid']}");
$uadr=$db->fetch_row($uade);
if ($uadr['2fa_on'] == 0)
{
    header("Location: login.php");
    exit;
}
if (isset($_POST['code']))
{
	$secret=$db->fetch_single($db->query("SELECT `secret_key` FROM `2fa_table` WHERE `userid` = {$_SESSION['userid']}"));
	if (empty($_POST['code']))
	{
		alert('danger',"Uh Oh!","You must enter a valid code from your authenticator app.");
	}
	else
	{
		$result=$ga->verifyCode($secret, $_POST['code'], 10);
		if ($result) 
		{
			$mem['userid'] = $_SESSION['userid'];
			//Redo sessions?
			session_regenerate_id();
			$_SESSION['userid'] = $mem['userid'];
			$_SESSION['loggedin'] = 1;
			$_SESSION['last_login'] = time();
			setcookie('login_expire', time() + 604800, time() + 604800);
			$db->query("UPDATE `users`
					  SET `loginip` = '{$IP}',
					  `last_login` = '{$CurrentTime}',
					  `laston` = '{$CurrentTime}'
					   WHERE `userid` = {$mem['userid']}");
			//Remove login attempts for this account.
			$db->query("DELETE FROM `login_attempts` WHERE `userid` = {$_SESSION['userid']}");
			$loggedin_url = 'loggedin.php';
			//Log that the user logged in successfully.
			$api->SystemLogsAdd($_SESSION['userid'], 'login', "Successfully logged in.");
			//Delete password recovery attempts from DB if they exist for this user.
			$db->query("DELETE FROM `pw_recovery` WHERE `pwr_email` = '{$form_email}'");
			header("Location: {$loggedin_url}");
			exit;
		}
		else
		{
			alert('danger',"Uh Oh!","The code you input was invalid. Go back and try again.");
			$db->query("INSERT INTO `login_attempts`
              (`ip`, `userid`, `timestamp`)
              VALUES
              ('{$IP}', '{$_SESSION['userid']}', '{$CurrentTime}');");
			 notification_add($_SESSION['userid'], "Someone has just recently attempted to gain access to your account and failed.
		    If this was you, you do not need to do anything. However, if this was not, you should change your password
		    immediately!");
		}
	}
}
else
{
	echo "Please enter your 2fa code...<br />
	<form method='post'>
		<input type='number' min='0' class='form-control' name='code'>
		<input type='submit' class='btn btn-primary'>
	</form>";
}