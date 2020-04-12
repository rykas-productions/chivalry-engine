<?php
function calculateLuck($user)
{
	global $db;
	$r=$db->fetch_single($db->query("/*qc=on*/SELECT `luck` FROM `userstats` WHERE `userid` = {$user}"));
	$lucked=$r*-1;
	$adjustedluck=100+(100+$lucked);
	$luckrng=Random(0,$adjustedluck);
	if ($luckrng == 1)
	{
		$db->query("UPDATE `userstats` SET `luck` = `luck` - 5 WHERE `userid` = {$user}");
		return true;
	}
}
function getSkillLevel($user,$id)
{
	global $db;
	$q = $db->query("/*qc=on*/SELECT `skill_lvl` 
					FROM `user_skills` 
					WHERE `userid` = {$user} 
					AND `skill_id` = {$id}");
	if ($db->num_rows($q) == 0)
		return 0;
	else
		return $db->fetch_single($q);
}
/**
 * Tests to see if the user's permission is allowed or not.
 *
 * @param string $perm The permission to test for
 * @param int $user The user to test on
 *
 * @return bool                Returns true if the user has this,
 *                            false if not.
 */
function permission($perm, $user)
{
    global $db;
    $Query = $db->query("/*qc=on*/SELECT `perm_disable` FROM `permissions` WHERE `perm_name` = '{$perm}' AND `perm_user` = {$user}");
    if ($db->num_rows($Query) == 0)
        return true;
    $q = $db->fetch_single($Query);
    if ($q == 'false')
        //User does have this permission
        return true;
}
/**
 * Internal function: used to see if a user is due to level up, and if so, perform that levelup.
 */
function check_level()
{
    global $ir, $userid, $db;
	$ir['xp_needed'] = (round(($ir['level'] + 1) * ($ir['level'] + 1) * ($ir['level'] + 1) * 2.2)/$ir['reset']);
	if ($ir['xp'] >= $ir['xp_needed']) {
		$expu = $ir['xp'] - $ir['xp_needed'];
		$ir['level'] += 1;
		$ir['xp'] = $expu;
		$ir['energy'] += 2;
		$ir['brave'] += 2;
		$ir['maxenergy'] += 2;
		$ir['maxbrave'] += 2;
		$ir['hp'] += 50;
		$ir['maxhp'] += 50;
		$ir['xp_needed'] = (round(($ir['level'] + 1) * ($ir['level'] + 1) * ($ir['level'] + 1) * 2.2)/$ir['reset']);
		//Increase user's everything.
		$db->query("UPDATE `users` SET `level` = `level` + 1, `xp` = '{$expu}', `energy` = `energy` + 2,
					`brave` = `brave` + 2, `maxenergy` = `maxenergy` + 2, `maxbrave` = `maxbrave` + 2,
					`hp` = `hp` + 50, `maxhp` = `maxhp` + 50 WHERE `userid` = {$userid}");
		//Give the user some stats for leveling up.
		$StatGain = round(($ir['level'] * Random(150,275)) / Random(2, 6));
		$StatGain = $StatGain+($StatGain*levelMultiplier($ir['level']));
		$StatGainFormat = number_format($StatGain);
		//Assign the stat gain to the user's class of choice.
		if ($ir['class'] == 'Warrior') {
			$Stat = 'strength';
		} elseif ($ir['class'] == 'Rogue') {
			$Stat = 'agility';
		} else {
			$Stat = 'guard';
		}
		//Credit the stat gain.
		$db->query("UPDATE `userstats` SET `{$Stat}` = `{$Stat}` + {$StatGain} WHERE `userid` = {$userid}");
		//Tell the user they've gained some stats.
		notification_add($userid, "You have successfully leveled up and gained {$StatGainFormat} in {$Stat}.", "game-icon game-icon-corporal");
		//Log the level up, along with the stats gained.
		SystemLogsAdd($userid, 'level', "Leveled up to level {$ir['level']} and gained {$StatGainFormat} in {$Stat}.");
	}
}

/*
	The function for testing if a player is in the hospital.
	@param int $user The user who to test for.
*/

function user_infirmary($user)
{
    global $db;
    //Assign current Unix Time to a variable.
    $CurrentTime = time();
    //Select user from infirmary if their exit infirmary time is after the current Unix Timestamp.
    $query = $db->query("/*qc=on*/SELECT `infirmary_user` FROM `infirmary` WHERE `infirmary_user` = {$user} AND
                        `infirmary_out` > {$CurrentTime}");
    //Return false if they return no rows, true if they do.
    $return = ($db->num_rows($query) == 0) ? false : true;
    return $return;
}

/*
	The function for testing if a player is in the dungeon.
	@param int $user The user who to test for.
*/
function user_dungeon($user)
{
    global $db;
    //Assign current Unix Time to a variable.
    $CurrentTime = time();
    //Select user from dungeon if their exit dungeon time is after the current Unix Timestamp.
    $query = $db->query("/*qc=on*/SELECT `dungeon_user` FROM `dungeon` WHERE `dungeon_user` = {$user} AND
						`dungeon_out` > {$CurrentTime}");
    //Return false if they return no rows, true if they do.
    $return = ($db->num_rows($query) == 0) ? false : true;
    return $return;
}

/*
	The function for putting/adding onto someones infirmary time.
	@param int $user The user to put in the infirmary
	@param int $time The time (in minutes) to add.
	@param text $reason The reason the user is in the infirmary.
*/
function put_infirmary($user, $time, $reason)
{
    global $db;
    //Assign current Unix Timestamp to a variable.
    $CurrentTime = time();
    //Select the $user's current infirmary out time.
    $Infirmary = $db->fetch_single($db->query("/*qc=on*/SELECT `infirmary_out` FROM `infirmary` WHERE `infirmary_user` = {$user}"));
    //Since the time is in minutes, lets multiply the $time by 60. (Otherwise we would be adding seconds)
    $TimeMath = ($time * 60)+Random(-59,59);
    //Time Reduction Skill
    $specialnumber=((getSkillLevel($user,22)*5)/100);
    $TimeMath = $TimeMath-($TimeMath*$specialnumber);
    //If $user is currently not in the infirmary, lets add the time! (Their out time is the Unix Timestamp plus $TimeMath)
    if ($Infirmary <= $CurrentTime) {
        $db->query("UPDATE `infirmary` SET `infirmary_out` = {$CurrentTime} + {$TimeMath}, `infirmary_in` = {$CurrentTime},
					`infirmary_reason` = '{$reason}' WHERE `infirmary_user` = {$user}");
    } //If $user is already in the infirmary, lets just add $TimeMath onto their current sentence.
    else {
        $db->query("UPDATE `infirmary` SET `infirmary_out` = `infirmary_out` + {$TimeMath}, `infirmary_reason` = '{$reason}'
					WHERE `infirmary_user` = {$user}");
    }
}

/*
	The function for removing someones infirmary time.
	@param int $user The user to put in the infirmary
	@param int $time The time (in minutes) to remove.
*/
function remove_infirmary($user, $time)
{
    global $db;
    //Multiply $time by 60 since we're dealing with minutes, not seconds.
    $TimeMath = $time * 60;
    //Remove $TimeMath from their stay. $user will be removed from infirmary automatically, if needed.
    $db->query("UPDATE `infirmary` SET `infirmary_out` = `infirmary_out` - '{$TimeMath}' WHERE `infirmary_user` = {$user}");
}

/*
	The function for putting/adding onto someones dungeon time.
	@param int $user The user to put in the dungeon
	@param int $time The time (in minutes) to add.
	@param text $reason The reason the user is in the dungeon.
*/
function put_dungeon($user, $time, $reason)
{
    global $db;
    //Assign current Unix Timestamp to a variable.
    $CurrentTime = time();
    //Select $user's dungeon exit time.
    $Dungeon = $db->fetch_single($db->query("/*qc=on*/SELECT `dungeon_out` FROM `dungeon` WHERE `dungeon_user` = {$user}"));
    //Since we're dealing with minutes, lets multiply $time by 60.
    $TimeMath = ($time * 60)+Random(-59,59);
    //Time Reduction Skill
    $specialnumber=((getSkillLevel($user,22)*5)/100);
    $TimeMath = $TimeMath-($TimeMath*$specialnumber);
    //If $user is not in the dungeon already, lets set their exit time to $CurrentTime + $TimeMath
    if ($Dungeon <= $CurrentTime) {
        $db->query("UPDATE `dungeon` SET `dungeon_out` = {$CurrentTime} + {$TimeMath}, `dungeon_in` = {$CurrentTime},
					`dungeon_reason` = '{$reason}' WHERE `dungeon_user` = {$user}");
    } //$user is already in the dungeon, so lets just add $TimeMath to their sentence.
    else {
        $db->query("UPDATE `dungeon` SET `dungeon_out` = `dungeon_out` + {$TimeMath}, `dungeon_reason` = '{$reason}'
					WHERE `dungeon_user` = {$user}");
    }
}

/*
	The function for removing someones infirmary time.
	@param int $user The user to put in the infirmary
	@param int $time The time (in minutes) to remove.
*/
function remove_dungeon($user, $time)
{
    global $db;
    //Multiply $time by 60 since we're dealing with minutes, not seconds.
    $TimeMath = $time * 60;
    //Remove $TimeMath from $user's dungeon sentence. $user will be automatically removed from the dungeon if needed.
    $db->query("UPDATE `dungeon` SET `dungeon_out` = `dungeon_out` - '{$TimeMath}' WHERE `dungeon_user` = {$user}");
}
/**
 * Get the "rank" a user has for a particular stat - if the return is n, then the user has the n'th highest value for that stat.
 * @param int $stat The value of the current user's stat.
 * @param string $mykey The stat to be ranked in. Must be a valid column name in the userstats table
 * @return integer The user's rank in the stat
 */
function get_rank($stat, $mykey)
{
    global $db, $userid;
    //Select count of users who have higher $mykey based upon $stat. Excluding the current user, admins and NPCs
    if ($mykey != 'all') {
        $q = $db->query("/*qc=on*/SELECT count(`u`.`userid`) FROM `userstats` AS `us` LEFT JOIN `users` AS `u`
                    ON `us`.`userid` = `u`.`userid` WHERE {$mykey} > {$stat} AND `us`.`userid` != {$userid}
                    AND `u`.`user_level` != 'Admin' AND `u`.`user_level` != 'NPC'");
    } else {
        $q = $db->query("/*qc=on*/SELECT count(`u`.`userid`) FROM `userstats` AS `us` LEFT JOIN `users` AS `u`
                    ON `us`.`userid` = `u`.`userid` WHERE `strength`+`agility`+`guard`+`labor`+`iq` > {$stat} AND `us`.`userid` != {$userid}
                    AND `u`.`user_level` != 'Admin' AND `u`.`user_level` != 'NPC'");
    }
    $result = $db->fetch_single($q) + 1;
    $db->free_result($q);
    //Return the count from earlier.
    return $result;
}
/**
 * Give a particular user a particular quantity of some item.
 * @param int $user The user ID who is to be given the item
 * @param int $itemid The item ID which is to be given
 * @param int $qty The item quantity to be given
 * @param int $notid [optional] If specified and greater than zero, prevents the item given database entry combining with inventory id $notid.
 */
function item_add($user, $itemid, $qty, $notid = 0)
{
    global $db;
    //Select $itemid's item name.
    $ie = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`itmname`) FROM `items` WHERE `itmid` = {$itemid}"));
    //If the name returns, continue
    if ($ie > 0) {
        //We want $itemid to go into its own stack. Select the inventory ID to make sure this doesn't happen.
        if ($notid > 0) {
            $q = $db->query("/*qc=on*/SELECT `inv_id` FROM `inventory` WHERE `inv_userid` = {$user} AND `inv_itemid` = {$itemid}
							 AND `inv_id` != {$notid} LIMIT 1");
        } //We don't care if the $itemid merges into an existing inventory stack. Let's select the first stack then.
        else {
            $q = $db->query("/*qc=on*/SELECT `inv_id` FROM `inventory` WHERE `inv_userid` = {$user} AND `inv_itemid` = {$itemid}
							 LIMIT 1");
        }
        //If the inventory stack exists, add $qty to it and return true to signify we succeeded at adding the item.
        if ($db->num_rows($q) > 0) {
            $r = $db->fetch_row($q);
            $db->query("UPDATE `inventory` SET `inv_qty` = `inv_qty` + {$qty} WHERE `inv_id` = {$r['inv_id']}");
            return true;
        }
        //The inventory does not exist and/or we don't want $itemid to merge into an inventory stack, so lets create
        //a new one and return true.
        else {
            $db->query("INSERT INTO `inventory` (`inv_itemid`, `inv_userid`, `inv_qty`) VALUES ({$itemid}, {$user}, {$qty})");
            return true;
        }
    }
}

/**
 * Take away from a particular user a particular quantity of some item.<br />
 * If they don't have enough of that item to be taken, takes away any that they do have.
 * @param int $user The user ID who is to lose the item
 * @param int $itemid The item ID which is to be taken
 * @param int $qty The item quantity to be taken
 */
function item_remove($user, $itemid, $qty)
{
    global $db;
    //Select $itemid's item name.
    $ie = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`itmname`) FROM `items` WHERE `itmid` = {$itemid}"));
    //If $itemid actually exists, it'll return a name, so lets continue if that's the case.
    if ($ie > 0) {
        //Select the inventory ID number where $itemid's is stored for $user.
        $q = $db->query("/*qc=on*/SELECT `inv_id`, `inv_qty` FROM `inventory` WHERE `inv_userid` = {$user}
						 AND `inv_itemid` = {$itemid} LIMIT 1");
        //User has an inventory id for $itemid!
        if ($db->num_rows($q) > 0) {
            $r = $db->fetch_row($q);
            //$user's $itemid quantity is greater than $qty, so remove only $qty and return true.
            if ($r['inv_qty'] > $qty) {
                $db->query("UPDATE `inventory` SET `inv_qty` = `inv_qty` - {$qty} WHERE `inv_id` = {$r['inv_id']}");
                return true;
            } //$user's $itemid quantity is lower than $qty, so delete the inventory ID entirely and return true.
            else {
                $db->query("DELETE FROM `inventory` WHERE `inv_id` = {$r['inv_id']}");
                return true;
            }
        }
    }
    $db->free_result($q);
}

function parseUsername($id)
{
    global $db;
    $q = $db->query("/*qc=on*/SELECT 	`username`, `vip_days`, `vipcolor`, 
										`equip_badge`, `fedjail`,`user_level`
										FROM `users` 
										WHERE `userid` = {$id}");
    $r = $db->fetch_row($q);
		
	if ($r['fedjail'] > 0)
	{
		$username = "<span class='text-muted'><s>{$r['username']}</s></span>";
	}
	
	elseif ($r['user_level'] == 'NPC')
	{
		$username = "<span class='font-weight-light'>{$r['username']}</span>";
	}
	elseif ($r['vip_days'] > 0)
	{
		if ($r['equip_badge'] == 0)
			$r['equip_badge'] = 159;
		$username = "<span class='{$r['vipcolor']}' data-toggle='tooltip' data-placement='bottom' title='" . number_format($r['vip_days']) . " VIP Days remaining.'>{$r['username']} " . returnIcon($r['equip_badge']) . "</span>";
	}
	else
	{
		$username = $r['username'];
	}
    return $username;
}

function parseDisplayPic($id)
{
    global $db;
    $q = $db->query("/*qc=on*/SELECT `display_pic` FROM `users` WHERE `userid` = {$id}");
    $r = $db->fetch_single($q);
    $pic = (empty($r)) ? "" : "" . parseImage($r) . "";
    return $pic;
}

function levelMultiplier($level)
{
	if ($level < 100)
		return 1;
	elseif (($level >= 100) && ($level < 150))
		return 1.5;
	elseif (($level >= 150) && ($level < 250))
		return 1.75;
	elseif (($level >= 250) && ($level < 400))
		return 2.25;
    elseif (($level >= 400) && ($level < 500))
		return 2.75;
    elseif (($level >= 500) && ($level < 600))
		return 3;
    elseif (($level >= 600) && ($level < 700))
		return 3.5;
    elseif (($level >= 700) && ($level < 800))
		return 4.25;
	elseif (($level >= 800) && ($level < 900))
		return 4.5;
	elseif (($level >= 900) && ($level < 1000))
		return 4.75;
	else
		return 5;
}

function returnMaxInterest($user)
{
	global $db;
	$level=$db->fetch_single($db->query("SELECT `level` FROM `users` WHERE `userid` = {$user}"));
	return round(20000000*levelMultiplier($level));
}