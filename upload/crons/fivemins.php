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
	//Mining refill
	$db->query("UPDATE mining SET miningpower=miningpower+(max_miningpower/(10)) WHERE miningpower<max_miningpower");
	$db->query("UPDATE mining SET miningpower=max_miningpower WHERE miningpower>max_miningpower");
	
	$allusers_query ="UPDATE `users` SET `brave` = LEAST(`brave` + ((`maxbrave` / 10) + 0.5), `maxbrave`),
        `hp` = LEAST(`hp` + (`maxhp` / 3), `maxhp`), `will` = LEAST(`will` + 10, `maxwill`),
        `energy` = IF(`vip_days` > 0,
                   LEAST(`energy` + (`maxenergy` / 6), `maxenergy`),
                   LEAST(`energy` + (`maxenergy` / 12.5), `maxenergy`))";
	$db->query($allusers_query);
	
	$time = 300;
	$db->query("UPDATE `crons` SET `nextUpdate`=`nextUpdate`+{$time} WHERE `file`='{$file}'");
}

?>
