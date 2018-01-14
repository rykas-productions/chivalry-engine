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
    $tokenreward=Random(5,25)*($ir['dayslogged']+1);
    $scrollreward=Random(1,2)*($ir['dayslogged']+1);
	$luck=Random(2,10);
    $reward=Random(-5,5);
    if ($reward <= 5)
    {
        $api->GameAddNotification($userid,"For logging in today, you have received " . number_format($tokenreward) . " Chivalry Tokens.");
        $api->UserGiveCurrency($userid,'secondary',$tokenreward);
    }
	elseif ($reward == 6 || $reward == 7)
    {
        $api->GameAddNotification($userid,"For logging in today, your luck has changed by {$luck}%.");
		$db->query("UPDATE `userstats` SET `luck` = `luck` + ({$luck}) WHERE `userid` = {$userid}");
        
    }
    if ($reward >= 8)
    {
        $api->GameAddNotification($userid,"For logging in today, you have received " . number_format($scrollreward) . " Chivalry Scrolls.");
        $api->UserGiveItem($userid,18,$scrollreward);
    }
    $db->query("UPDATE `users` SET `rewarded` = 1 WHERE `userid` = {$userid}");
}