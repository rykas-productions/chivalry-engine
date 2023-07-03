<?php
function calcExtraWill($gardenLevel, $estateWill)
{
	return (($estateWill*0.1)*$gardenLevel);
}

function calcSleepEfficiency($sleepLevel, $estateWill)
{
	return (($estateWill*0.01)*$sleepLevel);
}
function calcVaultCapacity($vaultLevel, $estatePrice)
{
	return (($estatePrice*0.18)*$vaultLevel);
}
//The next three functions are for calculating the costs for upgrading
//or buying the garden
function calcWaterCosts($gardenLevel, $estateWill)
{
	$gardenLevel++;
	$gardenLevel*=0.1;
	return ceil(($estateWill*0.025)*$gardenLevel)+$gardenLevel;
}
function calcStoneCosts($gardenLevel, $estateWill)
{
	$gardenLevel++;
	$gardenLevel*=0.1;
	return ceil(($estateWill*0.042)*$gardenLevel)+$gardenLevel;
}
function calcStickCosts($gardenLevel, $estateWill)
{
	$gardenLevel++;
	$gardenLevel*=0.1;
	return ceil(($estateWill*0.061)*$gardenLevel)+$gardenLevel;
}
//
function calcIronCosts($vaultLevel, $estateCost)
{
	$vaultLevel++;
	$ogVault = $vaultLevel;
	$vaultLevel*=0.001;
	$value = ceil(($estateCost*0.00518692)*$vaultLevel)+$vaultLevel;
	if ($value < 100)
		$value = 100 * $ogVault;
	return $value;
}
function doLeaveHouse($user)
{
	global $db;
	$q=$db->query("SELECT * FROM `user_estates` WHERE `estate` = 1 AND `userid` = {$user}");
	if ($db->num_rows($q) == 0)
	{
		$db->query("INSERT INTO `user_estates` 
					(`userid`, `estate`, `vault`, 
					`vaultUpgrade`, `gardenUpgrade`, 
					`sleepUpgrade`) VALUES ('{$user}', '1', '0', '0', '0', '0')");
		$i=$db->insert_id();
		$db->query("UPDATE `users` SET `maxwill` = 100, `estate` = {$i} WHERE `userid` = {$user}");
	}
	else
	{
		$r=$db->fetch_row($q);
		$r2=$db->fetch_single($db->query("SELECT `house_will` FROM `estates` WHERE `house_id` = 1"));
		$newWill=$r2+$r['bonusWill'];
		$db->query("UPDATE `users` SET `maxwill` = {$newWill}, `estate` = {$r['ue_id']} WHERE `userid` = {$user}");
	}
}
function doMoveIn($estate,$user)
{
	global $db;
	$q=$db->query("SELECT * FROM `user_estates` WHERE `ue_id` = {$estate} AND `userid` = {$user}");
	if ($db->num_rows($q) > 0)
	{
		$r=$db->fetch_row($q);
		$r2=$db->fetch_single($db->query("SELECT `house_will` FROM `estates` WHERE `house_id` = {$r['estate']}"));
		$newWill=$r2+$r['bonusWill']+calcExtraWill($r['gardenUpgrade'], $r2);
		$db->query("UPDATE `users` SET `maxwill` = {$newWill}, `estate` = {$r['ue_id']} WHERE `userid` = {$user}");
	}
}

function maxWillCheck()
{
	global $ir, $db;
	$estate=$db->fetch_row($db->query("SELECT * FROM `user_estates` WHERE `ue_id` = {$ir['estate']}"));
	$edb=$db->fetch_row($db->query("SELECT * FROM `estates` WHERE `house_id` = {$estate['estate']}"));
	$will=$edb['house_will']+calcExtraWill($estate['gardenUpgrade'], $edb['house_will'])+$estate['bonusWill'];
	if ($ir['maxwill'] != $will)
	{
		$db->query("UPDATE `users` SET `maxwill` = {$will} WHERE `userid` = {$ir['userid']}");
	}
}

function buyEstate($userid, $estate_id)
{
	global $db, $api;
	$db->query("INSERT INTO `user_estates` 
				(`userid`, `estate`, `vault`, 
				`vaultUpgrade`, `gardenUpgrade`, 
				`sleepUpgrade`, `bonusWill`) 
				VALUES ('{$userid}', '{$estate_id}', '0', '0', '0', '0', '0')");
	return $db->insert_id();
}

function sellEstate($estate_id)
{
	global $db;
	$db->query("DELETE FROM `user_estates` WHERE `ue_id` = {$estate_id}");
}

function calculateSellPrice($estate_id)
{
	global $db;
	$r=$db->fetch_row($db->query("SELECT * FROM `user_estates` WHERE `ue_id` = {$estate_id}"));
	$r2=$db->fetch_row($db->query("SELECT * FROM `estates` WHERE `house_id` = {$r['estate']}"));
	//start at 85% value due to depreciation
	$multi = 0.85;
	if ($r['gardenUpgrade'] > 0)
	    $multi = ($multi) + ($r['gardenUpgrade'] * 0.075);
    if ($r['vaultUpgrade'] > 0)
        $multi = ($multi) + ($r['vaultUpgrade'] * 0.07);
    if ($r['sleepUpgrade'] > 0)
        $multi = ($multi) + ($r['sleepUpgrade'] * 0.065);
    return round($multi * $r2['house_price']);
}

function increaseMaxWill($userid, $increase)
{
	global $db;
	$r=$db->fetch_single($db->query("SELECT `estate` FROM `users` WHERE `userid` = {$userid}"));
	$db->query("UPDATE `user_estates` SET `bonusWill` = `bonusWill` + ({$increase}) WHERE `ue_id` = {$r}");
}

function increaseMaxWillPercent($userid, $increase)
{
	global $db;
	$r=$db->fetch_single($db->query("SELECT `estate` FROM `users` WHERE `userid` = {$userid}"));
	$r2=$db->fetch_row($db->query("SELECT * FROM `user_estates` WHERE `ue_id` = {$r}"));
	$will=$db->fetch_single($db->query("SELECT * FROM `estates` WHERE `house_id` = {$r2['estate']}"));
	$increase = $increase / 100;
	$newwill = $will * $increase;
	$db->query("UPDATE `user_estates` SET `bonusWill` = `bonusWill` + {$newwill} WHERE `ue_id` = {$r}");
}

function getNameFromUserEstate(int $user)
{
    global $db;
    $estate = $db->fetch_single($db->query("SELECT `estate` FROM `users` WHERE `userid` = {$user}"));
    return getNameFromEstateID($estate);
}

function getNameFromEstateID(int $id)
{
    global $db;
    $q=$db->query("SELECT * FROM `user_estates`
	INNER JOIN `estates` as `e`
	ON `estate` = `e`.`house_id`
	WHERE `ue_id` = {$id}");
    
    $r = $db->fetch_row($q);
    return $r['house_name'];
}

function sleepTick()
{
    global $db, $api;
    $q = $db->query("SELECT `ue`.*, `u`.`estate`, `ue_id`, `uest`.`sleepUpgrade`, `e`.`house_will`
                FROM `users_effects` `ue`
                INNER JOIN `users` AS `u`
                ON `ue`.`userid` = `u`.`userid`
                INNER JOIN `user_estates` AS `uest`
                ON `u`.`estate` = `uest`.`ue_id`
                INNER JOIN `estates` AS `e`
                ON `e`.`house_id` = `uest`.`estate`
                WHERE `effectName` = 'sleep'");
    while ($r = $db->fetch_row($q))
    {
        $inc = calcSleepEfficiency($r['sleepUpgrade'], $r['house_will']);
        $db->query("UPDATE `users` SET `hp` = `hp` + {$inc} WHERE `userid` = {$r['userid']}");
        $db->query("UPDATE `users` SET `brave` = `brave` + {$inc} WHERE `userid` = {$r['userid']}");
        $db->query("UPDATE `users` SET `will` = `will` + {$inc} WHERE `userid` = {$r['userid']}");
        $db->query("UPDATE `users` SET `energy` = `energy` + {$inc} WHERE `userid` = {$r['userid']}");
    }
}

function countEstateTotalUpgrades($estate_id)
{
    global $db;
    $q = $db->query("SELECT `vaultUpgrade`, `gardenUpgrade`, `sleepUpgrade` FROM `user_estates` WHERE `ue_id` = {$estate_id}");
    $r = $db->fetch_row($q);
    return $r['vaultUpgrade'] + $r['gardenUpgrade'] + $r['sleepUpgrade'];
}