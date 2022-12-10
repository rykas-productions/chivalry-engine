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

/**
 * @internal
 * Return's the current user's armor effectiveness.
 * @param int $weapID Item ID of the armor.
 * @param int $attacker User ID of the person wielding the armor.
 */
function calcArmorEffectiveness($armorID, $attacker)
{
    global $db;
    $q1 = $db->query("SELECT `armor`, `ammo` FROM `items` WHERE `itmid` = {$armorID}");
    $r = $db->fetch_row($q1);
    $thickenedSkinSkill = ((getSkillLevel($attacker, 6) * 6.5) / 100);  //6.5% per skill level
    $r['armor'] += ($r['armor'] * $thickenedSkinSkill);
    return $r['armor'];
    
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
    } 
    //If the user is trying to attack himself.
    else if ($_GET['user'] == $userid) 
    {
        alert("danger", "Uh Oh!", "Depressed or not, you cannot attack yourself.", true, "{$ref}.php");
        die($h->endpage());
    } 
    //If the user has no HP, and is not already attacking.
    else if ($ir['hp'] <= 1 && $ir['attacking'] == 0) 
    {
        alert("danger", "Uh Oh!", "You have no health, so you cannot attack. Come back when your health has refilled.", true, "{$ref}.php");
        die($h->endpage());
    } 
    //If the user has left a previous after losing.
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
    if ($_GET['scroll'] == 1)
    {
        if (($ir['location'] + 2) < $odata['location'])
        {
            alert('danger',"Uh Oh!","This user is too far away to use a {$api->SystemItemIDtoName(90)}!",true,"{$ref}.php");
            die($h->endpage());
        }
        elseif (($ir['location'] - 2) > $odata['location'])
        {
            alert('danger',"Uh Oh!","This user is too far away to use a {$api->SystemItemIDtoName(90)}!",true,"{$ref}.php");
            die($h->endpage());
        }
        else
        {
            $_SESSION['attack_scroll']=1;
            $api->UserTakeItem($userid,90,1);
        }
        
    }
    if ($_GET['scroll'] == 2)
    {
        if (($ir['location'] + 5) < $odata['location'])
        {
            alert('danger',"Uh Oh!","This user is too far away to use a {$api->SystemItemIDtoName(247)}!",true,"{$ref}.php");
            die($h->endpage());
        }
        elseif (($ir['location'] - 5) > $odata['location'])
        {
            alert('danger',"Uh Oh!","This user is too far away to use a {$api->SystemItemIDtoName(247)}!",true,"{$ref}.php");
            die($h->endpage());
        }
        else
        {
            $_SESSION['attack_scroll']=1;
            $api->UserTakeItem($userid,247,1);
        }
        
    }
    if ($_GET['scroll'] == 3)
    {
        $_SESSION['attack_scroll']=1;
        if (Random(1,1000) == 512)
        {
            $api->UserTakeItem($userid,266,1);
            $api->GameAddNotification($userid,"Your {$api->SystemItemIDtoName(266)} has shattered.");
        }
        
    }
}

function handlePerfectionStatBonuses()
{
    global $ir, $userid, $odata;
    $specialnumber=((getSkillLevel($ir['userid'],1)*3)/100);
    if ($ir['class'] == 'Warrior')
        $ir['strength'] += ($ir['strength']*$specialnumber);
    if ($ir['class'] == 'Rogue')
        $ir['agility'] += ($ir['agility']*$specialnumber);
    if ($ir['class'] == 'Guardian')
        $ir['guard'] += $ir['guard']*$specialnumber;
    
    $specialnumber2=((getSkillLevel($_GET['user'],1)*3)/100);    //this is the problem line e_e
    if ($odata['class'] == 'Warrior')
        $odata['strength'] += ($odata['strength']*$specialnumber2);
    if ($odata['class'] == 'Rogue')
        $odata['agility'] += ($odata['agility']*$specialnumber2);
    if ($odata['class'] == 'Guardian')
        $odata['guard'] += ($odata['guard']*$specialnumber2);
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

function handleBossLogic()
{
    global $ir, $db, $bossq;
    if ($db->num_rows($bossq) > 0)
    {
        $bossr=$db->fetch_row($bossq);
        $scales = (Random(-5,5) + $bossr['boss_stat_scale']) / 100;
        $scalea = (Random(-5,5) + $bossr['boss_stat_scale']) / 100;
        $scaleg = (Random(-5,5) + $bossr['boss_stat_scale']) / 100;
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
    if ($_GET['user'] == 20)
    {
        $db->query("UPDATE `userstats` SET `strength` = 1000, `agility` = 1000, `guard` = 1000 WHERE `userid` = 20");
        
        $float = randomDecimal(0.90,1.15,2);
        $str=$ir['strength']*$float;
        $agl=$ir['agility']*$float;
        $grd=$ir['guard']*$float;
        
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
    if (Random(1,100) <= $chance)
    {
        $poirng = Random(20,50);
        userGiveEffect($receiver, effect_posion, $poirng * 60);
        $api->GameAddNotification($receiver, "You were poisoned in combat! Your Will won't regenerate naturally for the next {$poirng} minutes.");
        return true;
    }
}

function calcPoisonChance($userid)
{
    return returnEffectMultiplier($userid, effect_poisoned_weaps) * 8;
}