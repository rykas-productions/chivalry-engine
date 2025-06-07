<?php
/*
	File: crons/hour.php
	Created: 6/15/2016 at 2:44PM Eastern Time
	Info: Runs the queries below hourly. Place your own queries
	here that you wish to be executed hourly.
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine
*/
$menuhide=1;
require_once(__DIR__ .'/../globals_nonauth.php');
if (!isset($_GET['code']) || $_GET['code'] !== $_CONFIG['code'])
{
    exit;
}
doHourlyJobRewards();

$db->query("UPDATE `settings` SET `setting_value` = `setting_value` - 1 WHERE `setting_name` = 'raffle_chance' AND `setting_value` > 10");
$db->query("UPDATE `settings` SET `setting_value` = `setting_value` + 60000 WHERE `setting_id` = 28");

addToEconomyLog('Gambling', 'copper', 60000);

//Street Bum
if (currentMonth() == 9)
{
    $db->query("UPDATE `user_settings` SET `searchtown` = `searchtown` + 50");
    $db->query("UPDATE `user_settings` SET `searchtown` = 100 WHERE `searchtown` > 200");
}
else
{
    $db->query("UPDATE `user_settings` SET `searchtown` = `searchtown` + 25");
    $db->query("UPDATE `user_settings` SET `searchtown` = 100 WHERE `searchtown` > 100");
}

$month = currentMonth();
$day = currentDay();
$hour = currentHour();
$year = currentYear();


runMarketTick(3);   //med risk market
if ((currentHour() == 6) || (currentHour() == 12) || (currentHour() == 18) || (currentHour() == 0))
{
    runMarketTick(2);   //lower risk
	addTokenMarketListing();
}
backupDatabase();
giveNPCsMoney();
sendData();
if (currentMonth() == 10)
{
    $db->query("DELETE FROM `user_pref` WHERE `preference` = '" . currentYear() . "halloweenDailyThrow'");
    $db->query("TRUNCATE TABLE `2018_halloween_tot`");
}
if (($day == 1) && ($hour == 12))
{
    $q=$db->query("SELECT * FROM `vote_raffle` WHERE `userid` != 1 ORDER BY RAND() LIMIT 1");
    if ($db->num_rows($q) > 0)
    {
        $r=$db->fetch_row($q);
        $r['username'] = parseUsername($r['userid']);
        $announcement = "<a href='profile.php?user={$r['userid']}'>{$r['username']}</a> [{$r['userid']}] has won this month's voting raffle and received a $3 VIP Pack. Thanks for voting! The raffle has been reset, and everyone now has an equal shot to win this month's raffle!!";
        $api->GameAddAnnouncement($announcement, 1);
        $api->UserGiveItem($r['userid'],13,1);
        $api->GameAddNotification($r['userid'],"You were selected as the Monthly Vote Raffle winner! You've received a $3 VIP Pack to your inventory!");
        $db->query("TRUNCATE TABLE `vote_raffle`");
    }
    $db->query("UPDATE `settings` SET `setting_value` = '0.00' WHERE `setting_name` = 'MonthlyDonationGoal'");
}
?>
