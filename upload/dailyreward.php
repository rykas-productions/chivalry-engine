<?php
/*
 File:		dailyreward.php
 Created: 	10/24/2017 at 1:44PM Eastern Time
 Info: 		Daily rewards
 Author:		TheMasterGeneral
 Website: 	http://chivalryisdead.x10.mx
 */
$vipTokenBonus = 0.5;
if ($ir['rewarded'] == 0)
{
    if ($ir['dayslogged'] == 0)
        $daysLoggedMulti = 1;
    elseif (($ir['dayslogged'] <= 7) && ($ir['dayslogged'] > 0))
        $daysLoggedMulti = 1 + ($ir['dayslogged'] * 0.1);
    elseif (($ir['dayslogged'] <= 14) && ($ir['dayslogged'] > 7))
        $daysLoggedMulti = 1 + ($ir['dayslogged'] * 0.125);
    elseif (($ir['dayslogged'] <= 21) && ($ir['dayslogged'] > 14))
        $daysLoggedMulti = 1 + ($ir['dayslogged'] * 0.15);
    elseif (($ir['dayslogged'] <= 28) && ($ir['dayslogged'] > 21))
        $daysLoggedMulti = 1 + ($ir['dayslogged'] * 0.175);
    elseif (($ir['dayslogged'] <= 35) && ($ir['dayslogged'] > 28))
        $daysLoggedMulti = 1 + ($ir['dayslogged'] * 0.2);
    elseif (($ir['dayslogged'] <= 42) && ($ir['dayslogged'] > 35))
        $daysLoggedMulti = 1 + ($ir['dayslogged'] * 0.225);
    else
        $daysLoggedMulti = 1 + ($ir['dayslogged'] * 0.25);
        
    $daysLoggedMulti = clamp($daysLoggedMulti, 1, 8);
    
    $loginPoint = 1 * $daysLoggedMulti;
    setCurrentUserPref('loginPoints', getCurrentUserPref('loginPoints', 0) + $loginPoint);
    
    $notifText = "As thanks for logging into <i>{$set['WebsiteName']}</i> for {$ir['dayslogged']} days in a row, we've given you {$loginPoint} Login Point(s), which can be exchanged for cool rewards at the <a href='dailyrewardstore.php'>Login Point Store</a>. ";
    
    if ($ir['vip_days'] > 0)
        $notifText .= "As appreciation for you having VIP Days, we've given you a bonus {$vipTokenBonus} Login Points. ";

    // start holiday
    $month = date('n');
    $day = date('j');
    $year = date('Y');
    // Month = April
    if ($month == 4)
    {
        //Blaze it
        if ($day == 20)
        {
            $copper = 4200 * (levelMultiplier($ir['level'], $ir['reset']) * 1000);
            $token = 420 * (levelMultiplier($ir['level'], $ir['reset']) * 100);
            $api->UserGiveItem($userid, 447, 1);
            $api->UserGiveCurrency($userid, 'primary', $copper);
            $api->UserGiveCurrency($userid, 'secondary', $token);
            $notifText .= " As an added bonus for logging in today, we've given you an additional " . shortNumberParse($copper) . "
                            Copper Coins and " . shortNumberParse($token) . " Chivalry Tokens, along with a 
                            <a href='iteminfo.php?ID=447'>{$api->SystemItemIDtoName(447)}</a>.";
        }
    }
    if ($month == 5)
    {
        if (($day >= 2) && ($day <= 6))
        {
            $itemid = Random(236,243);
            $sword = $api->SystemItemIDtoName($itemid);
            $notifText .= " With May 4th being <i>Star Wars Day</i>, we're giving out unique weapons from now until May 6th. 
                            You received <a href='iteminfo.php?ID={$itemid}'>{$sword}</a> today. <i>May the force be with you.</i>";
            $api->UserGiveItem($userid, $itemid, 1);
        }
    }
    //Month = October
    if ($month == 10)
    {
        if ($day == 20)
        {
            $api->UserGiveItem($userid, 178, 15);
            $api->GameAddNotification($userid, "On this day in 2017, Chivalry is Dead launched. Thank you for many great years! We've given you some Birthday cake to your inventory.");
        }
        //Halloween
        if ($day == 31)
        {
            $api->UserGiveItem($userid, 449, 1);
            $api->UserGiveItem($userid, 450, 1);
            $api->GameAddNotification($userid, "We've given you a badge and a scratch-off ticket for logging into CID on Halloween!");
        }
    }
    //November
    if ($month == 11)
    {
        //Thanksgiving weekend
        if ($day == 26 || $day == 27 || $day == 28 || $day == 29 || $day == 30)
        {
            
        }
    }
    if ($month == 12)
    {
        if ($day == 26)
        {
            $api->UserGiveItem($userid, 449, 10);
            $api->GameAddNotification($userid, "Thank you for logging into CID on CID Admin's birthday! We've given you some Birthday cake in your inventory.");
        }
    }
    $api->GameAddNotification($userid, $notifText);
    $db->query("UPDATE `users` SET `rewarded` = 1, `dayslogged` = `dayslogged` + 1 WHERE `userid` = {$userid}");
}