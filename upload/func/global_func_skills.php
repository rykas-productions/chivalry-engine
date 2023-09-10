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
    trigger_error("Using depreciated method `getSkillLevel();`", E_USER_DEPRECATED);
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
    return (int) $db->fetch_single($db->query("SELECT `skRequired` FROM `user_skills_define` WHERE `skID` = {$skillID}"));
}

function purchaseSkill($userid,$skill)
{
    global $db;
    $cost = $db->fetch_single($db->query("SELECT `skCost` FROM `user_skills_define` WHERE `skID` = {$skill}"));
    $db->query("UPDATE `user_settings` SET `skill_points` = `skill_points` - {$cost} WHERE `userid` = {$userid}");
    $q=$db->query("/*qc=on*/SELECT `skill_lvl` FROM `user_skills` WHERE `userid` = {$userid} AND `skill_id` = {$skill}");
    if ($db->num_rows($q) == 0)
        $db->query("INSERT INTO `user_skills` (`userid`, `skill_id`, `skill_lvl`) VALUES ('{$userid}', '{$skill}', '1')");
    else
        $db->query("UPDATE `user_skills` SET `skill_lvl` = `skill_lvl` + 1 WHERE `userid` = {$userid} AND `skill_id` = {$skill}");
}