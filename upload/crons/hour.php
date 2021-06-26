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
if (!isset($argv))
{
    exit;
}
$_GET['code']=substr($argv[1],5);
if (!isset($_GET['code']) || $_GET['code'] !== $_CONFIG['code'])
{
    exit;
}
if ((date('G') >= 9) && (date('G') <= 17))
{
	//Job crons!
	$db->query("UPDATE `users` AS `u`
		LEFT JOIN `job_ranks` as `jr` ON `jr`.`jrID` = `u`.`jobrank`
		SET `u`.`primary_currency` = `u`.`primary_currency` + `jr`.`jrPRIMPAY`,
		`u`.`secondary_currency` = `u`.`secondary_currency` + `jr`.`jrSECONDARY` 
		WHERE `u`.`job` > 0 AND `u`.`jobrank` > 0");
}

$db->query("UPDATE `user_settings` SET `winnings_this_hour` = 0");
$db->query("UPDATE `settings` SET `setting_value` = `setting_value` - 1 WHERE `setting_name` = 'raffle_chance' AND `setting_value` > 10");
$db->query("UPDATE `settings` SET `setting_value` = `setting_value` + 60000 WHERE `setting_id` = 28");

addToEconomyLog('Gambling', 'copper', 60000);

//Street Bum
$db->query("UPDATE `user_settings` SET `searchtown` = `searchtown` + 25");
$db->query("UPDATE `user_settings` SET `searchtown` = 100 WHERE `searchtown` > 100");

runMarketTick(3);   //med risk market
if ((date('G') == 6) || (date('G') == 12))
    runMarketTick(2);   //lower risk
?>
