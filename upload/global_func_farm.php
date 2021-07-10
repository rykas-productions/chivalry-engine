<?php
function doFarmTick()
{
	fixFarmLevel();
	checkPlotInfo();
	checkFarmXP();
}
function fiveMinuteFarm()
{
	global $db, $api, $userid;
	$q=$db->query("/*qc=on*/SELECT * FROM `farm_data` WHERE `farm_stage` > 0");
	while ($r = $db->fetch_row($q))
	{
	    if (Random(1, 25) == 11)
	    {
    		$wellnessLost=Random(1,2);
    		$db->query("UPDATE `farm_data` SET `farm_wellness` = `farm_wellness` - {$wellnessLost} WHERE `farm_id` = {$r['farm_id']}");
	    }
	}
}
//Catch for lazy programming
function fixFarmLevel()
{
	global $db;
	$db->query("UPDATE `farm_users` SET `farm_level` = 1 WHERE `farm_level` < 1");
}
function checkPlotInfo()
{
	global $db, $api, $userid;
	$q=$db->query("/*qc=on*/SELECT * FROM `farm_data`");
	while ($r = $db->fetch_row($q))
	{
		$sq=$db->query("/*qc=on*/SELECT * FROM `farm_produce` WHERE `seed_item` = {$r['farm_seed']}");
		$sr=$db->fetch_row($sq);
		$wiltTime=$r['farm_time']+$sr['seed_safe_time'];
		if ($r['farm_wellness'] < $sr['seed_wellness_bad'])
		{
			$db->query("UPDATE `farm_data` SET `farm_time` = 0, `farm_stage` = 0, `farm_seed` = 0 WHERE `farm_id` = {$r['farm_id']}");
			$api->GameAddNotification($r['farm_owner'],"The wellness on one of your farm plots dropped and the {$api->SystemItemIDtoName($r['farm_seed'])} planted there rotted away.");
		}
		if ((time() > $wiltTime) && ($r['farm_stage'] > 0))
		{
			$db->query("UPDATE `farm_data` SET `farm_time` = 0, `farm_stage` = 0, `farm_seed` = 0 WHERE `farm_id` = {$r['farm_id']}");
			$api->GameAddNotification($r['farm_owner'],"You took too long to interact with one of your farm plots and the {$api->SystemItemIDtoName($r['farm_seed'])} planted there wilted away.");
		}
		if ($r['farm_wellness'] == 0)
		{
			$db->query("DELETE FROM `farm_data` WHERE `farm_id` = {$r['farm_id']}");
			$api->GameAddNotification($r['farm_owner'], "One of your fields reached 0% wellness and is no longer usable. You must purchase new land.");
		}
	}
}
function checkFarmXP()
{
	global $db, $userid, $FU;
    $FU['xp_needed'] = round(($FU['farm_level'] + 1.5) * ($FU['farm_level'] + 1.5) * ($FU['farm_level'] + 1.5) * 1.1);
    if ($FU['farm_xp'] >= $FU['xp_needed']) {
        $expu = $FU['farm_xp'] - $FU['xp_needed'];
        $FU['farm_level'] += 1;
        $FU['farm_xp'] = $expu;
		$FU['farm_water_max'] += 5;
        $FU['xp_needed'] =
            round(($FU['farm_level'] + 1.5) * ($FU['farm_level'] +1.5) * ($FU['farm_level'] + 1.5) * 1.1);
        $db->query("UPDATE `farm_users` SET 
					`farm_level` = {$FU['farm_level']},
					`farm_xp` = {$FU['farm_xp']},
					`farm_water_max` = {$FU['farm_water_max']}
					WHERE `userid` = {$userid}");
    }
}
function returnStageDetail($stageInt, $stageTime, $seedSafeTime)
{
	if ($stageInt == 0)
		return "Empty";
	if ($stageInt == 1)
	{
		if (time() < $stageTime)
			return "Planting";
		else
			return "<b>Planted</b><br />
		Wilt Time: " . TimeUntil_Parse($seedSafeTime+$stageTime) . "";
	}
	if ($stageInt == 2)
	{
		if (time() < $stageTime)
			return "Harvesting";
		else
			return "<b>Harvested</b><br />
		Wilt Time: " . TimeUntil_Parse($seedSafeTime+$stageTime) . "";
	}
	if ($stageInt >= 10)
	{
		if (time() < $stageTime)
			return "Tending";
		else
			return "<b>Tended</b><br />
		Wilt Time: " . TimeUntil_Parse($seedSafeTime+$stageTime) . "";
	}
}
function createField($user)
{
	global $db;
	$db->query("INSERT INTO `farm_data` (`farm_owner`, `farm_seed`, `farm_stage`, `farm_wellness`, `farm_time`) VALUES ('{$user}', '0', '0', '100', '0')");
}

function deleteField($field_id)
{
    global $db;
    $db->query("DELETE FROM `farm_data` WHERE `farm_id` = {$field_id}");
}
function countFarmland($user)
{
	global $db;
	$q=$db->query("/*qc=on*/SELECT `farm_id` FROM `farm_data` WHERE `farm_owner` = {$user}");
    return $db->num_rows($q);
}
function returnStageActions($stage,$fieldid,$stageTime,$seed)
{
	if ($stage == 0)
	{
		return "[<a href='?action=plant&id={$fieldid}'>Plant</a>]<br />
		[<a href='?action=water&id={$fieldid}'>Water</a>]<br />
			[<a href='?action=fertilize&id={$fieldid}'>Fertilize</a>]<br />
            [<a href='?action=torchland&id={$fieldid}'>Torch Land</a>]";
	}
	if ($stage == 1)
	{
		if (time() < $stageTime)
			return TimeUntil_Parse($stageTime) . "<br />
			[<a href='?action=water&id={$fieldid}'>Water</a>]<br />
			[<a href='?action=fertilize&id={$fieldid}'>Fertilize</a>]<br />
            [<a href='?action=torchland&id={$fieldid}'>Torch Land</a>]";
		else
			return "[<a href='?action=tend&id={$fieldid}'>Tend</a>]<br />
		[<a href='?action=water&id={$fieldid}'>Water</a>]<br />
			[<a href='?action=fertilize&id={$fieldid}'>Fertilize</a>]<br />
            [<a href='?action=torchland&id={$fieldid}'>Torch Land</a>]";
	}
	if ($stage == 2)
	{
		if (time() < $stageTime)
		{
			return TimeUntil_Parse($stageTime) . "<br />
			[<a href='?action=water&id={$fieldid}'>Water</a>]<br />
			[<a href='?action=fertilize&id={$fieldid}'>Fertilize</a>]<br />
            [<a href='?action=torchland&id={$fieldid}'>Torch Land</a>]";
		}
		else
		{
			return "[<a href='?action=harvest&id={$fieldid}'>Harvest</a>]<br />
			[<a href='?action=collect&id={$fieldid}'>Seed Collection</a>]<br />
			[<a href='?action=water&id={$fieldid}'>Water</a>]<br />
			[<a href='?action=fertilize&id={$fieldid}'>Fertilize</a>]<br />
            [<a href='?action=torchland&id={$fieldid}'>Torch Land</a>]";
		}
	}
	if ($stage >= 10)
	{
		if (time() < $stageTime)
			return TimeUntil_Parse($stageTime) . "<br />
			[<a href='?action=water&id={$fieldid}'>Water</a>]<br />
			[<a href='?action=fertilize&id={$fieldid}'>Fertilize</a>]<br />
            [<a href='?action=torchland&id={$fieldid}'>Torch Land</a>]";
		else
			return "[<a href='?action=tend&id={$fieldid}'>Tend</a>]<br />
		[<a href='?action=water&id={$fieldid}'>Water</a>]<br />
			[<a href='?action=fertilize&id={$fieldid}'>Fertilize</a>]<br />
            [<a href='?action=torchland&id={$fieldid}'>Torch Land</a>]";
	}
}
function seed_dropdown($ddname = "seed", $selected = -1)
{
   global $db;
    $ret = "<select name='$ddname' class='form-control' type='dropdown'>";
    $q =
        $db->query(
            "/*qc=on*/SELECT `s`.*, `i`.*
                     FROM `farm_produce` AS `s`
                     INNER JOIN `items` AS `i`
                     ON `s`.`seed_item` = `i`.`itmid`
                     ORDER BY `s`.`seed_lvl_requirement` ASC");
    if ($selected == -1) {
        $first = 0;
    } else {
        $first = 1;
    }
    while ($r = $db->fetch_row($q)) {
        $ret .= "\n<option value='{$r['seed_item']}'";
        if ($selected == $r['seed_id'] || $first == 0) {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['itmname']} [Farming Level: {$r['seed_lvl_requirement']}]</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}
function doWaterAttempt($userid)
{
	global $db,$api;
	//Use well water first, then use item stockpile. If have neither, then we fail.
	$q=$db->query("/*qc=on*/SELECT * FROM `farm_users` WHERE `userid` = {$userid}");
	if ($db->num_rows($q) == 0)
		$db->query("INSERT INTO `farm_users` (`userid`) VALUES ('{$userid}')");
	$FU = ($db->fetch_row($db->query("/*qc=on*/SELECT * FROM `farm_users` WHERE `userid` = {$userid}")));
	if ($FU['farm_water_available'] > 0)
	{
		$db->query("UPDATE `farm_users` SET `farm_water_available` = `farm_water_available` - 1 WHERE `userid` = {$userid}");
		return true;
	}
	elseif ($api->UserHasItem($userid,$api->SystemItemNameToID("Bucket of Water"),1))
	{
		$api->UserTakeItem($userid,$api->SystemItemNameToID("Bucket of Water"),1);
		return true;
	}
	else
	{
		return false;
	}
}

function getRemainingTime($complete)
{
	return $complete - time();
}

function addStageTime($fieldID, $change)
{
	global $db;
	$db->query("UPDATE `farm_data` SET `farm_time` = `farm_time` + {$change} WHERE `farm_id` = {$fieldID}");
}

function removeStageTime($fieldID, $change)
{
	global $db;
	$r=$db->fetch_row($db->query("/*qc=on*/SELECT * FROM `farm_data` WHERE `farm_id` = {$fieldID}"));
	$time=time();
	$safeTime = $r['farm_time'] - $change;
	if ($safeTime < $time)
		$db->query("UPDATE `farm_data` SET `farm_time` = '{$time}' WHERE `farm_id` = {$fieldID}");
	else
		$db->query("UPDATE `farm_data` SET `farm_time` = `farm_time` - {$change} WHERE `farm_id` = {$fieldID}");
	return true;
}

function returnSeedSafeTime($seedID)
{
	global $db;
	return $db->fetch_single($db->query("/*qc=on*/SELECT `seed_safe_time` FROM `farm_produce` WHERE `seed_id` = {$seedID}"));
}

function returnTotalStages($plotID)
{
	global $db;
	$seedID=$db->fetch_single($db->query("/*qc=on*/SELECT `farm_seed` FROM `farm_data` WHERE `farm_id` = {$plotID}"));
	$stage=$db->fetch_single($db->query("/*qc=on*/SELECT `seed_stages` FROM `farm_produce` WHERE `seed_item` = {$seedID}"));
	return $stage + 3;
}

function returnCurrentStage($plotID)
{
	global $db;
	$r=$db->fetch_row($db->query("/*qc=on*/SELECT `farm_seed`, `farm_stage` FROM `farm_data` WHERE `farm_id` = {$plotID}"));
	if ($r['farm_stage'] >= 10)
		return $r['farm_stage'] - 8;
	if ($r['farm_stage'] == 2)
		return returnTotalStages($plotID);
	if ($r['farm_stage'] == 0)
		return 0;
	else
		return $r['farm_stage'];
		
}