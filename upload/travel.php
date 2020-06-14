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
if ($api->UserHasItem($userid,269))
	$cost_of_travel = $cost_of_travel*0.5;
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
	<div class='row'>";
    //Select the towns that are not the current user's town, order them by level requirement
    $q = $db->query("/*qc=on*/SELECT * FROM `town` WHERE `town_id` != {$ir['location']} ORDER BY `town_min_level` ASC");

    //Show this information!
    while ($r = $db->fetch_row($q)) 
	{
		$guild_owner = $db->fetch_single($db->query("/*qc=on*/SELECT `guild_name` FROM `guild` WHERE `guild_id` = {$r['town_guild_owner']}"));
		$level = ($ir['level'] > $r['town_min_level']) ? "class='text-success'" : "class='text-danger font-weight-bold'" ;
		$name = ($r['town_guild_owner'] > 0) ? "Guild Owner: <a href='guilds.php?action=biew&id={$r['town_guild_owner']}'>{$guild_owner}</a><br />" : "" ;
		$tax = ($r['town_tax'] > 0) ? "Town Tax: {$r['town_tax']}%<br />" : "" ;
		$guildcolor = ($ir['guild'] == $r['town_guild_owner']) ? "class='text-success'" : "class='text-danger font-weight-bold'" ;
		$population = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `users` WHERE `location` = {$r['town_id']}"));
		echo "
		<div class='col-md-4'>
		<div class='card'>
            <div class='card-header'>
               <a href='?to={$r['town_id']}'>{$r['town_name']}</a>
            </div>
            <div class='card-body'>
				<span>
					Population: " . number_format($population) . "<br />
				</span>
                <span {$level}>
					Level Required: " . number_format($r['town_min_level']) . "<br />
				</span>
				<span {$guildcolor}>
					{$name}
				</span>
				<span>
					{$tax}
				</span>
            </div>
        </div>
		<br />
		</div>";
    }
    echo "</div>
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
			if (Random(1,100) == 42)
			{
				$api->GameAddNotification($userid,"You found a Travel Badge while travelling to your current town. Check your inventory.", "fas fa-horse", "#594026");
				$api->UserGiveItem($userid,273,1);
			}
			user_log($userid,'travel');
            die($h->endpage());
        }
        $db->free_result($q);
    }
}
$h->endpage();