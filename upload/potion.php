<?php
/*	File:		potion.php
	Created: 	Dec 1, 2022; 12:53:17 PM
	Info: 		
	Author:		Ryan
	Website: 	https://chivalryisdeadgame.com/
*/
require('globals.php');
if (!isset($_GET['potion'])) 
{
    $_GET['potion'] = '';
}
switch ($_GET['potion']) 
{
    case "poison":
        poison();
        break;
    default:
        home();
        break;
}

function poison()
{
    $potionID = 258;
    global $db, $userid, $h, $api;
    if ($api->UserHasItem($userid, $potionID))
    {
        if (userHasEffect($userid, effect_poisoned_weaps))
        {
            alert('danger',"Uh Oh!", "You already have poisoned your weaponry, no sense in doing it again.", true, 'inventory.php');
            die($h->endpage());
        }
        $api->UserTakeItem($userid, $potionID, 1);
        $rndtime = Random(15,45);
        userGiveEffect($userid, effect_poisoned_weaps, $rndtime * 60);
        alert("success","Success!","You've tipped your weapons in this vial of poison, your strikes now have a 8% chance of poisoning your opponent for the next {$rndtime} minutes.",true,'inventory.php');
        $api->SystemLogsAdd($userid, 'itemuse', "Used {$api->SystemItemIDtoName($potionID)}.");
        die($h->endpage());
    }
    else
    {
        alert('danger',"Uh Oh!","You need a {$api->SystemItemIDtoName($potionID)} to apply poison to your weapons.",true,'inventory.php');
        die($h->endpage());
    }
}