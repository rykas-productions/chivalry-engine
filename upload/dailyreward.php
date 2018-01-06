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
    $tokenreward=Random(1,10)*($ir['dayslogged']+1);
    $coinreward=Random(500,1000)*($ir['dayslogged']+1);
    $scrollreward=Random(1,2)*($ir['dayslogged']+1);
    $reward=Random(1,10);
    if ($reward <= 7)
    {
        $api->GameAddNotification($userid,"For logging in today, you have received " . number_format($coinreward) . " Copper Coins.");
        $api->UserGiveCurrency($userid,'primary',$coinreward);
    }
    elseif ($reward == 8 || $reward == 9)
    {
        $api->GameAddNotification($userid,"For logging in today, you have received " . number_format($tokenreward) . " Chivalry Tokens.");
        $api->UserGiveCurrency($userid,'secondary',$tokenreward);
    }
    if ($reward == 10)
    {
        $api->GameAddNotification($userid,"For logging in today, you have received " . number_format($scrollreward) . " Chivalry Scrolls.");
        $api->UserGiveItem($userid,18,$scrollreward);
    }
    $db->query("UPDATE `users` SET `rewarded` = 1 WHERE `userid` = {$userid}");
}