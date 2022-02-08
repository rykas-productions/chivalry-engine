<?php
require('globals.php');
if ($api->UserStatus($userid,'dungeon') || $api->UserStatus($userid,'infirmary'))
{
	alert('danger',"Uh Oh!","You cannot visit the Mission Center while in the dungeon or infirmary.",true,'explore.php');
	die($h->endpage());
}
$am=$db->query("SELECT * FROM `missions` WHERE `mission_userid` = {$userid}");
echo "<h3><i class='game-icon game-icon-stabbed-note'></i> Missions</h3><hr />";
if ($db->num_rows($am) == 0)
{
	if (isset($_GET['accept']))
	{
		$days=Random(1,3);
		$kills=(Random(5,15)+Random($ir['level']/4,$ir['level']/2))*$days;
		$reward = 0;
		$loops = 0;
		while ($loops != $kills)
		{
		    $random = Random(4000, 20000);
		    $reward = $reward + round($random + ($random * levelMultiplier($ir['level'], $ir['reset'])));
		    $loops++;
		}
		$endtime=time()+($days*86400);
		$db->query("INSERT INTO `missions` 
		(`mission_userid`, `mission_kills`, 
		`mission_end`, `mission_kill_count`, `mission_reward`) 
		VALUES ('{$userid}', '{$kills}', '{$endtime}', '0', '{$reward}')");
		echo "Your current mission:<br />
		Kills Required: " . number_format($kills) . "<br />
		Kill Count: 0<br />
		Reward: " . number_format($reward) . " Copper Coins<br />
		Time Left: " . TimeUntil_Parse($endtime) ."<br />";
	}
	else
	{
		echo "Here's how missions work. You will be given a task that involves beating other players in battle. 
		You will be given a number of people to kill, and you will have a set timeframe in which to kill them. 
		If you click Accept, you agree to accept the mission you receive. If you successfully complete your mission, 
		you will be rewarded.<br />
		<a href='?accept' class='btn btn-primary'>Accept Mission</a>";
	}
}
else
{
	$mr=$db->fetch_row($am);
	echo "Your current mission:<br />
		Kills Required: " . number_format($mr['mission_kills']) . "<br />
		Kill Count: " . number_format($mr['mission_kill_count']) . "<br />
		Reward: " . number_format($mr['mission_reward']) . " Copper Coins<br />
		Time Left: " . TimeUntil_Parse($mr['mission_end']) ."<br />";
}
$h->endpage();