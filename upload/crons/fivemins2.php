<?php
/*
	File: crons/fivemin.php
	Created: 6/15/2016 at 2:43PM Eastern Time
	Info: Runs the queries below every five minutes.
	Add queries of your own to have queries executed every five minutes
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
//Mining refill
$db->query("UPDATE `mining` SET `miningpower`=`miningpower`+(`max_miningpower`/(5)) WHERE `miningpower`<`max_miningpower`");
$db->query("UPDATE `mining` SET `miningpower`=`max_miningpower` WHERE `miningpower`>`max_miningpower`");
//Brave Refill
$db->query("UPDATE `users` SET `brave`=`brave`+((`maxbrave`/10)+0.5) WHERE `brave`<`maxbrave`");
$db->query("UPDATE `users` SET `brave`=`maxbrave` WHERE `brave`>`maxbrave`");
//HP Refill
$db->query("UPDATE users SET hp=hp+(maxhp/2) WHERE hp<maxhp");
$db->query("UPDATE users SET hp=maxhp WHERE hp>maxhp");
//Energy Refill
$db->query("UPDATE users SET energy=energy+(maxenergy/(6)) WHERE energy<maxenergy AND vip_days=0");
$db->query("UPDATE users SET energy=energy+(maxenergy/(3)) WHERE energy<maxenergy AND vip_days>0");
$db->query("UPDATE users SET energy=maxenergy WHERE energy>maxenergy");
//Will refill
$db->query("UPDATE users SET will=will+(maxwill/10) WHERE will<maxwill");
$db->query("UPDATE users SET will = maxwill WHERE will > maxwill");
//Farm water update
$db->query("UPDATE `farm_users` SET `farm_water_available` = `farm_water_available` + 5");
$db->query("UPDATE `farm_users` SET `farm_water_available` = `farm_water_max` WHERE `farm_water_available` > `farm_water_max`");

//Wood Cutter
$increase = $set['cutter_capacity_max'] * 0.02;
$db->query("UPDATE `settings` SET `setting_value` =  `setting_value` + {$increase} WHERE `setting_name` = 'cutter_capacity'");
$db->query("UPDATE `settings` SET `setting_value` = {$set['cutter_capacity_max']} WHERE `setting_name` = 'cutter_capacity' AND `setting_value` > {$set['cutter_capacity_max']}");

fiveMinuteFarm();
runMarketTick(4);   //med-high risk market
?>
