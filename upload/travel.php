<?php
/*
	File:		travel.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Allows players to travel to another town, if their 
				level is high enough.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
	MIT License

	Copyright (c) 2019 TheMasterGeneral

	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in all
	copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
	SOFTWARE.
*/
require('globals.php');
//Set cost to travel to a variable
$cost_of_travel = 250 * $ir['level'];
echo "<h3>Travel Agent</h3><hr />";
//Make sure GET is set to work with.
$_GET['to'] = (isset($_GET['to']) && is_numeric($_GET['to'])) ? abs($_GET['to']) : '';
if (empty($_GET['to'])) {
    echo "Welcome to the horse stable. You can travel to other cities here, but at a cost. Where would you like to
	travel today? Note that as you progress further in the game, more locations will be made available to you.
	It will cost you " . number_format($cost_of_travel) . " " . constant("primary_currency") . " to travel today.
	<table class='table table-bordered'>
	<tr>
		<th width='25%'>
			Town
		</th>
		<th width='15%'>
			Level Required
		</th>
		<th width='10%'>
			>>>
		</th>
	</tr>";
    //Select the towns that are not the current user's town, order them by level requirement
    $q = $db->query("SELECT * FROM `town` WHERE `town_id` != {$ir['location']} ORDER BY `town_min_level` ASC");

    //Show this information!
    while ($r = $db->fetch_row($q)) {
        echo "
		<tr>
			<td>{$r['town_name']}</td>
			<td>{$r['town_min_level']}</td>
			<td><a href='?to={$r['town_id']}'>Travel</a></td>
		</tr>
   		";
    }
    echo "</table>";
} else {
    //User does not have enough cash to travel to this city.
    if ($ir['primary_currency'] < $cost_of_travel) {
        alert('danger', "Uh Oh!", "You do not have enough " . constant("primary_currency") . " to travel today.", true, "travel.php");
        die($h->endpage());
    //User is trying to travel to the town they're already in.
    } elseif ($ir['location'] == $_GET['to']) {
        alert('danger', "Uh Oh!", "Why would you want to travel to the town you're already in.", true, "travel.php");
        die($h->endpage());
    } else {
        //Select town info.
        $q = $db->query("SELECT `town_name` FROM `town` WHERE `town_id` = {$_GET['to']} AND `town_min_level` <= {$ir['level']}");

        //Town does not exist or user's level is too low.
        if (!$db->num_rows($q)) {
            alert('danger', "Uh Oh!", "The town you wish to travel to does not exist, or you're too low of a level to reach.", true, "travel.php");
            die($h->endpage());
        } else {
            //Update user!
            $api->user->takeCurrency($userid,'primary',$cost_of_travel);
            $db->query("UPDATE `users` SET `location` = {$_GET['to']} WHERE `userid` = {$userid}");
            $cityName = $db->fetch_single($q);
            //Tell user they have traveled successfully.
            alert('success', "Success!", "You have successfully paid " . number_format($cost_of_travel) . " Primary
			 Currency to take a horse to {$cityName}.", true, "index.php");
            $api->game->addLog($userid, 'travel', "Traveled to {$cityName} for {$cost_of_travel}.");
            die($h->endpage());
        }
        $db->free_result($q);
    }
}
$h->endpage();