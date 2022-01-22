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
	    if (Random(1, 20) == 11)
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
	global $db, $userid, $FU, $ir;
	if (!isset($ir['reset']))
	    $ir['reset'] = 0;
	    $FU['xp_needed'] = round((($FU['farm_level'] + 1.5) * ($FU['farm_level'] + 1.5) * ($FU['farm_level'] + 1.5) * 1.1) * (1 - ($ir['reset'] * 0.1)));
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
function returnStageDetail($stageInt, $stageTime, $seedSafeTime, $farmID)
{
    if ($stageInt == 0)
    {
        return;
    }
    $wiltTime = $seedSafeTime+$stageTime;
    $timeRemain = $wiltTime - time();
    $timeRemainPerc = $timeRemain / $seedSafeTime * 100;
	if ($stageInt == 1)
	{
	    if (time() > $stageTime)
	        return createWiltBar($timeRemainPerc, "Wilt Time: " . TimeUntil_Parse($wiltTime));
	}
	if ($stageInt == 2)
	{
	    if (time() > $stageTime)
	        return createWiltBar($timeRemainPerc, "Wilt Time: " . TimeUntil_Parse($wiltTime));
	}
	if ($stageInt >= 10)
	{
		if (time() > $stageTime)
			return createWiltBar($timeRemainPerc, "Wilt Time: " . TimeUntil_Parse($wiltTime));
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
    global $db;
    if ($seed > 0)
    {
        $seedStageTime = $db->fetch_single($db->query("SELECT `seed_time` FROM `farm_produce` WHERE `seed_item` = {$seed}"));
        $timeRemain = $stageTime - time();
        $timeRemainPerc = $timeRemain / $seedStageTime * 100;
    }
    $links = "";
    $hotlink = "";
	if ($stage == 0)
	{
	    //see below
	    $hotlink .=  "<div class='col-12 col-sm-6 col-lg-4 col-xxl-3'><a href='?action=plant&id={$fieldid}' class='btn btn-success btn-block'>Plant</a><br /></div>";
	}
	if ($stage == 1)
	{
		if (time() < $stageTime)
		{
		    $links .= "
            <div class='row'>
            <div class='col-12'>
    			<div class='progress' style='height: 1rem;'>
    				<div class='progress-bar bg-success progress-bar-striped progress-bar-animated' role='progressbar' aria-valuenow='{$timeRemainPerc}' style='width:{$timeRemainPerc}%' aria-valuemin='0' aria-valuemax='100'>
    					<span>
    						" . TimeUntil_Parse($stageTime) . "
    					</span>
    				</div>
    			</div>
                <br />
    		</div>
            </div>";
		}
		else
		    $hotlink .=  "<div class='col-12 col-sm-6 col-lg-4 col-xxl-3'><a href='?action=tend&id={$fieldid}' class='btn btn-success btn-block'>Tend</a><br /></div>";
	}
	if ($stage == 2)
	{
		if (time() < $stageTime)
		{
		    $links .= "
            <div class='row'>
            <div class='col-12'>
    			<div class='progress' style='height: 1rem;'>
    				<div class='progress-bar bg-success progress-bar-striped progress-bar-animated' role='progressbar' aria-valuenow='{$timeRemainPerc}' style='width:{$timeRemainPerc}%' aria-valuemin='0' aria-valuemax='100'>
    					<span>
    						" . TimeUntil_Parse($stageTime) . "
    					</span>
    				</div>
    			</div>
                <br />
    		</div>
            </div>";
		}
		else
		{
		    $hotlink .=  "<div class='col-12 col-sm-6 col-lg-4 col-xxl-3'><a href='?action=harvest&id={$fieldid}' class='btn btn-success btn-block'>Harvest</a><br /></div>
			<div class='col-12 col-sm-6 col-lg-4 col-xxl-3'><a href='?action=collect&id={$fieldid}' class='btn btn-success btn-block'>Seed Collection</a><br /></div>";
		}
	}
	if ($stage >= 10)
	{
		if (time() < $stageTime)
		{
		    $links .= "
            <div class='row'>
            <div class='col-12'>
    			<div class='progress' style='height: 1rem;'>
    				<div class='progress-bar bg-success progress-bar-striped progress-bar-animated' role='progressbar' aria-valuenow='{$timeRemainPerc}' style='width:{$timeRemainPerc}%' aria-valuemin='0' aria-valuemax='100'>
    					<span>
    						" . TimeUntil_Parse($stageTime) . "
    					</span>
    				</div>
    			</div>
                <br />
    		</div>
            </div>";
		}
		else
		    $hotlink .=  "<div class='col-12 col-sm-6 col-lg-4 col-xxl-3'><a href='?action=tend&id={$fieldid}' class='btn btn-success btn-block'>Tend</a><br /></div>";
	}
	$links .= "
            <div class='row'>
                {$hotlink}
                <div class='col-12 col-sm-6 col-lg-4 col-xxl-3'>
                    <a href='?action=water&id={$fieldid}' class='btn btn-primary btn-block'>Water</a><br />
                </div>
                <div class='col-12 col-sm-6 col-lg-4 col-xxl-3'>
			         <a href='?action=fertilize&id={$fieldid}' class='btn btn-info btn-block'>Fertilize</a><br />
                </div>
                <div class='col-12 col-sm-6 col-lg-4 col-xxl-3'>
                    <a href='?action=torchland&id={$fieldid}' class='btn btn-danger btn-block'>Torch Land</a><br />
                </div>
            </div>";
	return $links;
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
	$waterBucketID = $api->SystemItemNameToID("Bucket of Water");
	if ($FU['farm_water_available'] > 0)
	{
		$db->query("UPDATE `farm_users` SET `farm_water_available` = `farm_water_available` - 1 WHERE `userid` = {$userid}");
		return true;
	}
	elseif ($api->UserHasItem($userid, $waterBucketID, 1))
	{
	    consumeBucket($userid, $waterBucketID, 1);
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

function createWellnessBar($wellness)
{
    if ($wellness < 34)
        $bg = 'danger';
    elseif ($wellness < 67)
        $bg = 'warning';
    elseif ($wellness <= 100)
        $bg = 'success';
    elseif ($wellness > 100)
        $bg = 'info';
    $bar = "<div class='progress' style='height: 1rem;'>
	           <div class='progress-bar bg-{$bg} progress-bar-striped progress-bar-animated' role='progressbar' aria-valuenow='{$wellness}' style='width:{$wellness}%' aria-valuemin='0' aria-valuemax='100'>
			     <span>
					Wellness ({$wellness}%)
				</span>
			</div>
		  </div>
            <br />";
    return $bar;
}

function createWiltBar($percent, $txt)
{
    $percent = round($percent);
    if ($percent < 15)
        $bg = 'dark';
    elseif ($percent < 34)
        $bg = 'danger';
    elseif ($percent < 67)
        $bg = 'warning';
    elseif ($percent <= 100)
        $bg = 'success';
    $bar = "<div class='progress' style='height: 1rem;'>
	           <div class='progress-bar bg-{$bg} progress-bar-striped progress-bar-animated' role='progressbar' aria-valuenow='{$percent}' style='width:{$percent}%' aria-valuemin='0' aria-valuemax='100'>
			     <span>
					{$txt} ({$percent}%)
				</span>
			</div>
		  </div>
            <br />";
    return $bar;
}

function createFarmStageBar($currentStage, $maxStage, $txt)
{
    $percent = round($currentStage / $maxStage * 100);
    if ($percent < 15)
        $bg = 'dark';
    elseif ($percent < 34)
        $bg = 'danger';
    elseif ($percent < 67)
        $bg = 'warning';
    elseif ($percent <= 100)
        $bg = 'success';
    $bar = "<div class='progress' style='height: 1rem;'>
           <div class='progress-bar bg-{$bg} progress-bar-striped progress-bar-animated' role='progressbar' aria-valuenow='{$percent}' style='width:{$percent}%' aria-valuemin='0' aria-valuemax='100'>
		     <span>
				{$txt} ({$currentStage}/{$maxStage})
			</span>
		</div>
	  </div>
        <br />";
	return $bar;
}

function returnStagebyID($stageInt, $stageTime)
{
    if ($stageInt == 0)
        return "Empty";
    if ($stageInt == 1)
    {
        if (time() < $stageTime)
            return "Planting";
        else
            return "Planted";
    }
    if ($stageInt == 2)
    {
        if (time() < $stageTime)
            return "Harvesting";
        else
            return "Harvested";
    }
    if ($stageInt >= 10)
    {
        if (time() < $stageTime)
            return "Tending";
        else
            return "Tended";
    }
}