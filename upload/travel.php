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
//Set cost to travel to a variable
$cost_of_travel = 15000 * levelMultiplier($ir['level']);
if ($cost_of_travel > 50000)
    $cost_of_travel=50000;
//Block access if user is in the infirmary.
if ($api->UserStatus($ir['userid'], 'infirmary')) {
    alert('danger', "Unconscious!", "You cannot travel while you're in the infirmary.", false);
    die($h->endpage());
}
//Block access if user is in the dungeon.
if ($api->UserStatus($ir['userid'], 'dungeon')) {
    alert('danger', "Locked Up!", "You cannot travel while you're in the dungeon.");
    die($h->endpage());
}
echo "<h3>Travel Agent</h3><hr />";
//Make sure GET is set to work with.
$_GET['to'] = (isset($_GET['to']) && is_numeric($_GET['to'])) ? abs($_GET['to']) : '';
if (empty($_GET['to'])) {
    echo "Welcome to the horse stable. You can travel to other cities here, but at a cost. Where would you like to
	travel today? Note that as you progress further in the game, more locations will be made available to you.
	It will cost you " . number_format($cost_of_travel) . " Copper Coins to travel today.
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
    //Select the towns that are not the current user's town, order them by level requirement
    $q = $db->query("/*qc=on*/SELECT * FROM `town` WHERE `town_id` != {$ir['location']} AND `town_min_level` <= {$ir['level']} ORDER BY `town_min_level` ASC");

    //Show this information!
    while ($r = $db->fetch_row($q)) {
        //Does the town in question have a guild owner? If so, select their name! If not... say its unowned.
        if ($r['town_guild_owner'] > 0) {
            $name = $db->fetch_single($db->query("/*qc=on*/SELECT `guild_name` FROM `guild` WHERE `guild_id` = {$r['town_guild_owner']}"));
        } else {
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
    echo "</table>
	<img src='https://res.cloudinary.com/dydidizue/image/upload/v1520819397/horse-stable-travel.jpg' class='img-thumbnail img-responsive'>";
} else {
    //User does not have enough cash to travel to this city.
    if ($ir['primary_currency'] < $cost_of_travel) {
        alert('danger', "Uh Oh!", "You do not have enough Copper Coins to travel today.", true, "travel.php");
        die($h->endpage());
    //User is trying to travel to the town they're already in.
    } elseif ($ir['location'] == $_GET['to']) {
        alert('danger', "Uh Oh!", "Why would you want to travel to the town you're already in.", true, "travel.php");
        die($h->endpage());
    } else {
        //Select town info.
        $q = $db->query("/*qc=on*/SELECT `town_name` FROM `town` WHERE `town_id` = {$_GET['to']} AND `town_min_level` <= {$ir['level']}");

        //Town does not exist or user's level is too low.
        if (!$db->num_rows($q)) {
            alert('danger', "Uh Oh!", "The town you wish to travel to does not exist, or you're too low of a level to reach.", true, "travel.php");
            die($h->endpage());
        } else {
            //Update user!
            $api->UserTakeCurrency($userid,'primary',$cost_of_travel);
            $db->query("UPDATE `users` SET `location` = {$_GET['to']} WHERE `userid` = {$userid}");
            $cityName = $db->fetch_single($q);
            //Tell user they have traveled successfully.
            alert('success', "Success!", "You have successfully paid " . number_format($cost_of_travel) . " Copper Coins to take a horse to {$cityName}.", true, "index.php");
            $api->SystemLogsAdd($userid, 'travel', "Traveled to {$cityName} for {$cost_of_travel} Copper Coins.");
			user_log($userid,'travel');
            die($h->endpage());
        }
        $db->free_result($q);
    }
}
$h->endpage();