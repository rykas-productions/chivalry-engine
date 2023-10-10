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
    if ($ir['dayslogged'] == 0)
        $daysLoggedMulti = 1;
    else
        $daysLoggedMulti = 1 + ($ir['dayslogged'] * 0.1);
    $daysLoggedMulti = clamp($daysLoggedMulti, 1, 31);
    $rng=Random(1,5);
    $tokenreward=(1500*$rng)+((1500*$rng)*levelMultiplier($ir['level'], $ir['reset']));
    $tokenreward = $tokenreward * $daysLoggedMulti;
    $medReward=Random(5 * $daysLoggedMulti, 15 * $daysLoggedMulti);
    $scrollreward=Random(10 * $daysLoggedMulti,20 * $daysLoggedMulti);
    $luck=Random(10,35);
    $reward=Random(1,100);
    $copper=1000000+(1000000*levelMultiplier($ir['level'], $ir['reset']));
    $copper = $copper  * $daysLoggedMulti;
    $bor=Random(500* $daysLoggedMulti,1025 * $daysLoggedMulti);
    $guardTime=Random(15,40);
    $autoHex=Random(50,105);
    
    $rrmin = 100000 * levelMultiplier($ir['level'], $ir['reset']);
    $rrmax = 3000000 * levelMultiplier($ir['level'], $ir['reset']);
    $rrBet=Random($rrmin * $daysLoggedMulti,$rrmax * $daysLoggedMulti);
    
    $skill = round(1  * $daysLoggedMulti);
    //Give skill point.
    if ($reward == 99 || $reward == 100)
    {
        $db->query("UPDATE `user_settings` SET `skill_points` = `skill_points` + {$skill} WHERE `userid` = {$userid}");
        $api->GameAddNotification($userid,"You have gained {$skill} skill point(s) for logging into Chivalry is Dead today. Thank you!");
        $api->SystemLogsAdd($userid, "loginreward", "Received {$skill} Skill Point.");
    }
    elseif ($reward <= 98 && $reward > 90)
    {
        $api->UserGiveItem($userid,207,$medReward);
        $api->GameAddNotification($userid,"While logging into Chivalry is Dead today, you were gifted " . shortNumberParse($medReward) . " Priority Vouchers, used at the infirmary. Consider it a gift from us. ;)");
        $api->SystemLogsAdd($userid, "loginreward", "Received " . shortNumberParse($medReward) . " Priority Vouchers.");
    }
    elseif ($reward <= 89 && $reward > 85)
    {
        $api->UserGiveItem($userid,284,$skill);
        $api->GameAddNotification($userid,"Overnight, your guild had robbed a local shop. They've returned and your gift is " . shortNumberParse($skill) . " Amulet of Criminal Mischief(s). Thank you for playing Chivalry is Dead!");
        $api->SystemLogsAdd($userid, "loginreward", "Received {$skill}x Amulet of Crminal Mischief(s).");
    }
    elseif ($reward <= 84 && $reward > 75)
    {
        $db->query("UPDATE `user_settings` SET `rickroll` = {$userid} WHERE `userid` = {$userid}");
        $api->UserGiveItem($userid,33,$bor);
        $api->GameAddNotification($userid,"You may have just been rickrolled, but we've given you " . shortNumberParse($bor) . " Boxes of Random. Just cuz! Thanks for playing Chivalry is Dead.");
        $api->SystemLogsAdd($userid, "loginreward", "Received " . shortNumberParse($bor) . " Boxes of Random and a Rick Roll.");
    }
    elseif ($reward <= 74 && $reward > 70)
    {
        $api->UserGiveItem($userid,259,$skill);
        $api->GameAddNotification($userid,"For logging into Chivalry is Dead today, we gave you {$skill} Marriage Rose(s). Hint, hint. :) We love you {$ir['username']}. We love you. < 3");
        $api->SystemLogsAdd($userid, "loginreward", "Received {$skill} Marriage Rose.");
    }
    elseif ($reward <= 69 && $reward > 65)
    {
        $api->UserGiveItem($userid,205,$scrollreward);
        $api->GameAddNotification($userid,"To hopefully jumpstart some training sessions, we've gifted you {$scrollreward} CID Admin Gym Access Scrolls. Thanks for playing Chivalry is Dead!");
        $api->SystemLogsAdd($userid, "loginreward", "Received {$scrollreward} CID Admin Gym Access Scrolls.");
    }
    elseif ($reward <= 64 && $reward > 60)
    {
        userGiveEffect($userid, basic_protection, ($guardTime*60));
        $api->GameAddNotification($userid,"We got your back {$ir['username']}! Someone was about to hit on you, so we bought you some protection. Its your login gift for the day. Thanks for playing CID!");
        $api->SystemLogsAdd($userid, "loginreward", "Received {$guardTime} minutes of Protection.");
    }
    elseif ($reward <= 59 && $reward > 53)
    {
        $db->query("UPDATE `user_settings` SET `autohex` = `autohex` + {$autoHex} WHERE `userid` = {$userid}");
        $api->GameAddNotification($userid,"For your dedication of logging into Chivalry is Dead today, we've given you {$autoHex} Auto Hexbags for you to use. We appreciate you playing.");
        $api->SystemLogsAdd($userid, "loginreward", "Received {$autoHex} Automatic Hexbags");
    }
    elseif ($reward <= 52 && $reward > 50)
    {
        $newTime=(60*60)*0.25;
        $formatTime = $newTime / 60;
        if (userHasEffect($userid, $effect))
            userUpdateEffect($userid, $effect, $newTime);
            else
                userGiveEffect($userid, constant("invisibility"), $newTime);
                $api->GameAddNotification($userid,"We've given you {$formatTime} minutes of invisibility for your daily log in reward.");
                $api->SystemLogsAdd($userid, "loginreward", "Received {$formatTime} minutes invisibility");
    }
    elseif ($reward <= 49 && $reward > 47)
    {
        $api->UserGiveItem($userid,285,$skill);
        $api->GameAddNotification($userid,"You've received {$skill} x Potion of Permanent Strength(s) for logging into Chivalry is Dead today. Thanks a lot!");
        $api->SystemLogsAdd($userid, "loginreward", "Received {$skill} x Potion of Permanent Strength(s)");
    }
    elseif ($reward <= 46 && $reward > 44)
    {
        $api->UserGiveItem($userid,286,$skill);
        $api->GameAddNotification($userid,"You've received {$skill} x Potion of Everlasting Speed(s) for logging into Chivalry is Dead today. Thanks a lot!");
        $api->SystemLogsAdd($userid, "loginreward", "Received {$skill} x Potion of Everlasting Speed(s)");
    }
    elseif ($reward <= 43 && $reward > 41)
    {
        $api->UserGiveItem($userid,287,$skill);
        $api->GameAddNotification($userid,"You've received {$skill} x Potion of Youthful Tolerance(s) for logging into Chivalry is Dead today. Thanks a lot!");
        $api->SystemLogsAdd($userid, "loginreward", "Received {$skill} x Potion of Youthful Tolerance(s)");
    }
    elseif ($reward <= 40 && $reward > 38)
    {
        $api->UserGiveItem($userid,148,$skill);
        $api->GameAddNotification($userid,"I'm sorry for the crappy gift... but all we have are these old {$skill} x Tome of Experience(s) we don't have a use for... so here, have it. Thanks for playing CID. :/");
        $api->SystemLogsAdd($userid, "loginreward", "Received {$skill} x Tome of Experience(s)");
    }
    elseif ($reward <= 37 && $reward > 26)
    {
        $api->UserGiveCurrency($userid,'secondary',$tokenreward);
        $api->GameAddNotification($userid,"We've given you " . shortNumberParse($tokenreward) . " Chivalry Tokens for logging into Chivalry is Dead today. Thanks~");
        $api->SystemLogsAdd($userid, "loginreward", "Received " . number_format($tokenreward) . " Chivalry Tokens");
    }
    elseif ($reward <= 25 && $reward > 23)
    {
        $db->query("UPDATE `users` SET `vip_days` = `vip_days` + 1 WHERE `userid` = {$userid}");
        $api->GameAddNotification($userid,"Enjoy the free VIP Day {$ir['username']}! Consider it a thanks from us at Chivalry is Dead.");
        $api->SystemLogsAdd($userid, "loginreward", "Received VIP Day.");
    }
    elseif ($reward <= 22 && $reward > 11)
    {
        $api->UserGiveCurrency($userid,'primary',$copper);
        $api->GameAddNotification($userid,"Here's a flat " . shortNumberParse($copper) . " Copper Coins just for logging into Chivalry is Dead today. Thanks babe!");
        $api->SystemLogsAdd($userid, "loginreward", "Received " . number_format($copper) . " Copper Coins.");
        addToEconomyLog('Daily Reward', 'copper', $copper);
    }
    elseif ($reward <= 10 && $reward > 8)
    {
        if ($ir['tokenbank'] == -1)
        {
            $newAddToken = 100;
            $api->UserGiveCurrency($userid, "secondary", $newAddToken);
            $api->GameAddNotification($userid,"We were going to run interest on your Chivalry Token account, but you don't have one. So, we're giving you {$newAddToken} Chivalry Tokens instead.");
            $api->SystemLogsAdd($userid, "loginreward", "Received {$newAddToken} Chivalry Tokens.");
        }
        elseif ($ir['tokenbank'] == 0)
        {
            $newAddToken = 100;
            $api->UserGiveCurrency($userid, "secondary", $newAddToken);
            $api->GameAddNotification($userid,"We were going to run interest on your Chivalry Token account, but you don't have any tokens in the account. So, we're giving you {$newAddToken} Chivalry Tokens instead.");
            $api->SystemLogsAdd($userid, "loginreward", "Received {$newAddToken} Chivalry Tokens.");
        }
        elseif ($ir['tokenbank'] <= 100000)
        {
            $newAddToken = $ir['tokenbank']/50;
            $db->query("UPDATE `users` SET `tokenbank`=`tokenbank`+(`tokenbank`/50) WHERE `tokenbank`>0 AND `userid` = {$userid}");
            $api->GameAddNotification($userid,"We <i>accidentally</i> ran 2% interest on your Chivalry Token account. Sorry! However, for the mess up, we'll allow you to keep the extra " . shortNumberParse($newAddToken) . " Chivalry Tokens in the account. Thanks for playing Chivalry is Dead!");
            $api->SystemLogsAdd($userid, "loginreward", "Received 2% Token Bank account interest. (" . shortNumberParse($newAddToken) . " Tokens)");
        }
        elseif (($ir['tokenbank'] > 100000) && ($ir['tokenbank'] <= 500000))
        {
            $newAddToken = $ir['tokenbank']/100;
            $db->query("UPDATE `users` SET `tokenbank`=`tokenbank`+(`tokenbank`/100) WHERE `tokenbank`>0 AND `userid` = {$userid}");
            $api->GameAddNotification($userid,"We <i>accidentally</i> ran 1% interest on your Chivalry Token account. Sorry! However, for the mess up, we'll allow you to keep the extra " . shortNumberParse($newAddToken) . " Chivalry Tokens in the account. Thanks for playing Chivalry is Dead!");
            $api->SystemLogsAdd($userid, "loginreward", "Received 1% Token Bank account interest");
        }
        else
        {
            $newAddToken = 5000;
            $db->query("UPDATE `users` SET `tokenbank`=`tokenbank`+5000 WHERE `tokenbank`>0 AND `userid` = {$userid}");
            $api->GameAddNotification($userid,"We've given you 5,000 Chivalry Tokens to your Chivalry Token bank account, just for logging into the game! Thank you!");
            $api->SystemLogsAdd($userid, "loginreward", "Received 5,000 Chivalry Tokens to their Chivalry Token account. (" . shortNumberParse($newAddToken) . " Tokens)");
        }
        addToEconomyLog('Daily Reward', 'token', $newAddToken);
    }
    elseif ($reward <= 7 && $reward > 1)
    {
        $db->query("INSERT INTO `russian_roulette` (`challengee`, `challenger`, `reward`) VALUES ('{$userid}', '1', '{$rrBet}');");
        $api->GameAddNotification($userid,"We've issued you a Russian Roulette challenge. If you win, you get " . shortNumberParse($rrBet) . " Copper Coins! Consider this a fun, risky login reward.");
        $api->SystemLogsAdd($userid, "loginreward", "Received Russian Roulette challenges");
        addToEconomyLog('Daily Reward', 'copper', $rrBet);
    }
    else
    {
        $api->UserGiveItem($userid,227,1);
        $api->GameAddNotification($userid,"You've received {$skill} x {$api->SystemItemIDtoName(227)}(s) for logging into Chivalry is Dead today. Thanks a lot!");
        $api->SystemLogsAdd($userid, "loginreward", "Received {$skill} x {$api->SystemItemIDtoName(227)}(s)");
    }
    if ($ir['vip_days'] > 0)
    {
        if ($ir['tokenbank'] > -1)
            $db->query("UPDATE `users` SET `tokenbank` = `tokenbank` + 750 WHERE `userid` = {$userid}");
            else
                $api->UserGiveCurrency($userid,'secondary',750);
                addToEconomyLog('Daily Reward', 'token', 750);
    }

    $month = date('n');
    $day = date('j');
    $year = date('Y');
    // Month = April
    if ($month == 4)
    {
        //Blaze it
        if ($day == 20)
        {
            $api->UserGiveItem($userid, 447, 1);
            $api->UserGiveCurrency($userid, 'primary', $copper);
            $api->UserGiveCurrency($userid, 'secondary', $tokenreward);
            $api->GameAddNotification($userid, "For logging in today, on 4/20, we've given you a Blaze Badge, along 
                                                with " . shortNumberParse($copper) . " Copper Coins AND 
                                                " . shortNumberParse($tokenreward) . " Chivalry Tokens. Thank you for playing.");
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
    $db->query("UPDATE `users` SET `rewarded` = 1 WHERE `userid` = {$userid}");
}