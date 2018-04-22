<?php
require_once('globals_nonauth.php');
$code='ce3a7b28b000daac7eb5fef75ecb5c64398344be';
if ($_GET['action'] != 'bbogd')
{
    if (!isset($_GET['code']))
        exit;
    if ($_GET['code'] != $code)
        exit;
}
switch ($_GET['action']) {
    case "twg":
        twg();
        break;
    case "top100":
        top100();
        break;
    case "mgpoll":
        mgpoll();
        break;
	case "apex":
        apex();
        break;
	case "dog":
        dog();
        break;
    case "bbogd":
        bbogd();
        break;
    default:
        die();
        break;
}
function mgpoll()
{
	global $api,$db;
	$_POST['id'] = (isset($_POST['id']) && is_numeric($_POST['id'])) ? abs($_POST['id']) : '';
	$q = $db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `votes` WHERE `userid` = {$_POST['id']} AND `voted` = 'mgp'");
    $vote_count = $db->fetch_single($q);
    $db->free_result($q);
    if ($vote_count > 0)
        exit;
	$api->SystemLogsAdd($_POST['id'],'voted',"Voted at MGPoll.");
	$db->query("INSERT INTO `votes` VALUES ({$_POST['id']}, 'mgp')");
	$db->query("INSERT INTO `vote_raffle` (`userid`) VALUES ('{$_POST['id']}')");
    $api->UserGiveItem($_POST['id'],33,50);
	$api->GameAddNotification($_POST['id'],"Your vote at MGPoll has been verified successfully. You have been credited 50 Boxes of Random.");
}
function top100()
{
	global $api,$db;
	$_GET['id'] = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs($_GET['id']) : '';
	$q = $db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `votes` WHERE `userid` = {$_GET['id']} AND `voted` = 'top100'");
    $vote_count = $db->fetch_single($q);
    $db->free_result($q);
    if ($vote_count > 0)
        exit;
	$api->SystemLogsAdd($_GET['id'],'voted',"Voted at Top 100 Arena.");
	$db->query("INSERT INTO `votes` VALUES ({$_GET['id']}, 'top100')");
	$db->query("INSERT INTO `vote_raffle` (`userid`) VALUES ('{$_GET['id']}')");
    $api->UserGiveItem($_GET['id'],130,1);
	$api->GameAddNotification($_GET['id'],"Your vote at Top 100 Arena has been verified successfully. You have been credited a 100 Chivalry Token Voucher.");
}
function twg()
{
	global $api,$db;
	$_POST['uid'] = (isset($_POST['uid']) && is_numeric($_POST['uid'])) ? abs($_POST['uid']) : '';
	$q = $db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `votes` WHERE `userid` = {$_POST['uid']} AND `voted` = 'twg'");
    $vote_count = $db->fetch_single($q);
    $db->free_result($q);
    if ($vote_count > 0)
        exit;
	$api->SystemLogsAdd($_POST['uid'],'voted',"Voted at Top Web Games.");
	$db->query("INSERT INTO `votes` VALUES ({$_POST['uid']}, 'twg')");
	$db->query("INSERT INTO `vote_raffle` (`userid`) VALUES ('{$_POST['uid']}')");
	$api->UserGiveCurrency($_POST['uid'],'primary',25000);
	$api->GameAddNotification($_POST['uid'],"Your vote at Top Web Games has been verified successfully. You have been credited 25,000 Copper Coins.");
}
function apex()
{
	global $api,$db;
	$_POST['i'] = (isset($_POST['i']) && is_numeric($_POST['i'])) ? abs($_POST['i']) : '';
	$q = $db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `votes` WHERE `userid` = {$_POST['i']} AND `voted` = 'apex'");
    $vote_count = $db->fetch_single($q);
    $db->free_result($q);
    if ($vote_count > 0)
        exit;
	$api->SystemLogsAdd($_POST['i'],'voted',"Voted at Apex Web Gaming.");
	$db->query("INSERT INTO `votes` VALUES ({$_POST['i']}, 'apex')");
	$db->query("INSERT INTO `vote_raffle` (`userid`) VALUES ('{$_POST['i']}')");
	$db->query("UPDATE `users` SET `hexbags` = `hexbags` + 25 WHERE `userid` = {$_POST['i']}");
	$api->GameAddNotification($_POST['i'],"Your vote at Apex Web Gaming has been verified successfully. You have been credited an extra 25 Hexbags.");
	
}
function dog()
{
	global $api,$db;
	$_GET['votedef'] = (isset($_GET['votedef']) && is_numeric($_GET['votedef'])) ? abs($_GET['votedef']) : '';
	$q = $db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `votes` WHERE `userid` = {$_GET['votedef']} AND `voted` = 'dog'");
    $vote_count = $db->fetch_single($q);
    $db->free_result($q);
    if ($vote_count > 0)
        exit;
	$api->SystemLogsAdd($_GET['votedef'],'voted',"Voted at Directory of Games.");
	$db->query("INSERT INTO `votes` VALUES ({$_GET['votedef']}, 'dog')");
	$db->query("INSERT INTO `vote_raffle` (`userid`) VALUES ('{$_GET['votedef']}')");
	$api->UserGiveCurrency($_GET['votedef'],'primary',100000);
	$api->GameAddNotification($_GET['votedef'],"Your vote at Directory of Games has been verified successfully. You have been credited 100,000 Copper Coins.");
}
function bbogd()
{
	global $api,$db;
	$_GET['userid'] = (isset($_GET['userid']) && is_numeric($_GET['userid'])) ? abs($_GET['userid']) : '';
	$q = $db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `votes` WHERE `userid` = {$_GET['userid']} AND `voted` = 'bbogd'");
    $vote_count = $db->fetch_single($q);
    $db->free_result($q);
    if ($vote_count > 0)
        exit;
	$api->SystemLogsAdd($_GET['userid'],'voted',"Voted at BBOGD.");
	$db->query("INSERT INTO `votes` VALUES ({$_GET['userid']}, 'bbogd')");
	$db->query("INSERT INTO `vote_raffle` (`userid`) VALUES ('{$_GET['userid']}')");
	$api->UserGiveCurrency($_GET['userid'],'secondary',75);
	$api->GameAddNotification($_GET['userid'],"Your vote at BBOGD has been verified successfully. You have been credited 75 Chivalry Tokens.");
}