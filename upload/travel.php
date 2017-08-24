<?php
/*
	File:		travel.php
	Created: 	4/5/2016 at 12:29AM Eastern Time
	Info: 		Allows players to travel to new locations, dependent
				on their level.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require('globals.php');
$cost_of_travel = 250*$ir['level'];
echo "<h3>Travel Agent</h3><hr />";
$_GET['to'] = (isset($_GET['to']) && is_numeric($_GET['to'])) ? abs($_GET['to']) : '';
if (empty($_GET['to']))
{
	echo "Welcome to the horse stable. You can travel to other cities here, but at a cost. Where would you like to
	travel today? Note that as you progress further in the game, more locations will be made available to you.
	It will cost you " . number_format($cost_of_travel) . " Primary Currency to travel today.
	<table class='table table-bordered'>
	<tr>
		<th width='25%'>
			Town
		</th>
		<th width='15%'>
			Level Required
		</th>
		<th>
			Guild
		</th>
		<th width='15%'>
			Tax Level
		</th>
		<th width='10%'>
			>>>
		</th>
	</tr>";
	$q = $db->query("SELECT * FROM `town` WHERE `town_id` != {$ir['location']} ORDER BY `town_min_level` ASC");
	while ($r = $db->fetch_row($q))
    {
		if ($r['town_guild_owner'] > 0)
		{
			$name=$db->fetch_single($db->query("SELECT `guild_name` FROM `guild` WHERE `guild_id` = {$r['town_guild_owner']}"));
		}
		else
		{
			$name = "Unowned";
		}
        echo "
		<tr>
			<td>{$r['town_name']}</td>
			<td>{$r['town_min_level']}</td>
			<td>{$name}</td>
			<td>{$r['town_tax']}%</td>
			<td><a href='?to={$r['town_id']}'>Travel</a></td>
		</tr>
   		";
    }
	echo"</table>";
}
else
{
	if ($ir['primary_currency'] < $cost_of_travel)
    {
        alert('danger',"Uh Oh!","You do not have enough Primary Currency to travel today.",true,"travel.php");
		die($h->endpage());
    }
    elseif ($ir['location'] == $_GET['to'])
    {
        alert('danger',"Uh Oh!","Why would you want to travel to the town you're already in.",true,"travel.php");
		die($h->endpage());
    }
	else
	{
		$q = $db->query("SELECT `town_name` FROM `town` WHERE `town_id` = {$_GET['to']}
                         AND `town_min_level` <= {$ir['level']}");
		if (!$db->num_rows($q))
        {
            alert('danger',"Uh Oh!","The town you wanna travel to does not exist.",true,"travel.php");
			die($h->endpage());
        }
		else
		{
			$db->query("UPDATE `users` SET `primary_currency` = `primary_currency` - {$cost_of_travel},
                     `location` = {$_GET['to']} WHERE `userid` = {$userid}");
			$cityName = $db->fetch_single($q);
			alert('success',"Success!","You have successfully paid " . number_format($cost_of_travel) . " Primary
			 Currency to take a horse to {$cityName}.",true,"index.php");
			$api->SystemLogsAdd($userid,'travel',"Traveled to {$cityName} for {$cost_of_travel}.");
			die($h->endpage());
		}
		$db->free_result($q);
	}
}
$h->endpage();