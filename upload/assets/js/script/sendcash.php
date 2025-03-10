<?php
/*!
	File: js/script/sendcash.php
	Created: 8/19/2016 at 12:45PM Eastern Time
	Info: Ajax for sending cash via profile
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine
*/
$menuhide=1;
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
	$cash = abs((int) $_POST['cash']);
	$receive = abs((int) $_POST['sendto']);
	if (!isset($_POST['verf']) || !verify_csrf_code('cash_send', stripslashes($_POST['verf'])))
	{
		alert('danger', "Action Blocked!", "Your action has been blocked for security reasons. Form requests expire fairly quickly. Be sure to be quicker next time.");
        exit;
	}
	if (empty($cash))
    {
		alert('danger',"Uh Oh!","Please specify the amount of cash you wish to send.",false);
        exit;
    }
	elseif ($cash <= 0)
    {
        alert('danger',"Uh Oh!","Please send more than 0 Copper Coins.",false);
        exit;
    }
	elseif ($cash > $ir['primary_currency'])
	{
		alert('danger',"Uh Oh!","You cannot send more cash than you currently have.",false);
		exit;
	}
	elseif ($userid == $receive)
	{
		alert('danger',"Uh Oh!","You cannot send yourself Copper Coins.",false);
		exit;
	}
	elseif (empty($receive))
    {
		alert('danger',"Uh Oh!","Please select the user you wish to send cash.",false);
        exit;
    }
	$q = $db->query("/*qc=on*/SELECT `userid` FROM `users` WHERE `userid` = '{$receive}'");
	if ($db->num_rows($q) == 0)
    {
        $db->free_result($q);
		alert('danger',"Uh Oh!","The user you're trying to send cash does not exist.",false);
		exit;
    }
    if ($api->SystemCheckUsersIPs($userid, $receive)) {
        alert('danger', 'Uh Oh!', 'You cannot send Copper Coins to anyone who has the same IP Address as you.', false);
        exit;
    }
	$userformat = "<a href='profile.php?user={$userid}'>{$ir['username']}</a> [{$userid}]";
    $user2format = "<a href='profile.php?user={$receive}'>{$api->SystemUserIDtoName($receive)}</a> [{$receive}]";
    $cashformat = number_format($cash);
    $api->GameAddNotification($receive, "{$userformat} has sent you {$cashformat} Copper Coins.");
    $api->UserGiveCurrency($receive, 'primary', $cash);
    $api->UserTakeCurrency($userid, 'primary', $cash);
    $api->SystemLogsAdd($userid, 'sendcash', "Sent {$cashformat} Copper Coins to {$user2format}.");
    alert("success", "Success!", "You have successfully sent {$user2format} {$cashformat} Copper Coins.", false);