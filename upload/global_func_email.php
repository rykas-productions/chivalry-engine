<?php
//Actual events
function sendVerificationEmail($email, $code)
{
	global $api;
	$username=emailToUsername($email);
	$body = "Greetings {$username}!<br />
	You may verify your email for your Chivalry is Dead account by clicking <a href=''>here</a>.";
	return $api->SystemSendEmail($email, $body);
}

function sendRegistrationEmail($email)
{
	global $api;
	$username=emailToUsername($email);
	$WelcomeMSG="Welcome to Chivalry is Dead, {$username}!<br />
	We hope you enjoy our lovely game and stick around for a while! 
	If you have any questions or concerns, please contact a staff member in-game!<br />
	Thank you!";
	return $api->SystemSendEmail($email,$WelcomeMSG);
}
function sendLoginFailEmail($userid)
{
	global $api, $db;
	$IP = $db->escape($_SERVER['REMOTE_ADDR']);
	$parseTime = date('g:i:s a') . " " .  date('F j, Y');
	$email = userIDtoEmail($userid);
	$username = emailToUsername($email);
	$body = "Greetings {$username}<br />
	We just wanted to let you know that you had a failed login attempt at <b>{$parseTime} Server Time</b> from the 
	IP Address <b>{$IP}</b>.<br />
	If this was you, you do not have to do anything. However, if it was not, we recommend you log in immediately 
	and change your password.";
	return $api->SystemSendEmail($email,$body);
}
function sendDonateStartEmail($userid, $donated)
{
	global $api;
	$email = userIDtoEmail($userid);
	$username = emailToUsername($email);
	$body = "Greetings {$username}<br />
	We wanted to let you know that we received your recent donation of \${$donated}, and we graciously thank 
	you for it. In most circumstances, your pack will be processed automatically, but please allow up to 24-48 
	hours for them to be given out. If all else fails, please contact CID Admin [1] in-game as soon as possible.<br />
	Your donation will be used to fund the game costs, which typically include hosting, domain costs, 
	and advertising campaigns.";
	return $api->SystemSendEmail($email,$body);
}

function sendDonateGoodEmail($userid, $donated)
{
	global $api;
	$email = userIDtoEmail($userid);
	$username = emailToUsername($email);
	$body = "Greetings {$username}<br />
	Your recent donation of \${$donated} has been proccessed and approved. Your pack should have been given to the recipient party. If not, please contact CID Admin [1] in-game as soon as possible.";
	return $api->SystemSendEmail($email,$body);
}

function sendDonateGiftEmail($for, $payer)
{
	global $api;
	$email = userIDtoEmail($for);
	$username = emailToUsername($email);
	$username2 = $api->SystemUserIDtoName($payer);
	$body = "Greetings {$username}<br />
		{$username2} has gifted you a VIP Pack! Log in now to use it! Make sure you say thanks!";
	return $api->SystemSendEmail($email,$body);
}

function sendRefferalEmail($sendToUserID, $newUserID, $newUserName)
{
    global $db, $api, $set;
    $st = $db->fetch_row($db->query("SELECT `username`, `email` FROM `users` WHERE `userid` = {$sendToUserID}"));
    $WelcomeMSGEmail = "Hey {$st['username']}!<br />We just wanted to thank you for referring your friend, {$newUserName} [{$newUserID}], to Chivalry is Dead. Make sure you log in and give them a warm welcome.";
    $api->SystemSendEmail($st['email'],$WelcomeMSGEmail,$set['WebsiteName'] . " Referral", $set['sending_email']);
}
//Helper functions
function emailToUsername($email)
{
	global $db;
	return $db->fetch_single($db->query("SELECT `username` FROM `users` WHERE `email` = '{$email}'"));
}

function emailToUserID($email)
{
	global $db;
	return $db->fetch_single($db->query("SELECT `userid` FROM `users` WHERE `email` = '{$email}'"));
}

function userIDtoEmail($id)
{
	global $db;
	return $db->fetch_single($db->query("SELECT `email` FROM `users` WHERE `userid` = {$id}"));
}