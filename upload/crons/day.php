<?php
/*
	File: crons/day.php
	Created: 6/15/2016 at 2:43PM Eastern Time
	Info: Runs the queries below when the server hits midnight.
	Add queries of your own to have queries executed at midnight
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine
*/
$menuhide=1;
require_once('../globals_nonauth.php');
if (!isset($_GET['code']) || $_GET['code'] !== $_CONFIG['code'])
{
    exit;
}
//Delete things from more than 30 days ago
$last24 = time() - 86400;
$ThirtyDaysAgo = time() - 2592000;
$db->query("DELETE FROM `logs` WHERE `log_time` < {$ThirtyDaysAgo}");
$db->query("DELETE FROM `mail` WHERE `mail_time` < {$ThirtyDaysAgo}");
$db->query("DELETE FROM `notifications` WHERE `notif_time` < {$ThirtyDaysAgo}");
$db->query("DELETE FROM `guild_notifications` WHERE `gn_time` < {$ThirtyDaysAgo}");

$db->query("UPDATE `users` SET `vip_days`=`vip_days`-1 WHERE `vip_days` > 0");
$db->query("UPDATE `users` SET `bank`=`bank`+(`bank`/50) WHERE `bank`>0 AND `laston` > {$last24} AND `bank`<10000000");
$db->query("UPDATE `users` SET `hexbags` = 50, `bor` = 500");

$db->query("UPDATE `users` SET `dayslogged` = 0 WHERE `laston` < {$last24}");
$db->query("UPDATE `users` SET `dayslogged` = `dayslogged` + 1 WHERE `laston` > {$last24}");
$db->query("UPDATE `users` SET `dayslogged` = 0 WHERE `dayslogged` > 7");
$db->query("UPDATE `users` SET `rewarded` = 0");
$db->query("UPDATE `userstats` SET `luck` = 100");

$db->query("UPDATE `bank_investments` SET `days_left` = `days_left` - 1");
$biq=$db->query("SELECT * FROM `bank_investments` WHERE `days_left` = 0");
while ($riq = $db->fetch_row($biq))
{
	$add=$riq['amount']*($riq['interest']/100);
	$investment=$riq['amount']+$add;
	$db->query("UPDATE `users` SET `bank`=`bank`+{$investment} WHERE `userid` = {$riq['userid']}");
	$api->GameAddNotification($riq['userid'],"Your bank investment of " . number_format($riq['amount']) . " Copper Coins has finished. " . number_format($investment) . " Copper Coins have been added to your bank account.");
	$db->query("DELETE FROM `bank_investments` WHERE `userid` = {$riq['userid']}");
}
$fiveday=Random(3,9);
$tenday=Random(7,20);
$twentyday=Random(16,48);
$thirtyday=Random(28,84);
$db->query("UPDATE `settings` SET `setting_value` = '{$fiveday}' WHERE `setting_name` = '5day'");
$db->query("UPDATE `settings` SET `setting_value` = '{$tenday}' WHERE `setting_name` = '10day'");
$db->query("UPDATE `settings` SET `setting_value` = '{$twentyday}' WHERE `setting_name` = '20day'");
$db->query("UPDATE `settings` SET `setting_value` = '{$thirtyday}' WHERE `setting_name` = '30day'");
$db->query("TRUNCATE TABLE `votes`");
?>
