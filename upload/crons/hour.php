<?php
/*
	File: crons/hour.php
	Created: 6/15/2016 at 2:44PM Eastern Time
	Info: Runs the queries below hourly. Place your own queries
	here that you wish to be executed hourly.
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine
*/

$file = 'crons/hour.php';

$ready_to_run = $db->query("SELECT `nextUpdate` FROM `crons` WHERE `file`='{$file}' AND `nextUpdate` <= unix_timestamp()");
//Place your queries inside the conditional
if ($db->num_rows($ready_to_run)) {
    $time = 3600;
    $db->query("UPDATE `crons` SET `nextUpdate`=`nextUpdate`+{$time} WHERE `file`='{$file}'");
}
?>
