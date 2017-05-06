<?php
/*
	File:		macro.php
	Created: 	4/5/2016 at 12:18AM Eastern Time
	Info: 		Verifies if the player is botting or not. Setup
				reCaptcha in the staff panel!
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
$nohdr = 1;
require_once('globals.php');
if(isset($_POST['g-recaptcha-response']))
{
	$captcha=$_POST['g-recaptcha-response'];
	$page = stripslashes(strip_tags($_POST['page']));
	if(!$captcha)
	{
		echo "<h2>{$lang['RECAPTCHA_EMPTY']}</h2>";
		$api->SystemLogsAdd($userid,'verify',"Verified unsuccessfully.");
		header("Location: {$page}");
		exit;
	}
	$response=json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$set['reCaptcha_private']}&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']), true); 
	if($response['success'] == false) 
	{
		echo "<h2>{$lang['RECAPTCHA_FAIL']}</h2>";
		$api->SystemLogsAdd($userid,'verify',"Verified unsuccessfully.");
		header("Location: {$page}");
		exit;
	} 
	else 
	{
		$time=time();
		$db->query("UPDATE users SET `last_verified`={$time}, `need_verify` = 0 WHERE userid={$userid}");
		$api->SystemLogsAdd($userid,'verify',"Verified successfully.");
		header("Location: {$page}");
	}
}