<?php
/*
	File: crons/day.php
	Created: 6/15/2016 at 2:43PM Eastern Time
	Info: Runs the queries below when the server hits midnight.
	Add queries of your own to have queries executed at midnight
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine
*/

$file = 'crons/day.php';

$ready_to_run = $db->query("SELECT `nextUpdate` FROM `crons` WHERE `file`='{$file}' AND `nextUpdate` <= unix_timestamp()");
//Place your queries inside this conditional.
if ($db->num_rows($ready_to_run))
{
    //Delete things from more than 30 days ago
    $ThirtyDaysAgo=time()-2592000;
    $db->query("DELETE FROM `logs` WHERE `log_time` < {$ThirtyDaysAgo}");
    $db->query("DELETE FROM `mail` WHERE `mail_time` < {$ThirtyDaysAgo}");
    $db->query("DELETE FROM `notifications` WHERE `notif_time` < {$ThirtyDaysAgo}");
    $db->query("DELETE FROM `guild_notifications` WHERE `gn_time` < {$ThirtyDaysAgo}");

	$db->query("UPDATE users SET `vip_days`=`vip_days`-1 WHERE `vip_days` > 0");
	$db->query("UPDATE users SET `bank`=`bank`+(`bank`/50) WHERE `bank`>0 AND `laston` > {$last24}");
	
	$time = 86400;
	$db->query("UPDATE `crons` SET `nextUpdate`=`nextUpdate`+{$time} WHERE `file`='{$file}'");
}

?>
