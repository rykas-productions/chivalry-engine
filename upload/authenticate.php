<?php
/*
	File:		authenticate.php
	Created: 	4/4/2016 at 11:53PM Eastern Time
	Info: 		Contains the authentication and login logic.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
$menuhide=true;
require_once('globals_nonauth.php');
$IP = $db->escape($_SERVER['REMOTE_ADDR']);
$CurrentTime=time();
$email = (array_key_exists('email', $_POST) && is_string($_POST['email'])) ? $_POST['email'] : '';
$password = (array_key_exists('password', $_POST) && is_string($_POST['password'])) ? $_POST['password'] : '';

$QuarterHour=($CurrentTime-900);
$Hour=($CurrentTime-3600);
$Day=($CurrentTime-86400);
$DQuery=$db->query("SELECT `timestamp` FROM `login_attempts` WHERE `ip` = '{$IP}' AND `timestamp` > {$Day}");
$HQuery=$db->query("SELECT `timestamp` FROM `login_attempts` WHERE `ip` = '{$IP}' AND `timestamp` > {$Hour}");
$FTMQuery=$db->query("SELECT `timestamp` FROM `login_attempts` WHERE `ip` = '{$IP}' AND `timestamp` > {$QuarterHour}");
if ($db->num_rows($DQuery) >= 9)
{
	die("<h3>{$set['WebsiteName']} Error</h3> {$lang['AUTH_ERROR1']}");
}
if ($db->num_rows($HQuery) >= 6)
{
	die("<h3>{$set['WebsiteName']} Error</h3> {$lang['AUTH_ERROR2']}");
}
if ($db->num_rows($FTMQuery) >= 3)
{
	die("<h3>{$set['WebsiteName']} Error</h3> {$lang['AUTH_ERROR3']}");
}
if (empty($email) || empty($password))
{
    $db->query("INSERT INTO `login_attempts` (`ip`, `userid`, `timestamp`) VALUES ('{$IP}', '0', '{$CurrentTime}');");
	die("<h3>{$set['WebsiteName']} Error</h3> {$lang['AUTH_ERROR4']}<br /> <a href='login.php'>Back</a>");
	
}
$form_email = $db->escape(stripslashes($email));
$raw_password = stripslashes($password);
$uq=$db->query("SELECT `userid`,`password` FROM `users` WHERE `email` = '$form_email' LIMIT 1");
$UQ=$db->query("SELECT `userid`,`password` FROM `users` WHERE `email` = '$form_email' LIMIT 1");

$userid=$db->fetch_row($uq);

$DUNQuery=$db->query("SELECT `timestamp` FROM `login_attempts` WHERE `userid` = '{$userid['userid']}' AND `timestamp` > {$Day}");
$HUNQuery=$db->query("SELECT `timestamp` FROM `login_attempts` WHERE `userid` = '{$userid['userid']}' AND `timestamp` > {$Hour}");
$QHQuery=$db->query("SELECT `timestamp` FROM `login_attempts` WHERE `userid` = '{$userid['userid']}' AND `timestamp` > {$QuarterHour}");
if ($db->num_rows($DUNQuery) >= 9)
{
	die("<h3>{$set['WebsiteName']} Error</h3> {$lang['AUTH_ERROR5']}");
}
if ($db->num_rows($HUNQuery) >= 6)
{
	die("<h3>{$set['WebsiteName']} Error</h3> {$lang['AUTH_ERROR6']}");
}
if ($db->num_rows($QHQuery) >= 3)
{
	die("<h3>{$set['WebsiteName']} Error</h3> {$lang['AUTH_ERROR7']}");
}
if ($db->num_rows($UQ) == 0)
{
    $db->free_result($uq);
	$db->query("INSERT INTO `login_attempts` (`ip`, `userid`, `timestamp`) VALUES ('{$IP}', '0', '{$CurrentTime}');");
    die("<h3>{$set['WebsiteName']} Error</h3> {$lang['AUTH_ERROR8']}<br /> <a href='login.php'>Back</a>");
}
else
{
	$mem = $db->fetch_row($UQ);
    $db->free_result($UQ);
    $login_failed = false;
	$login_failed = !(verify_user_password($raw_password, $mem['password']));
	if ($login_failed)
    {
		$db->query("INSERT INTO `login_attempts` (`ip`, `userid`, `timestamp`) VALUES ('{$IP}', '{$mem['userid']}', '{$CurrentTime}');");
		notification_add($mem['userid'],"Someone has just recently attempted to gain access to your account and failed. If this was you, you do not need to do anything. However, if this was not, you should change your password immediately!");
		die("<h3>{$set['WebsiteName']} Error</h3> {$lang['AUTH_ERROR8']}<br /> <a href='login.php'>Back</a>");
		
    } 
	session_regenerate_id();
    $_SESSION['loggedin'] = 1;
    $_SESSION['userid'] = $mem['userid'];
	$_SESSION['last_login']=time();
	$db->query("UPDATE `users` SET `loginip` = '{$IP}', `last_login` = '{$CurrentTime}', `laston` = '{$CurrentTime}'
             WHERE `userid` = {$mem['userid']}");
	$encpsw = encode_password($raw_password);
	$e_encpsw = $db->escape($encpsw);
	$db->query("UPDATE `users` SET `password` = '{$e_encpsw}' WHERE `userid` = {$_SESSION['userid']}");
	$db->query("DELETE FROM `login_attempts` WHERE `userid` = {$_SESSION['userid']}");
	$loggedin_url = 'loggedin.php';
	$api->SystemLogsAdd($_SESSION['userid'],'login',"Successfully logged in.");
	$db->query("DELETE FROM `pw_recovery` WHERE `pwr_email` = '{$form_email}'");
    header("Location: {$loggedin_url}");
    exit;
}