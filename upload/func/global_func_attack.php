<?php
//Functions relating to the attack system...

/**
 * @internal
 * Return's the current user's weapon effectiveness.
 * @param int $weapID Item ID of the weapon.
 * @param int $attacker User ID of the person wielding the weapon.
 */
function calcWeaponEffectiveness($weapID, $attacker)
{
    global $db, $api;
    $q1 = $db->query("SELECT `weapon`, `ammo` FROM `items` WHERE `itmid` = {$weapID}");
    $r = $db->fetch_row($q1);
    $sharperBladersSkill = ((getSkillLevel($attacker, 9) * 20) / 100);  //20% per skill level
    $r['weapon'] += ($r['weapon'] * $sharperBladersSkill);
    if ($weapID == 235)
        $r['weapon'] = ($r['weapon'] * 0.25) * $api->UserInfoGet($attacker, "level");
    return $r['weapon'];
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

function returnUserEffectiveStat($userid, $stat)
{
    global $db;
    $return = $db->fetch_single($db->query("SELECT `{$stat}` FROM `userstats` WHERE `userid` = {$userid}"));
    if (userHasEffect($userid, $stat))
    {
        $effectLvl = returnEffectMultiplier($userid, $stat);
        var_dump($effectLvl);
        $return = $return + ($return * ((5 * $effectLvl) / 100));
    }
    return $return;
}

function attacklog($attacker,$attacked,$result)
{
    global $db;
    $time=time();
    $db->query("INSERT INTO `attack_logs` (`attack_time`, `attacker`, `attacked`, `result`) VALUES ('{$time}', '{$attacker}', '{$attacked}', '{$result}')");
}

function doExtraBomb($user, $infirm)
{
    global $api;
    $doBomb = false;
    $infirmTime = Random(30,60);
    if ($api->UserEquippedItem($user, 'primary', 354))
        $doBomb = true;
    elseif ($api->UserEquippedItem($user, 'secondary', 354))
        $doBomb = true;
        if ($doBomb == true)
        {
            if (Random(1,100) == 29)
            {
                $api->UserStatusSet($infirm, 'infirmary', $infirmTime, '');
                $api->GameAddNotification($infirm, "You were bombed while being escorted to the infirmary and need to stay {$infirmTime} minutes longer.");
                $api->GameAddNotification($user, "You bombed {$api->SystemUserIDtoName($infirm)} while escorting them to the infirmary and did {$infirmTime} minutes of damage.");
                return true;
            }
        }
        else
            return false;
}

function logBossDmg($userid, $boss_id, $dmg)
{
    global $db;
    $q=$db->query("SELECT * FROM `bossDamage` WHERE `userid` = {$userid} AND `boss_id` = {$boss_id}");
    if ($db->num_rows($q) == 0)
        $db->query("INSERT INTO `bossDamage` (`userid`, `boss_id`, `dmg`) VALUES ('{$userid}', '{$boss_id}', '{$dmg}')");
        else
            $db->query("UPDATE `bossDamage` SET `dmg` = `dmg` + {$dmg} WHERE `userid` = {$userid} AND `boss_id` = {$boss_id}");
}

function preFightChecks()
{
    global $h, $ir, $ref, $userid, $votecount;  //you can use variables from outside the scope if defined here.

    //If user is not specified.
    if (!$_GET['user']) 
    {
        alert("danger", "Uh Oh!", "You've chosen to attack a non-existent user. Check your source and try again.", true, "{$ref}.php");
        die($h->endpage());
    } //If the user is trying to attack himself.
    else if ($_GET['user'] == $userid) 
    {
        alert("danger", "Uh Oh!", "Depressed or not, you cannot attack yourself.", true, "{$ref}.php");
        die($h->endpage());
    } //If the user has no HP, and is not already attacking.
    else if ($ir['hp'] <= 1 && $ir['attacking'] == 0) 
    {
        alert("danger", "Uh Oh!", "You have no HP, so you cannot attack. Come back when your HP has refilled.", true, "{$ref}.php");
        die($h->endpage());
    } //If the user has left a previous after losing.
    else if (isset($_SESSION['attacklost']) && $_SESSION['attacklost'] > 1) 
    {
        $_SESSION['attacklost'] = 0;
        alert("danger", "Uh Oh!", "You cannot start another attack after you ran from the last one.", true, "{$ref}.php");
        die($h->endpage());
    }
	else if ($_GET['user'] == 20 && $votecount != 5)
	{
		alert("danger", "Uh Oh!", "You cannot attack Your Doppleganger until you've voted completely for the day.", true, "{$ref}.php");
        die($h->endpage());
	}
	else if ($_GET['user'] == 21)
	{
		if (date('n') != 11)
		{
			alert("danger", "Uh Oh!", "Due to kingdom wide laws, turkeys may only be hunted during Novemeber.", true, "{$ref}.php");
			die($h->endpage());
		}
	}
	else if ($ir['att_dg'] == 1 && $_GET['user'] == 20)
	{
		alert("danger", "Uh Oh!", "You've already attacked your doppleganger for the day.", true, "{$ref}.php");
        die($h->endpage());
	}

}