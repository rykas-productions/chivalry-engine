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
$districtConfig['WarriorMeleeDmg'] = 200;
$districtConfig['WarriorRngDmg'] = 0;
$districtConfig['WarriorMeleeProt'] = 125;
$districtConfig['WarriorRngProt'] = 0;
$districtConfig['ArcherCost'] = 8000;
$districtConfig['ArcherCostDaily'] = 1100;
$districtConfig['ArcherMeleeDmg'] = 0;
$districtConfig['ArcherRngDmg'] = 225;
$districtConfig['ArcherMeleeProt'] = 0;
$districtConfig['ArcherRngProt'] = 175;
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
 * Called daily to charge guilds their daily fees.
 * @internal
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
			$api->GuildAddNotification($r['guild_id'],"Your guild has been charged a district's upkeep fee of " . shortNumberParse($upkeepFee) . " Copper Coins.");
		}
	}
	districtRewards();
}
/**
 * Returns the active members of the guild.
 * @param int $guild_id Guild's ID.
 * @return int Players in guild active within last 24 hours
 */
function countActiveGuildMembers24Hr($guild_id)
{
	global $db;
	$last_on = time() - (1440*60);
	$q = $db->query("/*qc=on*/SELECT `username` FROM `users` WHERE `guild` = {$guild_id} AND `laston` > {$last_on}");
	return $db->num_rows($q);
}

/**
 * Returns the number of warriors deployed on the districts, owned by a specific guild.
 * @param int $guild_id Guild's ID.
 * @return int Warriors deployed
 */
function countDeployedWarriors($guild_id)
{
	global $db;
	$q=$db->query("SELECT SUM(`district_melee`) FROM `guild_districts` WHERE `district_owner` = {$guild_id}");
	return $db->fetch_single($q);
}

/**
 * Returns the number of archers deployed on the districts, owned by a specific guild.
 * @param int $guild_id Guild's ID.
 * @return int Archers deployed
 */
function countDeployedArchers($guild_id)
{
	global $db;
	$q=$db->query("SELECT SUM(`district_range`) FROM `guild_districts` WHERE `district_owner` = {$guild_id}");
	return $db->fetch_single($q);
}

/**
 * Returns the number of generals deployed on the districts, owned by a specific guild.
 * @param int $guild_id Guild's ID.
 * @return int Generals deployed
 */
function countDeployedGenerals($guild_id)
{
	global $db;
	$q=$db->query("SELECT SUM(`district_general`) FROM `guild_districts` WHERE `district_owner` = {$guild_id}");
	return $db->fetch_single($q);
}

/**
 * Returns the number of districts controlled by a guild.
 * @param int $guild_id Guild's ID.
 * @return int Districts owned,
 */
function countOwnedDistricts($guild_id)
{
    global $db;
    return $db->num_rows($db->query("SELECT `district_id` FROM `guild_districts` WHERE `district_owner` = {$guild_id}"));
}

/**
 * Helper function to call to reward daily district awards.
 * @internal
 */
function districtRewards()
{
	districtRewardMostControlledTiles();
	districtRewardMostDeployedUnits();
}

/**
 * Reward the guild with most units deployed on the districts board with 5 CID Admin Gym scrolls.
 * @internal
 */
function districtRewardMostDeployedUnits()
{
	global $db, $api;
	$winnerguild = 0;
	$currentmax = 0;
	$q = $db->query("SELECT `guild_id` FROM `guild` WHERE `guild_id` != 20 AND `guild_id` != 16");
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

/**
 * Reward the guild with most controlled tiles on the districts board with 5 CID Admin scrolls.
 * @internal
 */
function districtRewardMostControlledTiles()
{
	global $db, $api;
	$winnerguild = 0;
	$currentmax = 0;
	$q = $db->query("SELECT `guild_id` FROM `guild` WHERE `guild_id` != 20 AND `guild_id` != 16");
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

/**
 * Tests to see if the player can access the input tile ID. This usually means if the player's guild has 
 * a tile nearby, or if the tile itself is on the outer border.
 * @internal
 * @param int $district_id District ID to test
 * @return bool Can be accessed
 */
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

/**
 * Function for handling attacks in the districts.
 * @internal
 * @param int $attackWarrior Warriors on attack.
 * @param int $attackArcher Archers on attack.
 * @param int $attackCaptain Captains on attack.
 * @param int $defenseWarrior Warriors on defense.
 * @param int $defenseArcher Archers on defense.
 * @param int $defenseGeneral Generals on defense.
 * @param float $attackBuff Buff levels for the attackers, defaults to 1.0
 * @param float $defenseBuff Buff levels for the defenders, defaults to 1.0
 * @return string json containing attack result.
 */
function doAttack($attackWarrior, $attackArcher, $attackCaptain, $defenseWarrior, $defenseArcher, $defenseGeneral = 0, $attackBuff = 1.0, $defenseBuff = 1.0)
{
    global $districtConfig;
    
    $attackTotal = $attackWarrior + $attackArcher;
    $defenseTotal = $defenseWarrior + $defenseArcher;
    
    $luck = Random(-30,30) / 100;
    //$luck = 0;
    
    $attackBuff += $luck;
    $defenseBuff += $luck;
    
    //results
    $result = array();
    $result['winner'] = '';
    $result['attack_warrior_lost'] = 0;
    $result['attack_archer_lost'] = 0;
    $result['attack_captain_lost'] = 0;
    $result['defense_warrior_lost'] = 0;
    $result['defense_archer_lost'] = 0;
    $result['defense_general_lost'] = 0;
    
    //Starting values
    $attMeleeRating = ($attackWarrior * $districtConfig['WarriorMeleeDmg']) + ($attackArcher * $districtConfig['ArcherMeleeDmg']);
    $attRangeRating = ($attackWarrior * $districtConfig['WarriorRngDmg']) + ($attackArcher * $districtConfig['ArcherRngDmg']);
    $defMeleeRating = ($defenseWarrior * $districtConfig['WarriorMeleeProt']) + ($defenseArcher * $districtConfig['ArcherMeleeProt']);
    $defRangeRating = ($defenseWarrior * $districtConfig['WarriorRngProt']) + ($defenseArcher * $districtConfig['ArcherRngProt']);
    
    //Captain buffs to attack
    if ($attackCaptain > 0)
    {
        $attMeleeRating *= ($attackCaptain * $districtConfig['CaptainBuff']);
        $attRangeRating *= ($attackCaptain * $districtConfig['CaptainBuff']);
    }
    //General buffs to defense
    if ($defenseGeneral > 0)
    {
        $defMeleeRating *= ($defenseGeneral * $districtConfig['GeneralBuff']);
        $defRangeRating *= ($defenseGeneral * $districtConfig['GeneralBuff']);
    }
    
    //Add attack/defense buffs
    $attMeleeRating *= $attackBuff;
    $attRangeRating *= $attackBuff;
    $defMeleeRating *= $defenseBuff;
    $defRangeRating *= $defenseBuff;
    
    $attMeleeStart = $attMeleeRating;
    $attRangeStart = $attRangeRating;
    $defMeleeStart = $defMeleeRating;
    $defRangeStart = $defRangeRating;
    
    $attMeleePerRating = ($attMeleeRating > 0) ? $attMeleeRating / $attackWarrior : 0;
    $attRangePerRating = ($attRangeRating > 0) ? $attRangeRating / $attackArcher : 0;
    $defMeleePerRating = ($defMeleeRating > 0) ? $defMeleeRating / $defenseWarrior : 0;
    $defRangePerRating = ($defRangeRating > 0) ? $defRangeRating / $defenseArcher : 0;
    
    //Calculate losses
    $itt=0;
    while (empty($result['winner']))
    {
        $itt++;
        if ($attMeleeRating > $defMeleeRating)  //attacker melee stronger than defending melee
        {
            $attMeleeRating -= $defMeleeRating;
            $defMeleeRating = 0;
        }
        elseif ($attMeleeRating < $defMeleeRating)  //attacker melee weaker than defending melee
        {
            $defMeleeRating -= $attMeleeRating;
            $attMeleeRating = 0;
        }
        elseif (($defMeleeRating == $attMeleeRating) && ($defMeleeRating != 0)) //even melee rating on both sides
        {
            $defMeleeRating -= 0;
            $attMeleeRating = 0;
        }
        
        if ($attRangeRating > $defRangeRating)  //attacker ranged stronger than defender range
        {
            $attRangeRating -= $defRangeRating;
            $defRangeRating = 0;
        }
        elseif ($attRangeRating < $defRangeRating)  //attacker range weaker than defender range
        {
            $defRangeRating -= $attRangeRating;
            $attRangeRating = 0;
        }
        elseif (($defRangeRating == $attRangeRating) && ($defRangeRating != 0)) //even ranged on both sides
        {
            $defRangeRating = 0;
            $attRangeRating = 0;
        }
        
        if (($attMeleeRating <= 0) && ($attRangeRating > 0))  //attacker warriors are dead.
        {
            if (($attRangeRating * 0.75) > ($defMeleeRating))    //attack range is better than def melee
            {
                $attRangeRating -= $defMeleeRating;
                $defMeleeRating = 0;
            }
            elseif (($attRangeRating * 0.75) < ($defMeleeRating))    //attack range is better than def melee
            {
                $defMeleeRating -= $defMeleeRating * 0.75;
                $attRangeRating = 0;
            }
            elseif ((($attRangeRating * 0.75) == ($defMeleeRating)) && ($defMeleeRating != 0))  //attack range = defense melee
            {
                $defRangeRating = 0;
                $attRangeRating = 0;
            }
        }
        
        
        if (($attRangeRating <= 0) && ($attMeleeRating > 0))  //attacker warriors are dead.
        {
            if (($attMeleeRating) > ($defMeleeRating * 0.75))    //attack range is better than def melee
            {
                $attRangeRating -= $defMeleeRating;
                $defMeleeRating = 0;
            }
            elseif (($attMeleeRating) < ($defRangeRating * 0.75))    //attack range is better than def melee
            {
                $defMeleeRating -= $defMeleeRating * 0.75;
                $attRangeRating = 0;
            }
            elseif ((($attMeleeRating) == ($defRangeRating * 0.75)) && ($defRangeRating != 0))  //attack range = defense melee
            {
                $defRangeRating = 0;
                $attRangeRating = 0;
            }
        }
        $attTotalRating = $attMeleeRating + $attRangeRating;
        $defTotalRating = $defMeleeRating + $defRangeRating;
        
        if ($attTotalRating <= 0)
        {
            $result['winner'] = 'defense';
            $result['attack_captain_lost'] = $attackCaptain;
        }
        elseif ($defTotalRating <= 0)
        {
            $result['winner'] = 'attack';
            $result['defense_general_lost'] = $defenseGeneral;
        }
        if ($itt == 30)
        {
            $result['winner'] = 'draw';
        }
    }
    $result['attack_warrior_lost'] = ($attMeleeStart > 0) ? round(($attMeleeRating - $attMeleeStart) / $attMeleePerRating) * -1 : 0;
    $result['attack_archer_lost'] = ($attRangeStart > 0) ? round(($attRangeRating - $attRangeStart) / $attRangePerRating) * -1 : 0;
    $result['defense_warrior_lost'] = ($defMeleeStart > 0) ? round(($defMeleeRating - $defMeleeStart) / $defMeleePerRating) * -1 : 0;
    $result['defense_archer_lost'] = ($defRangeStart > 0) ? round(($defRangeRating - $defRangeStart) / $defRangePerRating) * -1 : 0;
    //We will continue to loop while a winner is still undecided.
    return json_encode($result);
}

/**
 * Function testing if a tile can accessed from a specific tile.
 * @param int $from_tile Tile ID to test from
 * @param int $to_tile Tile ID to test if can be accessed.
 * @return boolean Is Accessible from $from_tile
 */
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
/**
 * Helper function to quickly update a tile's ownership. Note that this destroy's the fortification level of the tile.
 * @param int $district_id ID of the Tile you wish to update.
 * @param int $new_owner Guild ID of the Tile's new owner.
 */
function setTileOwnership($district_id, $new_owner)
{
    global $db;
    $db->query("UPDATE `guild_districts` SET `district_owner` = {$new_owner}, `district_fortify` = 0 WHERE `district_id` = {$district_id}");
}

/**
 * Helper function to quickly update a tile's troops.
 * @param int $district_id Tile ID to edit troops upon.
 * @param int $warriorChange Warriors to be added or removed.
 * @param int $archerChange Archers to be added or removed.
 * @param int $generalChange Generals to be added or removed.
 */
function updateTileTroops($district_id, $warriorChange, $archerChange, $generalChange)
{
    global $db;
    $db->query("UPDATE `guild_districts`
				SET `district_melee` = `district_melee` + ({$warriorChange}),
				`district_range` = `district_range` + ({$archerChange}),
				`district_general` = `district_general` + ({$generalChange})
				WHERE `district_id` = {$district_id}");
}


/**
 * Helper function to update a guild's district's barracks.
 * @param int $guild_id Guild ID of barracks to update.
 * @param int $warriorChange Warriors to add or remove from barracks.
 * @param int $archerChange Archers to add or remove from barracks.
 * @param int $generalChange Generals to add or remove from barracks.
 * @param int $captainChange Captains to add or remove from barracks.
 */
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

/**
 * Blocks access to certain areas if the input Guild ID is zero.
 * Typically used to block access to things that require you to be in
 * a guild.
 * @param int $guild Guild ID
 * @return boolean
 */
function blockAccess($guild)
{
    if ($guild == 0)
        return true;
        else
            false;
}

/**
 * Parses the input distrct ID into a human readable string in a X,Y format.
 * @param int $district_id District ID.
 * @return string X:x;Y:y
 */
function resolveCoordinates($district_id)
{
    global $db;
    $q=$db->query("SELECT `district_x`, `district_y` FROM `guild_districts` WHERE `district_id` = {$district_id}");
    $r=$db->fetch_row($q);
    return "X: {$r['district_x']}; Y: {$r['district_y']}";
}
/**
 * Tests if the specified user is owner or co-owner of the specified guild
 * @param int $guild Guild ID
 * @param int $user User ID to test
 * @return boolean True if part of leadership, false if not.
 */
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
/**
 * Returns a friendly string specifying the fortification level of the specified district.
 * @param int $district_id District ID to return fortification level.
 * @return string Roman Numerals (Up to 10)
 */
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

/**
 * Function to count all tiles considered a town, owned by the current user's guild. At 
 * this time, only markets are consider town tiles.
 * @internal
 * @return int Town tiles owned.
 * @todo Expand this to be not reliant on the current user
 */
function countTowns()
{
    return countMarkets();
}

/**
 * Function to count all tiles considered a market, owned by the current user's guild.
 * @return int Markets owned
 * @internal
 * @todo Expand this to be not reliant on the current user
 */
function countMarkets()
{
    global $db, $ir;
    return $db->fetch_single($db->query("SELECT COUNT(`district_id`) FROM `guild_districts` WHERE `district_type` = 'market' AND `district_owner` = {$ir['guild']}"));
}

/**
 * Counts all tiles considered an outpost, owned by the current user's guild.
 * @internal
 * @return int Outposts owned.
 * @todo Expand this to be not reliant on the current user
 */
function countOutposts()
{
    global $db, $ir;
    return $db->fetch_single($db->query("SELECT COUNT(`district_id`) FROM `guild_districts` WHERE `district_type` = 'outpost' AND `district_owner` = {$ir['guild']}"));
}

/**
 * Counts all active generals, archers and warriors on district tiles, owned by the current user's guild.
 * @return int Total troops
 * @todo Expand this to be not reliant on the current user
 */
function countActiveTroops()
{
    global $db, $ir;
    $warriors = $db->fetch_single($db->query("SELECT SUM(`district_melee`) FROM `guild_districts` WHERE `district_owner` = {$ir['guild']}"));
    $archers = $db->fetch_single($db->query("SELECT SUM(`district_range`) FROM `guild_districts` WHERE `district_owner` = {$ir['guild']}"));
    $generals = $db->fetch_single($db->query("SELECT SUM(`district_general`) FROM `guild_districts` WHERE `district_owner` = {$ir['guild']}"));
    return $archers + $warriors + $generals;
}

/**
 * Counts the current guild's total generals.
 * @return int Total generals
 * @todo Rewrite to not rely on the current user's guild.
 */
function countGenerals()
{
    global $db, $ir;
    $deployed = $db->fetch_single($db->query("SELECT SUM(`district_general`) FROM `guild_districts` WHERE `district_owner` = {$ir['guild']}"));
    $barracks = $db->fetch_single($db->query("SELECT SUM(`barracks_generals`) FROM `guild_district_info` WHERE `guild_id` = {$ir['guild']}"));
    return $deployed + $barracks;
}

/**
 * Counts the current guild's total captains.
 * Captains can only be stored in the barracks.
 * @return int Captains owned.
 * @todo Rewrite to not rely on current user's guild.
 */
function countCaptains()
{
    global $db, $ir;
    $barracks = $db->fetch_single($db->query("SELECT SUM(`barracks_captains`) FROM `guild_district_info` WHERE `guild_id` = {$ir['guild']}"));
    return $barracks;
}

/**
 * Function to log guild district attacks into the database
 * @param int $attacker Guild ID of the attacking guild.
 * @param int $defender Guild ID of defending guild.
 * @param int $att_war Attacking guild's warriors.
 * @param int $att_range Attacking guild's archers
 * @param int $att_war_lost Attacking guild's fallen warriors.
 * @param int $att_range_lost Attacking guild's fallen archers.
 * @param int $def_war Defender guild's warriors.
 * @param int $def_range Defender guild's archers
 * @param int $def_war_lost Defender guild's warriors fallen
 * @param int $def_range_lost Defender guild's fallen archers
 * @param int $def_general Defender guild's generals
 * @param int $fortify Defending's tile's fortification level
 * @param int $winner Guild ID of winner of battle.
 * @param int $att_capt Captains deployed by ataccker guild.
 * @return int Battle Log ID.
 */
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

/**
 * Function to display tiles. We may pass extra data to display extra info.
 * @param int $districtID District
 * @param string $extra Data to display more links, can be empty.
 * @return object Creates the tile.
 */
function parseTile(int $districtID, $extra = '')
{
    global $db, $api;
    $id=$db->query("SELECT * FROM `guild_districts` WHERE `district_id` = {$districtID}");
    if ($db->num_rows($id) > 0)
    {
        $district_id = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs($_GET['id']) : '';
        $r=$db->fetch_row($id);
        $class = returnTileClass($r['district_id']);
            echo "
            <div class='card {$class} text-muted'>
                <div class='card-header'>
                    Y: {$r['district_y']}; X: {$r['district_x']}
                </div>
                <div class='card-body'>
                    <div class='row'>
                        <div class='col-12'>
                            <div class='row'>
                                <div class='col-12'>
                                    Guild
                                </div>
                                <div class='col-12'>
                                    <a href='guilds.php?action=view&id={$r['district_owner']}'>{$api->GuildFetchInfo($r['district_owner'],'guild_name')}</a>
                                </div>
                            </div>
                        </div>";
                        if (isDistrictAccessible($r['district_id']))
                        {
                            echo "<div class='col-12 col-xl-6'>
                                    <div class='row'>
                                        <div class='col-12'>
                                            Warriors
                                        </div>
                                        <div class='col-12'>
                                            " . shortNumberParse($r['district_melee']) . "
                                        </div>
                                    </div>
                                </div>
                                <div class='col-12 col-xl-6'>
                                    <div class='row'>
                                        <div class='col-12'>
                                            Archers
                                        </div>
                                        <div class='col-12'>
                                            " . shortNumberParse($r['district_range']) . "
                                        </div>
                                    </div>
                                </div>
                                <div class='col-12 col-xl-6'>
                                    <div class='row'>
                                        <div class='col-12'>
                                            Generals
                                        </div>
                                        <div class='col-12'>
                                            " . shortNumberParse($r['district_general']) . "
                                        </div>
                                    </div>
                                </div>
                                <div class='col-12 col-xl-6'>
                                    <div class='row'>
                                        <div class='col-12'>
                                            Fortification
                                        </div>
                                        <div class='col-12'>
                                            " . returnForticationLevel($r['district_id']) . "
                                        </div>
                                    </div>
                                </div>
                                <div class='col-12'>
                                    <div class='row'>";
                                if ($extra == "attack")
                                {
                                    if (isGuildDistrict($r['district_id']))
                                    {
                                        if (isAccessibleFromTile($r['district_id'], $district_id))
                                        {
                                            echo "<div class='col-12 col-xl'>
                                                    <a href='?action=attackform&from={$r['district_id']}&to={$district_id}' class='btn btn-danger'>Attack from Here</a>
                                                  </div>";
                                        }
                                    }
                                    if ($district_id == $r['district_id'])
                                    {
                                        echo "<div class='col-12 col-xl'>
                                                <a href='?action=attackformbarracks&to={$district_id}' class='btn btn-danger'>Barracks Attack</a>
                                              </div>";
                                    }
                                }
                                if ($extra == "info")
                                {
                                    if ($district_id == $r['district_id'])
                                    {
                                        if (!isGuildDistrict($r['district_id']))
                                        {
                                            echo "<div class='col-12 col-xl'>
                                                    <a href='?action=attack&id={$r['district_id']}' class='btn btn-danger btn-block'>Attack</a>
                                                  </div>
                                                  <div class='col-12 col-xl'>
                                                    <a href='?action=sabotage&id={$r['district_id']}' class='btn btn-secondary btn-block'>Sabotage</a>
                                                  </div>";
                                        }
                                        if (isGuildDistrict($r['district_id']))
                                        {
                                            echo "  <div class='col-12 col-xl-6'>
                                                        <a href='?action=moveto&id={$r['district_id']}' class='btn btn-warning btn-block'>Move Troops</a>
                                                    </div>
                                                    <div class='col-12 col-xl col-xl-6'>
        						                        <a href='?action=movebarracks&id={$r['district_id']}' class='btn btn-secondary btn-block'>Barracks Move</a>
                                                    </div>
                                                    <div class='col-12 col-xl col-xl-6'>
        						                          <a href='?action=fortify&id={$r['district_id']}' class='btn btn-success btn-block'>Fortify</a>
                                                    </div>";
                                        }
                                    }
                                    else
                                    {
                                        echo "<div class='col-12 col-xl'>
                                                <a href='?action=view&id={$r['district_id']}' class='btn btn-primary btn-block'>View Info</a>
                                              </div>";
                                    }
                                }
                                if ($extra == "moveto")
                                {
                                    if (isGuildDistrict($r['district_id']) && ($district_id != $r['district_id']))
                                    {
                                        if (isAccessibleFromTile($r['district_id'], $district_id))
                                        {
                                            echo "<div class='col-12 col-xl'>
                                                    <a href='?action=moveform&from={$r['district_id']}&to={$district_id}' class='btn btn-warning btn-block'>Move to Here</a>
                                                </div>";
                                        }
                                    }
                                    if ($district_id == $r['district_id'])
                                    {
                                        echo "<div class='col-12'>";
                                        alert('info',"","Moving troops from this tile...",false);
                                        echo "</div>";
                                    }
                                }
                                echo "</div></div>";
                        }
                        echo "
                            </div>
                        </div>
                    </div>";
    }
}

/**
 * Parses the tile to the North West of the current selected tile.
 * @param int $districtID Tile ID being looked at currently.
 * @param string $extra
 */
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

/**
 * Parses the tile to the North of the current selected tile.
 * @param int $districtID Tile ID being looked at currently.
 * @param string $extra
 */
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

/**
 * Parses the tile to the North East of the current selected tile.
 * @param int $districtID Tile ID being looked at currently.
 * @param string $extra
 */
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

/**
 * Parses the tile to the West of the current selected tile.
 * @param int $districtID Tile ID being looked at currently.
 * @param string $extra
 */
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

/**
 * Parses the tile to the East of the current selected tile.
 * @param int $districtID Tile ID being looked at currently.
 * @param string $extra
 */
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

/**
 * Parses the tile to the South West of the current selected tile.
 * @param int $districtID Tile ID being looked at currently.
 * @param string $extra
 */
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

/**
 * Parses the tile to the South of the current selected tile.
 * @param int $districtID Tile ID being looked at currently.
 * @param string $extra
 */
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

/**
 * Parses the tile to the South East of the current selected tile.
 * @param int $districtID Tile ID being looked at currently.
 * @param string $extra
 */
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

/**
 * Helper function to help the district tiles look significantly better.
 */
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
    else
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