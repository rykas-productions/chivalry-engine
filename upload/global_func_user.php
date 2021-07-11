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
function check_level($disableLevelUp = false)
{
    global $ir, $userid, $db;
	$ir['xp_needed'] = calculateXPNeeded($userid);
	if (hasPendantEquipped($ir['userid'],93))
	{
		$ir['xp_needed']=$ir['xp_needed']-($ir['xp_needed']*0.1);
	}
	if ($disableLevelUp == false)
	{
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
			$ir['xp_needed'] = (round(($ir['level'] + 1) * ($ir['level'] + 1) * ($ir['level'] + 1) * 2.2) * (1 - ($r['reset'] * 0.1)));
			//Increase user's everything.
			$db->query("UPDATE `users` SET `xp` = '{$expu}' WHERE `userid` = {$userid}");
			doLevelUpBonus($userid, $ir['reset']);
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
		$username = "<span class='font-weight-bold' style='color:{$r['vipcolor']}' data-toggle='tooltip' data-placement='top' title='" . number_format($r['vip_days']) . " VIP Days remaining.'>{$r['username']} " . returnIcon($r['equip_badge']) . "</span>";
	}
	else
	{
		$username = $r['username'];
	}
	//Now for dungeon and infirmary icons
	if (user_dungeon($id))
		$username .= " <i class='fas fa-unlock-alt text-danger' data-toggle='tooltip' data-placement='top' title='{$r['username']} is currently in the dungeon.'></i>";
	if (user_infirmary($id))
		$username .= " <i class='fas fa-hospital text-danger' data-toggle='tooltip' data-placement='top' title='{$r['username']} is currently in the infirmary.'></i>";
    return $username;
}

function parseDisplayPic($id)
{
    global $db;
    $q = $db->query("/*qc=on*/SELECT `display_pic` FROM `users` WHERE `userid` = {$id}");
    $r = $db->fetch_single($q);
    $pic = (empty($r)) ? parseImage(getGravatarPic($id)) : parseImage($r);
    return $pic;
}

function getGravatarPic($id)
{
	global $db;
	$r=$db->fetch_single($db->query("SELECT `email` FROM `users` WHERE `userid` = {$id}"));
	$link = "https://www.gravatar.com/avatar/" . md5(strtolower(trim($r))) . "?s=250.jpg";
	return $link;
}

function levelMultiplier($level)
{
	if ($level < 100)
		return 1;
	elseif (($level >= 100) && ($level < 150))
		return 1.5;
	elseif (($level >= 150) && ($level < 250))
		return 1.75;
	elseif (($level >= 250) && ($level < 325))
		return 2.25;
	elseif (($level >= 325) && ($level < 400))
		return 2.5;
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

function getCurrentUserPref($prefName, $defaultValue)
{
	global $userid, $db;
	$q=$db->query("SELECT `value` FROM `user_pref` WHERE `preference` = '{$prefName}' AND `userid` = {$userid}");
	if ($db->num_rows($q) == 0)
	{
		$db->query("INSERT INTO `user_pref` (`userid`, `preference`, `value`) VALUES ('{$userid}', '{$prefName}', '{$defaultValue}')");
		return $defaultValue;
	}
	else
	{
		return $db->fetch_single($q);
	}
}

function setCurrentUserPref($prefName, $value)
{
	global $userid, $db;
	getCurrentUserPref($prefName, $value);
	$db->query("UPDATE `user_pref` SET `value` = '{$value}' WHERE `userid` = {$userid} AND `preference` = '{$prefName}'");
}

//Returns remaining XP to have inserted into the user's xp.
function autoDonateXP($user, $xp, $guild)
{
	global $db, $api;
	$xpformula = calculateXPNeeded($user)/65;
	if ($xpformula < 1000)
		$xpformula = 1000;
	$xpformula=round($xpformula);
	if ($guild > 0)
	{
		$xpAutoDonate=getCurrentUserPref('autoDonateXP', 0);
		if ($xpAutoDonate > 0)
		{
			$toDonate = $xp * ($xpAutoDonate / 100);
			$points=floor($toDonate/$xpformula);
			$xprequired=$points*$xpformula;
			if ($toDonate < $xprequired)
			{
				return $xp;
			}
			else
			{
				updateDonations($guild,$user,'xp',$xprequired);
				updateDonations($guild,$user,'guild_xp',$points);
				$db->query("UPDATE `guild` SET `guild_xp` = `guild_xp` + {$points} WHERE `guild_id` = {$guild}");
				$event = "<a href='profile.php?user={$user}'>{$api->SystemUserIDtoName($user)}</a> exchanged " . number_format($xprequired) . " experience for " . number_format($points) . " guild experience.";
				$api->GuildAddNotification($guild, $event);
				$api->SystemLogsAdd($user, 'xp_gain', "-" . number_format($xprequired) . "XP");
				return $xp - $toDonate;
			}
		}
		else
		{
			return $xp;
		}
	}
	else
	{
		return $xp;
	}
}

function calculateXPNeeded($user)
{
	global $db;
	$q=$db->query("
		SELECT `level`, `reset`
		FROM `users` `u`
		LEFT JOIN `user_settings` AS `us`
		ON `u`.`userid` = `us`.`userid`
		WHERE `u`.`userid` = {$user}");
	//$q=$db->query("SELECT `u`.`level`,`us`.`reset` FROM `users` AS `u` INNER JOIN `user_settings` AS `us` ON `u`.`userid` = `us`.`userid` WHERE `us`.`userid` = {$user}");
	$r=$db->fetch_row($q);
	if (!isset($r['reset']))
		$r['reset'] = 0;
	return round(($r['level'] + 1) * ($r['level'] + 1) * ($r['level'] + 1) * 2.2) * (1 - ($r['reset'] * 0.1));
}
function isCourseComplete($userid, $course)
{
	global $db;
	$q=$db->query("SELECT `userid` FROM `academy_done` WHERE `userid` = {$userid} AND `course` = {$course}");
	if ($db->num_rows($q) > 0)
		return true;
	else
		return false;
}

function activityLevel($user)
{
	global $db;
	$r=$db->fetch_row($db->query("SELECT `laston` FROM `users` WHERE `userid` = {$user}"));
	if ($r['laston'] > time() - 300)
		return 'active';
	elseif (($r['laston'] < time() - 300) && ($r['laston'] > time() - 900))
		return 'idle';
	else
		return 'inactive';
}

function parseActivity($user)
{
	$act = activityLevel($user);
	if ($act == 'active')
	{
		$activeText = "Online";
		$activeColor = "text-success";
	}
	elseif ($act == 'idle')
	{
		$activeText = "Idle";
		$activeColor = "text-warning";
	}
	elseif ($act == 'inactive')
	{
		$activeText = "Offline";
		$activeColor = "text-danger";
	}
	return "<span class='{$activeColor}'>{$activeText}</span>";
}

function updateMostUsersCount()
{
	global $db, $set;
	$currentTime=time();
	$cutOff=$currentTime-900;	//15 Minutes
	$countUsers=$db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `users` WHERE `laston` > {$cutOff}"));
	if ($set['mostUsersOn'] <= $countUsers)
	{
		$db->query("UPDATE `settings` SET `setting_value` = {$currentTime} WHERE `setting_name` = 'mostUsersOnTime'");
		$db->query("UPDATE `settings` SET `setting_value` = {$countUsers} WHERE `setting_name` = 'mostUsersOn'");
	}
}

function userGiveEffect($user, $effect, $time = 30, $multiplier = 1)
{
	//just in case
	//CREATE TABLE `users_effects` ( `userid` INT(11) UNSIGNED NOT NULL , `effectName` VARCHAR(255) NOT NULL , `effectMulti` VARCHAR(255) NOT NULL , `effectTimeOut` INT(11) UNSIGNED NOT NULL ) ENGINE = InnoDB;
	global $db;
	$q = $db->query("SELECT * FROM `users_effects` WHERE `userid` = {$user} AND `effectName` = '{$effect}'");
	if ($db->num_rows($q) == 0)
	{
		$timeout = time() + $time;
		$db->query("INSERT INTO `users_effects` 
				(`userid`, `effectName`, 
				`effectMulti`, `effectTimeOut`) 
				VALUES ('{$user}', '{$effect}', 
				 '{$multiplier}', '{$timeout}')");
	}
	else
	{
		userUpdateEffect($user, $effect, $time, $multiplier);
	}
}

function userRemoveEffect($user, $effect)
{
	global $db;
	if (userHasEffect($user, $effect))
		$db->query("DELETE FROM `users_effects` WHERE `userid` = {$user} AND `effectName` = '{$effect}'");
}

function userUpdateEffect($user, $effect, $time = 30, $multiplier = 0)
{
	global $db;
	if (userHasEffect($user, $effect))
	{
		$r=$db->fetch_row($db->query("SELECT * FROM `users_effects` WHERE `userid` = {$user} AND `effectName` = '{$effect}'"));
		$multi = ($multiplier == 0) ? $r['effectMulti'] : $multiplier;
		$newtime = $r['effectTimeOut'] + ($time);
		$db->query("UPDATE `users_effects` 
					SET `effectTimeOut` = {$newtime}, 
					`effectMulti` = {$multi} 
					WHERE `userid` = {$user}
					AND `effectName` = '{$effect}'");
	}
}

function userHasEffect($user, $effect)
{
	global $db;
	$q = $db->query("SELECT * FROM `users_effects` WHERE `userid` = {$user} AND `effectName` = '{$effect}'");
	if ($db->num_rows($q) == 0)
		return false;
	else
		return true;
}

function returnEffectDone($user, $effect)
{
	global $db;
	return $db->fetch_single($db->query("SELECT `effectTimeOut` FROM `users_effects` WHERE `userid` = {$user} AND `effectName` = '{$effect}'"));
}

function returnEffectMultiplier($user, $effect)
{
	global $db;
	return $db->fetch_single($db->query("SELECT `effectMulti` FROM `users_effects` WHERE `userid` = {$user} AND `effectName` = '{$effect}'"));
}

function deleteUser($user)
{
	global $db;
	$db->query("DELETE FROM `2fa_table` WHERE `userid` = {$user}");
	$db->query("DELETE FROM `2018_christmas_tree` WHERE `userid_from` = {$user}");
	$db->query("DELETE FROM `2018_christmas_tree` WHERE `userid_to` = {$user}");
	$db->query("DELETE FROM `2018_christmas_wishes` WHERE `userid` = {$user}");
	$db->query("DELETE FROM `2018_halloween_chuck` WHERE `userid` = {$user}");
	$db->query("DELETE FROM `2018_halloween_tot` WHERE `userid` = {$user}");
	$db->query("DELETE FROM `2018_halloween_tot` WHERE `visited` = {$user}");
	$db->query("DELETE FROM `2019_bigbang` WHERE `userid` = {$user}");
	$db->query("DELETE FROM `academy_done` WHERE `userid` = {$user}");
	$db->query("DELETE FROM `achievements_done` WHERE `userid` = {$user}");
	$db->query("DELETE FROM `activeBosses` WHERE `boss_user` = {$user}");
	$db->query("DELETE FROM `advent_calender` WHERE `userid` = {$user}");
	$db->query("DELETE FROM `achievements_done` WHERE `userid` = {$user}");
	$db->query("DELETE FROM `artifacts` WHERE `userid` = {$user}");
	$db->query("DELETE FROM `attack_logs` WHERE `attacker` = {$user}");
	$db->query("DELETE FROM `attack_logs` WHERE `attacked` = {$user}");
	$db->query("DELETE FROM `auto_login` WHERE `userid` = {$user}");
	$db->query("DELETE FROM `bank_investments` WHERE `userid` = {$user}");
	$db->query("DELETE FROM `blocklist` WHERE `blocked` = {$user}");
	$db->query("DELETE FROM `blocklist` WHERE `blocker` = {$user}");
	$db->query("DELETE FROM `botlist` WHERE `botuser` = {$user}");
	$db->query("DELETE FROM `botlist_hits` WHERE `userid` = {$user}");
	$db->query("DELETE FROM `botlist_hits` WHERE `botid` = {$user}");
	$db->query("DELETE FROM `bounty_hunter` WHERE `bh_creator` = {$user}");
	$db->query("DELETE FROM `bounty_hunter` WHERE `bh_user` = {$user}");
	$db->query("DELETE FROM `chat` WHERE `chat_user` = {$user}");
	$db->query("DELETE FROM `comments` WHERE `cRECEIVE` = {$user}");
	$db->query("DELETE FROM `comments` WHERE `cSEND` = {$user}");
	$db->query("DELETE FROM `contact_list` WHERE `c_ADDED` = {$user}");
	$db->query("DELETE FROM `contact_list` WHERE `c_ADDER` = {$user}");
	$db->query("DELETE FROM `crime_logs` WHERE `userid` = {$user}");
	$db->query("DELETE FROM `dungeon` WHERE `dungeon_user` = {$user}");
	$db->query("DELETE FROM `enemy` WHERE `enemy_user` = {$user}");
	$db->query("DELETE FROM `enemy` WHERE `enemy_adder` = {$user}");
	$db->query("DELETE FROM `equip_gains` WHERE `userid` = {$user}");
	$db->query("DELETE FROM `farm_data` WHERE `farm_owner` = {$user}");
	$db->query("DELETE FROM `farm_users` WHERE `userid` = {$user}");
	$db->query("DELETE FROM `fedjail` WHERE `fed_userid` = {$user}");
	$db->query("DELETE FROM `fedjail_appeals` WHERE `fja_user` = {$user}");
	$db->query("DELETE FROM `fedjail_appeals` WHERE `fja_responder` = {$user}");
	$db->query("DELETE FROM `forum_bans` WHERE `fb_user` = {$user}");
	$db->query("DELETE FROM `forum_posts` WHERE `fp_poster_id` = {$user}");
	$db->query("DELETE FROM `forum_topics` WHERE `ft_owner_id` = {$user}");
	$db->query("DELETE FROM `forum_tops_rating` WHERE `userid` = {$user}");
	$db->query("DELETE FROM `friends` WHERE `friended` = {$user}");
	$db->query("DELETE FROM `friends` WHERE `friender` = {$user}");
	$db->query("DELETE FROM `guild_applications` WHERE `ga_user` = {$user}");
	$db->query("DELETE FROM `guild_donations` WHERE `userid` = {$user}");
	$db->query("DELETE FROM `infirmary` WHERE `infirmary_user` = {$user}");
	$db->query("DELETE FROM `inventory` WHERE `inv_userid` = {$user}");
	$db->query("DELETE FROM `itemauction` WHERE `ia_adder` = {$user}");
	$db->query("DELETE FROM `itemmarket` WHERE `imADDER` = {$user}");
	$db->query("DELETE FROM `itemrequest` WHERE `irUSER` = {$user}");
	$db->query("DELETE FROM `login_attempts` WHERE `userid` = {$user}");
	$db->query("DELETE FROM `logs` WHERE `log_user` = {$user}");
	$db->query("DELETE FROM `mail` WHERE `mail_from` = {$user}");
	$db->query("DELETE FROM `mail` WHERE `mail_to` = {$user}");
	$db->query("DELETE FROM `mail_bans` WHERE `mbUSER` = {$user}");
	$db->query("DELETE FROM `marriage_tmg` WHERE `proposer_id` = {$user}");
	$db->query("DELETE FROM `marriage_tmg` WHERE `proposed_id` = {$user}");
	$db->query("DELETE FROM `mining` WHERE `userid` = {$user}");
	$db->query("DELETE FROM `missions` WHERE `mission_userid` = {$user}");
	$db->query("DELETE FROM `newspaper_ads` WHERE `news_owner` = {$user}");
	$db->query("DELETE FROM `notepads` WHERE `np_owner` = {$user}");
	$db->query("DELETE FROM `notifications` WHERE `notif_user` = {$user}");
	$db->query("DELETE FROM `permissions` WHERE `perm_user` = {$user}");
	$db->query("DELETE FROM `referals` WHERE `referal_userid` = {$user}");
	$db->query("DELETE FROM `referals` WHERE `refered_id` = {$user}");
	$db->query("DELETE FROM `reports` WHERE `reporter_id` = {$user}");
	$db->query("DELETE FROM `reports` WHERE `reportee_id` = {$user}");
	$db->query("DELETE FROM `russian_roulette` WHERE `challengee` = {$user}");
	$db->query("DELETE FROM `russian_roulette` WHERE `challenger` = {$user}");
	$db->query("DELETE FROM `sec_market` WHERE `sec_user` = {$user}");
	$db->query("DELETE FROM `shortcut` WHERE `sc_userid` = {$user}");
	$db->query("DELETE FROM `smelt_inprogress` WHERE `sip_user` = {$user}");
	$db->query("DELETE FROM `spy_advantage` WHERE `user` = {$user}");
	$db->query("DELETE FROM `spy_advantage` WHERE `spied` = {$user}");
	$db->query("DELETE FROM `steam_account_link` WHERE `steam_linked` = {$user}");
	$db->query("DELETE FROM `tcl_userdata` WHERE `tsl_userid` = {$user}");
	$db->query("DELETE FROM `thanksgiving_trivia` WHERE `userid` = {$user}");
	$db->query("DELETE FROM `trading` WHERE `tradeusera` = {$user}");
	$db->query("DELETE FROM `trading` WHERE `tradeuserb` = {$user}");
	$db->query("DELETE FROM `tsl_user_mercs` WHERE `tsl_userid` = {$user}");
	$db->query("DELETE FROM `userdata` WHERE `userid` = {$user}");
	$db->query("DELETE FROM `users` WHERE `tradeusera` = {$user}");
	$db->query("DELETE FROM `userstats` WHERE `userid` = {$user}");
	$db->query("DELETE FROM `users_effects` WHERE `userid` = {$user}");
	$db->query("DELETE FROM `uservotes` WHERE `userid` = {$user}");
	$db->query("DELETE FROM `user_equips` WHERE `userid` = {$user}");
	$db->query("DELETE FROM `user_logging` WHERE `userid` = {$user}");
	$db->query("DELETE FROM `user_pref` WHERE `userid` = {$user}");
	$db->query("DELETE FROM `user_settings` WHERE `userid` = {$user}");
	$db->query("DELETE FROM `user_skills` WHERE `userid` = {$user}");
	$db->query("DELETE FROM `vip_market` WHERE `vip_user` = {$user}");
	$db->query("DELETE FROM `vips_accepted` WHERE `vipBUYER` = {$user}");
	$db->query("DELETE FROM `vips_accepted` WHERE `vipFOR` = {$user}");
}

function createUser($name,$email,$pw,$gender='Male',$class='Warrior')
{
	global $db, $api;
	$IP = $db->escape($_SERVER['REMOTE_ADDR']);
	$CurrentTime = time();
	$profilepic = "https://www.gravatar.com/avatar/" . md5(strtolower(trim($email))) . "?s=250.jpg";
	$db->query("INSERT INTO `users`
					(`username`,`email`,`password`,`level`,`gender`,`class`,
					`lastip`,`registerip`,`registertime`,`loginip`,`display_pic`,`vip_days`)
					VALUES ('{$name}','{$email}','{$pw}','1','{$gender}',
					'{$class}','{$IP}','{$IP}','{$CurrentTime}', '{$IP}', 
					'{$profilepic}','3')");
	$i = $db->insert_id();
	$db->query("UPDATE `users` SET `brave`='10',`maxbrave`='10',`hp`='100',
					`maxhp`='100',`maxwill`='100',`will`='100',`energy`='24',
					`maxenergy`='24' WHERE `userid`={$i}");
	if ($class == 'Warrior') 
		$api->UserGiveItem($i,365,1);
	if ($class == 'Rogue') 
		$api->UserGiveItem($i,366,1);
	if ($class == 'Guardian') 
		$api->UserGiveItem($i,367,1);
	$db->query("INSERT INTO `userstats` VALUES({$i}, 1000, 1000, 1000, 1000, 1000, 100)");
	$db->query("INSERT INTO `infirmary`
			(`infirmary_user`, `infirmary_reason`, `infirmary_in`, `infirmary_out`) 
			VALUES ('{$i}', 'N/A', '0', '0');");
        $db->query("INSERT INTO `dungeon`
			(`dungeon_user`, `dungeon_reason`, `dungeon_in`, `dungeon_out`) 
			VALUES ('{$i}', 'N/A', '0', '0');");
	//Give starter items.
	$api->UserGiveItem($i,6,50);
	$api->UserGiveItem($i,30,50);
	$api->UserGiveItem($i,33,3000);
	$api->UserGiveCurrency($i,'primary',10000);
	$api->UserGiveCurrency($i,'secondary',50);
	
	$db->query("INSERT INTO `user_settings` (`userid`) VALUES ('{$i}')");
        $randophrase=randomizer();
        $db->query("UPDATE `user_settings` SET `security_key` = '{$randophrase}', `theme` = 7 WHERE `userid` = {$i}");
	return $i;
}

function equipUserSlot($user, $slot, $itemID)
{
	global $db, $api;
	//Very dirty work around because I'm lazy :D
	$wepArray = array("equip_armor", "equip_badge", "equip_primary", "equip_secondary", "equip_potion");
	if (!in_array($slot, $wepArray))
	{
		equipUserWearable($user, $slot, $itemID);
	}
	else
	{
		$userInfo = $db->fetch_row($db->query("SELECT `{$slot}` FROM `users` WHERE `userid` = {$user}"));
		$userGains = $db->fetch_row($db->query("SELECT * FROM `equip_gains` WHERE `userid` = {$user} AND `slot` = '{$slot}'"));
		$itemInfo = $db->fetch_row($db->query("SELECT * FROM `items` WHERE `itmid` = {$itemID}"));
		if ($userInfo[$slot] > 0)
		{
			unequipUserSlot($user, $slot);
		}
        if ($slot != "equip_potion")
		{
			$api->UserTakeItem($user, $itemID, 1);
			setEquipGains($user, $slot, $itemID);
		}
		$db->query("UPDATE `users` SET `{$slot}` = {$itemID} WHERE `userid` = {$user}");
		$api->SystemLogsAdd($user, 'equip', "Equipped {$itemInfo['itmname']} as their " . equipSlotParser($slot) . ".");
	}
}

function equipUserWearable($user, $slot, $itemID)
{
	global $db, $api;
	//Very dirty work around because I'm lazy :D
	$wepArray = array("equip_armor", "equip_badge", "equip_primary", "equip_secondary", "equip_potion");
	if (in_array($slot, $wepArray))
	{
		equipUserSlot($user, $slot, $itemID);
	}
	else
	{
		$userInfo = $db->fetch_single($db->query("SELECT `itemid` FROM `user_equips` WHERE `userid` = {$user} AND `equip_slot` = '{$slot}'"));
		$userGains = $db->fetch_row($db->query("SELECT * FROM `equip_gains` WHERE `userid` = {$user} AND `slot` = '{$slot}'"));
		$itemInfo = $db->fetch_row($db->query("SELECT * FROM `items` WHERE `itmid` = {$itemID}"));
		if ($userInfo > 0)
		{
			unequipUserWearable($user, $slot);
		}
        setEquipGains($user, $slot, $itemID);
		$api->UserTakeItem($user, $itemID, 1);
		$db->query("DELETE FROM `user_equips` WHERE `userid` = {$user} AND `equip_slot` = '{$slot}'");
		$db->query("INSERT INTO `user_equips` (`userid`, `equip_slot`, `itemid`) VALUES ('{$user}', '{$slot}', '{$itemID}')");
		$api->SystemLogsAdd($user, 'equip', "Equipped {$itemInfo['itmname']} as their " . equipSlotParser($slot) . ".");
	}
}

function unequipUserWearable($user, $slot)
{
	global $db, $api;
	$wepArray = array("equip_armor", "equip_badge", "equip_primary", "equip_secondary", "equip_potion");
	if (in_array($slot, $wepArray))
	{
		unequipUserSlot($user, $slot);
	}
	else
	{
		$itemID = getUserItemEquippedSlot($user,$slot);
		$userInfo = $db->fetch_single($db->query("SELECT `itemid` FROM `user_equips` WHERE `userid` = {$user} AND `equip_slot` = '{$slot}'"));
		if ($userInfo != 0)
		{
		    $itemInfo = $db->fetch_row($db->query("SELECT * FROM `items` WHERE `itmid` = {$itemID}"));
			undoEquipGains($user, $slot);
			$api->UserGiveItem($user, $itemID, 1);
			$db->query("DELETE FROM `user_equips` WHERE `userid` = {$user} AND `equip_slot` = '{$slot}'");
			$api->SystemLogsAdd($user, 'equip', "Unequipped {$itemInfo['itmname']} as their " . equipSlotParser($slot) . ".");
			return true;
		}
	}
}

function unequipUserSlot($user, $slot)
{
	global $db, $api;
	//Very dirty work around because I'm lazy :D
	$wepArray = array("equip_armor", "equip_badge", "equip_primary", "equip_secondary", "equip_potion");
	if (!in_array($slot, $wepArray))
	{
		unequipUserWearable($user, $slot);
	}
	else
	{
		$itemID = getUserItemEquippedSlot($user,$slot);
		if (empty($itemID))
		    $itemID = 0;
		$userInfo = $db->fetch_row($db->query("SELECT `{$slot}` FROM `users` WHERE `userid` = {$user}"));
		if ($userInfo[$slot] != 0)
		{
		    $itemInfo = $db->fetch_row($db->query("SELECT * FROM `items` WHERE `itmid` = {$itemID}"));
			undoEquipGains($user, $slot);
			$db->query("UPDATE `users` SET `{$slot}` = 0 WHERE `userid` = {$user}");
			if ($slot != "equip_potion")
				$api->UserGiveItem($user, $userInfo[$slot], 1);
			$api->SystemLogsAdd($user, 'equip', "Unequipped {$itemInfo['itmname']} as their " . equipSlotParser($slot) . ".");
			return true;
		}
	}
}

function undoEquipGains($user, $slot)
{
	global $db, $api;
	$sbq=$db->query("/*qc=on*/SELECT * FROM `equip_gains` WHERE `userid` = {$user} and `slot` = '{$slot}'");
	if ($db->num_rows($sbq) > 0)
	{
		$statloss="";
		while ($sbr=$db->fetch_row($sbq))
		{
			if ($sbr['direction'] == 'pos')
			{
				if (in_array($sbr['stat'], array('strength', 'agility', 'guard', 'labor', 'iq'))) 
				{
					$db->query("UPDATE `userstats` SET `{$sbr['stat']}` = `{$sbr['stat']}` - {$sbr['number']} WHERE `userid` = {$user}");
				} 
				elseif (!(in_array($sbr['stat'], array('dungeon', 'infirmary')))) 
				{
					$db->query("UPDATE `users` SET `{$sbr['stat']}` = `{$sbr['stat']}` - {$sbr['number']} WHERE `userid` = {$user}");
				}
				$mod='lost';
			}
			else
			{
				if (in_array($sbr['stat'], array('strength', 'agility', 'guard', 'labor', 'iq'))) 
				{
					$db->query("UPDATE `userstats` SET `{$sbr['stat']}` = `{$sbr['stat']}` + {$sbr['number']} WHERE `userid` = {$user}");
				} 
				elseif (!(in_array($sbr['stat'], array('dungeon', 'infirmary')))) 
				{
					$db->query("UPDATE `users` SET `{$sbr['stat']}` = `{$sbr['stat']}` + {$sbr['number']} WHERE `userid` = {$user}");
				}
				$mod='gained';
			}
			if (empty($statloss))
					$statloss .= "{$mod} " . number_format($sbr['number']) . " " . statParser($sbr['stat']);
			else
					$statloss .= ", {$mod} " . number_format($sbr['number']) . " " . statParser($sbr['stat']);
			$db->query("DELETE FROM `equip_gains` WHERE `userid` = {$user} AND `stat` = '{$sbr['stat']}' AND `slot` = '{$slot}'");
		}
		if (!empty($statloss))
		{
			$itmname = $api->SystemItemIDtoName(getUserItemEquippedSlot($user,$slot));
			$api->GameAddNotification($user, "By unequipping the {$itmname} as your " . equipSlotParser($slot) . ", you have {$statloss}.");
		}
	}
}

function setEquipGains($user, $slot, $item)
{
	global $db, $api;
	$q = $db->query("/*qc=on*/SELECT `itmid`, `itmname`, `effect1`, `effect2`, 
					`effect3`,  `effect1_on`, `effect2_on`, `effect3_on`
					FROM `items` AS `it`
					WHERE `itmid` = {$item}");
	$uiq =
        $db->query(
            "/*qc=on*/SELECT `u`.*, `us`.*
                     FROM `users` AS `u`
                     INNER JOIN `userstats` AS `us`
                     ON `u`.`userid`=`us`.`userid`
                     WHERE `u`.`userid` = {$user}
                     LIMIT 1");
	$ur = $db->fetch_row($uiq);
	$r = $db->fetch_row($q);
	$txt='';
	for ($enum = 1; $enum <= 3; $enum++) 
	{
		if ($r["effect{$enum}_on"] == 'true') 
		{
			$einfo = unserialize($r["effect{$enum}"]);
			if ($einfo['inc_type'] == "percent") 
			{
				if (in_array($einfo['stat'], array('energy', 'will', 'brave', 'hp'))) 
				{
					$inc = round($ur['max' . $einfo['stat']] / 100 * $einfo['inc_amount']);
					$einfo['stat'] = 'max' . $einfo['stat'];
				}
				else
				{
					$inc = round($ur[$einfo['stat']] / 100 * $einfo['inc_amount']);
				}
			}
			else
			{
			 $inc = $einfo['inc_amount'];
			}
			if ($einfo['dir'] == "pos") 
			{
				if (in_array($einfo['stat'], array('energy', 'will', 'brave', 'hp'))) 
				{
					$ur['max' . $einfo['stat']] = $ur['max' . $einfo['stat']] + $einfo['inc_amount'];
					$einfo['stat'] = 'max' . $einfo['stat'];
				} 
				else 
				{
					$ur[$einfo['stat']] += $inc;
				}
			}
			else
			{
				if (in_array($einfo['stat'], array('energy', 'will', 'brave', 'hp'))) 
				{
					$ur[$einfo['stat']] = min($ur[$einfo['stat']] + $inc, $ur['max' . $einfo['stat']]);
					$einfo['stat'] = 'max' . $einfo['stat'];
				}
				else
				{
					$ur[$einfo['stat']] = max($ur[$einfo['stat']] - $inc, 0);
				}
			}
			if (!(in_array($einfo['stat'], array('dungeon', 'infirmary')))) 
			{
				$upd = $ur[$einfo['stat']];
			}
			if (in_array($einfo['stat'], array('strength', 'agility', 'guard', 'labor', 'iq'))) 
			{
                    $db->query("UPDATE `userstats` SET `{$einfo['stat']}` = '{$upd}' WHERE `userid` = {$user}");
			} 
			elseif (!(in_array($einfo['stat'], array('dungeon', 'infirmary')))) 
			{
				$db->query("UPDATE `users` SET `{$einfo['stat']}` = '{$upd}' WHERE `userid` = {$user}");
			}
			$dir = ($einfo['dir'] == 'pos') ? "gained" : "lost";
			if (empty($txt))
                    $txt.=" {$dir} " . number_format($inc) . " " . statParser($einfo['stat']);
                else
                    $txt.=", {$dir} " . number_format($inc) . " " . statParser($einfo['stat']);
			$db->query("INSERT INTO `equip_gains` VALUES ('{$user}', '{$einfo['stat']}', '{$einfo['dir']}', '{$inc}', '{$slot}')");
		}
	}
	if (!empty($txt))
		$api->GameAddNotification($user, "By equipping the {$r['itmname']} in your " . equipSlotParser($slot) . " slot, you have {$txt}.");	
}


function getUserItemEquippedSlot($user,$slot)
{
	global $db;
	$wepArray = array("equip_armor", "equip_badge", "equip_primary", "equip_secondary", "equip_potion");
	if (in_array($slot, $wepArray))
		return $db->fetch_single($db->query("SELECT `{$slot}` FROM `users` WHERE `userid` = {$user}"));
	else
		return $db->fetch_single($db->query("SELECT `itemid` FROM `user_equips` WHERE `userid` = {$user} AND `equip_slot` = '{$slot}'"));
}

function getUserCurrentEstate(int $user)
{
    global $db;   
    return $db->fetch_single($db->query("SELECT `estate` FROM `users` WHERE `userid` = {$user}"));
}

//Old function do not use.
function calculateTotalXP($user)
{
    global $db;
    $q=$db->query("
		SELECT `level`, `reset`
		FROM `users` `u`
		LEFT JOIN `user_settings` AS `us`
		ON `u`.`userid` = `us`.`userid`
		WHERE `u`.`userid` = {$user}");
    //$q=$db->query("SELECT `u`.`level`,`us`.`reset` FROM `users` AS `u` INNER JOIN `user_settings` AS `us` ON `u`.`userid` = `us`.`userid` WHERE `us`.`userid` = {$user}");
    $r=$db->fetch_row($q);
    if (!isset($r['reset']))
        $r['reset'] = 1;
    if ($r['reset'] == 1)
        $total = round(($r['level'] + 1) * ($r['level'] + 1) * ($r['level'] + 1) * 2.2);
    if ($r['reset'] == 2)
    {
        $total = round((500 + 1) * (500 + 1) * (500 + 1) * 2.2);
        $total = $total + round(($r['level'] + 1) * ($r['level'] + 1) * ($r['level'] + 1) * 2.2) / 2;
    }
    if ($r['reset'] == 3)
    {
        $total = round((500 + 1) * (500 + 1) * (500 + 1) * 2.2);
        $total = $total + round((500 + 1) * (500 + 1) * (500 + 1) * 2.2) / 2;
        $total = $total + round(($r['level'] + 1) * ($r['level'] + 1) * ($r['level'] + 1) * 2.2) / 3;
    }
    if ($r['reset'] == 4)
    {
        $total = round((500 + 1) * (500 + 1) * (500 + 1) * 2.2);
        $total = $total + round((500 + 1) * (500 + 1) * (500 + 1) * 2.2) / 2;
        $total = $total + round((500 + 1) * (500 + 1) * (500 + 1) * 2.2) / 3;
        $total = $total + round(($r['level'] + 1) * ($r['level'] + 1) * ($r['level'] + 1) * 2.2) / 4;
    }
    if ($r['reset'] == 5)
    {
        $total = round((500 + 1) * (500 + 1) * (500 + 1) * 2.2);
        $total = $total + round((500 + 1) * (500 + 1) * (500 + 1) * 2.2) / 2;
        $total = $total + round((500 + 1) * (500 + 1) * (500 + 1) * 2.2) / 3;
        $total = $total + round((500 + 1) * (500 + 1) * (500 + 1) * 2.2) / 4;
        $total = $total + round(($r['level'] + 1) * ($r['level'] + 1) * ($r['level'] + 1) * 2.2) / 5;
    }
    if ($r['reset'] == 6)
    {
        $total = round((500 + 1) * (500 + 1) * (500 + 1) * 2.2);
        $total = $total + round((500 + 1) * (500 + 1) * (500 + 1) * 2.2) / 2;
        $total = $total + round((500 + 1) * (500 + 1) * (500 + 1) * 2.2) / 3;
        $total = $total + round((500 + 1) * (500 + 1) * (500 + 1) * 2.2) / 4;
        $total = $total + round((500 + 1) * (500 + 1) * (500 + 1) * 2.2) / 5;
        $total = $total + round(($r['level'] + 1) * ($r['level']+ 1) * ($r['level'] + 1) * 2.2) / 6;
    }
    return $total;
}

//Old function, do not use.
function calcUserMRbyXP($user)
{
    $MR1=round((500 + 1) * (500 + 1) * (500 + 1) * 2.2);
    $MR2 = $MR1 + round((500 + 1) * (500 + 1) * (500 + 1) * 2.2) / 2;
    $MR3 = $MR2 + round((500 + 1) * (500 + 1) * (500 + 1) * 2.2) / 3;
    $MR4 = $MR3 + round((500 + 1) * (500 + 1) * (500 + 1) * 2.2) / 4;
    $MR5 = $MR4 + round((500 + 1) * (500 + 1) * (500 + 1) * 2.2) / 5;
    
    $totalXP = calculateTotalXP($user);
    if ($totalXP < $MR1)
        return 0;
    elseif (($totalXP >= $MR1) && ($totalXP < $MR2))
        return 1;
    elseif (($totalXP >= $MR2) && ($totalXP < $MR3))
        return 2;
    elseif (($totalXP >= $MR3) && ($totalXP < $MR4))
        return 3;
    elseif (($totalXP >= $MR4) && ($totalXP < $MR5))
        return 4;
    elseif ($totalXP >= $MR5)
        return 5;
}

function calcUserNewMRbyXP($user)
{
    $MR1=round((500 + 1) * (500 + 1) * (500 + 1) * 2.2);
    $MR2 = $MR1 + round((750 + 1) * (750 + 1) * (750 + 1) * 2.2) * 0.9;
    $MR3 = $MR2 + round((1000 + 1) * (1000 + 1) * (1000 + 1) * 2.2) * 0.8;
    $MR4 = $MR3 + round((1250 + 1) * (1250 + 1) * (1250 + 1) * 2.2) * 0.7;
    $MR5 = $MR4 + round((1500 + 1) * (1500 + 1) * (1500 + 1) * 2.2) * 0.6;
    
    $totalXP = calculateTotalXP($user);
    
    if ($totalXP < $MR1)
        return 0;
    elseif (($totalXP >= $MR1) && ($totalXP < $MR2))
        return 1;
    elseif (($totalXP >= $MR2) && ($totalXP < $MR3))
        return 2;
    elseif (($totalXP >= $MR3) && ($totalXP < $MR4))
        return 3;
    elseif (($totalXP >= $MR4) && ($totalXP < $MR5))
        return 4;
    elseif ($totalXP >= $MR5)
        return 5;
}

function calcUserXPafterMR($user)
{
    $MR1=round((500 + 1) * (500 + 1) * (500 + 1) * 2.2);
    $MR2 = $MR1 + round((750 + 1) * (750 + 1) * (750 + 1) * 2.2) * 0.9;
    $MR3 = $MR2 + round((1000 + 1) * (1000 + 1) * (1000 + 1) * 2.2) * 0.8;
    $MR4 = $MR3 + round((1250 + 1) * (1250 + 1) * (1250 + 1) * 2.2) * 0.7;
    $MR5 = $MR4 + round((1500 + 1) * (1500 + 1) * (1500 + 1) * 2.2) * 0.6;
    
    $totalXP = calculateTotalXP($user);
    
    if ($totalXP < $MR1)
        return $totalXP;
    elseif (($totalXP >= $MR1) && ($totalXP < $MR2))
        return $totalXP - $MR1;
    elseif (($totalXP >= $MR2) && ($totalXP < $MR3))
        return $totalXP - $MR2;
    elseif (($totalXP >= $MR3) && ($totalXP < $MR4))
        return $totalXP - $MR3;
    elseif (($totalXP >= $MR4) && ($totalXP < $MR5))
        return $totalXP - $MR4;
    elseif ($totalXP >= $MR5)
        return $totalXP - $MR5;
}

function convertToNewMR()
{
    global $db;
    $q=$db->query("SELECT `us`.`userid`, `u`.* 
                    FROM `user_settings` AS `us` 
                    LEFT JOIN `users` AS `u`
                    ON `u`.`userid` = `us`.`userid`
                    WHERE `us`.`reset` > 1");
    if ($db->num_rows($q) > 0)
    {
        while ($r = $db->fetch_row($q))
        {
            $newMR = calcUserNewMRbyXP($r['userid']);
            $newXP = calcUserXPafterMR($r['userid']);
            $newLvl = calcUserLevelFromXP($r['userid']);
            echo $newMR . "<br />";
            echo $newXP . "<br />";
            echo $newLvl['lvl'] . "<br />";
            $db->query("UPDATE `users` SET `level` = {$newLvl['lvl']}, `xp` = 0 WHERE `userid` = {$r['userid']}");
            $db->query("UPDATE `user_settings` SET `reset` = {$newMR} WHERE `userid` = {$r['userid']}");
        }
        //fixMRUserStats();
    }
}

function calcUserLevelFromXP($user)
{
    global $db;
    $totalXP = calcUserXPafterMR($user);
    $q=$db->query("SELECT `us`.*, `u`.*
                    FROM `user_settings` AS `us`
                    LEFT JOIN `users` AS `u`
                    ON `u`.`userid` = `us`.`userid`
                    WHERE `us`.`reset` > 1");
    $r = $db->fetch_row($q);
    
    $return['lvl'] = 1;
    $xp_needed = calculateXPNeededByLevel($return['lvl'], $r['reset'] - 1);
    while ($totalXP > $xp_needed)
    {
        $return['lvl']++;
        $xp_needed = calculateXPNeededByLevel($return['lvl'], $r['reset'] - 1);
    }
    return $return;
}

function doPlayerofWeekTick()
{
    global $db, $set, $api;
    $laston = time() - (60*60*24*7);
    $q = $db->query("SELECT `userid` 
                    FROM `users` 
                    WHERE `user_level` = 'Member' 
                    AND `userid` != {$set['random_player_showcase']} 
                    AND `laston` >= {$laston}
                    ORDER BY RAND() LIMIT 1");
    $r = $db->fetch_single($q);
    $db->query("UPDATE `settings` SET `setting_value` = {$r} WHERE `setting_name` = 'random_player_showcase'");
    $api->GameAddNotification($r, "You have been selected as the Player of the week. This gives you 25K Chivalry Tokens and a cool-ass badge. Your name and pic will be shown on the login page until next week.");
    $api->UserGiveCurrency($r, "secondary", 25000);
    $api->UserGiveItem($r, 419, 1);
}

function calculateXPNeededByLevel($lvl, $mr = 0)
{
    return round(($lvl + 1) * ($lvl + 1) * ($lvl + 1) * 2.2) * (1 - ($mr * 0.1));
}

function doLevelUpBonus($userid, $mr)
{
    global $db;
    $bonusEnergy = ($mr == 1) ? 2 : 2 + ($mr * 2);
    $bonusBrave = ($mr == 1) ? 2 : 2 + ($mr * 2);
    $bonusHP = ($mr == 1) ? 50 : 50 + ($mr * 50);
    $db->query("UPDATE `users` SET `level` = `level` + 1, `energy` = `energy` + {$bonusEnergy},
						`brave` = `brave` + {$bonusBrave}, `maxenergy` = `maxenergy` + {$bonusEnergy}, `maxbrave` = `maxbrave` + {$bonusBrave},
						`hp` = `hp` + {$bonusHP}, `maxhp` = `maxhp` + {$bonusHP} WHERE `userid` = {$userid}");
}

function fixMRUserStats()
{
    global $db;
    $q=$db->query("SELECT `us`.*, `u`.*
                    FROM `user_settings` AS `us`
                    LEFT JOIN `users` AS `u`
                    ON `u`.`userid` = `us`.`userid`
                    WHERE `us`.`reset` > 1");
    while ($r = $db->fetch_row($q))
    {
        userUnequipAll($r['userid']);
        $newBrave = ($r['level'] * 2) + (($r['reset']-1) * 2) + 8;
        $newEnergy = ($r['level'] * 2) + (($r['reset']-1) * 2) + 22;
        $newHP = ($r['level'] * 50) + (($r['reset']-1) * 50) + 50;
        
        $db->query("UPDATE `users` SET `energy` = '{$newEnergy}',
						`brave` = '{$newBrave}', `maxenergy` = '{$newEnergy}', `maxbrave` = '{$newBrave}',
						`hp` = '{$newHP}', `maxhp` = '{$newHP}' WHERE `userid` = {$r['userid']}");
    }
}

function userUnequipAll($userid)
{
    unequipUserSlot($userid, "equip_primary");
    unequipUserSlot($userid, "equip_secondary");
    unequipUserSlot($userid, "equip_armor");
    unequipUserSlot($userid, "equip_badge");
    unequipUserSlot($userid, "equip_primary_ring");
    unequipUserSlot($userid, "equip_secondary_ring");
    unequipUserSlot($userid, "equip_necklace");
    unequipUserSlot($userid, "equip_pendant");
    unequipUserSlot($userid, "equip_potion");
}