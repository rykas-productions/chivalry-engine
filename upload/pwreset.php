<?php
/*
	File:		pwreset.php
	Created: 	4/5/2016 at 12:23AM Eastern Time
	Info: 		Allows players to reset their password if they have
				forgotten it. Please fill in the $from field below.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require('globals_nonauth.php');
$from = 'fillinplease';
if (!isset($_GET['step']))
{
    $_GET['step'] = '';
}
switch ($_GET['step'])
{
	case 'two':
		two();
		break;
	default:
		one();
		break;
}
function one()
{
	global $db,$lang,$from,$set;
	if (isset($_POST['email']))
	{
		if (!isset($_POST['email']) || !valid_email(stripslashes($_POST['email'])))
		{
			alert('danger',$lang['ERROR_INVALID'],$lang['REG_EMAILERROR'],false);
			require("footer.php");
			exit;
		}
		$e_email = $db->escape(stripslashes($_POST['email']));
		$IP = $db->escape($_SERVER['REMOTE_ADDR']);
		$email=$db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `users` WHERE `email` = '{$e_email}'"));
		$token = bin2hex(randomizer());
		if ($email > 0)
		{
			$to = $e_email;
			$subject = "{$set['WebsiteName']} Password Recovery";
			$body = "Recently, someone has attempted to reset your password. Click <a href='http://" .determine_game_urlbase() . "/pwreset.php?step=two&code={$token}'>here</a> 
			to start the password reset process. If this wasn't you, do not click this link. 
			The link will expire approximately 30 minutes after the password reset process.<br />
			<br />
			If you cannot click the URL for whatever reason, please paste in http://" .determine_game_urlbase() . "/pwreset.php?step=two&code={$token} into your URL bar.";
			$headers[] = 'MIME-Version: 1.0';
			$headers[] = 'Content-type: text/html; charset=iso-8859-1';
			$headers[] = "From: {$from}";
			mail($to, $subject, $body, implode("\r\n", $headers));
			$expire=time() + 1800;
			$db->query("UPDATE `users` SET `force_logout` = 'true' WHERE `email` = '{$e_email}'");
			$db->query("INSERT INTO `pw_recovery` (`pwr_ip`, `pwr_email`, `pwr_code`, `pwr_expire`) VALUES ('{$IP}', '{$e_email}', '{$token}', '{$expire}')");
		}
		alert('success',$lang['ERROR_SUCCESS'],$lang['PWR_SUCC'],false);
	}
	else
	{
		alert('info',$lang['ERROR_INFO'],$lang['PWR_INFO'],false);
		echo "
		<form method='post'>
			<input type='email' name='email' required='1' class='form-control'>
			<br />
			<input type='submit' class='btn btn-default'>
		</form>";
	}
}
function two()
{
	global $db,$lang,$from,$set;
	if (isset($_GET['code']))
	{
		$token = $db->escape(stripslashes($_GET['code']));
		if ($db->num_rows($db->query("SELECT `pwr_id` FROM `pw_recovery` WHERE `pwr_code` = '{$token}'")) == 0)
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['PWR_ERR'],false);
		}
		else if ($db->fetch_single($db->query("SELECT `pwr_expire` FROM `pw_recovery` WHERE `pwr_code` = '{$token}'")) < time())
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['PWR_ERR'],false);
		}
		else
		{
			$pwr=$db->fetch_row($db->query("SELECT * FROM `pw_recovery` WHERE `pwr_code` = '{$token}'"));
			$pw = base64_encode(openssl_random_pseudo_bytes(16));
			$to = $pwr['pwr_email'];
			$subject = "{$set['WebsiteName']} Password Recovery";
			$body = "Your password has been successfully updated to {$pw}
			<br /> Please use this to log in from now on. We highly recommend changing your password as soon as you log in.";
			$headers[] = 'MIME-Version: 1.0';
			$headers[] = 'Content-type: text/html; charset=iso-8859-1';
			$headers[] = "From: {$from}";
			mail($to, $subject, $body, implode("\r\n", $headers));
			$expire=time() + 1800;
			$db->query("UPDATE `users` SET `force_logout` = 'true' WHERE `email` = '{$pwr['pwr_email']}'");
			$e_pw=encode_password($pw);
			$db->query("UPDATE `users` SET `password` = '{$e_pw}' WHERE `email` = '{$pwr['pwr_email']}'");
			$db->query("DELETE FROM `pw_recovery` WHERE `pwr_code` = '{$token}'");
			alert('success',$lang['ERROR_SUCCESS'],$lang['PWR_SUCC1'],false);
		}
	}
	else
	{
		alert('danger',$lang['ERROR_GENERIC'],$lang['PWR_ERR'],false);
	}
}
require("footer.php");
exit;