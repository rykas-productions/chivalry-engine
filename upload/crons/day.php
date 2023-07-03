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
doDailyDistrictTick();
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

$month = date('n');
$day = date('j');
$year = date('Y');

if ($year == 2023)
{
	if ($month == 6)
	{
		if ($day >= 26 && $day <= 30)
			$db->query("UPDATE `users` SET `hexbags` = `hexbags` + 100, `bor` = `bor` + 1000");
	}
}

if ($month == 12)
{
    if ($day == 1)
    {
        $api->GameAddAnnouncement("That concludes the turkey hunting season for " . date('Y') . "! The top five players who bagged the most kills are now visible on the Milestone page on explore. With that in mind, we're rolling right into Christmas season with the reintroduction of the CID Advent Calendar, Christmas Tree and Christmas Wish! See them on the explore page! Happy Holidays! :)");
    }
}

if ($month == 4)
{
    if ($day == 20)
    {
        $api->GameAddAnnouncement("Greetings warriors! I know today isn't techincally a holiday, but it is sorta a 
                                    holiday to some. I figured, to make up for missing Easter and St. Patties 
                                    (life, sorry!), I'd throw in a small, unexpected event today.<br />
                                    I know it isn't much, but logging in will give you a new badge, and a 
                                    random amount of Copper Coins and Chivalry Tokens. I can't stress enough, I appreciate 
                                    and thank each and everyone who logs in and plays Chivalry is Dead.");
    }
}
?>
