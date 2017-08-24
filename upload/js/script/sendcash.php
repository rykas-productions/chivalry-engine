<?php
/*!
	File: js/script/sendcash.php
	Created: 8/19/2016 at 12:45PM Eastern Time
	Info: Ajax for sending cash via profile
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine
*/
$menuhide=1;
error_reporting(E_ALL);
require_once('../../globals.php');
function csrf_error($goBackTo)
{
    function csrf_error()
    {
        global $h;
        alert('danger',"Action Blocked!","The action you were trying to do was blocked. It was blocked because you loaded
        another page on the game. If you have not loaded a different page during this time, change your password
        immediately, as another person may have access to your account!");
        die($h->endpage());
    }
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
		alert('danger',"Uh Oh!","Please specify the amount of cash you wish to send.",false);
        exit;
    }
	elseif ($cash <= 0)
    {
        alert('danger',"Uh Oh!","Please send more than 0 cash.",false);
        exit;
    }
	elseif ($cash > $ir['primary_currency'])
	{
		alert('danger',"Uh Oh!","You cannot send more cash than you currently have.",false);
		exit;
	}
	elseif ($userid == $receive)
	{
		alert('danger',"Uh Oh!","You cannot send yourself cash.",false);
		exit;
	}
	if (empty($receive))
    {
		alert('danger',"Uh Oh!","Please select the user you wish to send cash.",false);
        exit;
    }
	$q = $db->query("SELECT `userid` FROM `users` WHERE `userid` = '{$receive}'");
	if ($db->num_rows($q) == 0)
    {
        $db->free_result($q);
		alert('danger',"Uh Oh!","The user you're trying to send cash does not exist.",false);
		exit;
    }
	$to = $db->fetch_single($q);
    $db->free_result($q);
	$time=time();
	$db->query("UPDATE `users` SET `primary_currency` = `primary_currency` - {$cash} WHERE `userid` = {$userid}");
	$db->query("UPDATE `users` SET `primary_currency` = `primary_currency` + {$cash} WHERE `userid` = {$receive}");
	notification_add($to,"{$ir['username']} has sent you {$cash} Primary Currency.");
	alert('success',"Success!","You have sent your cash to User ID #{$to}.",false);