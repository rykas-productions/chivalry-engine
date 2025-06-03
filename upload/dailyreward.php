<?php
/*
 File:		dailyreward.php
 Created: 	10/24/2017 at 1:44PM Eastern Time
 Info: 		Daily rewards
 Author:		TheMasterGeneral
 Website: 	http://chivalryisdead.x10.mx
 */
$vipTokenBonus = 750;
if ($ir['rewarded'] == 0)
{
    if ($ir['dayslogged'] == 0)
        $daysLoggedMulti = 1;
    else
        $daysLoggedMulti = 1 + ($ir['dayslogged'] * 0.1);
    $daysLoggedMulti = clamp($daysLoggedMulti, 1, 42);
    
    $notifText = "As thanks for logging into <i>{$set['WebsiteName']}</i> for {$ir['dayslogged']} days in a row, we've given you ";
    if ($ir['dayslogged'] <= 7)
    {
        $copper = 500000 * $daysLoggedMulti;
        $token = 10000 * $daysLoggedMulti;
        $api->UserGiveCurrency($userid, "primary", $copper);
        $api->UserGiveCurrency($userid, "secondary", $token);
        $notifText .= shortNumberParse($copper) . " Copper Coins and " . shortNumberParse($token) . " Chivalry Tokens to your wallet. ";
        addToEconomyLog('Daily Reward', 'token', $token);
        addToEconomyLog('Daily Reward', 'copper', $copper);
    }
    elseif (($ir['dayslogged'] <= 14) && ($ir['dayslogged'] > 7))
    {
        $gym = round(10 * $daysLoggedMulti);
        $api->UserGiveItem($userid, 18, $gym);
        $notifText .= "<a href='iteminfo.php?ID=18'>" .shortNumberParse($gym) . " {$api->SystemItemIDtoName(18)}(s)</a> to your inventory. ";
    }
    elseif (($ir['dayslogged'] <= 21) && ($ir['dayslogged'] > 14))
    {
        $gym = round(10 * $daysLoggedMulti);
        $api->UserGiveItem($userid, 205, $gym);
        $notifText .= "<a href='iteminfo.php?ID=205'>" .shortNumberParse($gym) . " {$api->SystemItemIDtoName(205)}(s)</a> to your inventory. ";
    }
    elseif (($ir['dayslogged'] <= 21) && ($ir['dayslogged'] > 14))
    {
        $gym = round(10 * $daysLoggedMulti);
        $api->UserGiveItem($userid, 205, $gym);
        $notifText .= "<a href='iteminfo.php?ID=205'>" .shortNumberParse($gym) . " {$api->SystemItemIDtoName(205)}(s)</a> to your inventory. ";
    }
    elseif (($ir['dayslogged'] <= 28) && ($ir['dayslogged'] > 21))
    {
        $mining = round(12 * $daysLoggedMulti);
        $api->UserGiveItem($userid, 227, $mining);
        $notifText .= "<a href='iteminfo.php?ID=227'>" .shortNumberParse($mining) . " {$api->SystemItemIDtoName(227)}(s)</a> to your inventory. ";
    }
    elseif (($ir['dayslogged'] <= 35) && ($ir['dayslogged'] > 28))
    {
        $itemGiveArray = array(266,332,152,210,356,521,522,523,524,525,
                                526,527,528,529,530,531,532,533,534,535,
                                536,537,538,539,540,541,542,543,544,545,
                                546,547);
        $itemGiveRandom = array_rand($itemGiveArray);
        $notifText .= "a <a href='iteminfo.php?ID={$itemGiveArray[$itemGiveRandom]}'>{$api->SystemItemIDtoName($itemGiveArray[$itemGiveRandom])}</a> to your inventory. ";
        $api->UserGiveItem($userid, $itemGiveArray[$itemGiveRandom], 1);
    }
    else if ($ir['dayslogged'] > 35)      
    {
        $copper = 1500000 * $daysLoggedMulti;
        $token = 25000 * $daysLoggedMulti;
        $api->UserGiveCurrency($userid, "primary", $copper);
        $api->UserGiveCurrency($userid, "secondary", $token);
        $notifText .= shortNumberParse($copper) . " Copper Coins and " . shortNumberParse($token) . " Chivalry Tokens to your wallet.";
        addToEconomyLog('Daily Reward', 'token', $token);
        addToEconomyLog('Daily Reward', 'copper', $copper);
    }
    
    if ($ir['vip_days'] > 0)
    {
        if ($ir['tokenbank'] > -1)
            $db->query("UPDATE `users` SET `tokenbank` = `tokenbank` + {$vipTokenBonus} WHERE `userid` = {$userid}");
        else
            $api->UserGiveCurrency($userid,'secondary',$vipTokenBonus);
        addToEconomyLog('Daily Reward', 'token', $vipTokenBonus);
        $notifText .= "As appreciation for you having VIP Days, we've credited you a bonus " . shortNumberParse($vipTokenBonus) . " Chivalry Tokens. ";
    }

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