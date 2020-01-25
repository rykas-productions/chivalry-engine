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
require_once(__DIR__ .'/../globals_nonauth.php');
if (!isset($argv))
{
    exit;
}
$_GET['code']=substr($argv[1],5);
if (!isset($_GET['code']) || $_GET['code'] !== $_CONFIG['code'])
{
    exit;
}
//Delete things from more than 30 days ago
$last24 = time() - 86400;
$plussevenday = time() + 604800;

$db->query("UPDATE `users` SET `vip_days`=`vip_days`-1 WHERE `vip_days` > 0");
//Non-VIP Bank Interest
$db->query("UPDATE `users` SET `bank`=`bank`+(`bank`/50) WHERE `bank`>0 AND `laston` > {$last24} AND `bank`<20000001 AND `vip_days` = 0");
$db->query("UPDATE `users` SET `bigbank`=`bigbank`+(`bigbank`/50) WHERE `bigbank`>0 AND `laston` > {$last24} AND `bigbank`<100000001 AND `vip_days` = 0");
$db->query("UPDATE `users` SET `vaultbank`=`vaultbank`+(`vaultbank`/50) WHERE `vaultbank`>0 AND `laston` > {$last24} AND `vaultbank`<300000001 AND `vip_days` = 0");
//VIP Bank Interest
$db->query("UPDATE `users` SET `bank`=`bank`+(`bank`/20) WHERE `bank`>0 AND `laston` > {$last24} AND `bank`<20000001 AND `vip_days` != 0");
$db->query("UPDATE `users` SET `bigbank`=`bigbank`+(`bigbank`/20) WHERE `bigbank`>0 AND `laston` > {$last24} AND `bigbank`<100000001 AND `vip_days` != 0");
$db->query("UPDATE `users` SET `vaultbank`=`vaultbank`+(`vaultbank`/20) WHERE `vaultbank`>0 AND `laston` > {$last24} AND `vaultbank`<300000001 AND `vip_days` != 0");

$db->query("UPDATE `users` SET `hexbags` = 100, `bor` = 1000");
$db->query("UPDATE `user_settings` SET `att_dg` = 0");

$db->query("UPDATE `users` SET `dayslogged` = 0 WHERE `laston` < {$last24}");
$db->query("UPDATE `users` SET `dayslogged` = `dayslogged` + 1 WHERE `laston` > {$last24}");
$db->query("UPDATE `users` SET `rewarded` = 0");
$db->query("UPDATE `userstats` SET `luck` = 100");

$db->query("UPDATE `settings` SET `setting_value` = 0 WHERE `setting_name` = 'casino_give'");
$db->query("UPDATE `settings` SET `setting_value` = 0 WHERE `setting_name` = 'casino_take'");

$db->query("UPDATE `bank_investments` SET `days_left` = `days_left` - 1");
$biq=$db->query("/*qc=on*/SELECT * FROM `bank_investments` WHERE `days_left` = 0");
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

//Guild daily fee
$gdfq=$db->query("/*qc=on*/SELECT * FROM `guild`");
while ($gfr=$db->fetch_row($gdfq))
{
	$warquery=$db->query("/*qc=on*/SELECT `gw_id` FROM `guild_wars` WHERE `gw_declarer` = {$gfr['guild_id']} OR `gw_declaree` = {$gfr['guild_id']}");
	if ($db->num_rows($warquery) == 0)
	{
		if ($gfr['guild_primcurr'] < 100000)
		{
			$db->query("UPDATE `guild` 
						SET `guild_primcurr` = `guild_primcurr` - 100000
						WHERE `guild_id` = {$gfr['guild_id']}");
			$db->query("UPDATE `guild` 
						SET `guild_debt_time` = {$plussevenday} 
						WHERE `guild_id` = {$gfr['guild_id']} 
						AND `guild_debt_time` = 0");
			$api->GuildAddNotification($gfr['guild_id'], "Your guild has paid 100,000 Copper Coins in upkeep, but has gone into debt.");
			$api->GameAddNotification($gfr['guild_owner'], "Your guild has gone into debt!");
		}
		else
		{
			$db->query("UPDATE `guild` SET `guild_primcurr` = `guild_primcurr` - 100000 WHERE `guild_id` = {$gfr['guild_id']}");
			$api->GuildAddNotification($gfr['guild_id'], "Your guild has paid 100,000 Copper Coins in upkeep.");
		}
	}
}
//Guild daily interest.
$db->query("UPDATE `guild` SET `guild_primcurr`=`guild_primcurr`+(`guild_primcurr`/20) WHERE `guild_primcurr`>0");

//Random player showcase
$cutoff = time() - 86400;
$uq=$db->query("/*qc=on*/SELECT `userid` FROM `users` WHERE `userid` != 1 AND `laston` > {$cutoff} ORDER BY RAND() LIMIT 1");
$ur=$db->fetch_single($uq);
$api->GameAddNotification($ur,"You have been chosen as the Player of the Day! Your profile will be displayed on the login page, and you've received a unique badge in your inventory.");
item_add($ur,154,1);

//Holiday!
$db->query("UPDATE `user_settings` SET `holiday` = 0");
?>
