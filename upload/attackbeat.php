<?php
$atkpage = 1;
require_once 'globals.php';
$_GET['ID'] = isset($_GET['ID']) && ctype_digit($_GET['ID']) ? $_GET['ID'] : 0;
$_SESSION['attacking'] = 'false';
$ir['attacking'] = 'false';
$db->query("UPDATE `users` SET `attacking` = 0 WHERE `userid` = $userid");
$od = $db->query("SELECT * FROM `users` WHERE `userid` = {$_GET['ID']}");
if (!isset($_SESSION['attackwon']) || $_SESSION['attackwon'] != $_GET['ID'])
{
    alert('warning',"{$lang['CSRF_ERROR_TITLE']}","{$lang['ATT_NC']}");
	die($h->endpage());
}
if(!$db->num_rows($od)) 
{
	alert('danger',"{$lang['ERROR_NONUSER']}","{$lang['ATT_PDE']}");
    exit($h->endpage());
}
if ($db->num_rows($od) > 0)
{
	$r = $db->fetch_row($od);
	$db->free_result($od);
	if ($r['hp'] == 1) 
	{
		alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['ATT_HP']}");
		exit($h->endpage());
	}
	$hosptime = mt_rand(75, 175) + floor($ir['level'] / 2);
	alert('success',"{$lang['ATT_BEAT']} {$r['username']}!!","{$lang['ATT_BEAT']} {$r['username']} {$lang['ATT_BU_TEXT1']} {$hosptime} {$lang["GEN_MINUTES"]} {$lang['ATT_BU_TEXT2']}");
	$hospreason = $db->escape("Brutally Hurt by <a href='profile.php?user={$userid}'>{$ir['username']}</a>");
	$db->query("UPDATE `users` SET `hp` = 1 WHERE `userid` = {$r['userid']}");
	put_infirmary($r['userid'],$hosptime,$hospreason);
	event_add($r['userid'], "<a href='profile.php?user=$userid'>{$ir['username']}</a> brutally attacked you and caused {$hosptime} minutes worth of damage.");
	$api->SystemLogsAdd($userid,'attacking',"Attacked {$r['username']} [{$r['userid']}] and brutally injured them, causing {$hosptime} minutes of damage.");
	$_SESSION['attackwon'] = 0;
	if ($r['user_level'] == 'NPC')
	{
		$db->query("UPDATE `users` SET `hp` = `maxhp` WHERE `userid` = {$r['userid']}");
		$db->query("UPDATE `infirmary` SET `infirmary_out` = 0 WHERE `infirmary_user` ={$r['userid']}");
	}
}
else
{
	echo "404";
}
$h->endpage();