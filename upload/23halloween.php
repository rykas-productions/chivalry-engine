<?php
/*	File:		23halloween.php
    Created: 	Oct 22, 2022; 7:59:05 PM
    Info:
    Author:		Ryan
    Website: 	https://chivalryisdeadgame.com/
 */
require('globals.php');						//uncomment if user needs to be auth'd.
if ((date('n') != 10) && ($userid != 1))
{
        alert('danger',"Uh Oh!", "Fool, its not even October!!", true, "profile.php?user={$userid}");
        die($h->endpage());
}
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
switch ($_GET['action'])
{
    case "ticket":
        ticket();
        break;
    case "pumpkin":
        pumpkin();
        break;
    case "tnt":
        trick_or_treat();
        break;
    default:
        alert('danger',"Uh Oh!","Please specify a valid action.",true,'explore.php');
        $h->endpage();
        break;
}

function trick_or_treat()
{
    global $db, $userid, $api, $h;
    $_GET['user'] = (isset($_GET['user']) && is_numeric($_GET['user'])) ? abs($_GET['user']) : '';
    if (empty($_GET['user']))
    {
        alert('danger',"Uh Oh!", "Please select a valid player to visit and treat or treat from.", true, "profile.php?user={$userid}");
        die($h->endpage());
    }
    $q=$db->query("SELECT `username` FROM `users` WHERE `userid` = {$_GET['user']}");
    if ($db->num_rows($q) == 0)
    {
        alert('danger',"Uh Oh!", "You cannot trick or treat from non-existent players.", true, "profile.php?user={$userid}");
        die($h->endpage());
    }
    if ($_GET['user'] == $userid)
    {
        alert('danger',"Uh Oh!", "You cannot trick or treat from yourself...", true, "profile.php?user={$_GET['user']}");
        die($h->endpage());
    }
    $db->free_result($q);
    $q=$db->query("SELECT * FROM `2018_halloween_tot` WHERE `userid` = {$userid} AND `visited` = {$_GET['user']}");
    if ($db->num_rows($q) > 0)
    {
        alert('danger',"Uh Oh!", "You've already visited this player. It'd be a little rude to visit again so soon.", true, "profile.php?user={$_GET['user']}");
        die($h->endpage());
    }
    $candyItems = array(66,139,201,279,282,466,467,468,469);    //add if more candy
    $giftedItem = array_rand($candyItems, 1);
    $candy = $candyItems[$giftedItem];
    $newValue = getCurrentUserPref(date('Y') . "halloweenCandies",0) + 1;
    alert('success',"Trick or Treat!","While visiting {$api->SystemUserIDtoName($_GET['user'])}'s property for some candy, you were given a {$api->SystemItemIDtoName($candy)} and sent along your way. You've collected " . shortNumberParse($newValue) . " candies this Halloween season.", true, "profile.php?user={$_GET['user']}");
    $api->UserGiveItem($userid, $candy, 1);
    $api->SystemLogsAdd($userid, date('Y') . "halloween", "Given {$api->SystemItemIDtoName($candy)} from {$api->SystemUserIDtoName($_GET['user'])} " . parseUserID($_GET['user']));
    setCurrentUserPref(date('Y') . "halloweenCandies", $newValue);
    $api->GameAddNotification($_GET['user'], "<a href='profile.php?user={$userid}'>" . parseUsername($userid) . " " . parseUserID($userid) . "</a> has visitied you for Halloween!!");
    doHalloweenVisit($userid, $_GET['user']);
    $h->endpage();
}

function doHalloweenVisit($visitor, $visited)
{
    global $db;
    $db->query("INSERT INTO `2018_halloween_tot` (`userid`, `visited`) VALUES ('{$visitor}', '{$visited}')");
}