<?php
$atkpage = 1;
require_once('globals.php');

$_GET['ID'] = (isset($_GET['ID']) && ctype_digit($_GET['ID'])) ? abs((int) $_GET['ID']) : 0;
$_SESSION['attacking'] = 'false';
$_SESSION['attacklost'] = 'false';
if(!$_GET['ID']) 
{
	alert('warning',"{$lang['CSRF_ERROR_TITLE']}","{$lang['ATT_NC']}");
	die($h->endpage());
}
$od = $db->query("SELECT `username`, `level`, `user_level`, `guild` FROM `users` WHERE `userid` = {$_GET['ID']}");
if(!$db->num_rows($od)) 
{
	echo "404";
	exit($h->endpage());
}
$r = $db->fetch_row($od);
$db->free_result($od);
$qe = $r['level'] * $r['level'] * $r['level'];
$expgain = mt_rand($qe / 2, $qe);
if ($expgain < 0)
{
	$expgain=$expgain*-1;
}

$expgainp = $expgain / $ir['xp_needed'] * 100;
alert('danger',"{$lang['ATT_L_TEXT1']} {$r['username']}!","{$lang['ATT_L_TEXT1']} {$r['username']} {$lang['ATT_L_TEXT2']} " . number_format($expgainp, 2) . "% {$lang['GEN_EXP']}!");
$db->query("UPDATE `users` SET `xp` = `xp` - {$expgain}, `attacking` = 0 WHERE `userid` = {$userid}");
$hosptime = mt_rand(75, 175) + floor($ir['level'] / 2);
$hospreason = 'Picked a fight and lost';
put_infirmary($userid,$hosptime,$hospreason);

//Give winner some XP
$r['xp_needed'] = round($r['level']+($r['level'] * 115)+($r['level'] * 115));
$qe2 = $ir['level'] * $ir['level'] * $ir['level'];
$expperc2 = round($expgainp / $r['xp_needed'] * 100);
event_add($_GET['ID'], "<a href='profile.php?u=$userid'>{$ir['username']}</a> attacked you and lost, which gave you {$expperc2}% Experience.");
$atklog = $db->escape($_SESSION['attacklog']);
$db->query("UPDATE `users` SET `xp` = `xp` + {$expgainp} WHERE `userid` = {$_GET['ID']}");
$db->query("UPDATE `users` SET `xp` = 0 WHERE `xp` < 0");
$db->query("INSERT INTO `attacklogs` VALUES(NULL, $userid, {$_GET['ID']}, 'lost', " . time() . ", 0, '$atklog')");
$h->endpage();