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
function csrf_error($goBackTo)
{
	alert('danger',"Action Blocked!","Your action was blocked for security reasons. Fill out the form quicker next
	time.",false);
}
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
	$msg = $db->escape(str_replace("\n", "<br />",strip_tags(stripslashes($_POST['msg']))));
	if (!isset($_POST['verf']) || !verify_csrf_code('inbox_send', stripslashes($_POST['verf'])))
	{
		csrf_error('');
	}
	if (empty($msg))
    {
		alert('danger',"Uh Oh!","Please input the message you wish to send.",false);
        exit;
    }
	elseif (strlen($msg) > 65655)
    {
        alert('danger',"Uh Oh!","You cannot send messages longer than 65,655 characters in length.",false);
        exit;
    }
	 $sendto = (isset($_POST['sendto']) && preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i", $_POST['sendto']) && ((strlen($_POST['sendto']) < 32) && (strlen($_POST['sendto']) >= 3))) ? $_POST['sendto'] : '';
	 if (empty($_POST['sendto']))
    {
		alert('danger',"Uh Oh!","You are trying to message a non-existent user.",false);
        exit;
    }
	$q = $db->query("SELECT `userid` FROM `users` WHERE `username` = '{$sendto}'");
	if ($db->num_rows($q) == 0)
    {
        $db->free_result($q);
		alert('danger',"Uh Oh!","You are trying to message a non-existent user.",false);
		exit;
    }
	$to = $db->fetch_single($q);
    $db->free_result($q);
	$time=time();
	$mailtime=$db->fetch_single($db->query("SELECT `mail_time` FROM `mail` WHERE `mail_to` = {$to} ORDER BY `mail_time` DESC LIMIT 1"));
	$TimeSinceLastMail=$time-$mailtime;
	if (!($TimeSinceLastMail > 60))
	{
		alert('danger',"Uh Oh!","You can only send messages with this form once every 60 seconds.",false);
		exit;
	}
	$db->query("INSERT INTO `mail` 
	(`mail_id`, `mail_to`, `mail_from`, `mail_status`, `mail_text`, `mail_time`) 
	VALUES (NULL, '{$to}', '{$userid}', 'unread', '{$msg}', '{$time}');");
	alert('success',"Success!","Message has been sent successfully.",false);