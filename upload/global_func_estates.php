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
	return ceil(($estateWill*0.05)*$gardenLevel)+$gardenLevel;
}
function calcStoneCosts($gardenLevel, $estateWill)
{
	$gardenLevel++;
	$gardenLevel*=0.5;
	return ceil(($estateWill*0.08)*$gardenLevel)+$gardenLevel;
}
function calcStickCosts($gardenLevel, $estateWill)
{
	$gardenLevel++;
	$gardenLevel*=0.5;
	return ceil(($estateWill*0.12)*$gardenLevel)+$gardenLevel;
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
	global $db, $userid;
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
		$db->query("UPDATE `users` SET `maxwill` = 100, `estate` = {$r['ue_id']} WHERE `userid` = {$user}");
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
	global $db;
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
	$startPrice = $r2['house_price'];
	$startSellPrice = $r2['house_price'] * 0.55;
	$upgradeCount = $r['gardenUpgrade'] + $r['sleepUpgrade'] + $r['vaultUpgrade'];
	$multi = 0.0585617821 * $upgradeCount;
	$addToSell = $startPrice * $multi;
	$finalSell = $addToSell + $startSellPrice;
	return $finalSell;
}

function increaseMaxWill($userid, $increase)
{
	global $db;
	$r=$db->fetch_single($db->query("SELECT `estate` FROM `users` WHERE `userid` = {$userid}"));
	$db->query("UPDATE `user_estates` SET `bonusWill` = `bonusWill` + {$increase} WHERE `ue_id` = {$r}");
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
    $q=$db->query("SELECT * FROM `user_estates`
	INNER JOIN `estates` as `e`
	ON `estate` = `e`.`house_id`
	WHERE `userid` = {$user}");
    
    $r = $db->fetch_row($q);
    return $r['house_name'];
}