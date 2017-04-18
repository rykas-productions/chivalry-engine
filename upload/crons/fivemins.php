<?php
/*
	File: crons/fivemin.php
	Created: 6/15/2016 at 2:43PM Eastern Time
	Info: Runs the queries below every five minutes.
	Add queries of your own to have queries executed every five minutes
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine
*/
$file = 'crons/fivemins.php';

$ready_to_run = $db->query("SELECT `nextUpdate` FROM `crons` WHERE `file`='{$file}' AND `nextUpdate` <= unix_timestamp()");
//Place your queries inside this conditional
if ($db->num_rows($ready_to_run)) 
{
	//Brave update
	$db->query("UPDATE users SET brave=brave+((maxbrave/10)+0.5) WHERE brave<maxbrave");
	$db->query("UPDATE users SET brave=maxbrave WHERE brave>maxbrave");
	
	//HP Update
	$db->query("UPDATE users SET hp=hp+(maxhp/3) WHERE hp<maxhp");
	$db->query("UPDATE users SET hp=maxhp WHERE hp>maxhp");
	
	//energy update
	$db->query("UPDATE users SET energy=energy+(maxenergy/(12.5)) WHERE energy<maxenergy AND `vip_days`=0");
	$db->query("UPDATE users SET energy=energy+(maxenergy/(6)) WHERE energy<maxenergy AND `vip_days`>0");
	$db->query("UPDATE users SET energy=maxenergy WHERE energy>maxenergy");
	
	//Will Update
	$db->query("UPDATE users SET will=will+(maxwill/(10)) WHERE will<maxwill");
	$db->query("UPDATE users SET will=maxwill WHERE will>maxwill");
	
	//Mining refill
	$db->query("UPDATE mining SET miningpower=miningpower+(max_miningpower/(10)) WHERE miningpower<max_miningpower");
	$db->query("UPDATE mining SET miningpower=max_miningpower WHERE miningpower>max_miningpower");
	
	$time = 1;
	$db->query("UPDATE `crons` SET `nextUpdate`=`nextUpdate`+{$time} WHERE `file`='{$file}'");
}

?>
