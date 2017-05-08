<?php
/*
	File:		donatordone.php
	Created: 	4/4/2016 at 11:58PM Eastern Time
	Info: 		End page player is greeted with after accepting/declining Paypal charge.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require_once('globals.php');
if (!isset($_GET['action']))
{
    ob_get_clean();
    header('HTTP/1.1 400 Bad Request');
    exit;
}
if ($_GET['action'] == "cancel")
{
    alert("success",$lang['ERROR_SUCCESS'],$lang['VIP_CANCEL']);
}
else if ($_GET['action'] == "done")
{
    if (!$_POST['txn_id'])
    {
        die($h->endpage());
    }
    alert("success","{$lang['VIP_THANKS']} {$set['WebsiteName']}",$lang['VIP_SUCCESS']);
}
$h->endpage();
