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
    $tokenreward=$ir['level']*Random(300,1500);
    $scrollreward=Random(10,20);
	$luck=Random(10,35);
    $reward=Random(1,10);
    if ($reward <= 5)
    {
        $api->GameAddNotification($userid,"For logging in today, you have received " . number_format($tokenreward) . " Copper Coins.", "game-icon game-icon-coins", "#B87333");
        $api->UserGiveCurrency($userid,'primary',$tokenreward);
        $api->SystemLogsAdd($userid, 'loginreward', "Received " . number_format($tokenreward) . " Copper Coins.");
    }
	elseif ($reward == 6 || $reward == 7)
    {
        $api->GameAddNotification($userid,"For logging in today, your luck has increased by {$luck}%.", "game-icon game-icon-clover", "green");
		$db->query("UPDATE `userstats` SET `luck` = `luck` + ({$luck}) WHERE `userid` = {$userid}");
        $api->SystemLogsAdd($userid, 'loginreward', "Received {$luck}% Luck.");
        
    }
    if ($reward == 8)
    {
        $api->GameAddNotification($userid,"For logging in today, you have received a Mysterious Potion.", "game-icon game-icon-drink-me light_to_dark");
        $api->UserGiveItem($userid,123,1);
        $api->SystemLogsAdd($userid, 'loginreward', "Received Mysterious Potion."); 
    }
    if ($reward >= 9)
    {
        $api->GameAddNotification($userid,"For logging in today, you have received " . number_format($scrollreward) . " Chivalry Gym Scrolls.", "game-icon game-icon-scroll-unfurled", "#f1e9d2");
        $api->UserGiveItem($userid,18,$scrollreward);
        $api->SystemLogsAdd($userid, 'loginreward', "Received {$scrollreward} Chivalry Gym Scrolls.");
    }
    $db->query("UPDATE `users` SET `rewarded` = 1 WHERE `userid` = {$userid}");
	/*if (date('j') == 25)
	{
		$api->UserGiveItem($userid,203,1);
	}*/
}