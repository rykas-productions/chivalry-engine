<?php
require('globals.php');
$time = time();
$q=$db->query("SELECT * FROM `users_effects` WHERE `userid` = {$userid} AND `effectTimeOut` > {$time}");
while ($r = $db->fetch_row($q))
{
	echo "{$r['effectName']} x {$r['effectMulti']} until " . TimeUntil_Parse($r['effectTimeOut']) . ".<br />";
}
$h->endpage();