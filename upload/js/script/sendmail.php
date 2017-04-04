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
    global $h,$lang;
	echo "<div class='alert alert-danger'> <strong>{$lang['CSRF_ERROR_TITLE']}</strong> 
	{$lang['CSRF_ERROR_TEXT']} {$lang['CSRF_PREF_MENU']} <a href='inbox.php'>{$lang['GEN_HERE']}.</div>";
    exit;
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
		alert('danger',$lang['ERROR_EMPTY'],$lang['MAIL_EMPTYINPUT'],false);
        exit;
    }
	elseif (strlen($msg) > 65655)
    {
        alert('danger',$lang['ERROR_LENGTH'],$lang['MAIL_INPUTLNEGTH'],false);
        exit;
    }
	 $sendto = (isset($_POST['sendto']) && preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i", $_POST['sendto']) && ((strlen($_POST['sendto']) < 32) && (strlen($_POST['sendto']) >= 3))) ? $_POST['sendto'] : '';
	 if (empty($_POST['sendto']))
    {
		alert('danger',$lang['ERROR_EMPTY'],$lang['MAIL_NOUSER'],false);
        exit;
    }
	$q = $db->query("SELECT `userid` FROM `users` WHERE `username` = '{$sendto}'");
	if ($db->num_rows($q) == 0)
    {
        $db->free_result($q);
		alert('danger',$lang['MAIL_UDNE'],$lang['MAIL_UDNE_TEXT'],false);
		exit;
    }
	$to = $db->fetch_single($q);
    $db->free_result($q);
	$time=time();
	$mailtime=$db->fetch_single($db->query("SELECT `mail_time` FROM `mail` WHERE `mail_to` = {$to} ORDER BY `mail_time` DESC LIMIT 1"));
	$TimeSinceLastMail=$time-$mailtime;
	if (!($TimeSinceLastMail > 60))
	{
		alert('danger',$lang['ERROR_GENERIC'],$lang['MAIL_TIMEERROR'],false);
		exit;
	}
	$db->query("INSERT INTO `mail` 
	(`mail_id`, `mail_to`, `mail_from`, `mail_status`, `mail_text`, `mail_time`) 
	VALUES (NULL, '{$to}', '{$userid}', 'unread', '{$msg}', '{$time}');");
	alert('success',$lang['ERROR_SUCCESS'],$lang['MAIL_SUCCESS'],false);