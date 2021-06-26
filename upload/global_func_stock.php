<?php
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
        $r=$db->fetch_single($q);
        $db->query("UPDATE `asset_market_owned` 
                    SET `shares_owned` = `shares_owned` + {$totalShares} 
                    WHERE `amo_id` = {$r['amo_id']}");
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
    global $db;
    $q = $db->query("SELECT * FROM `asset_market` WHERE `am_risk` = {$riskLevel}");
    if ($db->num_rows($q) > 0)
    {
        while ($r = $db->fetch_row($q))
        {
            if ($r['am_cost'] == $r['am_min'])
                $change = Random($r['am_change'] * 0.1, $r['am_change']);   //This way it always goes up when it bottoms out.
            elseif ($r['am_cost'] == $r['am_max'])
                $change = Random($r['am_change'] * -1, $r['am_change'] * -0.1); //Goes down when it maxes out.
            else
                $change = Random($r['am_change'] * -1, $r['am_change']);    //normal formula
            $newVal = clamp(($r['am_cost'] + $change), $r['am_min'], $r['am_max']);
            $db->query("UPDATE `asset_market` SET `am_cost` = {$newVal} WHERE `am_id` = {$r['am_id']}");
            logAssetChange($r['am_id'], $r['am_cost'], $change, $newVal);
        }
    }
}

function logAssetChange($assetID, $oldVal, $change, $newVal)
{
    global $db;
    $time = time();
    $db->query("INSERT INTO `asset_market_history` 
                (`am_id`, `old_value`, `difference`, `new_value`, `timestamp`) 
                VALUES ('{$assetID}', '{$oldVal}', '{$change}', '{$newVal}', '{$time}')");
}

function createStockAsset($name, $cost, $change, $risk, $min, $max)
{
    global $db;
    $db->query("INSERT INTO `asset_market` 
        (`am_name`, `am_min`, `am_max`, `am_start`, `am_cost`, `am_change`, `am_risk`) 
        VALUES ('{$name}', '{$min}', '{$max}', '{$cost}', '{$cost}', '{$change}', '{$risk}')");
}