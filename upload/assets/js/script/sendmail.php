<?php
/*!
	File: js/script/sendmail.php
	Created: 3/15/2016 at 10:45AM Eastern Time
	Info: Ajax for sending mail via profile
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine
*/
$menuhide=1;
error_reporting(E_ALL);
require_once('../../globals.php');
if (isset($_SERVER['REQUEST_METHOD']) && is_string($_SERVER['REQUEST_METHOD']))
{
    if (strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST')
    {
        // Ignore a GET request
        header('HTTP/1.1 400 Bad Request');
        exit;
    }
}
if (!is_ajax())
{
    header('HTTP/1.1 400 Bad Request');
    exit;
}
	$msg = $db->escape(str_replace("\n", "<br />",strip_tags(htmlentities(stripslashes($_POST['msg'])))));
	if (!isset($_POST['verf']) || !verify_csrf_code('inbox_send', stripslashes($_POST['verf'])))
	{
		alert('danger', "Action Blocked!", "Your action has been blocked for security reasons. Form requests expire fairly quickly. Be sure to be quicker next time.");
        exit;
	}
	if (empty($msg))
    {
		alert('danger', "Uh Oh!", "Please enter a message before submitting the form.", false);
        exit;
    }
	elseif (strlen($msg) > 65655)
    {
        alert('danger', "Uh Oh!", "Your subject and/or message is too long. They can only be 50 and/or 65655
            characters in length, in that order.",false);
        exit;
    }
	 $sendto = (isset($_POST['sendto']) && preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i", $_POST['sendto']) && ((strlen($_POST['sendto']) < 32) && (strlen($_POST['sendto']) >= 3))) ? $_POST['sendto'] : '';
	 if (empty($_POST['sendto']))
    {
		alert('danger', "Uh Oh!", "You are trying to send a message to an invalid or non-existent user.",false);
        exit;
    }
	$q = $db->query("/*qc=on*/SELECT `userid` FROM `users` WHERE `username` = '{$sendto}'");
	if ($db->num_rows($q) == 0)
    {
        $db->free_result($q);
		alert('danger', "Uh Oh!", "You are trying to send a message to an invalid or non-existent user.",false);
		exit;
    }
	$to = $db->fetch_single($q);
    $db->free_result($q);
	$time=time();
    if ($api->UserBlocked($userid,$to))
	{
		alert('danger', "Uh Oh!", "This user has you blocked. You cannot send messages to players that have you blocked.", false);
        die($h->endpage());
	}
	$mailtime=$db->fetch_single($db->query("/*qc=on*/SELECT `mail_time` FROM `mail` WHERE `mail_to` = {$to} ORDER BY `mail_time` DESC LIMIT 1"));
	$TimeSinceLastMail=$time-$mailtime;
	if (!($TimeSinceLastMail > 60))
	{
		alert('danger',"Uh Oh!","You may not use this form more than once per 60 seconds.",false);
		exit;
	}
    $input=$msg;
    $msg=encrypt_message($msg,$userid,$to);
	$db->query("INSERT INTO `mail` 
	(`mail_id`, `mail_to`, `mail_from`, `mail_status`, `mail_text`, `mail_time`) 
	VALUES (NULL, '{$to}', '{$userid}', 'unread', '{$msg}', '{$time}');");
	alert('success', "Success!", "Message has been sent successfully",false);
    //Mailban the user if needed?
	$fiveminago=time()-300;
	$lastthreemsg=$db->query("/*qc=on*/SELECT * 
								FROM `mail` 
								WHERE `mail_from` = {$userid} 
								AND `mail_time` >= {$fiveminago}");
	$same=0;
	while ($ltr = $db->fetch_row($lastthreemsg))
	{
		$decrypt=decrypt_message($ltr['mail_text'],$userid,$ltr['mail_to']);
		if ($decrypt == $input)
			$same=$same+1;
	}
	if ($same >= 7)
	{
		$timed=time()+259200;
		$db->query("INSERT INTO `mail_bans`
                    (`mbUSER`, `mbREASON`, `mbBANNER`, `mbTIME`) VALUES
                    ('{$userid}', 'Spamming', '1', '{$timed}')");
		$api->GameAddNotification($userid, "You have been mail-banned for 3 days for the reason: 'Spamming'.");
		staffnotes_entry($userid,"Mail banned for 3 for 'Spamming'.",0);
	}