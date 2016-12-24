<?php
/*!
	File: js/script/sendcash.php
	Created: 8/19/2016 at 12:45PM Eastern Time
	Info: Ajax for sending cash via profile
	Author: TheMasterGeneral
	Website: http://mastergeneral156.pcriot.com/
*/
$menuhide=1;
error_reporting(E_ALL);
require_once('../../globals.php');
function csrf_error($goBackTo)
{
    global $h,$lang;
	echo "<div class='alert alert-danger'> <strong>{$lang['CSRF_ERROR_TITLE']}</strong> 
	{$lang['CSRF_ERROR_TEXT']} {$lang['CSRF_PREF_MENU']} <a href='profile.php'>{$lang['GEN_HERE']}.</div>";
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
	$cash = abs((int) $_POST['cash']);
	$receive = abs((int) $_POST['sendto']);
	if (!isset($_POST['verf']) || !verify_csrf_code('cash_send', stripslashes($_POST['verf'])))
	{
		csrf_error('');
	}
	if (empty($cash))
    {
		alert('danger',"{$lang['ERROR_EMPTY']}","{$lang['SCF_POSCASH']}");
        exit;
    }
	elseif ($cash <= 0)
    {
        alert('danger',"{$lang['ERROR_LENGTH']}","{$lang['SCF_POSCASH']}");
        exit;
    }
	elseif ($cash > $ir['primary_currency'])
	{
		alert('danger',"{$lang['ERROR_LENGTH']}","{$lang['SCF_NEC']}");
		exit;
	}
	elseif ($userid == $receive)
	{
		alert('danger',"No!","Why would you want to send yourself currency anyway?");
	}
	if (empty($receive))
    {
		alert('danger',"{$lang['ERROR_NONUSER']}","{$lang['SCF_UNE']}");
        exit;
    }
	$q = $db->query("SELECT `userid` FROM `users` WHERE `userid` = '{$receive}'");
	if ($db->num_rows($q) == 0)
    {
        $db->free_result($q);
		alert('danger',"{$lang['ERROR_NONUSER']}","{$lang['SCF_UNE']}");
		exit;
    }
	$to = $db->fetch_single($q);
    $db->free_result($q);
	$time=time();
	$db->query("UPDATE `users` SET `primary_currency` = `primary_currency` - {$cash} WHERE `userid` = {$userid}");
	$db->query("UPDATE `users` SET `primary_currency` = `primary_currency` + {$cash} WHERE `userid` = {$receive}");
	event_add($to,"{$ir['username']} has sent you {$cash} Primary Currency.");
	alert('success',"{$lang['ERROR_SUCCESS']}","{$lang['SCF_SUCCESS']}");