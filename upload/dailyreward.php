<?php
/*
	File:		dailyreward.php
	Created: 	10/24/2017 at 1:44PM Eastern Time
	Info: 		Daily rewards
	Author:		TheMasterGeneral
	Website: 	http://chivalryisdead.x10.mx
*/
if ($ir['rewarded'] == 0)
{
    $tokenreward=$ir['level']*Random(50,750);
    $scrollreward=Random(5,15);
	$luck=Random(5,25);
    $reward=Random(1,10);
    if ($reward <= 5)
    {
        $api->GameAddNotification($userid,"For logging in today, you have received " . number_format($tokenreward) . " Copper Coins.");
        $api->UserGiveCurrency($userid,'primary',$tokenreward);
        $api->SystemLogsAdd($userid, 'loginreward', "Received " . number_format($tokenreward) . " Copper Coins.");
    }
	elseif ($reward == 6 || $reward == 7)
    {
        $api->GameAddNotification($userid,"For logging in today, your luck has increased by {$luck}%.");
		$db->query("UPDATE `userstats` SET `luck` = `luck` + ({$luck}) WHERE `userid` = {$userid}");
        $api->SystemLogsAdd($userid, 'loginreward', "Received {$luck}% Luck.");
        
    }
    if ($reward == 8)
    {
        $api->GameAddNotification($userid,"For logging in today, you have received a Mysterious Potion.");
        $api->UserGiveItem($userid,123,1);
        $api->SystemLogsAdd($userid, 'loginreward', "Received Mysterious Potion."); 
    }
    if ($reward >= 9)
    {
        $api->GameAddNotification($userid,"For logging in today, you have received " . number_format($scrollreward) . " Chivalry Gym Scrolls.");
        $api->UserGiveItem($userid,18,$scrollreward);
        $api->SystemLogsAdd($userid, 'loginreward', "Received {$scrollreward} Chivalry Gym Scrolls.");
    }
    $db->query("UPDATE `users` SET `rewarded` = 1 WHERE `userid` = {$userid}");
}