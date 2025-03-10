<?php
$menuhide=1;
$nohdr=true;
require_once('../../globals.php');
if (isset($_SERVER['REQUEST_METHOD']) && is_string($_SERVER['REQUEST_METHOD']))
{
    if (strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST')
    {
        // Ignore a GET request
        header('HTTP/1.1 400 Bad Request');
        exit;
    }
}
if (!is_ajax())
{
    header('HTTP/1.1 400 Bad Request');
    exit;
}
$cost_of_travel = round(15 * levelMultiplier($ir['level'], $ir['reset']));

if ($cost_of_travel > 50)
    $cost_of_travel=50;

if ($api->UserHasItem($userid, 269))
    $cost_of_travel = $cost_of_travel * 0.5;

if ($api->UserStatus($ir['userid'], 'infirmary')) 
{
    alert('danger', "Unconscious!", "You cannot travel while you're in the infirmary.", false);
    die($h->endpage());
}
//Block access if user is in the dungeon.
if ($api->UserStatus($ir['userid'], 'dungeon')) 
{
    alert('danger', "Locked Up!", "You cannot travel while you're in the dungeon.", false);
    die($h->endpage());
}
$destination = (isset($_POST['to']) && is_numeric($_POST['to'])) ? abs($_POST['to']) : '';
if ($ir['secondary_currency'] < $cost_of_travel) 
{
    alert('danger', "Uh Oh!", "You do not have enough Chivalry Tokens to travel today.", false);
    die($h->endpage());
    //User is trying to travel to the town they're already in.
} 
elseif ($ir['location'] == $destination) 
{
    alert('danger', "Uh Oh!", "Why would you want to travel to the town you're already in.", false);
    die($h->endpage());
}
$q = $db->query("/*qc=on*/SELECT `town_name` FROM `town` WHERE `town_id` = {$_POST['to']} AND `town_min_level` <= {$ir['level']}");

if (!$db->num_rows($q)) 
{
    alert('danger', "Uh Oh!", "The town you wish to travel to does not exist, or you're too low of a level to reach.", false);
    die($h->endpage());
}
$api->UserTakeCurrency($userid,'secondary',$cost_of_travel);
$db->query("UPDATE `users` SET `location` = {$_POST['to']} WHERE `userid` = {$userid}");
$cityName = $db->fetch_single($q);
//Tell user they have traveled successfully.
alert('success', "Success!", "You have successfully paid " . number_format($cost_of_travel) . " Chivalry Tokens to take a horse to {$cityName}.", false);
$api->SystemLogsAdd($userid, 'travel', "Traveled to {$cityName} for {$cost_of_travel} Chivalry Tokens.");
if (Random(1,100) == 42)
{
    $api->GameAddNotification($userid,"You found a Travel Badge while travelling to your current town. Check your inventory.", "fas fa-horse", "#594026");
    $api->UserGiveItem($userid,273,1);
}
user_log($userid,'travel');
addToEconomyLog('Travel', 'token', $cost_of_travel*-1);
die($h->endpage());