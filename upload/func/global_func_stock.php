<?php
/**
 * Friendly function to adjust a player's assets shares. Fires assetsOwnedCleanup(); when 
 * completed.
 * @param int $userid User ID to tweak shares.
 * @param int $assetID Asset ID of the asset to change shares of.
 * @param int $costPerShare Average cost per share
 * @param int $totalShares Total shares to be added or removed.
 */
function setUserShares($userid, $assetID, $costPerShare, $totalShares)
{
    if ($totalShares > 0)
        addUserShares($userid, $assetID, $costPerShare, $totalShares);
    else
        removeUserShares($userid, $assetID, $totalShares);
    assetsOwnedCleanup();
}

function insertUserShares($userid, $assetID, $costPerShare, $totalShares)
{
    global $db;
    $db->query("INSERT INTO `asset_market_owned` 
        (`userid`, `am_id`, `shares_owned`, `shares_cost`) 
        VALUES ('{$userid}', '{$assetID}', '{$totalShares}', '{$costPerShare}')");
}

function addUserShares($userid, $assetID, $costPerShare, $totalShares)
{
    global $db;
    $q = $db->query("SELECT * 
                    FROM `asset_market_owned` 
                    WHERE `userid` = {$userid} 
                    AND `am_id` = {$assetID} 
                    AND `shares_cost` = {$costPerShare}");
    
    if ($db->num_rows($q) == 0)
    {
        insertUserShares($userid, $assetID, $costPerShare, $totalShares);
    }
    else
    {
        $r=$db->fetch_row($q);
        $db->query("UPDATE `asset_market_owned` 
                    SET `shares_owned` = `shares_owned` + {$totalShares} 
                    WHERE `am_id` = {$r['amo_id']}");
    }
}

function removeUserShares($userid, $assetID, $totalShares)
{
    global $db;
    $q = $db->query("SELECT *
                    FROM `asset_market_owned`
                    WHERE `userid` = {$userid}
                    AND `am_id` = {$assetID}");
    $sharesToTake = $totalShares * -1;
    if ($db->num_rows($q) > 0)
    {
        while ($sharesToTake != 0)
        {
            while ($r=$db->fetch_row($q))
            {
                if ($r['shares_owned'] < $sharesToTake)
                {
                    $sharesToTake = $sharesToTake - $r['shares_owned'];
                    $db->query("UPDATE `asset_market_owned` SET `shares_owned` = 0 WHERE `amo_id` = {$r['amo_id']}");
                }
                else 
                {
                    $db->query("UPDATE `asset_market_owned` SET `shares_owned` = `shares_owned` - {$sharesToTake} WHERE `amo_id` = {$r['amo_id']}");
                    $sharesToTake = 0;
                }
            }
        }
    }
}

function assetsOwnedCleanup()
{
    global $db;
    $fiftyDays = (((60 * 60) * 24) * 50);
    $historyDelTime = time() - $fiftyDays;
    $db->query("DELETE FROM `asset_market_owned` WHERE `shares_owned` <= 0");
    $db->query("DELETE FROM `asset_market_history` WHERE `timestamp` < {$historyDelTime}");
}

function runMarketTick($riskLevel)
{
    global $db, $api;
    $q = $db->query("SELECT * FROM `asset_market` WHERE `am_risk` = {$riskLevel}");
    if ($db->num_rows($q) > 0)
    {
        while ($r = $db->fetch_row($q))
        {
            $RNG = Random(1,500);
            $perc = $riskLevel / 100;
            if ($RNG <= 3)   //market crash
            {
                $min = $r['am_cost'] * 0.15;
                $max = $r['am_cost'] * 0.40;
                $change = mt_rand($min, $max) * -1;
                //$api->GameAddNotification(1, "{$r['am_name']} has a market crash.");
            }
            else if ($RNG >= 498) //market bubble
            {
                $min = $r['am_cost'] * 0.10;
                $max = $r['am_cost'] * 0.35;
                $change = mt_rand($min, $max);
                //$api->GameAddNotification(1, "{$r['am_name']} has a market bubble.");
            }
            else
            {
                
                if ($r['am_cost'] <= 5)
                    $maxChange = $r['am_cost'];
                elseif (($r['am_cost'] > 5) && ($r['am_cost'] <= 10))
                    $maxChange = $r['am_cost'] / 2;
                elseif (($r['am_cost'] > 10) && ($r['am_cost'] <= 20))
                    $maxChange = $r['am_cost'] / 3;
                elseif (($r['am_cost'] > 20) && ($r['am_cost'] <= 40))
                    $maxChange = $r['am_cost'] / 4;
                elseif (($r['am_cost'] > 40) && ($r['am_cost'] <= 50))
                    $maxChange = $r['am_cost'] / 5;
                else
                    $maxChange = $r['am_cost'] * $perc;
                $change = mt_rand($maxChange * -1, $maxChange);
            }
            $newVal = clamp(($r['am_cost'] + $change), 0, $r['am_max']);
            $newVal = ($newVal == 0) ? $r['am_start'] : $newVal;
            //Force sell on crash.
            if ($newVal == 0)
            {
                $q2 = $db->query("SELECT * FROM `asset_market_owned` WHERE `am_id` = {$r['am_id']}");
                $alreadyNotif = array();
                while ($r2 = $db->fetch_row($q2))
                {
                    $assetAlert = getUserPref($r2['userid'], 'assetAlert', 'true');
                    removeUserShares($r2['userid'], $r['am_id'], $r2['shares_owned']);
                    if ($assetAlert == 'true')
                    {
                        if (!in_array($r2['userid'], $alreadyNotif))
                        {
                            $notifText = "The {$r['am_name']} asset has crashed and your " . number_format($r2['shares_owned']) . " shares 
                                of {$r['am_name']} have been voided and your original investment is lost";
                            $api->GameAddNotification($r2['userid'], $notifText);
                            array_push($alreadyNotif, $r2['userid']);
                        }
                    }
                    //$api->GameAddNotification(1, "{$r['am_name']} has crashed.");
                }
            }
            
            if (($newVal <= $r['am_min']) && ($newVal >= $r['am_min'] - (5 / 100)))
            {
                if ($assetAlert == 'true')
                    stockNotifDrop($r['am_id']);
                //$api->GameAddNotification(1, "{$r['am_name']} is failing.");
            }
            
            $db->query("UPDATE `asset_market` SET `am_cost` = '{$newVal}' WHERE `am_id` = {$r['am_id']}");
            logAssetChange($r['am_id'], $r['am_cost'], $change, $newVal);
        }
    }
}

function logAssetChange($assetID, $oldVal, $change, $newVal)
{
    global $db;
    $time = time();
    return $db->query("INSERT INTO `asset_market_history` 
                (`am_id`, `old_value`, `difference`, `new_value`, `timestamp`) 
                VALUES ('{$assetID}', '{$oldVal}', '{$change}', '{$newVal}', '{$time}')");
}

function createStockAsset($name, $cost, $change, $risk, $min, $max)
{
    global $db;
    return $db->query("INSERT INTO `asset_market` 
        (`am_name`, `am_min`, `am_max`, `am_start`, `am_cost`, `am_change`, `am_risk`) 
        VALUES ('{$name}', '{$min}', '{$max}', '{$cost}', '{$cost}', '{$change}', '{$risk}')");
}

function logAssetProfit($userid, $profit)
{
    global $db;
    $q = $db->query("SELECT * FROM `asset_market_profit` WHERE `userid` = {$userid}");
    if ($db->num_rows($q) == 0)
        $db->query("INSERT INTO `asset_market_profit` (`userid`, `profit`) VALUES ('{$userid}', '{$profit}')");
    else
        $db->query("UPDATE `asset_market_profit` SET `profit` = `profit` + ({$profit}) WHERE `userid` = {$userid}");
}

function returnUserMaxShares($userid)
{
    global $db, $api;
    $level = $api->UserInfoGet($userid, "level");
    $masterRank = $db->fetch_single($db->query("SELECT `reset` FROM `user_settings` WHERE `userid` = {$userid}"));
    
    $ret = 5000;
    $ret = $ret * levelMultiplier($level);
    $ret = $ret * $masterRank;
    
    return $ret;
}

function returnUserAssetShares($userid, $assetID)
{
    global $db;
    $q = $db->query("SELECT SUM(`shares_owned`) FROM `asset_market_owned` WHERE `am_id` = {$assetID} AND `userid` = {$userid}");
    return $db->fetch_single($q);
}

function returnUserAssetCosts($userid, $assetID)
{
    global $db;
    $q = $db->query("SELECT * FROM `asset_market_owned` WHERE `am_id` = {$assetID} AND `userid` = {$userid}");
    $r = $db->fetch_row($q);
    return $r['shares_cost'] * $r['shares_owned'];
}

function calculateUserCurrentAssetValue($userid, $assetID)
{
    global $db;
    $totalShares = returnUserAssetShares($userid, $assetID);
    $currentVal = $db->fetch_single($db->query("SELECT `am_cost` FROM `asset_market` WHERE `am_id` = {$assetID}"));
    return $totalShares * $currentVal;
}

function resetAsset($assetID)
{
    global $db;
    return $db->query("UPDATE `asset_market` SET `am_cost` = `am_start` WHERE `am_id` = {$assetID}");
}

function returnUserAllAssetShares($userid)
{
    global $db;
    $q = $db->query("SELECT SUM(`shares_owned`) FROM `asset_market_owned` WHERE `userid` = {$userid}");
    return $db->fetch_single($q);
}

function returnUserAllAssetCosts($userid)
{
    global $db;
    $return = 0;
    $q = $db->query("SELECT * FROM `asset_market_owned` WHERE `userid` = {$userid}");
    while ($r = $db->fetch_row($q))
    {
        $return = $return + ($r['shares_owned'] * $r['shares_cost']);
    }
    return $return;
}

function returnUserCurrentValueAllAsset($userid)
{
    global $db;
    $return = 0;
    $q = $db->query("SELECT * FROM `asset_market_owned` WHERE `userid` = {$userid}");
    while ($r = $db->fetch_row($q))
    {
        $q2 = $db->query("SELECT * FROM `asset_market` WHERE `am_id` = {$r['am_id']}");
        while ($r2 = $db->fetch_row($q2))
        {
            $return = $return + ($r2['am_cost'] * $r['shares_owned']);
        }
    }
    return $return;
}

function stockNotifDrop($stock_id)
{
    global $db, $api;
    $q2 = $db->query("SELECT * FROM `asset_market_owned` WHERE `am_id` = {$stock_id}");
    $r['am_name'] = $db->fetch_single($db->query("SELECT `am_name` FROM `asset_market` WHERE `am_id` = {$stock_id}"));
    $alreadyNotif = array();
    while ($r2 = $db->fetch_row($q2))
    {
        if (!in_array($r2['userid'], $alreadyNotif))
        {
            $notifText = "The {$r['am_name']} asset is failing! Sell your assets before it does, or risk your investment!";
            $api->GameAddNotification($r2['userid'], $notifText);
            array_push($alreadyNotif, $r2['userid']);
        }
    }
}