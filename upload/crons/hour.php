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
require_once('../globals_nonauth.php');
if (!isset($_GET['code']) || $_GET['code'] !== $_CONFIG['code'])
{
    exit;
}
//Job crons!
$db->query("UPDATE `users` AS `u`
	LEFT JOIN `job_ranks` as `jr` ON `jr`.`jrID` = `u`.`jobrank`
	SET `u`.`primary_currency` = `u`.`primary_currency` + `jr`.`jrPRIMPAY`,
	`u`.`secondary_currency` = `u`.`secondary_currency` + `jr`.`jrSECONDARY`
	WHERE `u`.`job` > 0 AND `u`.`jobrank` > 0");
$db->query("UPDATE `users` AS `u`
	LEFT JOIN `job_ranks` as `jr` ON `jr`.`jrID` = `u`.`jobrank`
	SET `u`.`primary_currency` = `u`.`primary_currency` + (`jr`.`jrPRIMPAY`*0.5),
	`u`.`secondary_currency` = `u`.`secondary_currency` + (`jr`.`jrSECONDARY`*0.5)
	WHERE `u`.`job` > 0 AND `u`.`jobrank` > 0 AND `u`.`jobwork` >= `jr`.`jrACT`");
$db->query("UPDATE `users` SET `jobwork` = 0 WHERE `jobwork` > 0 AND `job` > 0 AND `jobrank` > 0");

$db->query("UPDATE `user_settings` SET `winnings_this_hour` = 0");
?>
