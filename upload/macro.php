<?php
$nohdr = 1;
require_once('globals.php');
if(isset($_POST['g-recaptcha-response']))
{
	$captcha=$_POST['g-recaptcha-response'];
	$page = stripslashes(strip_tags($_POST['page']));
	if(!$captcha)
	{
		echo '<h2>Please check the the captcha form.</h2>';
		header("Location: {$page}");
		$api->SystemLogsAdd($userid,'verify',"Verified unsuccessfully.");
		exit;
	}
	$response=json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$set['reCaptcha_private']}&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']), true); 
	if($response['success'] == false) 
	{
		echo '<h2>You failed the captcha!</h2>';
		header("Location: {$page}");
		$api->SystemLogsAdd($userid,'verify',"Verified unsuccessfully.");
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