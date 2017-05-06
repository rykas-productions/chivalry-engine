<?php
/*
	File: crons/minute.php
	Created: 6/15/2016 at 2:45PM Eastern Time
	Info: Runs the queries below every minute.
	Place queries below that you wish to have rand
	every minute.
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine
*/
$file = 'crons/minute.php';

$ready_to_run = $db->query("SELECT `nextUpdate` FROM `crons` WHERE `file`='{$file}' AND `nextUpdate` <= unix_timestamp()");
//Place your queries inside this conditional
if ($db->num_rows($ready_to_run)) 
{
	$time = 60;
	$db->query("UPDATE `crons` SET `nextUpdate`=`nextUpdate`+{$time} WHERE `file`='{$file}'");
}

?>
