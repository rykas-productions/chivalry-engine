<?php
//Module config
$districtConfig['MaxSizeX'] = 5;
$districtConfig['MaxSizeY'] = 5;
$districtConfig['BarracksMaxWarriors'] = 3000;
$districtConfig['BarracksMaxArchers'] = 1500;
$districtConfig['GeneralBuff'] = 0.1821715;
$districtConfig['GeneralTroops'] = 2500;
$districtConfig['GeneralCost'] = 250000;
$districtConfig['GeneralCostDaily'] = 30000;
$districtConfig['WarriorCost'] = 4500;
$districtConfig['WarriorCostDaily'] = 550;
$districtConfig['ArcherCost'] = 8000;
$districtConfig['ArcherCostDaily'] = 1100;
$districtConfig['copperPerFortify']=5000;
$districtConfig['xpPerFortify']=125;
$districtConfig['xpPerFortifyMulti']=2.2578721;
$districtConfig['fortifyBuffMulti']=0.0613655;
$districtConfig['attackRangeDmgMulti']=1.194115;
$districtConfig['attackDmgWeakeness']=0.741294;
$districtConfig['attackDmgStrength']=1.04901724;
$districtConfig['attackDefenseAdvantage']=1.1582412;
$districtConfig['maxGenerals'] = 2;
$districtConfig['maxFortify'] = 5;
$districtConfig['sabotageItem'] = 62;
$districtConfig['townLessCost'] = 0.15;
$districtConfig['outpostExtraTroops'] = 0.15;
$districtConfig['CaptainBuff'] = 0.211240;
$districtConfig['CaptainCost'] = 175000;
$districtConfig['CaptainCostUse'] = 25000;
$districtConfig['CaptainTroops'] = $districtConfig['GeneralTroops'];
$districtConfig['upkeepPerTile'] = 10000;

//end module config

/**
 * @internal
 * Called daily to charge guilds their daily fees.
 */
function doDailyDistrictTick()
{
	global $db, $api, $districtConfig;
	$db->query("UPDATE `guild_district_info` SET `warriors_bought` = 0, `archers_bought` = 0, `moves` = 2");
	$q=$db->query("SELECT * FROM `guild_district_info`");
	while ($r=$db->fetch_row($q))
	{
		$upkeepFee=0;
		$warriors = countDeployedWarriors($r['guild_id']);
		$archers = countDeployedArchers($r['guild_id']);
		$generals = countDeployedGenerals($r['guild_id']);
		$tiles = countOwnedDistricts($r['guild_id']);
		if ($warriors > 0)
			$upkeepFee=$upkeepFee + ($warriors * $districtConfig['WarriorCostDaily']);
		if ($archers > 0)
			$upkeepFee=$upkeepFee + ($archers * $districtConfig['ArcherCostDaily']);
		if ($generals > 0)
			$upkeepFee=$upkeepFee + ($generals * $districtConfig['GeneralCostDaily']);
		if ($tiles > 0)
		    $upkeepFee=$upkeepFee + ($tiles * $districtConfig['upkeepPerTile']);
		if ($upkeepFee > 0)
		{
			$db->query("UPDATE `guild` SET `guild_primcurr` = `guild_primcurr` - {$upkeepFee} WHERE `guild_id` = {$r['guild_id']}");
			addToEconomyLog('Guild Upkeep', 'copper', $upkeepFee*-1);
			$api->GuildAddNotification($r['guild_id'],"Your guild has been charged a district's upkeep fee of " . number_format($upkeepFee) . " Copper Coins.");
		}
	}
	districtRewards();
}
function countActiveGuildMembers24Hr($guild_id)
{
	global $db;
	$last_on = time() - (1440*60);
	$q = $db->query("/*qc=on*/SELECT `username` FROM `users` WHERE `guild` = {$guild_id} AND `laston` > {$last_on}");
	return $db->num_rows($q);
}
function countDeployedWarriors($guild_id)
{
	global $db;
	$q=$db->query("SELECT SUM(`district_melee`) FROM `guild_districts` WHERE `district_owner` = {$guild_id}");
	return $db->fetch_single($q);
}
function countDeployedArchers($guild_id)
{
	global $db;
	$q=$db->query("SELECT SUM(`district_range`) FROM `guild_districts` WHERE `district_owner` = {$guild_id}");
	return $db->fetch_single($q);
}
function countDeployedGenerals($guild_id)
{
	global $db;
	$q=$db->query("SELECT SUM(`district_general`) FROM `guild_districts` WHERE `district_owner` = {$guild_id}");
	return $db->fetch_single($q);
}
function countOwnedDistricts($guild_id)
{
    global $db;
    return $db->num_rows($db->query("SELECT `district_id` FROM `guild_districts` WHERE `district_owner` = {$guild_id}"));
}
function districtRewards()
{
	districtRewardMostControlledTiles();
	districtRewardMostDeployedUnits();
}

function districtRewardMostDeployedUnits()
{
	global $db, $api;
	$winnerguild = 0;
	$currentmax = 0;
	$q = $db->query("SELECT `guild_id` FROM `guild` WHERE `guild_id` != 1 AND `guild_id` != 16");
	while ($r = $db->fetch_row($q))
	{
		$currentGuildID = $r['guild_id'];
		$currentGuild = 0;
		$q2 = $db->query("SELECT `district_melee`, `district_range` FROM `guild_districts` WHERE `district_owner` = {$r['guild_id']}");
		while ($r2 = $db->fetch_row($q2))
		{
			$currentGuild = $currentGuild + ($r2['district_melee'] + $r2['district_range']);
		}
		if ($currentGuild > $currentmax)
		{
			$currentmax = $currentGuild;
			$winnerguild = $currentGuildID;
		}
	}
	$api->GuildAddItem($winnerguild,205,5);
	$api->GuildAddNotification($winnerguild, "Your guild has the most deployed units on the guild districts and has received 5 {$api->SystemItemIDtoName(205)} to your armory.");
}

function districtRewardMostControlledTiles()
{
	global $db, $api;
	$winnerguild = 0;
	$currentmax = 0;
	$q = $db->query("SELECT `guild_id` FROM `guild` WHERE `guild_id` != 1 AND `guild_id` != 16");
	while ($r = $db->fetch_row($q))
	{
		$currentGuildID = $r['guild_id'];
		$currentGuild = 0;
		$q2 = $db->query("SELECT `district_owner` FROM `guild_districts` WHERE `district_owner` = {$r['guild_id']}");
		while ($r2 = $db->fetch_row($q2))
		{
			$currentGuild = $currentGuild + 1;
		}
		if ($currentGuild > $currentmax)
		{
			$currentmax = $currentGuild;
			$winnerguild = $currentGuildID;
		}
	}
	$api->GuildAddItem($winnerguild,205,5);
	$api->GuildAddNotification($winnerguild, "Your guild has the most controlled tiles on the guild districts and has received 5 {$api->SystemItemIDtoName(205)} to your armory.");
}
function isDistrictAccessible($district_id)
{
    global $db, $ir, $districtConfig;
    $q=$db->query("SELECT * FROM `guild_districts` WHERE `district_id` = {$district_id}");
    if ($ir['guild'] == 0)
        $guild=-1;
        else
            $guild=$ir['guild'];
            $r=$db->fetch_row($q);
            $minX=$r['district_x'] - 1;
            $minY=$r['district_y'] - 1;
            $maxX=$r['district_x'] + 1;
            $maxY=$r['district_y'] + 1;
            $count = $db->num_rows($db->query("SELECT `district_id`
											FROM `guild_districts`
											WHERE `district_owner` = {$guild}
											AND (`district_x` >= {$minX} AND `district_x` <= {$maxX})
											AND (`district_y` >= {$minY} AND `district_y` <= {$maxY})"));
            if ($r['district_type'] == 'river')
            {
                return false;
                exit;
            }
            if ($count > 0)
                return true;
                elseif ($r['district_y'] == 1)
                return true;
                elseif ($r['district_y'] == $districtConfig['MaxSizeY'])
                return true;
                elseif ($r['district_x'] == $districtConfig['MaxSizeX'])
                return true;
                elseif ($r['district_x'] == 1)
                return true;
                else
                    return false;
}

function isGuildDistrict($district_id)
{
    global $db, $ir, $districtConfig;
    $q=$db->query("SELECT `district_owner` FROM `guild_districts` WHERE `district_id` = {$district_id}");
    if ($ir['guild'] == 0)
        $guild=-1;
        else
            $guild=$ir['guild'];
            $r=$db->fetch_single($q);
            if ($r == $guild)
                return true;
                else
                    return false;
}

function doAttack($attackWarrior, $attackArcher, $attackCaptain, $defenseWarrior, $defenseArcher, $defenseGeneral = 0, $attackBuff = 1.0, $defenseBuff = 1.0)
{
    global $api, $userid, $districtConfig;
    
    $attackTotal = $attackWarrior + $attackArcher;
    $defenseTotal = $defenseWarrior + $defenseArcher;
    
    //results
    $result = array();
    $result['winner'] = '';
    $result['attack_warrior_lost'] = 0;
    $result['attack_archer_lost'] = 0;
    $result['attack_captain_lost'] = 0;
    $result['defense_warrior_lost'] = 0;
    $result['defense_archer_lost'] = 0;
    $result['defense_general_lost'] = 0;
    while (empty($result['winner']))
    {
        $defenderRating=0;
        $attackerRating=0;
        $dfndr=0;
        $fght=0;
        
        //Pick defender
        if (($defenseWarrior > 0) && ($defenseArcher > 0))
        {
            $dfndr=mt_rand(1,2);
        }
        elseif (($defenseArcher == 0) && ($defenseWarrior > 0))
        {
            $dfndr = 1;
        }
        elseif (($defenseArcher > 0) && ($defenseWarrior == 0))
        {
            $dfndr = 2;
        }
        else
        {
            $dfndr = 0;
        }
        
        if ($dfndr > 0)
        {
            //Pick attacker
            if (($attackWarrior > 0) && ($attackArcher > 0))
            {
                $fght=mt_rand(1,2);
            }
            elseif (($attackArcher == 0) && ($attackWarrior > 0))
            {
                $fght = 1;
            }
            elseif (($attackArcher > 0) && ($attackWarrior == 0))
            {
                $fght = 2;
            }
            else
            {
                $fght = 0;
            }
            if ($fght > 0)
            {
                //attacker is warrior
                if ($fght == 1)
                {
                    //defender is warrior
                    if ($dfndr == 1)
                    {
                        $defenderRating = mt_rand(100,300) * $defenseBuff;
                        $defenderRating = $defenderRating + ($defenderRating * ($defenseGeneral*$districtConfig['GeneralBuff']));
                        $attackerRating = mt_rand(100,300) * $attackBuff;
                        $attackerRating = mt_rand(100,300) + ($attackBuff * ($attackCaptain*$districtConfig['CaptainBuff']));
                        //Attacker warrior beats defender warrior
                        if ($attackerRating >= $defenderRating)
                        {
                            $defenseWarrior--;
                            $defenseTotal--;
                            $result['defense_warrior_lost']++;
                        }
                        //Attack warrior loses to defender warrior
                        else
                        {
                            $attackWarrior--;
                            $attackTotal--;
                            $result['attack_warrior_lost']++;
                        }
                    }
                    //defender is archer
                    else
                    {
                        $defenderRating = mt_rand(50,268) * $defenseBuff;
                        $defenderRating = $defenderRating + ($defenderRating * ($defenseGeneral*$districtConfig['GeneralBuff']));
                        $attackerRating = mt_rand(50,268) * $attackBuff;
                        $attackerRating = mt_rand(100,300) + ($attackBuff * ($attackCaptain*$districtConfig['CaptainBuff']));
                        //Attacker warrior beats defender archer
                        if ($attackerRating >= $defenderRating)
                        {
                            $defenseArcher--;
                            $defenseTotal--;
                            $result['defense_archer_lost']++;
                        }
                        //Attack warrior loses to defender archer
                        else
                        {
                            $attackWarrior--;
                            $attackTotal--;
                            $result['attack_warrior_lost']++;
                        }
                    }
                    
                }
                //attacker is archer
                else
                {
                    //defender is warrior
                    if ($dfndr == 1)
                    {
                        $defenderRating = mt_rand(150,350) * $defenseBuff;
                        $defenderRating = $defenderRating + ($defenderRating * ($defenseGeneral* $districtConfig['GeneralBuff']));
                        $attackerRating = mt_rand(75,400) * $attackBuff;
                        //Attacker warrior beats defender warrior
                        if ($attackerRating >= $defenderRating)
                        {
                            $defenseWarrior--;
                            $defenseTotal--;
                            $result['defense_warrior_lost']++;
                        }
                        //Attack archer loses to defender warrior
                        else
                        {
                            $attackArcher--;
                            $attackTotal--;
                            $result['attack_archer_lost']++;
                        }
                    }
                    //defender is archer
                    else
                    {
                        $defenderRating = mt_rand(75,400) * $defenseBuff;
                        $defenderRating = $defenderRating + ($defenderRating * ($defenseGeneral*$districtConfig['GeneralBuff']));
                        $attackerRating = mt_rand(150,350) * $attackBuff;
                        //Attacker archer beats defender archer
                        if ($attackerRating >= $defenderRating)
                        {
                            $defenseArcher--;
                            $defenseTotal--;
                            $result['defense_archer_lost']++;
                        }
                        //Attack archer loses to defender archer
                        else
                        {
                            $attackArcher--;
                            $attackTotal--;
                            $result['attack_archer_lost']++;
                        }
                    }
                }
            }
            else
            {
                //defender wins battle
                $result['winner'] = 'defense';
                $result['attack_captain_lost'] = $attackCaptain;
                $winner=true;
            }
        }
        else
        {
            //attacker wins battle
            $result['winner'] = 'attack';
            $result['defense_general_lost'] = $defenseGeneral;
            $winner=true;
        }
    }
    return json_encode($result);
}

function isAccessibleFromTile($from_tile, $to_tile)
{
    global $db;
    $q=$db->query("SELECT * FROM `guild_districts` WHERE `district_id` = {$from_tile}");
    $r=$db->fetch_row($q);
    $minX=$r['district_x'] - 1;
    $minY=$r['district_y'] - 1;
    $maxX=$r['district_x'] + 1;
    $maxY=$r['district_y'] + 1;
    $count = $db->num_rows($db->query("SELECT `district_id`
											FROM `guild_districts`
											WHERE `district_id` = {$to_tile}
											AND (`district_x` >= {$minX} AND `district_x` <= {$maxX})
											AND (`district_y` >= {$minY} AND `district_y` <= {$maxY})"));
    if ($count > 0)
        return true;
        else
            return false;
}
function setTileOwnership($district_id, $new_owner)
{
    global $db;
    $db->query("UPDATE `guild_districts` SET `district_owner` = {$new_owner}, `district_fortify` = 0 WHERE `district_id` = {$district_id}");
}
function updateTileTroops($district_id, $warriorChange, $archerChange, $generalChange)
{
    global $db;
    $db->query("UPDATE `guild_districts`
				SET `district_melee` = `district_melee` + ({$warriorChange}),
				`district_range` = `district_range` + ({$archerChange}),
				`district_general` = `district_general` + ({$generalChange})
				WHERE `district_id` = {$district_id}");
}
function updateBarracksTroops($guild_id, $warriorChange, $archerChange, $generalChange, $captainChange)
{
    global $db;
    $db->query("UPDATE `guild_district_info`
				SET `barracks_warriors` = `barracks_warriors` + ({$warriorChange}),
				`barracks_archers` = `barracks_archers` + ({$archerChange}),
				`barracks_generals` = `barracks_generals` + ({$generalChange}),
                `barracks_captains` = `barracks_captains` + ({$captainChange})
				WHERE `guild_id` = {$guild_id}");
}
function blockAccess($guild)
{
    if ($guild == 0)
        return true;
        else
            false;
}
function resolveCoordinates($district_id)
{
    global $db;
    $q=$db->query("SELECT `district_x`, `district_y` FROM `guild_districts` WHERE `district_id` = {$district_id}");
    $r=$db->fetch_row($q);
    return "X: {$r['district_x']}; Y: {$r['district_y']}";
}
function isGuildLeaders($guild, $user)
{
    global $db;
    $q=$db->query("SELECT `guild_owner`, `guild_coowner` FROM `guild` WHERE `guild_id` = {$guild}");
    $r=$db->fetch_row($q);
    if ($r['guild_owner'] == $user)
        return true;
        elseif ($r['guild_coowner'] == $user)
        return true;
        else
            return false;
}
function returnForticationLevel($district_id)
{
    global $db;
    $q=$db->fetch_single($db->query("SELECT `district_fortify` FROM `guild_districts` WHERE `district_id` = {$district_id}"));
    
    if ($q == 1)
        return "I";
        elseif ($q == 2)
        return "II";
        elseif ($q == 3)
        return "III";
        elseif ($q == 4)
        return "IV";
        elseif ($q == 5)
        return "V";
        elseif ($q == 6)
        return "VI";
        elseif ($q == 7)
        return "VII";
        elseif ($q == 8)
        return "VIII";
        elseif ($q == 9)
        return "IX";
        elseif ($q == 10)
        return "X";
        else
            return "∅";
}

function countTowns()
{
    return countMarkets();
}

function countMarkets()
{
    global $db, $ir;
    return $db->fetch_single($db->query("SELECT COUNT(`district_id`) FROM `guild_districts` WHERE `district_type` = 'market' AND `district_owner` = {$ir['guild']}"));
}

function countOutposts()
{
    global $db, $ir;
    return $db->fetch_single($db->query("SELECT COUNT(`district_id`) FROM `guild_districts` WHERE `district_type` = 'outpost' AND `district_owner` = {$ir['guild']}"));
}

function countActiveTroops()
{
    global $db, $ir;
    $warriors = $db->fetch_single($db->query("SELECT SUM(`district_melee`) FROM `guild_districts` WHERE `district_owner` = {$ir['guild']}"));
    $archers = $db->fetch_single($db->query("SELECT SUM(`district_range`) FROM `guild_districts` WHERE `district_owner` = {$ir['guild']}"));
    return $archers + $warriors;
}

function countGenerals()
{
    global $db, $ir;
    $deployed = $db->fetch_single($db->query("SELECT SUM(`district_general`) FROM `guild_districts` WHERE `district_owner` = {$ir['guild']}"));
    $barracks = $db->fetch_single($db->query("SELECT SUM(`barracks_generals`) FROM `guild_district_info` WHERE `guild_id` = {$ir['guild']}"));
    return $deployed + $barracks;
}

function countCaptains()
{
    global $db, $ir;
    $barracks = $db->fetch_single($db->query("SELECT SUM(`barracks_captains`) FROM `guild_district_info` WHERE `guild_id` = {$ir['guild']}"));
    return $barracks;
}

function logBattle($attacker, $defender, $att_war, $att_range, $att_war_lost, $att_range_lost, $def_war, $def_range, $def_war_lost, $def_range_lost, $def_general, $fortify, $winner, $att_capt)
{
    global $db;
    $time = time();
    $db->query("INSERT INTO `guild_district_battlelog`
		(`attacker`, `defender`, `winner`, `attack_war`,
		`attack_war_lost`, `attack_arch`, `attack_arch_lost`, `attack_captains`, 
		`defend_war`, `defend_war_lost`, `defend_arch`,
		`defend_archer_lost`, `defend_general`, `defend_fortify`,
		`log_time`)
		VALUES ('{$attacker}', '{$defender}', '{$winner}', '{$att_war}', '{$att_war_lost}', 
		'{$att_range}', '{$att_range_lost}', '{$att_capt}', '{$def_war}', '{$def_war_lost}',
		'{$def_range}', '{$def_range_lost}', '{$def_general}', '{$fortify}', '{$time}')");
    return $db->insert_id();
}

function parseTile(int $districtID, $extra = '')
{
    global $db, $ir, $api;
    $id=$db->query("SELECT * FROM `guild_districts` WHERE `district_id` = {$districtID}");
    if ($db->num_rows($id) > 0)
    {
        $district_id = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs($_GET['id']) : '';
        $r=$db->fetch_row($id);
        $class = returnTileClass($r['district_id']);
            echo "
			<td width='33%' class='{$class}'>
				<b>Y: {$r['district_y']}; X: {$r['district_x']}</b><br />
				Guild: <a href='guilds.php?action=view&id={$r['district_owner']}'>{$api->GuildFetchInfo($r['district_owner'],'guild_name')}</a><br />";
            if (isDistrictAccessible($r['district_id']))
            {
                echo "Warriors: " . number_format($r['district_melee']) . "<br />
					Archers: " . number_format($r['district_range']) . "<br />
					Generals: " . number_format($r['district_general']) . "<br />
					Fortification: " . returnForticationLevel($r['district_id']) . "<br />";
                    if ($extra == "attack")
                    {
                        if (isGuildDistrict($r['district_id']))
                        {
                            if (isAccessibleFromTile($r['district_id'], $district_id))
                                echo "<a href='?action=attackform&from={$r['district_id']}&to={$district_id}' class='btn btn-danger'>Attack from Here</a>";
                        }
                        if ($district_id == $r['district_id'])
                            echo "<a href='?action=attackformbarracks&to={$district_id}' class='btn btn-danger'>Attack from Barracks</a>";
                    }
                    if ($extra == "moveto")
                    {
                        if (isGuildDistrict($r['district_id']) && ($district_id != $r['district_id']))
                        {
                            if (isAccessibleFromTile($r['district_id'], $district_id))
                                echo "<a href='?action=moveform&from={$r['district_id']}&to={$district_id}' class='btn btn-warning'>Move to Here</a>";
                        }
                        if ($district_id == $r['district_id'])
                            echo "<i><b>Moving troops from here...</b></i>";
                    }
                    if ($extra == "info")
                    {
                        if ($district_id == $r['district_id'])
                        {
                            if (!isGuildDistrict($r['district_id']))
                            {
                                echo "<a href='?action=attack&id={$r['district_id']}' class='btn btn-danger'>Attack Tile</a><br />
                                    <a href='?action=sabotage&id={$r['district_id']}' class='btn btn-secondary'>Sabotage Tile</a><br />";
                            }
                            if (isGuildDistrict($r['district_id']))
                            {
                                echo "<a href='?action=moveto&id={$r['district_id']}' class='btn btn-warning'>Move Troops</a><br />
        						<a href='?action=movebarracks&id={$r['district_id']}' class='btn btn-secondary'>Move from Barracks</a><br />
        						<a href='?action=fortify&id={$r['district_id']}' class='btn btn-success'>Fortify</a><br />";
                            }
                        }
                        else
                        {
                            echo "<a href='?action=view&id={$r['district_id']}' class='btn btn-primary'>View Info</a>";
                        }
                    }
            }
            "</td>";
    }
}

function parseNWtile(int $districtID, $extra = '')
{
    global $db;
    $id=$db->query("SELECT * FROM `guild_districts` WHERE `district_id` = {$districtID}");
    if ($db->num_rows($id) > 0)
    {
        $r=$db->fetch_row($id);
        $NW=$db->query("SELECT * FROM `guild_districts` WHERE `district_x` = ({$r['district_x']} - 1) AND `district_y` = ({$r['district_y']} - 1)");
        if ($db->num_rows($NW) > 0)
        {
            $r2=$db->fetch_row($NW);
            parseTile($r2['district_id'], $extra);
        }
    }
}

function parseNtile(int $districtID, $extra = '')
{
    global $db;
    $id=$db->query("SELECT * FROM `guild_districts` WHERE `district_id` = {$districtID}");
    if ($db->num_rows($id) > 0)
    {
        $r=$db->fetch_row($id);
        $NW=$db->query("SELECT * FROM `guild_districts` WHERE `district_x` = ({$r['district_x']}) AND `district_y` = ({$r['district_y']} - 1)");
        if ($db->num_rows($NW) > 0)
        {
            $r2=$db->fetch_row($NW);
            parseTile($r2['district_id'], $extra);
        }
    }
}

function parseNEtile(int $districtID, $extra = '')
{
    global $db;
    $id=$db->query("SELECT * FROM `guild_districts` WHERE `district_id` = {$districtID}");
    if ($db->num_rows($id) > 0)
    {
        $r=$db->fetch_row($id);
        $NW=$db->query("SELECT * FROM `guild_districts` WHERE `district_x` = ({$r['district_x']} + 1) AND `district_y` = ({$r['district_y']} - 1)");
        if ($db->num_rows($NW) > 0)
        {
            $r2=$db->fetch_row($NW);
            parseTile($r2['district_id'], $extra);
        }
    }
}
function parseWtile(int $districtID, $extra = '')
{
    global $db;
    $id=$db->query("SELECT * FROM `guild_districts` WHERE `district_id` = {$districtID}");
    if ($db->num_rows($id) > 0)
    {
        $r=$db->fetch_row($id);
        $NW=$db->query("SELECT * FROM `guild_districts` WHERE `district_x` = ({$r['district_x']} - 1) AND `district_y` = ({$r['district_y']})");
        if ($db->num_rows($NW) > 0)
        {
            $r2=$db->fetch_row($NW);
            parseTile($r2['district_id'], $extra);
        }
    }
}

function parseEtile(int $districtID, $extra = '')
{
    global $db;
    $id=$db->query("SELECT * FROM `guild_districts` WHERE `district_id` = {$districtID}");
    if ($db->num_rows($id) > 0)
    {
        $r=$db->fetch_row($id);
        $NW=$db->query("SELECT * FROM `guild_districts` WHERE `district_x` = ({$r['district_x']} + 1) AND `district_y` = ({$r['district_y']})");
        if ($db->num_rows($NW) > 0)
        {
            $r2=$db->fetch_row($NW);
            parseTile($r2['district_id'], $extra);
        }
    }
}

function parseSWtile(int $districtID, $extra = '')
{
    global $db;
    $id=$db->query("SELECT * FROM `guild_districts` WHERE `district_id` = {$districtID}");
    if ($db->num_rows($id) > 0)
    {
        $r=$db->fetch_row($id);
        $NW=$db->query("SELECT * FROM `guild_districts` WHERE `district_x` = ({$r['district_x']} - 1) AND `district_y` = ({$r['district_y']} + 1)");
        if ($db->num_rows($NW) > 0)
        {
            $r2=$db->fetch_row($NW);
            parseTile($r2['district_id'], $extra);
        }
    }
}

function parseStile(int $districtID, $extra = '')
{
    global $db;
    $id=$db->query("SELECT * FROM `guild_districts` WHERE `district_id` = {$districtID}");
    if ($db->num_rows($id) > 0)
    {
        $r=$db->fetch_row($id);
        $NW=$db->query("SELECT * FROM `guild_districts` WHERE `district_x` = ({$r['district_x']}) AND `district_y` = ({$r['district_y']} + 1)");
        if ($db->num_rows($NW) > 0)
        {
            $r2=$db->fetch_row($NW);
            parseTile($r2['district_id'], $extra);
        }
    }
}

function parseSEtile(int $districtID, $extra = '')
{
    global $db;
    $id=$db->query("SELECT * FROM `guild_districts` WHERE `district_id` = {$districtID}");
    if ($db->num_rows($id) > 0)
    {
        $r=$db->fetch_row($id);
        $NW=$db->query("SELECT * FROM `guild_districts` WHERE `district_x` = ({$r['district_x']} + 1) AND `district_y` = ({$r['district_y']} + 1)");
        if ($db->num_rows($NW) > 0)
        {
            $r2=$db->fetch_row($NW);
            parseTile($r2['district_id'], $extra);
        }
    }
}

function returnTileClass($district)
{
    global $db, $ir;
    $q=$db->query("SELECT * FROM `guild_districts` WHERE `district_id` = {$district}");
    $r=$db->fetch_row($q);
    
    if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
        $class="friendly";
    elseif ($r['district_owner'] == 0)
        $class="vacant";
    elseif (($r['district_owner'] != $ir['guild']) && ($ir['guild'] != 0))
        $class="enemy";
    
    if ($r['district_type'] == 'river')
        $class .= ' river';
    if ($r['district_type'] == 'elevated')
        $class .= " elevated";
    if ($r['district_type'] == 'lowered')
        $class .= " lowered";
    if ($r['district_type'] == 'market')
        $class .= " town";
    if ($r['district_type'] == 'outpost')
        $class .= " outpost";
    if ($r['district_fortify'] > 0)
        $class .= " fortify_{$r['district_fortify']}";
    return $class;
}