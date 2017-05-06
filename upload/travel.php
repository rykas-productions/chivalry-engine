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
echo "<h3>{$lang['TRAVEL_TITLE']}</h3><hr />";
$_GET['to'] = (isset($_GET['to']) && is_numeric($_GET['to'])) ? abs($_GET['to']) : '';
if (empty($_GET['to']))
{
	echo "{$lang['TRAVEL_TABLE']} " . number_format($cost_of_travel) . " {$lang['TRAVEL_TABLE2']}
	<table class='table table-bordered'>
	<tr>
		<th width='25%'>
			{$lang['TRAVEL_TABLE_HEADER']}
		</th>
		<th width='15%'>
			{$lang['TRAVEL_TABLE_LEVEL']}
		</th>
		<th>
			{$lang['TRAVEL_TABLE_GUILD']}
		</th>
		<th width='15%'>
			{$lang['TRAVEL_TABLE_TAX']}
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
			<td><a href='?to={$r['town_id']}'>{$lang['TRAVEL_TABLE_TRAVEL']}</a></td>
		</tr>
   		";
    }
	echo"</table>";
}
else
{
	if ($ir['primary_currency'] < $cost_of_travel)
    {
        alert('danger',$lang['ERROR_GENERIC'],$lang['TRAVEL_ERROR_CASHLOW'],true,"travel.php");
		die($h->endpage());
    }
    elseif ($ir['location'] == $_GET['to'])
    {
        alert('danger',$lang['ERROR_GENERIC'],$lang['TRAVEL_ERROR_ALREADYTHERE'],true,"travel.php");
		die($h->endpage());
    }
	else
	{
		$q = $db->query("SELECT `town_name` FROM `town` WHERE `town_id` = {$_GET['to']}
                         AND `town_min_level` <= {$ir['level']}");
		if (!$db->num_rows($q))
        {
            alert('danger',$lang['ERROR_GENERIC'],$lang['TRAVEL_ERROR_ERRORGEN'],true,"travel.php");
			die($h->endpage());
        }
		else
		{
			$db->query("UPDATE `users` SET `primary_currency` = `primary_currency` - {$cost_of_travel},
                     `location` = {$_GET['to']} WHERE `userid` = {$userid}");
			$cityName = $db->fetch_single($q);
			alert('success',$lang['ERROR_SUCCESS'],"{$lang['TRAVEL_SUCCESS']} " . $cityName . " {$lang['GEN_FOR']} " . number_format($cost_of_travel),true,"index.php");
			$api->SystemLogsAdd($userid,'travel',"Traveled to {$cityName}.");
			die($h->endpage());
		}
		$db->free_result($q);
	}
}
$h->endpage();