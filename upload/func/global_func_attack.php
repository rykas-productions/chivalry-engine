<?php
// Functions relating to the attack system...

/**
 * Calculates weapon effectiveness with skill and mastery bonuses.
 */
function calcWeaponEffectiveness($weapID, $attacker)
{
    global $db, $api;
    $weapon = $db->fetch_row($db->query("SELECT `weapon` FROM `items` WHERE `itmid` = {$weapID}"))['weapon'];

    // Apply Sharper Blade Skill bonus
    $skillLevel = getUserSkill($attacker, 8);
    if ($skillLevel > 0) {
        $weapon += $weapon * ($skillLevel * getSkillBonus(8) / 100);
    }

    // Apply unique multiplier for special weapon
    if ($weapID == 235) {
        $level = $api->UserInfoGet($attacker, "level");
        $mastery = getUserMasteryRank($attacker);
        $weapon = ($weapon * $weapon) * levelMultiplier($level, $mastery);
    }

    return $weapon;
}

/**
 * Calculates armor effectiveness with Thickened Skin skill bonus.
 */
function calcArmorEffectiveness($armorID, $attacker)
{
    global $db;
    $armor = $db->fetch_row($db->query("SELECT `armor` FROM `items` WHERE `itmid` = {$armorID}"))['armor'];
    $skillBonus = getUserSkill($attacker, 6) * 6.5 / 100;
    return $armor + ($armor * $skillBonus);
}

function returnEffectiveUserStrength($userid)
{
    return returnUserEffectiveStat($userid, "strength");
}
function returnEffectiveUserAgility($userid)
{
    return returnUserEffectiveStat($userid, "agility");
}
function returnEffectiveUserGuard($userid)
{
    return returnUserEffectiveStat($userid, "guard");
}

/**
 * Returns a user's effective stat with skill/class modifiers.
 */
function returnUserEffectiveStat($userid, $stat)
{
    global $db;
    $class = $db->fetch_single($db->query("SELECT `class` FROM `users` WHERE `userid` = {$userid}"));
    $baseStat = $db->fetch_single($db->query("SELECT `{$stat}` FROM `userstats` WHERE `userid` = {$userid}"));

    // Effect potion bonus
    if (userHasEffect($userid, $stat)) {
        $baseStat += $baseStat * (5 * returnEffectMultiplier($userid, $stat)) / 100;
    }

    // Class skill bonus
    if (getUserSkill($userid, 1) > 0 && classApplies($class, $stat)) {
        $baseStat += $baseStat * (getSkillBonus(1) * getUserSkill($userid, 1));
    }

    return $baseStat;
}

function classApplies($class, $stat)
{
    return (
        ($class === 'Warrior' && $stat === 'strength') ||
        ($class === 'Rogue' && $stat === 'agility') ||
        ($class === 'Guardian' && $stat === 'guard')
    );
}

/**
 * Logs an attack result.
 */
function attacklog($attacker, $attacked, $result)
{
    global $db;
    $time = time();
    $db->query("INSERT INTO `attack_logs` (`attack_time`, `attacker`, `attacked`, `result`) VALUES ({$time}, {$attacker}, {$attacked}, '{$result}')");
}

/**
 * Handles bomb chance during an infirmary escort.
 */
function doExtraBomb($user, $infirm)
{
    global $api;
    $infirmTime = Random(30, 60);
    $equippedBomb = $api->UserEquippedItem($user, 'primary', 354) || $api->UserEquippedItem($user, 'secondary', 354);

    if ($equippedBomb && Random(1, 100) == 29) {
        $api->UserStatusSet($infirm, 'infirmary', $infirmTime, '');
        $api->GameAddNotification($infirm, "You were bombed and need {$infirmTime} more minutes in the infirmary.");
        $api->GameAddNotification($user, "You bombed {$api->SystemUserIDtoName($infirm)} for {$infirmTime} minutes of damage.");
        return true;
    }
    return false;
}

/**
 * Logs boss fight damage.
 */
function logBossDmg($userid, $boss_id, $dmg)
{
    global $db;
    $exists = $db->num_rows($db->query("SELECT * FROM `bossDamage` WHERE `userid` = {$userid} AND `boss_id` = {$boss_id}"));
    if ($exists == 0) {
        $db->query("INSERT INTO `bossDamage` (`userid`, `boss_id`, `dmg`) VALUES ({$userid}, {$boss_id}, {$dmg})");
    } else {
        $db->query("UPDATE `bossDamage` SET `dmg` = `dmg` + {$dmg} WHERE `userid` = {$userid} AND `boss_id` = {$boss_id}");
    }
}

function preFightChecks()
{
    global $h, $ir, $ref, $userid, $votecount;  //you can use variables from outside the scope if defined here.

    //If user is not specified.
    if (!$_GET['user']) {
        alert("danger", "Uh Oh!", "You've chosen to attack a non-existent user. Check your source and try again.", true, "{$ref}.php");
        die($h->endpage());
    }
    //If the user is trying to attack himself.
    else if ($_GET['user'] == $userid) {
        alert("danger", "Uh Oh!", "Depressed or not, you cannot attack yourself.", true, "{$ref}.php");
        die($h->endpage());
    }
    //If the user has no HP, and is not already attacking.
    else if ($ir['hp'] <= 1 && $ir['attacking'] == 0) {
        alert("danger", "Uh Oh!", "You have no health, so you cannot attack. Come back when your health has refilled.", true, "{$ref}.php");
        die($h->endpage());
    }
    //If the user has left a previous after losing.
    else if (isset($_SESSION['attacklost']) && $_SESSION['attacklost'] > 1) {
        $_SESSION['attacklost'] = 0;
        alert("danger", "Uh Oh!", "You cannot start another attack after you ran from the last one.", true, "{$ref}.php");
        die($h->endpage());
    } else if ($_GET['user'] == 20 && $votecount != 3) {
        alert("danger", "Uh Oh!", "You cannot attack Your Doppleganger until you've voted completely for the day.", true, "{$ref}.php");
        die($h->endpage());
    } else if ($_GET['user'] == 21) {
        if (date('n') != 11) {
            alert("danger", "Uh Oh!", "Due to kingdom wide laws, turkeys may only be hunted during Novemeber.", true, "{$ref}.php");
            die($h->endpage());
        }
    } else if ($ir['att_dg'] == 1 && $_GET['user'] == 20) {
        alert("danger", "Uh Oh!", "You've already attacked your doppleganger for the day.", true, "{$ref}.php");
        die($h->endpage());
    }
}

function resetAttackStatus()
{
    global $userid, $api, $ir;
    $_SESSION['attacking'] = 0;
    $_SESSION['attack_scroll'] = 0;
    $ir['attacking'] = 0;
    $api->UserInfoSetStatic($userid, "attacking", 0);
}

function handleAttackScrollLogic()
{
    global $api, $h, $odata, $ir, $ref, $userid;
    if ($_GET['scroll'] == 1) {
        if (($ir['location'] + 2) < $odata['location']) {
            alert('danger', "Uh Oh!", "This user is too far away to use a {$api->SystemItemIDtoName(90)}!", true, "{$ref}.php");
            die($h->endpage());
        } elseif (($ir['location'] - 2) > $odata['location']) {
            alert('danger', "Uh Oh!", "This user is too far away to use a {$api->SystemItemIDtoName(90)}!", true, "{$ref}.php");
            die($h->endpage());
        } else {
            $_SESSION['attack_scroll'] = 1;
            $api->UserTakeItem($userid, 90, 1);
        }
    }
    if ($_GET['scroll'] == 2) {
        if (($ir['location'] + 5) < $odata['location']) {
            alert('danger', "Uh Oh!", "This user is too far away to use a {$api->SystemItemIDtoName(247)}!", true, "{$ref}.php");
            die($h->endpage());
        } elseif (($ir['location'] - 5) > $odata['location']) {
            alert('danger', "Uh Oh!", "This user is too far away to use a {$api->SystemItemIDtoName(247)}!", true, "{$ref}.php");
            die($h->endpage());
        } else {
            $_SESSION['attack_scroll'] = 1;
            $api->UserTakeItem($userid, 247, 1);
        }
    }
    if ($_GET['scroll'] == 3) {
        $_SESSION['attack_scroll'] = 1;
        if (Random(1, 1000) == 512) {
            $api->UserTakeItem($userid, 266, 1);
            $api->GameAddNotification($userid, "Your {$api->SystemItemIDtoName(266)} has shattered.");
        }
    }
}

function handlePerfectionStatBonuses()
{
    global $ir, $userid, $odata;
    $specialnumber = ((getUserSkill($ir['userid'], 1) * 3) / 100);
    if ($ir['class'] == 'Warrior')
        $ir['strength'] += ($ir['strength'] * $specialnumber);
    if ($ir['class'] == 'Rogue')
        $ir['agility'] += ($ir['agility'] * $specialnumber);
    if ($ir['class'] == 'Guardian')
        $ir['guard'] += $ir['guard'] * $specialnumber;

    $specialnumber2 = ((getUserSkill($_GET['user'], 1) * 3) / 100);    //this is the problem line e_e
    if ($odata['class'] == 'Warrior')
        $odata['strength'] += ($odata['strength'] * $specialnumber2);
    if ($odata['class'] == 'Rogue')
        $odata['agility'] += ($odata['agility'] * $specialnumber2);
    if ($odata['class'] == 'Guardian')
        $odata['guard'] += ($odata['guard'] * $specialnumber2);
}

function mirrorEquipsToDefender()
{
    global $userid, $ir;
    $prim_ring = getUserItemEquippedSlot($userid, slot_prim_ring);
    $sec_ring = getUserItemEquippedSlot($userid, slot_second_ring);
    $wed_ring = getUserItemEquippedSlot($userid, slot_wed_ring);
    $neck = getUserItemEquippedSlot($userid, slot_necklace);
    $pend = getUserItemEquippedSlot($userid, slot_pendant);

    equipUserSlot($_GET['user'], slot_prim_wep, $ir['equip_primary']);
    equipUserSlot($_GET['user'], slot_second_wep, $ir['equip_secondary']);
    equipUserSlot($_GET['user'], slot_armor, $ir['equip_armor']);
    equipUserSlot($_GET['user'], slot_prim_ring, $prim_ring);
    equipUserSlot($_GET['user'], slot_second_ring, $sec_ring);
    equipUserSlot($_GET['user'], slot_wed_ring, $wed_ring);
    equipUserSlot($_GET['user'], slot_necklace, $neck);
    equipUserSlot($_GET['user'], slot_pendant, $pend);
    equipUserSlot($_GET['user'], slot_potion, $ir['equip_potion']);
}

/**
 * Adjusts boss statistics based on player stats and boss scaling parameters.
 * 
 * This function scales the player's strength, agility, and guard stats according to
 * the boss's stat scale modifier and random variation. It then updates the boss's
 * userstats and level to match the attacking player's stats and equipment.
 */
function handleBossLogic()
{
    global $ir, $db, $bossq;
    if ($db->num_rows($bossq) > 0) {
        $bossr = $db->fetch_row($bossq);
        $scales = (Random(-5, 5) + $bossr['boss_stat_scale']) / 100;
        $scalea = (Random(-5, 5) + $bossr['boss_stat_scale']) / 100;
        $scaleg = (Random(-5, 5) + $bossr['boss_stat_scale']) / 100;
        $str = $ir['strength'] * $scales;
        $agl = $ir['agility'] * $scalea;
        $grd = $ir['guard'] * $scaleg;

        //Set stats for this boss to be relative to the player.
        $db->query("UPDATE `userstats` SET
					`strength` = {$str},
					`agility` = {$agl},
					`guard` = {$grd}
					WHERE `userid` = {$_GET['user']}");

        //Set the boss to have same level and gear as the person attacking them.
        $db->query("UPDATE `users` SET `level` = {$ir['level']} WHERE `userid` = {$_GET['user']}");
        mirrorEquipsToDefender();
    }
}

function handleDopplegangerLogic()
{
    global $db, $ir;
    //Doppleganger
    if ($_GET['user'] == 20) {
        $db->query("UPDATE `userstats` SET `strength` = 1000, `agility` = 1000, `guard` = 1000 WHERE `userid` = 20");

        $float = randomDecimal(0.90, 1.15, 2);
        $str = $ir['strength'] * $float;
        $agl = $ir['agility'] * $float;
        $grd = $ir['guard'] * $float;

        $db->query("UPDATE `userstats`
					SET `strength` = {$str},
					`agility` = {$agl},
					`guard` = {$grd}
					WHERE `userid` = 20");
        $db->query("UPDATE `users` SET `level` = {$ir['level']} WHERE `userid` = 20");

        mirrorEquipsToDefender();
    }
}

function setAttackStatus()
{
    global $ir, $userid, $api;
    $_SESSION['attacking'] = $_GET['user'];
    $ir['attacking'] = $_GET['user'];
    $api->UserInfoSetStatic($userid, "attacking", $ir['attacking']);
}

function doPoisonLogic($userid, $receiver)
{
    global $api;
    $chance = calcPoisonChance($userid);
    if (Random(1, 100) <= $chance) {
        $poirng = Random(20, 50);
        userGiveEffect($receiver, effect_posion, $poirng * 60);
        $api->GameAddNotification($receiver, "You were poisoned in combat! Your Will won't regenerate naturally for the next {$poirng} minutes.");
        return true;
    }
}

function calcPoisonChance($userid)
{
    return returnEffectMultiplier($userid, effect_poisoned_weaps) * 8;
}
