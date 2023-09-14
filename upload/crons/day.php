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
if (!isset($_GET['code']) || $_GET['code'] !== $_CONFIG['code'])
{
    exit;
}
//Delete things from more than 30 days ago
$last24 = time() - 86400;

$db->query("UPDATE `users` SET `vip_days`=`vip_days`-1 WHERE `vip_days` > 0");

$db->query("UPDATE `users` SET `hexbags` = 100, `bor` = 1000");
$db->query("UPDATE `user_settings` SET `att_dg` = 0");

$db->query("UPDATE `users` SET `dayslogged` = 0 WHERE `laston` < {$last24}");
$db->query("UPDATE `users` SET `dayslogged` = `dayslogged` + 1 WHERE `laston` > {$last24}");
$db->query("UPDATE `users` SET `rewarded` = 0");
$db->query("UPDATE `userstats` SET `luck` = 100");

$db->query("UPDATE `settings` SET `setting_value` = 0 WHERE `setting_name` = 'casino_give'");
$db->query("UPDATE `settings` SET `setting_value` = 0 WHERE `setting_name` = 'casino_take'");

$db->query("UPDATE `bank_investments` SET `days_left` = `days_left` - 1");
//Guild daily interest.
$db->query("UPDATE `guild` SET `guild_primcurr`=`guild_primcurr`+(`guild_primcurr`/20) WHERE `guild_primcurr`>0");
$biq=$db->query("/*qc=on*/SELECT * FROM `bank_investments` WHERE `days_left` = 0");
while ($riq = $db->fetch_row($biq))
{
	$add=$riq['amount']*($riq['interest']/100);
	$investment=$riq['amount']+$add;
	$db->query("UPDATE `users` SET `bank`=`bank`+{$investment} WHERE `userid` = {$riq['userid']}");
	$api->GameAddNotification($riq['userid'],"Your bank investment of " . shortNumberParse($riq['amount']) . " Copper Coins has finished. " . shortNumberParse($investment) . " Copper Coins have been added to your bank account.");
	$db->query("DELETE FROM `bank_investments` WHERE `userid` = {$riq['userid']} AND `invest_id` = {$riq['invest_id']}");
	addToEconomyLog('Bank Investments', 'copper', $add);
}
$fiveday=Random(3,9);
$tenday=Random(7,20);
$twentyday=Random(16,48);
$thirtyday=Random(35,64);
$db->query("UPDATE `settings` SET `setting_value` = '{$fiveday}' WHERE `setting_name` = '5day'");
$db->query("UPDATE `settings` SET `setting_value` = '{$tenday}' WHERE `setting_name` = '10day'");
$db->query("UPDATE `settings` SET `setting_value` = '{$twentyday}' WHERE `setting_name` = '20day'");
$db->query("UPDATE `settings` SET `setting_value` = '{$thirtyday}' WHERE `setting_name` = '30day'");
$db->query("TRUNCATE TABLE `votes`");
//doDailyDistrictTick();
doDailyGuildFee();
//Banks daily interest
doDailyBankInterest();
doDailyFedBankInterest();
doDailyVaultBankInterest();


//Random player showcase
/*$cutoff = time() - 86400;
$uq=$db->query("SELECT `userid` FROM `users` WHERE `userid` != 1 AND `laston` > {$cutoff} ORDER BY RAND() LIMIT 1");
$ur=$db->fetch_single($uq);
//$api->GameAddNotification($ur,"You have been chosen as the Player of the Day! Your profile will be displayed on the login page, and you've received a unique badge in your inventory.");
item_add($ur,154,1);*/
runMarketTick(1);   //low risk market
runMarketTick(2);   //low risk market
purgeOldLogs();
addAutoBountyListing();

$month = date('n');
$day = date('j');
$year = date('Y');

if (($month == 10) && ($day == 1))
{
    $api->GameAddAnnouncement("Hey folks! To kickstart the Halloween season, you may now visit a player's profile and Trick or Treat using the now-available link under the action section. You may trick or treat on a player once an hour. Doing so will grant you random candies that may be helpful on your journey. Stay tuned for more Halloween tricks as we get closer to the holiday!");
}

?>
