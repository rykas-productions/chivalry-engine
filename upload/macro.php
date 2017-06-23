<?php
/*
	File:		macro.php
	Created: 	4/5/2016 at 12:18AM Eastern Time
	Info: 		Verifies if the player is botting or not. Setup
				reCaptcha in the staff panel!
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require_once('globals.php');
$page = stripslashes(strip_tags($_POST['page']));
if(isset($_POST['g-recaptcha-response']))
{
	$captcha=$_POST['g-recaptcha-response'];
	if(!$captcha)
	{
        alert('danger',$lang['ERROR_GENERIC'],$lang['RECAPTCHA_EMPTY'],true,$page);
		$api->SystemLogsAdd($userid,'verify',"Verified unsuccessfully.");
		header("refresh:5;url={$page}");
		die($h->endpage());
	}
	$response=json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$set['reCaptcha_private']}&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']), true); 
	if($response['success'] == false) 
	{
        alert('danger',$lang['ERROR_GENERIC'],$lang['RECAPTCHA_FAIL'],true,$page);
		$api->SystemLogsAdd($userid,'verify',"Verified unsuccessfully.");
		header("refresh:5;url={$page}");
		die($h->endpage());
	} 
	else 
	{
		$time=time();
		$db->query("UPDATE users SET `last_verified`={$time}, `need_verify` = 0 WHERE userid={$userid}");
		$api->SystemLogsAdd($userid,'verify',"Verified successfully.");
		header("refresh:0;url={$page}");
        die($h->endpage());
	}
}
else
{
    $api->SystemLogsAdd($userid,'verify',"Did not receive ReCaptcha response.");
    alert('danger',$lang['ERROR_GENERIC'],$lang['RECAPTCHA_NOTSET'],true,$page);
    header("refresh:5;url={$page}");
    die($h->endpage());
}