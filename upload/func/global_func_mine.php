<?php
/*	File:		global_func_mine.php
	Created: 	Aug 6, 2021; 9:50:30 PM
	Info: 		
	Author:		Ryan
	Website: 	https://chivalryisdeadgame.com/
*/
function doAutoMiner()
{
    //CREATE TABLE `mining_auto` ( `userid` INT(11) UNSIGNED NULL , `miner_location` INT(11) UNSIGNED NULL , `miner_time` INT(11) UNSIGNED NOT NULL ) ENGINE = InnoDB;
    global $db, $api;
    $q = $db->query("SELECT * FROM `mining_auto`");
    $api->GameAddNotification(1, "Auto miner tick");
    while ($r = $db->fetch_row($q))
    {
        if (!userHasEffect($r['userid'], effect_drill_jam))
        {
            $mineinfo = $db->query("/*qc=on*/SELECT * FROM `mining_data` WHERE `mine_id` = {$r['miner_location']}");
            $MSI = $db->fetch_row($mineinfo);
            $MSI['mine_iq'] = calcMineIQ($r['userid'], $r['miner_location']);
            $Rolls = getMineRolls($r['userid'], $MSI['mine_iq']);
            if ($Rolls <= 3)
            {
                $negTime = Random(20,40);
                //negative event
                $api->GameAddNotification($r['userid'], "One of your powered miners has jammed, and the crews managing your miners have stopped working until they can resolve why the one miner jammed. Should be fixed in about {$negTime} minutes!");
                userGiveEffect($r['userid'], effect_drill_jam, $negTime * 60);
            }
            elseif ($Rolls >= 3 && $Rolls <= 14)
            {
                $Pos = Random(1,3);
                $dropList = json_decode(getMineDrop($r['miner_location'], $Pos), true);
                $drops = randMineDropCalc($r['userid'], $r['miner_location'], $Pos);
                $api->UserGiveItem($r['userid'], $dropList['itemDrop'], $drops);
            }
            else
            {
                $dropList = json_decode(getMineDrop($r['miner_location'], 4), true);
                $drops = randMineDropCalc($r['userid'], $r['miner_location'], 4);
                $api->UserGiveItem($r['userid'], $dropList['itemDrop'], $drops);
            }
            $db->query("UPDATE `mining_auto` SET `miner_time` = `miner_time` - 1 WHERE `userid` = {$r['userid']} AND `miner_location` = {$r['miner_location']}");
        }
    }
    $db->query("DELETE FROM `mining_auto` WHERE `miner_time` <= 0");
}

function getMineRolls($userid, $mineIQ)
{
    global $db;
    $userIQ = $db->fetch_single($db->query("SELECT `iq` FROM `userstats` WHERE `userid` = {$userid}"));
    if ($userIQ <= $mineIQ + ($mineIQ * .3))
        $Rolls = Random(1, 5);
    elseif ($userIQ >= $mineIQ + ($mineIQ * .3) && ($userIQ <= $mineIQ + ($mineIQ * .6)))
        $Rolls = Random(2, 10);
    else
        $Rolls = Random(3, 15);
        return $Rolls;
}

function calculateMinePowerCost($userid)
{
    $miningLevel = getUserMiningLevel($userid);
    if ($miningLevel < 10)
        $CostForPower=10;
    elseif (($miningLevel >= 10) && ($miningLevel < 20))
        $CostForPower=15;
    elseif (($miningLevel >= 20) && ($miningLevel < 50))
        $CostForPower=25;
    elseif (($miningLevel >= 50) && ($miningLevel < 75))
        $CostForPower=50;
    elseif (($miningLevel >= 75) && ($miningLevel < 100))
        $CostForPower=75;
    elseif (($miningLevel >= 100) && ($miningLevel < 150))
        $CostForPower=100;
    elseif (($miningLevel >= 150) && ($miningLevel < 200))
        $CostForPower=175;
    elseif (($miningLevel >= 200) && ($miningLevel < 300))
        $CostForPower=325;
    else
        $CostForPower=500;
    return $CostForPower;
}

function getUserMiningLevel($userid)
{
    global $db;
    return $db->fetch_single($db->query("SELECT `mining_level` FROM `mining` WHERE `userid` = {$userid}"));
}

function calculateFirstMineDrop($userid, $mineID)
{
    return randMineDropCalc($userid, $mineID, 1);
}

function calculateSecondMineDrop($userid, $mineID)
{
    return randMineDropCalc($userid, $mineID, 2);
}

function calculateThirdMineDrop($userid, $mineID)
{
    return randMineDropCalc($userid, $mineID, 3);
}

function getMineDrop($mineID, $dropID)
{
    global $db;
    if ($dropID == 1)
        $drop = "copper";
    elseif ($dropID == 2)
        $drop = "silver";
    elseif ($dropID == 3)
        $drop = "copper";
    elseif ($dropID == 4)
        $drop = "gem";
    if ($dropID < 4)
        $q = $db->query("SELECT `mine_{$drop}_min`, `mine_{$drop}_max`, `mine_{$drop}_item` FROM `mining_data` WHERE `mine_id` = {$mineID}");
    else
        $q = $db->query("SELECT `mine_{$drop}_item` FROM `mining_data` WHERE `mine_id` = {$mineID}");
    $r = $db->fetch_row($q);
    if (empty($r["mine_{$drop}_min"]))
        $r["mine_{$drop}_min"] = 0;
    if (empty($r["mine_{$drop}_max"]))
        $r["mine_{$drop}_max"] = 0;
    return json_encode(array("minDrop" => $r["mine_{$drop}_min"], "maxDrop" => $r["mine_{$drop}_max"], "itemDrop" => $r["mine_{$drop}_item"]));
}

function randMineDropCalc($userid, $mineID, $dropID)
{
    global $db;
    $itemMultipler = 1.0;
    if ($dropID < 4)
    {
        if (hasNecklaceEquipped($userid, 332))
            $itemMultipler += 0.05;
        $drop = json_decode(getMineDrop($mineID, $dropID), true);
        if (calculateLuck($userid))
            $drops = Random($drop['minDrop']+($drop['minDrop']/4), $drop['maxDrop']+($drop['maxDrop']/4));
        else
            $drops = Random($drop['minDrop'], $drop['maxDrop']);
        $userLevel = $db->fetch_single($db->query("SELECT `level` FROM `users` WHERE `userid` = {$userid}"));
        $drops = round($drops + ($drops * levelMultiplier($userLevel)));
        $drops = $drops + ($drops * $itemMultipler);
    }
    else
        $drops = 1 * $itemMultipler;
    return $drops;
}

function calcMineXPGains($userid, $mineID, $dropID, $dropCount)
{
    $xpMultiplier = 1.0;
    $gainedXP = 0;
    if ($dropID == 1)
        $baseXP = 0.35;
    elseif ($dropID == 2)
        $baseXP = 0.55;
    elseif ($dropID == 3)
        $baseXP = 0.75;
    elseif ($dropID == 4)
        $baseXP = 14 * getUserMiningLevel($userid);
    $gainedXP = $baseXP * $dropCount;
    if (userHasEffect($userid, mining_xp_boost))
        $gainedXP = $gainedXP * returnEffectMultiplier($userid, mining_xp_boost);
        if ($mineID == 10)
    $gainedXP = $gainedXP * $mineID;
    $gainedXP = $gainedXP / 7;
    $gainedXP = $gainedXP * $xpMultiplier;
    return $gainedXP;
}

function calcMineIQ($userid, $mineID)
{
    global $db;
    $specialnumber = ((getSkillLevel($userid, 15) * 10) / 100); //less mining iq skill
    $mineIQ = $db->fetch_single($db->query("SELECT `mine_iq` FROM `mining_data` WHERE `mine_id` = {$mineID}"));
    
    $mineIQ = $mineIQ - ($mineIQ * $specialnumber);
    
    return $mineIQ;
}

function mining_levelup()
{
    global $db, $userid, $MUS, $ir;
    if (!isset($ir['reset']))
        $ir['reset'] = 0;
        $MUS['xp_needed'] = (round(($MUS['mining_level'] + 0.75) * ($MUS['mining_level'] + 0.75) * ($MUS['mining_level'] + 0.75) * 1) * (1 - ($ir['reset'] * 0.1)));
        if ($MUS['miningxp'] >= $MUS['xp_needed']) {
            $expu = $MUS['miningxp'] - $MUS['xp_needed'];
            $MUS['mining_level'] += 1;
            $MUS['miningxp'] = $expu;
            $MUS['buyable_power'] += 1;
            $MUS['xp_needed'] =
            round(($MUS['mining_level'] + 0.75) * ($MUS['mining_level'] + 0.75) * ($MUS['mining_level'] + 0.75) * 1);
            $db->query("UPDATE `mining` SET `mining_level` = `mining_level` + 1, `miningxp` = {$expu},
                 `buyable_power` = `buyable_power` + 1 WHERE `userid` = {$userid}");
        }
}