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
$cost_of_travel = round(15 * levelMultiplier($ir['level'], $ir['reset']));
if ($cost_of_travel > 75)
    $cost_of_travel = 75;
if ($api->UserHasItem($userid,269))
	$cost_of_travel *= 0.5;
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
//Make sure GET is set to work with.
$_GET['to'] = (isset($_GET['to']) && is_numeric($_GET['to'])) ? abs($_GET['to']) : '';
if (empty($_GET['to'])) {
    alert("secondary","","As the sun sets beyond the horizon, casting a golden glow upon the tranquil countryside, the Horse Stable stands as a steadfast symbol of unity and resilience in a world fraught with uncertainty. For in its humble confines, the bond between horse and rider is forged anew with each passing day, echoing the timeless spirit of medieval Europe.", false);
    alert("info","","<b>It will cost you " . shortNumberParse($cost_of_travel) . " Chivalry Tokens to travel.</b>",false);
    echo "
	<div class='row'>
        <div class='col-12'>
            <div class='card'>
                <div class='card-header'>
                    {$set['WebsiteName']} Towns
                </div>
                <div class='card-body'> ";
    //Select the towns that are not the current user's town, order them by level requirement
    $q = $db->query("/*qc=on*/SELECT * FROM `town` WHERE `town_id` != {$ir['location']} ORDER BY `town_min_level` ASC");

    //Show this information!
    while ($r = $db->fetch_row($q)) 
	{
		$guild_owner = $db->fetch_single($db->query("/*qc=on*/SELECT `guild_name` FROM `guild` WHERE `guild_id` = {$r['town_guild_owner']}"));
		$level = ($ir['level'] > $r['town_min_level']) ? "text-success" : "text-danger font-weight-bold" ;
		$name = ($r['town_guild_owner'] > 0) ? "<a href='guilds.php?action=view&id={$r['town_guild_owner']}'>{$guild_owner}</a>": "" ;
		$tax = ($r['town_tax'] > 0) ? "{$r['town_tax']}%" : "0%" ;
		$guildcolor = ($ir['guild'] == $r['town_guild_owner']) ? "class='text-success'" : "class='text-danger font-weight-bold'" ;
		$population = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `users` WHERE `location` = {$r['town_id']}"));
		echo "<div class='row'>
                <div class='col-12 col-lg-3 col-xl-2'>
                    <div class='row'>
                        <div class='col-12'>
                             <small><b>Town Name</b></small>
                        </div>
                        <div class='col-12'>
                            <a href='?to={$r['town_id']}'>{$r['town_name']}</a>
                        </div>
                    </div>
                </div>
                <div class='col-12 col-lg-9 col-xl'>
                    <div class='row'>
                        <div class='col-12'>
                             <small><b>Town Info</b></small>
                        </div>
                        <div class='col-12'>
                            <i>{$r['town_desc']}</i>
                        </div>
                        <div class='col-12'>
                            <small><b>Population:</b> " . shortNumberParse($population) . "</small>
                        </div>
                    </div>
                </div>
                <div class='col-12 col-xl-3 col-xxxl-2'>
                    <div class='row'>
                        <div class='col-12'>
                             <small><b>Regulations</b></small>
                        </div>
                        <div class='col-12 {$level} col-sm-6 col-lg-4 col-xl-12'>
                            Minimum Level " . shortNumberParse($r['town_min_level']) . "
                        </div>";
		                  if ($r['town_tax'] > 0)
		                  {
		                      echo"
                                <div class='text-danger col-12 col-sm-6 col-lg-3 col-xl-12'>
                                    Tax {$tax}
                                </div>";
		                  }
		                  if ($r['town_guild_owner'] > 0)
		                  {
		                      echo"
                                <div class='col-12 col-sm'>
                                    Controlled by {$name}
                                </div>";
		                  }
		                  echo"
                    </div>
                </div>";
        		echo"
            </div>
            <br />";
    }
    echo "
            </div>
        </div>
    <br />

    <div class='card'>
        <div class='card-body'>
            <div class='row'>
                <div class='col-12'>
                    <img src='https://res.cloudinary.com/dydidizue/image/upload/v1520819397/horse-stable-travel.jpg' class='img-thumbnail img-responsive'>
                </div>
            </div>
        </div>
    </div></div>";
} else {
    //User does not have enough cash to travel to this city.
    if ($ir['secondary_currency'] < $cost_of_travel) {
        alert('danger', "Uh Oh!", "You do not have enough Chivalry Tokens to travel today.", true, "travel.php");
        die($h->endpage());
    //User is trying to travel to the town they're already in.
    } 
    elseif ($ir['location'] == $_GET['to']) 
    {
        alert('danger', "Uh Oh!", "Why would you want to travel to the town you're already in.", true, "travel.php");
        die($h->endpage());
    } 
    else 
    {
        if (!isCourseComplete($ir['userid'], 29) && ($_GET['to'] == 27))
        {
            alert('danger',"Uh Oh!", "Please complete the <i>Runic Mysteries</i> academic course to learn the way to travel to the Essence Isle.", true, "travel.php");
            die($h->endpage());
        }
        //Select town info.
        $q = $db->query("/*qc=on*/SELECT `town_name` FROM `town` WHERE `town_id` = {$_GET['to']} AND `town_min_level` <= {$ir['level']}");

        //Town does not exist or user's level is too low.
        if (!$db->num_rows($q)) 
        {
            alert('danger', "Uh Oh!", "The town you wish to travel to does not exist, or you're too low of a level to reach.", true, "travel.php");
            die($h->endpage());
        }
        else 
        {
            //Update user!
            $api->UserTakeCurrency($userid,'secondary',$cost_of_travel);
            $db->query("UPDATE `users` SET `location` = {$_GET['to']} WHERE `userid` = {$userid}");
            $cityName = $db->fetch_single($q);
            //Tell user they have traveled successfully.
            alert('success', "Success!", "You have successfully paid " . number_format($cost_of_travel) . " Chivalry Tokens to take a horse to {$cityName}.", true, "index.php");
            $api->SystemLogsAdd($userid, 'travel', "Traveled to {$cityName} for {$cost_of_travel} Chivalry Tokens.");
			if (Random(1,100) == 42)
			{
				$api->GameAddNotification($userid,"You found a Travel Badge while travelling to your current town. Check your inventory.", "fas fa-horse", "#594026");
				$api->UserGiveItem($userid,273,1);
			}
			user_log($userid,'travel');
			addToEconomyLog('Travel', 'token', $cost_of_travel*-1);
            die($h->endpage());
        }
        $db->free_result($q);
    }
}
$h->endpage();