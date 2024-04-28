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


runMarketTick(3);   //med risk market
if ((date('G') == 6) || (date('G') == 12) || (date('G') == 18))
{
    runMarketTick(2);   //lower risk
	addTokenMarketListing();
}
backupDatabase();
giveNPCsMoney();
//halloween 2023
$db->query("UPDATE `2018_halloween_chuck` SET `count` = `count` + 2 WHERE `count` < 20");
$db->query("UPDATE `2018_halloween_chuck` SET `count` = 20 WHERE `count` > 20");
$db->query("TRUNCATE `2018_halloween_tot`");
?>
