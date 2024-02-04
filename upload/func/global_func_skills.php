<?php
/**
 * @desc Get the skill level of the user.
 * @param int $user
 * @param skill id $id
 * @return number Player's skill level
 * @deprecated Removed for vague func name. Use getUserSkill($userid, $skillID);
 */
function getSkillLevel($user, $id)
{
    trigger_error("Using depreciated method `getSkillLevel();` Please report this to CID Admin. This is a non-fatal error.", E_USER_DEPRECATED);
    return getUserSkill($user, $id);
}

/**
 * @desc Return the bonus multiplier of the current skill.
 * @param Skill ID $skillID
 * @return number
 */
function getSkillBonus($skillID)
{
    global $db;
    return (int) $db->fetch_single($db->query("SELECT `skMultiplier` FROM `user_skills_define` WHERE `skID` = {$skillID}"));
}

/**
 * @desc Return the current skill's name.
 * @param Skill ID $skillID
 * @return string
 */
function getSkillName($skillID)
{
    global $db;
    return $db->fetch_single($db->query("SELECT `skName` FROM `user_skills_define` WHERE `skID` = {$skillID}"));
}

/**
 * @desc Return the skill point cost of the current skill.
 * @param Skill ID $skillID
 * @return number
 */
function getSkillCost($skillID)
{
    global $db;
    return (int) $db->fetch_single($db->query("SELECT `skCost` FROM `user_skills_define` WHERE `skID` = {$skillID}"));
}

/**
 * @desc Return the maximum times this skill may be purchased.
 * @param Skill ID $skillID
 * @return number
 */
function getSkillMaxAllowed($skillID)
{
    global $db;
    return (int) $db->fetch_single($db->query("SELECT `skMaxBuy` FROM `user_skills_define` WHERE `skID` = {$skillID}"));
}

/**
 * @desc Return the Skill ID required of the current skill.
 * @param Skill ID $skillID
 * @return number
 */
function getSkillRequirement($skillID)
{
    global $db;
    return $db->fetch_single($db->query("SELECT `skRequired` FROM `user_skills_define` WHERE `skID` = {$skillID}"));
}

/**
 * @desc Internal function to safely purchase a skill. Will either add the skill to the table, or add a level the to
 * @desc Player's skill, if they already have it. Will also remove the required Skill Points.
 * @param int $userid
 * @param int $skillID
 */
function purchaseSkill($userid,$skillID)
{
    global $db;
    $cost = $db->fetch_single($db->query("SELECT `skCost` FROM `user_skills_define` WHERE `skID` = {$skillID}"));
    $db->query("UPDATE `user_settings` SET `skill_points` = `skill_points` - {$cost} WHERE `userid` = {$userid}");
    $q=$db->query("/*qc=on*/SELECT `skill_lvl` FROM `user_skills` WHERE `userid` = {$userid} AND `skill_id` = {$skillID}");
    if ($db->num_rows($q) == 0)
        $db->query("INSERT INTO `user_skills` (`userid`, `skill_id`, `skill_lvl`) VALUES ('{$userid}', '{$skillID}', '1')");
    else
        $db->query("UPDATE `user_skills` SET `skill_lvl` = `skill_lvl` + 1 WHERE `userid` = {$userid} AND `skill_id` = {$skillID}");
}

/**
 * @desc Internal function to safely remove a skill from a player. Will credit them the spent skill points.
 * @desc Note: If $level != -1, will remove that many levels from the player.
 * @param int $userid
 * @param int $skillID
 * @param int $level = -1
 */
function removeUserSkill($userid, $skillID, $level = -1)
{
    global $db;
    $r=$db->fetch_row($db->query("SELECT * FROM `user_skills_define` WHERE `skID` = {$skillID}"));
    if ($level == -1)
    {
        $cost = $r['skCost'] * getUserSkill($userid, $skillID);
        $db->query("UPDATE `user_skills` SET `skill_level` = 0 WHERE `userid` = {$userid} AND `skill_id` = {$skillID}");
    }
    else
    {
        $cost = $r['skCost'] * $level;
        $db->query("UPDATE `user_skills` SET `skill_level` = `skill_level` - {$level} WHERE `userid` = {$userid} AND `skill_id` = {$skillID}");
    }
    $db->query("UPDATE `user_settings` SET `skill_points` = `skill_points` + {$cost} WHERE `userid` = {$userid} AND `skill_id` = {$skillID}");
}

/**
 * @desc    Cycle through the players' skills and safely remove them, credit the player the skill points, 
 * @desc    then delete the entries from the user skill table, and the skill define table.
 * @param number $skillID
 */
function safelyDeleteSkill($skillID)
{
    global $db, $api;
    $q = $db->query("SELECT * FROM `user_skills` WHERE `skill_id` = {$skillID} and `skill_lvl` > 0");
    $sd=$db->fetch_row($db->query("SELECT * FROM `user_skills_define` WHERE `skID` = {$skillID}"));
    while ($r = $db->fetch_row())
    {
        $cost = $r['skCost'] * getUserSkill($r['userid'], $skillID);
        removeUserSkill($r['userid'], $skillID);
        $api->GameAddNotification($r['userid'], "The game administration has removed the {$sd['skName']} Skill from the game. You've been credited back " . shortNumberParse($cost) . " Skill Points.");
    }
    $db->query("DELETE FROM `user_skills_define` WHERE `skID` = {$skillID}");
    $db->query("DELETE FROM `user_skills` WHERE `skill_id` = {$skillID}");
}