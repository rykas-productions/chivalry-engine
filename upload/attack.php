<?php
/*
	File:		attack.php
	Created: 	4/4/2016 at 11:51PM Eastern Time
	Info: 		File that contains all PVP Logic.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
$atkpage = 1;
require("globals.php");
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case 'xp':
        xp();
        break;
    case 'mug':
        mug();
        break;
    case 'lost':
        lost();
        break;
    case 'beat':
        beat();
        break;
    default:
        attacking();
        break;
}
function attacking()
{
    global $db, $userid, $ir, $h, $api, $set, $atkpage;
	$_SESSION['attack_scroll']=0;
    $menuhide = 1;                    //Hide the menu so players cannot load other pages,
    //and lessens the chance of a misclick and losing XP.
    $atkpage = 1;
    $tresder = Random(100, 999);    //RNG to prevent refreshing while attacking, thus
    //breaking progression of the attack system.
    $_GET['user'] = (isset($_GET['user']) && is_numeric($_GET['user'])) ? abs($_GET['user']) : '';
	$_GET['scroll'] = (isset($_GET['scroll']) && is_numeric($_GET['scroll'])) ? abs($_GET['scroll']) : '';
    if (empty($_GET['nextstep'])) {
        $_GET['nextstep'] = -1;
    }
    if ($_GET['nextstep'] > 0) {
        $_GET['tresde'] = (isset($_GET['tresde']) && is_numeric($_GET['tresde'])) ? abs($_GET['tresde']) : 0;
        //If RNG is not set, set it to 0.
        if (!isset($_SESSION['tresde'])) {
            $_SESSION['tresde'] = 0;
        }
        //If RNG is not the same number stored in session
        if (($_SESSION['tresde'] == $_GET['tresde']) || $_GET['tresde'] < 100) {
            $_SESSION['attacking'] = 0;
            $_SESSION['attack_scroll'] = 0;
            $ir['attacking'] = 0;
            $api->UserInfoSetStatic($userid, "attacking", 0);
            alert("danger", "Uh Oh!", "Please do not refresh while attacking. Thank you!", true, "attack.php?user={$_GET['user']}");
            die($h->endpage());
        }
		if ($ir['protection'] > time())
		{
			$db->query("UPDATE `user_settings` SET `protection` = 0 WHERE `userid` = {$userid}");
			alert('warning',"Warning!","For attacking this user, you have lost your protection.",false);
		}
        //Set RNG
        $_SESSION['tresde'] = $_GET['tresde'];
    }
	$npcquery = $db->query("/*qc=on*/SELECT * FROM `botlist` WHERE `botuser` = {$_GET['user']}");
	//Opponent is registered on bot list.
	if ($db->num_rows($npcquery) > 0) {
		$results2 = $db->fetch_row($npcquery);
		$timequery = $db->query("/*qc=on*/SELECT `lasthit` FROM `botlist_hits` WHERE `userid` = {$userid} && `botid` = {$_GET['user']}");
		$r2d2 = $db->fetch_single($timequery);
		//Opponent's drop has already been collected and the time hasn't reset.
		if ((time() <= ($r2d2 + $results2['botcooldown'])) && ($r2d2 > 0)) {
			alert("danger", "Uh Oh!", "You have already killed this NPC. Try again in " . ParseTimestamp($results2['botcooldown']) .".", true, 'index.php');
        die($h->endpage());
		}
	}
    //If user is not specified.
    if (!$_GET['user']) {
        alert("danger", "Uh Oh!", "You've chosen to attack a non-existent user. Check your source and try again.", true, 'index.php');
        die($h->endpage());
    } //If the user is trying to attack himself.
    else if ($_GET['user'] == $userid) {
        alert("danger", "Uh Oh!", "Depressed or not, you cannot attack yourself.", true, 'index.php');
        die($h->endpage());
    } //If the user has no HP, and is not already attacking.
    else if ($ir['hp'] <= 1 && $ir['attacking'] == 0) {
        alert("danger", "Uh Oh!", "You have no HP, so you cannot attack. Come back when your HP has refilled.", true, 'index.php');
        die($h->endpage());
    } //If the user has left a previous after losing.
    else if (isset($_SESSION['attacklost']) && $_SESSION['attacklost'] > 1) {
        $_SESSION['attacklost'] = 0;
        alert("danger", "Uh Oh!", "You cannot start another attack after you ran from the last one.", true, 'index.php');
        die($h->endpage());
    }
    $youdata = $ir;
    $laston = time() - 900;
    $q = $db->query("/*qc=on*/SELECT `u`.`userid`, `hp`, `equip_armor`, `username`,
	       `equip_primary`, `equip_secondary`, `equip_potion`, `guild`, `location`, `maxhp`, `class`, 
	       `guard`, `agility`, `strength`, `gender`, `level`, `laston`, `protection`, `display_pic`
			FROM `users` AS `u`
			INNER JOIN `userstats` AS `us` ON `u`.`userid` = `us`.`userid`
			LEFT JOIN `user_settings` AS `uas` ON `u`.`userid` = `uas`.`userid`
			WHERE `u`.`userid` = {$_GET['user']}
			LIMIT 1");
    //Test for if the specified user is a valid and registered user.
    if ($db->num_rows($q) == 0) {
        alert("danger", "Uh Oh!", "The user you are trying to attack does not exist.", true, 'index.php');
        die($h->endpage());
    }
    $odata = $db->fetch_row($q);
    $db->free_result($q);
    //Check current user's last attacked user, and see that its the specified user.
    if ($ir['attacking'] && $ir['attacking'] != $_GET['user']) {
        $_SESSION['attacklost'] = 0;
        alert("danger", "Uh Oh!", "An unknown error has occurred. Please try again, or contact the admin team.", true, 'index.php');
        $api->UserInfoSetStatic($userid, "attacking", 0);
        die($h->endpage());
    }
	if ($_GET['scroll'] > 0)
	{
		if (($ir['location'] + 2) < $odata['location'])
		{
			alert('danger',"Uh Oh!","This user is too far away to use a Distant Attack Scroll!",true,'index.php');
			die($h->endpage());
		}
		elseif (($ir['location'] - 2) > $odata['location'])
		{
			alert('danger',"Uh Oh!","This user is too far away to use a Distant Attack Scroll!",true,'index.php');
			die($h->endpage());
		}
		else
		{
			$_SESSION['attack_scroll']=1;
			$api->UserTakeItem($userid,90,1);
		}
		
	}
	//Perfection skill
	$specialnumber=((getSkillLevel($userid,1)*3)/100);
	if ($ir['class'] == 'Warrior')
	{
		$ir['strength']=$ir['strength']+($ir['strength']*$specialnumber);
	}
	if ($ir['class'] == 'Rogue')
	{
		$ir['agility']=$ir['agility']+($ir['agility']*$specialnumber);
	}
	if ($ir['class'] == 'Guardian')
	{
		$ir['guard']=$ir['guard']+($ir['guard']*$specialnumber);
	}
	$specialnumber2=((getSkillLevel($odata['userid'],1)*3)/100);
	if ($odata['class'] == 'Warrior')
	{
		$odata['strength']=$odata['strength']+($odata['strength']*$specialnumber2);
	}
	if ($odata['class'] == 'Rogue')
	{
		$odata['agility']=$odata['agility']+($odata['agility']*$specialnumber2);
	}
	if ($odata['class'] == 'Guardian')
	{
		$odata['guard']=$odata['guard']+($odata['guard']*$specialnumber2);
	}
    //Check that the opponent has 1 health point.
    if ($odata['hp'] == 1) {
        $_SESSION['attacking'] = 0;
		$_SESSION['attack_scroll'] = 0;
        $ir['attacking'] = 0;
        $api->UserInfoSetStatic($userid, "attacking", 0);
        alert("danger", "Uh Oh!", "{$odata['username']} doesn't have health to be attacked.", true, 'index.php');
        die($h->endpage());
    } //Check if the opponent is currently in the infirmary.
    else if ($api->UserStatus($_GET['user'], 'infirmary') == true) {
        $_SESSION['attacking'] = 0;
		$_SESSION['attack_scroll'] = 0;
        $ir['attacking'] = 0;
        $api->UserInfoSetStatic($userid, "attacking", 0);
        alert("danger", "Uh Oh!", "{$odata['username']} is currently in the infirmary. Try again later.", true, 'index.php');
        die($h->endpage());
    }
	//Check if opponnent has at least 1/2 health
	else if (($api->UserInfoGet($_GET['user'],'hp',true) < 50) && $ir['attacking'] == 0) {
		$_SESSION['attacking'] = 0;
		$_SESSION['attack_scroll'] = 0;
        $ir['attacking'] = 0;
        $api->UserInfoSetStatic($userid, "attacking", 0);
        alert("danger", "Uh Oh!", "{$odata['username']} does not have at least half their health.", true, 'index.php');
        die($h->endpage());
	}
	//Check if the current user is in the infirmary.
    else if ($api->UserStatus($ir['userid'], 'infirmary') == true) {
        $_SESSION['attacking'] = 0;
		$_SESSION['attack_scroll'] = 0;
        $ir['attacking'] = 0;
        $api->UserInfoSetStatic($userid, "attacking", 0);
        alert("danger", "Uh Oh!", "You are currently in the infirmary. Try again after you heal out.", true, 'index.php');
        die($h->endpage());
    } //Check if the opponent is in the dungeon.
    else if ($api->UserStatus($_GET['user'], 'dungeon') == true) {
        $_SESSION['attacking'] = 0;
		$_SESSION['attack_scroll'] = 0;
        $ir['attacking'] = 0;
        $api->UserInfoSetStatic($userid, "attacking", 0);
        alert("danger", "Uh Oh!", "{$odata['username']} is currently in the dungeon. Try again later.", true, 'index.php');
        die($h->endpage());
    } //Check if the current user is in the dungeon.
    else if ($api->UserStatus($userid, 'dungeon') == true) {
        $_SESSION['attacking'] = 0;
		$_SESSION['attack_scroll'] = 0;
        $ir['attacking'] = 0;
        $api->UserInfoSetStatic($userid, "attacking", 0);
        alert("danger", "Uh Oh!", "You are currently in the dungeon. Try again after you've paid your debt to society.", true, 'index.php');
        die($h->endpage());
    } //Check if opponent has permission to be attacked.
    else if (permission('CanBeAttack', $_GET['user']) == false) {
        $_SESSION['attacking'] = 0;
		$_SESSION['attack_scroll'] = 0;
        $ir['attacking'] = 0;
        $api->UserInfoSetStatic($userid, "attacking", 0);
        alert("danger", "Uh Oh!", "Your opponent cannot be attacked this way.", true, 'index.php');
        die($h->endpage());
    } //Check if the current player has permission to attack.
    else if (permission('CanAttack', $userid) == false) {
        $_SESSION['attacking'] = 0;
		$_SESSION['attack_scroll'] = 0;
        $ir['attacking'] = 0;
        $api->UserInfoSetStatic($userid, "attacking", 0);
        alert("danger", "Uh Oh!", "A magical force keeps you from attacking your opponent. (Or anyone, for that matter)", true, 'index.php');
        die($h->endpage());
    } //Check if the opponent is level 2 or lower, and has been on in the last 15 minutes.
    else if ($odata['level'] < 3 && $odata['laston'] > $laston) {
        $_SESSION['attacking'] = 0;
		$_SESSION['attack_scroll'] = 0;
        $ir['attacking'] = 0;
        $api->UserInfoSetStatic($userid, "attacking", 0);
        alert("danger", "Uh Oh!", "You cannot attack online players who are level two or below.", true, 'index.php');
        die($h->endpage());
    }
	else if ($odata['protection'] > time())
	{
		$_SESSION['attacking'] = 0;
		$_SESSION['attack_scroll'] = 0;
        $ir['attacking'] = 0;
        $api->UserInfoSetStatic($userid, "attacking", 0);
        alert("danger", "Uh Oh!", "You cannot attack this player as they have protection.", true, 'index.php');
        die($h->endpage());
	}
    $_GET['weapon'] = (isset($_GET['weapon']) && is_numeric($_GET['weapon'])) ? abs($_GET['weapon']) : '';
    //If weapon is specified via $_GET, attack!!
    if ($_GET['weapon']) {
        if (!$_GET['nextstep']) {
            $_GET['nextstep'] = 1;
        }
        //Check for if current step is greater than the maximum attacks per session.
        if ($_GET['nextstep'] >= $set['MaxAttacksPerSession']) {
            $_SESSION['attacking'] = 0;
			$_SESSION['attack_scroll'] = 0;
            $ir['attacking'] = 0;
            $api->UserInfoSetStatic($userid, "attacking", 0);
            alert("warning", "Uh Oh!", "Get stronger dude. This fight ends in stalemate.", true, 'index.php');
            die($h->endpage());
        }
        //Check if the attack is currently stored in session.
        if ($_SESSION['attacking'] == 0 && $ir['attacking'] == 0) {
            //Check if the current user has enough energy for this attack.
            if ($youdata['energy'] >= $youdata['maxenergy'] / $set['AttackEnergyCost']) {
                $youdata['energy'] -= floor($youdata['maxenergy'] / $set['AttackEnergyCost']);
                $cost = floor($youdata['maxenergy'] / $set['AttackEnergyCost']);
                $api->UserInfoSet($userid, 'energy', "-{$cost}");
                $_SESSION['attackdmg'] = 0;
            } //If not enough energy, stop the fight.
            else {
                $EnergyPercent = floor(100 / $set['AttackEnergyCost']);
                $UserCurrentEnergy = $api->UserInfoGet($userid,'energy',true);
                alert("danger", "Uh Oh!", "Attacking someone requires you to have {$EnergyPercent}% Energy. You currently
				                        only have {$UserCurrentEnergy}%", true, 'index.php');
                die($h->endpage());
            }
        }
        $_SESSION['attacking'] = $odata['userid'];
        $ir['attacking'] = $odata['userid'];
        $api->UserInfoSetStatic($userid, "attacking", $ir['attacking']);
        $_GET['nextstep'] = (isset($_GET['nextstep']) && is_numeric($_GET['nextstep'])) ? abs($_GET['nextstep']) : '';
        //Check if the current user is attacking with a weapon that they have equipped.
        if ($_GET['weapon'] != $ir['equip_primary'] && $_GET['weapon'] != $ir['equip_secondary'] && $_GET['weapon'] != $ir['equip_potion']) {
            alert("danger", "Security Issue!", "You cannot attack with a weapon you don't have equipped... You lost your
			                                experience for that.", true, 'index.php');
            die($h->endpage());
        }
        $winfo_sql = "/*qc=on*/SELECT `itmname`, `weapon`, `ammo`, `itmtype`, `effect1`, `effect2`, `effect3`,  `effect1_on`, `effect2_on`, `effect3_on` FROM `items` WHERE `itmid` = {$_GET['weapon']} LIMIT 1";
        $qo = $db->query($winfo_sql);
        //If the weapon chosen is not a valid weapon.
        if ($db->num_rows($qo) == 0) {
            alert("danger", "Uh Oh!", "The weapon you're trying to attack with isn't valid. This likely means the weapon
			                        you chosen doesn't have a weapon value. Contact the admin team.", true, 'index.php');
            die($h->endpage());
        }
        $r1 = $db->fetch_row($qo);
        $db->free_result($qo);
		$spied=$db->query("/*qc=on*/SELECT `user` FROM `spy_advantage` WHERE `user` = {$userid} and `spied` = {$_GET['user']}");
		if (($db->num_rows($spied) > 0) && ($_GET['nextstep'] == 1))
		{
			$ir['strength']=$ir['strength']+($ir['strength']*0.1);
			$ir['agility']=$ir['agility']+($ir['agility']*0.1);
		}
		//Sharper blades skill
		$specialnumber=((getSkillLevel($userid,9)*13)/100);
		$r1['weapon']=$r1['weapon']+($r1['weapon']*$specialnumber);
		$mydamage = round(($r1['weapon'] * $youdata['strength'] / ($odata['guard'] / 1.5)) * (Random(10000, 12000) / 10000));
		$hitratio = max(10, min(60 * $ir['agility'] / $odata['agility'], 95));
		$ttu='';
        //Item used is a potion... so don't do damage.
        if (($r1['itmtype'] == 8) || ($r1['itmtype'] == 7))
        {
            $dodamage=false;
            $usepotion=true;
        }
        else
        {
            $dodamage=true;
            $usepotion=false;
        }
		if ($r1['ammo'] > 0)
		{
			if (!$api->UserHasItem($userid,$r1['ammo'],1))
			{
				if ($_GET['weapon'] == $ir['equip_primary'])
				{
					$ir['equip_primary'] = 0;
					$slot='equip_primary';
				}
				if ($_GET['weapon'] == $ir['equip_secondary'])
				{
					$ir['equip_secondary'] = 0;
					$slot='equip_secondary';
				}
				$sbq=$db->query("/*qc=on*/SELECT * FROM `equip_gains` WHERE `userid` = {$userid} and `slot` = '{$slot}'");
				$statloss='';
				if ($db->num_rows($sbq) > 0)
				{
					$statloss .= "You have lost ";
					while ($sbr=$db->fetch_row($sbq))
					{
						if ($sbr['direction'] == 'pos')
						{
							if (in_array($sbr['stat'], array('strength', 'agility', 'guard', 'labour', 'iq'))) {
								$db->query("UPDATE `userstats` SET `{$sbr['stat']}` = `{$sbr['stat']}` - {$sbr['number']} WHERE `userid` = {$userid}");
							} elseif (!(in_array($sbr['stat'], array('dungeon', 'infirmary')))) {
								$db->query("UPDATE `users` SET `{$sbr['stat']}` = `{$sbr['stat']}` - {$sbr['number']} WHERE `userid` = {$userid}");
							}
						}
						else
						{
							if (in_array($sbr['stat'], array('strength', 'agility', 'guard', 'labour', 'iq'))) {
								$db->query("UPDATE `userstats` SET `{$sbr['stat']}` = `{$sbr['stat']}` + {$sbr['number']} WHERE `userid` = {$userid}");
							} elseif (!(in_array($sbr['stat'], array('dungeon', 'infirmary')))) {
								$db->query("UPDATE `users` SET `{$sbr['stat']}` = `{$sbr['stat']}` + {$sbr['number']} WHERE `userid` = {$userid}");
							}
						}
						$ir[$sbr['stat']] = $ir[$sbr['stat']]-$sbr['number'];
						$statloss .= "{$sbr['number']} {$sbr['stat']}, ";
						$db->query("DELETE FROM `equip_gains` WHERE `userid` = {$userid} AND `stat` = '{$sbr['stat']}' AND `slot` = '{$slot}'");
					}
					$statloss .= "when you unequipped this item.";
				}
				alert('danger',"Uh Oh!","You need at least one {$api->SystemItemIDtoName($r1['ammo'])} to use your {$api->SystemItemIDtoName($_GET['weapon'])}. {$statloss} It has been unequipped and moved to your inventory.",false);
				$dodamage=false;
				$db->query("UPDATE `users` SET `equip_primary` = 0 WHERE `equip_primary` = {$_GET['weapon']} AND `userid` = {$userid}");
				$db->query("UPDATE `users` SET `equip_secondary` = 0 WHERE `equip_secondary` = {$_GET['weapon']} AND `userid` = {$userid}");
				$api->UserGiveItem($userid,$_GET['weapon'],1);
			}
			else
			{
			    //Ammo dispensery skill
			    if (getSkillLevel($userid,7) == 0)
				    $api->UserTakeItem($userid,$r1['ammo'],1);
			    else
			    {
			        if (Random(1,4) != 1)
			        {
			            $api->UserTakeItem($userid,$r1['ammo'],1);
			        }
			    }
				$ttu="You take aim with your {$api->SystemItemIDtoName($_GET['weapon'])} and fire.";
			}
		}
		$missed=0;
        //If the attack attempt was connected.
		if ($dodamage)
		{
			if (Random(1, 100) <= $hitratio) {
				//If the opponent has armor equipped.
				if ($odata['equip_armor'] > 0) {
					$armorinfo_sql = "/*qc=on*/SELECT `armor` FROM `items` WHERE `itmid` = {$odata['equip_armor']} LIMIT 1";
					$q3 = $db->query($armorinfo_sql);
					//Check that the armor is valid.
					if ($db->num_rows($q3) > 0) {
					    //Thickened Skin skill
						$specialnumb=((getSkillLevel($odata['userid'],6)*6.5)/100);
						$mydamage -= $db->fetch_single($q3)-($db->fetch_single($q3)*$specialnumb);
					}
					$db->free_result($q3);
				}
				$theirbeforehp = $odata['hp'];
				//Fix damage...
				if ($mydamage < -100000) {
					$mydamage = abs($mydamage);
				} else if ($mydamage < 1) {
					$mydamage = 1;
				}
				$crit = Random(1, 40);
				//If user makes a critical hit, multiply damage.
				if ($crit == 17) {
					$mydamage *= Random(20, 40) / 10;
				} //If unlucky crit... reduce damage.
				else if ($crit == 25 OR $crit == 8) {
					$mydamage /= (Random(20, 40) / 10);
				}
				if ($mydamage > $theirbeforehp) {
					$mydamage = $odata['hp'];
				}
				$mydamage = round($mydamage);
				$odata['hp'] -= $mydamage;
				if ($odata['hp'] == 1) {
					$odata['hp'] = 0;
					$mydamage += 1;
				}
				if ($r1['ammo'] > 0)
				{
    				//True Shot skill
    				$yourbowdamage=((getSkillLevel($userid,4)*5)/100);
    				$mydamage=$mydamage+($mydamage*$yourbowdamage);
				}
			}
			else
			{
				$missed=1;
			}
		}
		else
		{
			$mydamage=0;
		}
        if (!$usepotion)
        {
            if ($missed == 1)
            {
                alert('warning', "Attempt {$_GET['nextstep']}!", "{$ttu} You attempt to strike {$odata['username']} but missed. Your
                    opponent has {$odata['hp']} HP Remaining.", false, '', true);
            }
            else
            {
                //Reduce health.
                $db->query("UPDATE `users` SET `hp` = `hp` - {$mydamage} WHERE `userid` = {$_GET['user']}");
                $db->query("DELETE FROM `spy_advantage` WHERE `user` = {$userid} AND `spied` = {$_GET['user']}");
                alert('success', "Attempt {$_GET['nextstep']}!", "{$ttu} Using your {$r1['itmname']} you manage to strike
                {$odata['username']} dealing {$mydamage} damage. Your opponent has {$odata['hp']} HP remaining.", false, '', true);
                $_SESSION['attackdmg'] += $mydamage;
                user_log($userid,'dmgdone',$mydamage);
            }
        }
        else
        {
            if ($api->UserHasItem($userid,$_GET['weapon'],1))
            {
                for ($enum = 1; $enum <= 3; $enum++) {
                    if ($r1["effect{$enum}_on"] == 'true') {
                        $einfo = unserialize($r1["effect{$enum}"]);
                        if ($einfo['inc_type'] == "percent") {
                            if (in_array($einfo['stat'], array('energy', 'will', 'brave', 'hp'))) {
                                $inc = round($ir['max' . $einfo['stat']] / 100 * $einfo['inc_amount']);
                                //Potent Potion
                                $specialnumber=((getSkillLevel($userid,3)*1.5)/100);
                                $inc=$inc+($inc*$specialnumber);
                            } elseif (in_array($einfo['stat'], array('dungeon', 'infirmary'))) {
                                $EndTime = $db->fetch_single($db->query("/*qc=on*/SELECT `{$einfo['stat']}_out` FROM `{$einfo['stat']}` WHERE `{$einfo['stat']}_user` = {$userid}"));
                                $inc = round((($EndTime - $Time) / 100 * $einfo['inc_amount']) / 60);
                                //Potent Potion
                                $specialnumber=((getSkillLevel($userid,3)*1.5)/100);
                                $inc=$inc+($inc*$specialnumber);
                            } else {
                                $inc = round($ir[$einfo['stat']] / 100 * $einfo['inc_amount']);
                                //Potent Potion
                                $specialnumber=((getSkillLevel($userid,3)*1.5)/100);
                                $inc=$inc+($inc*$specialnumber);
                            }
                        } else {
                            $inc = $einfo['inc_amount'];
                            //Potent Potion
                            $specialnumber=((getSkillLevel($userid,3)*1.5)/100);
                            $inc=$inc+($inc*$specialnumber);
                        }
                        if ($einfo['dir'] == "pos") {
                            if (in_array($einfo['stat'], array('energy', 'will', 'brave', 'hp'))) {
                                $ir[$einfo['stat']] = min($ir[$einfo['stat']] + $inc, $ir['max' . $einfo['stat']]);
                            } elseif ($einfo['stat'] == 'infirmary') {
                                put_infirmary($userid, $inc, 'Item Misuse');
                            } elseif ($einfo['stat'] == 'dungeon') {
                                put_dungeon($userid, $inc, 'Item Misuse');
                            } else {
                                $ir[$einfo['stat']] += $inc;
                            }
                        } else {
                            if ($einfo['stat'] == 'infirmary') {
                                if (user_infirmary($userid) == true) {
                                    remove_infirmary($userid, $inc);
                                }
                            } elseif ($einfo['stat'] == 'dungeon') {
                                if (user_dungeon($userid) == true) {
                                    remove_dungeon($userid, $inc);
                                }
                            } else {
                                $ir[$einfo['stat']] = max($ir[$einfo['stat']] - $inc, 0);
                            }
                        }
                        if (!(in_array($einfo['stat'], array('dungeon', 'infirmary')))) {
                            $upd = $ir[$einfo['stat']];
                        }
                        if (in_array($einfo['stat'], array('strength', 'agility', 'guard', 'labour', 'iq', 'luck'))) {
                            $db->query("UPDATE `userstats` SET `{$einfo['stat']}` = '{$upd}' WHERE `userid` = {$userid}");
                        } elseif (!(in_array($einfo['stat'], array('dungeon', 'infirmary')))) {
                            $db->query("UPDATE `users` SET `{$einfo['stat']}` = '{$upd}' WHERE `userid` = {$userid}");
                        }
                    }
                }
                alert('success', "Attempt {$_GET['nextstep']}!", "{$ttu} You consume your {$r1['itmname']}.", false, '', true);
                $api->UserTakeItem($userid,$_GET['weapon'],1);
            }
            else
            {
                alert('warning',"Warning!","You do not have enough of {$api->SystemItemIDtoName($_GET['weapon'])} to use.",false);
                $db->query("UPDATE `users` SET `equip_potion` = 0 WHERE `userid` = {$userid}");
            }
        }
        //Win fight because opponent's health is 0 or lower.
        if ($odata['hp'] <= 0) {
            $odata['hp'] = 0;
            $_SESSION['attackwon'] = $_GET['user'];
            $api->UserInfoSet($_GET['user'], 'hp', 0);
            echo "<br />
			<b>You have struck down {$odata['username']}. What do you wish to do to them now?</b><br />
			<form action='?action=mug&ID={$_GET['user']}' method='post'><input class='btn btn-primary' type='submit' value='Rob Them' /></form><br />
			<form action='?action=beat&ID={$_GET['user']}' method='post'><input class='btn btn-primary' type='submit' value='Increase Infirmary Time' /></form><br />
			<form action='?action=xp&ID={$_GET['user']}' method='post'><input class='btn btn-primary' type='submit' value='Gain Experience' /></form>";
        } //The opponent is not down... he gets to attack.
        else {
            $eq = $db->query("/*qc=on*/SELECT `itmname`,`weapon`,`ammo`,`itmid`,`itmtype`, `effect1`, `effect2`, `effect3`,  `effect1_on`, `effect2_on`, `effect3_on` FROM  `items` WHERE `itmid` IN({$odata['equip_primary']}, {$odata['equip_secondary']}, {$odata['equip_potion']})");
            //If opponent does not have a valid weapon equipped, make them punch with fists.
            if ($db->num_rows($eq) == 0) {
                $wep = "Fists";
                $dam = round(round((($odata['strength'] / $ir['guard'] / 100)) + 1) * (Random(10000, 12000) / 10000));
                $theydodmg=true;
                $theyusepotion=false;
            } else {
                $cnt = 0;
                while ($r = $db->fetch_row($eq)) {
                    $enweps[] = $r;
                    $cnt++;
                }
                $db->free_result($eq);
                $weptouse = Random(0, $cnt - 1);    //Select opponent weapon to use.
                $wep = $enweps[$weptouse]['itmname'];
                //Sharper blades skill
                $specialnumber=((getSkillLevel($_GET['user'],9)*13)/100);
                $enweps[$weptouse]['weapon']=$enweps[$weptouse]['weapon']+($enweps[$weptouse]['weapon']*$specialnumber);
                
                $dam = round(($enweps[$weptouse]['weapon'] * $odata['strength'] / ($youdata['guard'] / 1.5)) * (Random(10000, 12000) / 10000));
				//Item used is a potion... so don't do damage.
                if (($enweps[$weptouse]['itmtype'] == 8) || ($enweps[$weptouse]['itmtype'] == 7))
                {
                    $theydodmg=false;
                    $theyusepotion=true;
                }
                else
                {
                    $theydodmg=true;
                    $theyusepotion=false;
                }
                if ($enweps[$weptouse]['ammo'] > 0)
                {
                    if (!$api->UserHasItem($_GET['user'],$enweps[$weptouse]['ammo'],1))
                    {
                        $theydodmg=false;
                        $ouq=$db->query("/*qc=on*/SELECT `equip_primary`,`equip_secondary`,`equip_potion` FROM `users` WHERE `userid` = {$_GET['user']}");
                        $our=$db->fetch_row($ouq);
                        if ($enweps[$weptouse]['itmid'] == $our['equip_primary'])
                        {
                            $slot = 'equip_primary';
                        }
                        elseif ($enweps[$weptouse]['itmid'] == $our['equip_secondary'])
                        {
                            $slot = 'equip_secondary';
                        }
                        elseif ($enweps[$weptouse]['itmid'] == $our['equip_potion'])
                        {
                            $slot = 'equip_potion';
                        }
                        $sbq=$db->query("/*qc=on*/SELECT * FROM `equip_gains` WHERE `userid` = {$_GET['user']} and `slot` = '{$slot}'");
                        $statloss='';
                        if ($db->num_rows($sbq) > 0)
                        {
                            $statloss .= "You have lost ";
                            while ($sbr=$db->fetch_row($sbq))
                            {
                                if ($sbr['direction'] == 'pos')
                                {
                                    if (in_array($sbr['stat'], array('strength', 'agility', 'guard', 'labour', 'iq'))) {
                                        $db->query("UPDATE `userstats` SET `{$sbr['stat']}` = `{$sbr['stat']}` - {$sbr['number']} WHERE `userid` = {$_GET['user']}");
                                    } elseif (!(in_array($sbr['stat'], array('dungeon', 'infirmary')))) {
                                        $db->query("UPDATE `users` SET `{$sbr['stat']}` = `{$sbr['stat']}` - {$sbr['number']} WHERE `userid` = {$_GET['user']}");
                                    }
                                }
                                else
                                {
                                    if (in_array($sbr['stat'], array('strength', 'agility', 'guard', 'labour', 'iq'))) {
                                        $db->query("UPDATE `userstats` SET `{$sbr['stat']}` = `{$sbr['stat']}` + {$sbr['number']} WHERE `userid` = {$userid}");
                                    } elseif (!(in_array($sbr['stat'], array('dungeon', 'infirmary')))) {
                                        $db->query("UPDATE `users` SET `{$sbr['stat']}` = `{$sbr['stat']}` + {$sbr['number']} WHERE `userid` = {$userid}");
                                    }
                                }
                                $ir[$sbr['stat']] = $ir[$sbr['stat']]-$sbr['number'];
                                $statloss .= "{$sbr['number']} {$sbr['stat']}, ";
                                $db->query("DELETE FROM `equip_gains` WHERE `userid` = {$_GET['user']} AND `stat` = '{$sbr['stat']}' AND `slot` = '{$slot}'");
                            }
                            $statloss .= "when you unequipped this item.";
                            $db->query("UPDATE `users` SET `equip_primary` = 0 WHERE `equip_primary` = {$enweps[$weptouse]['itmid']} AND `userid` = {$_GET['user']}");
                            $db->query("UPDATE `users` SET `equip_secondary` = 0 WHERE `equip_secondary` = {$enweps[$weptouse]['itmid']} AND `userid` = {$_GET['user']}");
                            $api->UserGiveItem($_GET['user'],$enweps[$weptouse]['itmid'],1);
                            $api->GameAddNotification($_GET['user'],"You have ran out of ammo for your {$wep}. {$statloss} It has been unequipped and moved to your inventory.");
                        }
                        else
                        {
                            //Ammo dispensery skill
                            if (getSkillLevel($_GET['user'],7) == 0)
                                $api->UserTakeItem($_GET['user'],$enweps[$weptouse]['ammo'],1);
                            else
                            {
                                if (Random(1,4) != 1)
                                {
                                    $api->UserTakeItem($_GET['user'],$enweps[$weptouse]['ammo'],1);
                                }
                            }
                        }
                    }
                }
            }
                $miss=0;
                if ($theydodmg)
                {
                    $hitratio = max(10, min(60 * $odata['agility'] / $ir['agility'], 95));
                    //If hit connects with user.
                    if (Random(1, 100) <= $hitratio) {
                        //If user has armor equipped.
                        if ($ir['equip_armor'] > 0) {
                            $q3 = $db->query("/*qc=on*/SELECT `armor` FROM `items` WHERE `itmid` = {$ir['equip_armor']} LIMIT 1");
                            //If user has valid armor equipped.
                            if ($db->num_rows($q3) > 0) {
                                $specialnumb2=((getSkillLevel($ir['userid'],6)*7.5)/100);
                                $dam -= $db->fetch_single($q3)-($db->fetch_single($q3)*$specialnumb2);
                            }
                            $db->free_result($q3);
                        }
                        $yourbeforehp = $ir['hp'];
                        //If the user doesn't have armor equipped.
                        if ($dam < -100000) {
                            $dam = abs($dam);
                        } //If damage is lower than 1, set to 1.
                        else if ($dam < 1) {
                            $dam = 1;
                        }
                        $crit = Random(1, 40);
                        //RNG for critical damage, multiply damage!
                        if ($crit == 17) {
                            $dam *= Random(20, 40) / 10;
                        } //Unlucky... crit is weak.
                        else if ($crit == 25 OR $crit == 8) {
                            $dam /= (Random(20, 40) / 10);
                        }
                        $dam = round($dam);
                        $youdata['hp'] -= $dam;
                        //If user has 1 hp.
                        if ($youdata['hp'] == 1) {
                            $dam += 1;
                            $youdata['hp'] = 0;
                        }
                        if (($ir['hp'] - $dam) < 0) {
                            $dam = $ir['hp'];
                            $youdata['hp'] = 0;
                        }
                        //If user HP less than 0, set to 0.
                        if ($ir['hp'] < 0) {
                            $ir['hp'] = 0;
                        }
                        if ($dam > $yourbeforehp) {
                            $dam = $ir['hp'];
                        }
                        if ($enweps[$weptouse]['ammo'] > 0)
                        {
                            //True Shot skill
                            $theirbowdamage=((getSkillLevel($_GET['user'],4)*5)/100);
                            $dam=$dam+($dam*$theirbowdamage);
                        }
                    } //Opponent misses their hit.
                    else {
                        $miss=1;
                    }
                }
                else
                {
                    $dam=0;
                }
                $ns = $_GET['nextstep'] + 1;
                if (!$theyusepotion)
                {
                    if ($miss == 1)
                    {
                        alert('info', "Attempt {$ns}!", "{$odata['username']} attempted to strike you, but missed. You have {$youdata['hp']} HP remaining.", false);
                    }
                    else
                    {
                        $db->query("UPDATE `users` SET `hp` = `hp` - {$dam} WHERE `userid` = {$userid}");
                        alert('danger', "Attempt {$ns}!", "Using their {$wep}, {$odata['username']} managed to strike you dealing
                         {$dam} damage. You have {$youdata['hp']} HP remaining.", false, '', true);
                         user_log($_GET['user'],'dmgdone',$dam);
                    }
                }
                else
                {
                    if ($api->UserHasItem($_GET['user'],$enweps[$weptouse]['itmid'],1))
                    {
                        for ($enum = 1; $enum <= 3; $enum++) {
                            if ($enweps[$weptouse]["effect{$enum}_on"] == 'true') {
                                $einfo = unserialize($enweps[$weptouse]["effect{$enum}"]);
                                if ($einfo['inc_type'] == "percent") {
                                    if (in_array($einfo['stat'], array('energy', 'will', 'brave', 'hp'))) {
                                        $inc = round($odata['max' . $einfo['stat']] / 100 * $einfo['inc_amount']);
                                        //Potent Potion
                                        $specialnumber=((getSkillLevel($_GET['user'],3)*1.5)/100);
                                        $inc=$inc+($inc*$specialnumber);
                                    } elseif (in_array($einfo['stat'], array('dungeon', 'infirmary'))) {
                                        $EndTime = $db->fetch_single($db->query("/*qc=on*/SELECT `{$einfo['stat']}_out` FROM `{$einfo['stat']}` WHERE `{$einfo['stat']}_user` = {$userid}"));
                                        $inc = round((($EndTime - $Time) / 100 * $einfo['inc_amount']) / 60);
                                        //Potent Potion
                                        $specialnumber=((getSkillLevel($_GET['user'],3)*1.5)/100);
                                        $inc=$inc+($inc*$specialnumber);
                                    } else {
                                        $inc = round($odata[$einfo['stat']] / 100 * $einfo['inc_amount']);
                                        //Potent Potion
                                        $specialnumber=((getSkillLevel($_GET['user'],3)*1.5)/100);
                                        $inc=$inc+($inc*$specialnumber);
                                    }
                                } else {
                                    $inc = $einfo['inc_amount'];
                                    //Potent Potion
                                    $specialnumber=((getSkillLevel($_GET['user'],3)*1.5)/100);
                                    $inc=$inc+($inc*$specialnumber);
                                }
                                if ($einfo['dir'] == "pos") {
                                    if (in_array($einfo['stat'], array('energy', 'will', 'brave', 'hp'))) {
                                        $odata[$einfo['stat']] = min($odata[$einfo['stat']] + $inc, $odata['max' . $einfo['stat']]);
                                    } elseif ($einfo['stat'] == 'infirmary') {
                                        put_infirmary($_GET['user'], $inc, 'Item Misuse');
                                    } elseif ($einfo['stat'] == 'dungeon') {
                                        put_dungeon($_GET['user'], $inc, 'Item Misuse');
                                    } else {
                                        $odata[$einfo['stat']] += $inc;
                                    }
                                } else {
                                    if ($einfo['stat'] == 'infirmary') {
                                        if (user_infirmary($_GET['user']) == true) {
                                            remove_infirmary($_GET['user'], $inc);
                                        }
                                    } elseif ($einfo['stat'] == 'dungeon') {
                                        if (user_dungeon($_GET['user']) == true) {
                                            remove_dungeon($_GET['user'], $inc);
                                        }
                                    } else {
                                        $odata[$einfo['stat']] = max($odata[$einfo['stat']] - $inc, 0);
                                    }
                                }
                                if (!(in_array($einfo['stat'], array('dungeon', 'infirmary')))) {
                                    $upd = $odata[$einfo['stat']];
                                }
                                if (in_array($einfo['stat'], array('strength', 'agility', 'guard', 'labour', 'iq', 'luck'))) {
                                    $db->query("UPDATE `userstats` SET `{$einfo['stat']}` = '{$upd}' WHERE `userid` = {$_GET['user']}");
                                } elseif (!(in_array($einfo['stat'], array('dungeon', 'infirmary')))) {
                                    $db->query("UPDATE `users` SET `{$einfo['stat']}` = '{$upd}' WHERE `userid` = {$_GET['user']}");
                                }
                            }
                        }
                        alert('danger', "Attempt {$ns}!", "{$odata['username']} consumes {$wep}!", false, '', true);
                        $api->UserTakeItem($_GET['user'],$enweps[$weptouse]['itmid'],1);
                    }
                    else
                    {
                        $api->GameAddNotification($_GET['user'],"You ran out of your potion in combat.");
                        alert('info', "Attempt {$ns}!", "{$odata['username']} attempted to strike you, but missed. You have {$youdata['hp']} HP remaining.", false);
                        $db->query("UPDATE `users` SET `equip_potion` = 0 WHERE `userid` = {$_GET['user']}");
                    }
                }
                //User has no HP left, redirect to loss.
                if ($youdata['hp'] <= 0) {
                    $youdata['hp'] = 0;
                    $_SESSION['attacklost'] = $_GET['user'];
                    $api->UserInfoSet($userid, "hp", 0);
                    echo "<form action='?action=lost&ID={$_GET['user']}' method='post'><input type='submit' class='btn btn-primary' value='Lose Fight' />";
                }
            }
	}	//Opponent has less than 5 HP, fight cannot start.
    else if ($odata['hp'] < 5) {
		$_SESSION['attacking'] = 0;
		$_SESSION['attack_scroll'] = 0;
        $ir['attacking'] = 0;
        alert("danger", "Uh Oh!", "{$odata['username']}'s health is too low to be attacked.", true, 'index.php');
        die($h->endpage());
    } //Stop combat if user and opponent are in same guild.
    else if ($ir['guild'] == $odata['guild'] && $ir['guild'] > 0) {
        alert("danger", "Uh Oh!", "Hit the bong, not your guild mates. {$odata['username']} is in the same guild as you!", true, 'index.php');
        die($h->endpage());
    } //If user does not have enough energy.
    else if ($youdata['energy'] < $youdata['maxenergy'] / $set['AttackEnergyCost']) {
        $EnergyPercent = floor(100 / $set['AttackEnergyCost']);
        $UserCurrentEnergy = $api->UserInfoGet($userid,'energy',true);
        alert("danger", "Uh Oh!", "You need to have {$EnergyPercent}% Energy to attack someone. You only have
		                        {$UserCurrentEnergy}%", true, 'index.php');
        die($h->endpage());
    } //If user and opponent are in different towns.
    else if (($youdata['location'] != $odata['location']) && $_SESSION['attack_scroll'] == 0) {
        alert("danger", "Uh Oh!", "You and your opponent are in different towns.", true, 'index.php');
        if ($api->UserHasItem($userid,90,1))
		{
			echo "<br />[<a href='?user={$_GET['user']}&scroll=1'>Use Distant Attack Scroll</a>]";
		}
		
		die($h->endpage());
    }
    //If opponent or user have no HP.
    if ($youdata['hp'] <= 0 OR $odata['hp'] <= 0) {
        //lol no
    } else {
        $vars['hpperc'] = round($youdata['hp'] / $youdata['maxhp'] * 100);
        $vars['hpopp'] = 100 - $vars['hpperc'];
        $vars2['hpperc'] = round($odata['hp'] / $odata['maxhp'] * 100);
        $vars2['hpopp'] = 100 - $vars2['hpperc'];
        $mw = $db->query("/*qc=on*/SELECT `itmid`,`itmname` FROM  `items`  WHERE `itmid` IN({$ir['equip_primary']}, {$ir['equip_secondary']}, {$ir['equip_potion']})");
        echo "<table class='table table-bordered'>
		<tr>
			<th colspan='3'>
				Choose a weapon to attack with.
			</th>
		</tr>";
        //If user has weapons equipped, allow him to select one.
        if ($db->num_rows($mw) > 0) {
            while ($r = $db->fetch_row($mw)) {
                if (!isset($_GET['nextstep'])) {
                    $ns = 1;
                } else {
                    $ns = $_GET['nextstep'] + 2;
                }
                if ($r['itmid'] == $ir['equip_primary']) {
                    echo "<tr><th align='left'>Primary Weapon</th>";
                }
                if ($r['itmid'] == $ir['equip_secondary']) {
                    echo "<tr><th align='left'>Secondary Weapon</th>";
                }
                if ($r['itmid'] == $ir['equip_potion']) {
                    echo "<tr><th align='left'>Potion</th>";
                }
                echo "<td>" . returnIcon($r['itmid'],2) . "</td><td align='left'><a href='?nextstep={$ns}&user={$_GET['user']}&weapon={$r['itmid']}&tresde={$tresder}'>{$r['itmname']}</a></td></tr>";
            }
        } //If no weapons equipped, tell him to get back!
        else {
            alert("warning", "Uh Oh!", "Sir, you don't have a weapon equipped. You might wanna go back.", true, 'index.php');
        }
        $db->free_result($mw);
        echo "</table>";
		
		$yourpic = ($ir['display_pic']) ? "<img src='{$ir['display_pic']}' class='img-thumbnail img-responsive' width='125'>" : "";
		$theirpic = ($odata['display_pic']) ? "<img src='{$odata['display_pic']}' class='img-thumbnail img-responsive' width='125'>" : "";
        echo "
		<table class='table table-bordered'>
			<tr>
				<th width='25%' class='align-middle'>
					{$yourpic}<br />{$ir['username']}
				</th>
				<td class='align-middle'>
					<div class='progress' style='height: 1rem;'>
						<div class='progress-bar bg-success' role='progressbar' aria-valuenow='{$vars['hpperc']}' aria-valuemin='0' aria-valuemax='100' style='width:{$vars['hpperc']}%'></div>
							<span>{$vars['hpperc']}% ({$youdata['hp']} / {$youdata['maxhp']})</span>
					</div>
				</td>
			</tr>
			<tr>
				<th class='align-middle'>
					{$theirpic}<br />{$odata['username']}
				</th>
				<td class='align-middle'>
					<div class='progress' style='height: 1rem;'>
						<div class='progress-bar bg-success' role='progressbar' aria-valuenow='{$vars2['hpperc']}' aria-valuemin='0' aria-valuemax='100' style='width:{$vars2['hpperc']}%'></div>
							<span>{$vars2['hpperc']}% ({$odata['hp']} / {$odata['maxhp']})</span>
					</div>
				</td>
			</tr>
		</table>";
    }
}

function beat()
{
    global $db, $userid, $ir, $h, $api;
    $_GET['ID'] = isset($_GET['ID']) && ctype_digit($_GET['ID']) ? $_GET['ID'] : 0;
    $_SESSION['attacking'] = 0;
	$_SESSION['attack_scroll'] = 0;
    $ir['attacking'] = 0;
    $api->UserInfoSetStatic($userid, "attacking", 0);
    $od = $db->query("/*qc=on*/SELECT * FROM `users` WHERE `userid` = {$_GET['ID']}");
    //User attempts to win a fight they didn't win.
    if (!isset($_SESSION['attackwon']) || $_SESSION['attackwon'] != $_GET['ID']) {
        $api->UserInfoSet($userid, "xp", 0);
        alert("danger", "Security Issue!", "You did not beat this player. You've lost all your experience for that.", true, 'index.php');
        die($h->endpage());
    }
    //The opponent does not exist.
    if (!$db->num_rows($od)) {
        $api->UserInfoSet($userid, "xp", 0);
        alert('danger', "Uh Oh!", "You are trying to beat a non-existent user. You've lost all your experience for that.", true, 'index.php');
        die($h->endpage());
    }
    if ($db->num_rows($od) > 0) {
        $r = $db->fetch_row($od);
        $db->free_result($od);
        //Opponent's HP is 1, meaning the user has already claimed victory.
        if ($r['hp'] == 1) {
            alert('danger', "Uh Oh!", "Your opponent was already beat. Maybe next time.", true, 'index.php');
            die($h->endpage());
        }
        $hosptime = Random(75, 175) + floor($ir['level'] / 2);
        $hospreason = $db->escape("Hospitalized By <a href='profile.php?user={$userid}'>{$ir['username']}</a>");
        //Set opponent's HP to 1. Means fight is over.
        $api->UserInfoSet($r['userid'], "hp", 1);
        //Place opponent in infirmary.
        $api->UserStatusSet($r['userid'], 'infirmary', $hosptime, $hospreason);
        //Give opponent notification that they were attacked.
        $api->GameAddNotification($r['userid'], "You were hospitalized by <a href='profile.php?user=$userid'>{$ir['username']}</a>
                                                    for {$hosptime} minutes.");
        //Log that the user won the fight.
        $api->SystemLogsAdd($userid, 'attacking', "Hospitalized <a href='profile.php?user={$r['userid']}'>{$r['username']}</a> [{$_GET['ID']}]
                                                for {$hosptime} minutes.");
        //Log that the opponent lost the fight.
        $api->SystemLogsAdd($_GET['ID'], 'attacking', "Hospitalized by <a href='profile.php?user={$userid}'>{$ir['username']}</a>
                                                    [{$userid}] for {$hosptime} minutes.");
        $_SESSION['attackwon'] = false;
        $additionaltext = "";
        //Both players are in a guild.
        if ($ir['guild'] > 0 && $r['guild'] > 0) {
            $oppguild = $db->query("/*qc=on*/SELECT `guild_id` FROM `guild` WHERE `guild_id` = {$r['guild']} LIMIT 1");
            if ($db->num_rows($oppguild) > 0) {
                $warq = $db->query("/*qc=on*/SELECT `gw_id` FROM `guild_wars`
				WHERE `gw_winner` = 0 AND (`gw_declarer` = {$ir['guild']} AND `gw_declaree` = {$r['guild']})
				OR (`gw_declaree` = {$ir['guild']} AND `gw_declarer` = {$r['guild']})");
                //Both players' guilds are at war with each other.
                if ($db->num_rows($warq) > 0) {
                    $wr = $db->fetch_single($warq);
                    $whoswho = $db->fetch_row($db->query("/*qc=on*/SELECT `gw_declarer`, `gw_declaree` FROM `guild_wars` WHERE `gw_id` = {$wr}"));
                    //Give points to user's guild.
                    if ($whoswho['gw_declarer'] == $ir['guild']) {
                        $db->query("UPDATE `guild_wars` SET `gw_drpoints` = `gw_drpoints` + 3 WHERE `gw_id` = {$wr}");
                    } else {
                        $db->query("UPDATE `guild_wars` SET `gw_depoints` = `gw_depoints` + 3 WHERE `gw_id` = {$wr}");
                    }
                    $additionaltext .= "By winning this fight, you've earned your guild a point in the guild war.";
					guilddebt($ir['guild'],$r['guild']);
                }
            }

        }
        //Bounty hunter stuff!
        $bhq=$db->query("/*qc=on*/SELECT * FROM `bounty_hunter` WHERE `bh_user` = {$r['userid']}");
        if ($db->num_rows($bhq) > 0)
        {
            $bhr=$db->fetch_row($bhq);
            //Double the infirmary time.
            put_infirmary($r['userid'], $hosptime, $hospreason);
            $api->GameAddNotification($r['userid'], "Your infirmary time was doubled since you had a bounty on your head.");
            $api->UserGiveCurrency($userid,'primary',$bhr['bh_bounty']);
            $bounty_format=number_format($bhr['bh_bounty']);
            $db->query("DELETE FROM `bounty_hunter` WHERE `bh_id` = {$bhr['bh_id']}");
            $additionaltext .= " You have received {$bounty_format} Copper Coins for hospitalizing this user while they had a bounty on their head. They also received double infirmary time.";
        }
        //Tell player they won and ended the fight, and if they gained a guild war point.
        alert('success', "You've Bested {$r['username']}!!", "Your actions have caused {$r['username']} {$hosptime} minutes in the infirmary. {$additionaltext}", true, 'index.php');
		attacklog($userid,$r['userid'],'beatup');
        $db->query("UPDATE `users` SET `kills` = `kills` + 1 WHERE `userid` = {$userid}");
        $db->query("UPDATE `users` SET `deaths` = `deaths` + 1 WHERE `userid` = {$r['userid']}");
        //Opponent an NPC? Set their HP to 100%, and remove infirmary time.
        if ($r['user_level'] == 'NPC') {
            $db->query("UPDATE `users` SET `hp` = `maxhp` WHERE `userid` = {$r['userid']}");
            $db->query("UPDATE `infirmary` SET `infirmary_out` = 0 WHERE `infirmary_user` ={$r['userid']}");

        }
		$npcquery = $db->query("/*qc=on*/SELECT * FROM `botlist` WHERE `botuser` = {$r['userid']}");
		//Opponent is registered on bot list.
		if ($db->num_rows($npcquery) > 0) {
			$results2 = $db->fetch_row($npcquery);
			$timequery = $db->query("/*qc=on*/SELECT `lasthit` FROM `botlist_hits` WHERE `userid` = {$userid} && `botid` = {$r['userid']}");
			$r2 = $db->fetch_single($timequery);
			//Opponent's drop has already been collected and the time hasn't reset.
			if ((time() <= ($r2 + $results2['botcooldown'])) && ($r2 > 0)) {}
			else {
				$time = time();
				$exists = $db->query("/*qc=on*/SELECT `botid` FROM `botlist_hits` WHERE `userid` = {$userid} AND `botid` = {$r['userid']}");
				if ($db->num_rows($exists) == 0) {
					$db->query("INSERT INTO `botlist_hits` (`userid`, `botid`, `lasthit`) VALUES ('{$userid}', '{$r['userid']}', '{$time}')");
				} else {
					$db->query("UPDATE `botlist_hits` SET `lasthit` = {$time} WHERE `userid` = {$userid} AND `botid` = {$r['userid']}");
				}
			}
		}
    } else {
        die($h->endpage());
    }
}

function lost()
{
    global $db, $userid, $ir, $h, $api;
    $_GET['ID'] = isset($_GET['ID']) && ctype_digit($_GET['ID']) ? $_GET['ID'] : 0;
    $_SESSION['attacking'] = 0;
	$_SESSION['attack_scroll'] = 0;
    $ir['attacking'] = 0;
    //User did not lose fight, or lost fight to someone else.
    if (!isset($_SESSION['attacklost']) || $_SESSION['attacklost'] != $_GET['ID']) {
        $api->UserInfoSet($userid, "xp", 0);
        alert("danger", "Security Issue!", "You did not lose your last fight to this player. You've lost all your
		                                    experience for that.", true, 'index.php');
        die($h->endpage());
    }
    //If the opponent is not specified
    if (!$_GET['ID']) {
        alert('warning', "Security Issue!", "You are trying to lose a fight against non-existent user.", true, 'index.php');
        die($h->endpage());
    }
    $od = $db->query("/*qc=on*/SELECT `username`, `level`, `user_level`,
                      `guild`, `xp` FROM `users` WHERE `userid` = {$_GET['ID']}");
    //The opponent does not exist.
    if (!$db->num_rows($od)) {
        echo "The user you've lost to does not exist.";
        die($h->endpage());
    }
    $r = $db->fetch_row($od);
    $db->free_result($od);
    $qe = $ir['level'] * $ir['level'] * $ir['level'];
    $expgain = Random($qe / 4, $qe);
    //User loses XP for losing the fight.
    if ($expgain < 0) {
        $expgain = $expgain * -1;
    }
    $expgainp = $expgain / $ir['xp_needed'] * 100;
    if ($ir['xp'] - $expgain < 0) {
        $api->UserInfoSetStatic($userid, "xp", 0);
    } else {
        $api->UserInfoSet($userid, "xp", "-{$expgain}");
    }
    $api->UserInfoSetStatic($userid, "attacking", 0);
    $hosptime = Random(10, 20) + floor($ir['level'] / 2);
    $hospreason = "Lost a Fight";
    //Place user in infirmary.
    $api->UserStatusSet($userid, 'infirmary', $hosptime, $hospreason);
    //Give winner some XP
    $r['xp_needed'] = round(($r['level'] + 1.5) * ($r['level'] + 1.5) * ($r['level'] + 1.5) * 1.5);
    $qe2 = $r['level'] * $r['level'] * $r['level'];
    $expgain2 = Random($qe2 / 4, $qe2);
    $expgainp2 = $expgain2 / $r['xp_needed'] * 100;
    $expperc2 = round($expgainp2 / $r['xp_needed'] * 100);
    //Tell opponent that they were attacked by user, and emerged victorious.
    $api->GameAddNotification($_GET['ID'], "<a href='profile.php?user=$userid'>{$ir['username']}</a>
                                            picked a fight against you and lost. You've gained {$expperc2}% experience.");
    //Log that the user lost.
    $api->SystemLogsAdd($userid, 'attacking', "Attacked <a href='../profile.php?user={$_GET['ID']}'>{$r['username']}</a> [{$_GET['ID']}] and lost.");
    //Log that the opponent won.
    $api->SystemLogsAdd($_GET['ID'], 'attacking', "Attacked by <a href='../profile.php?user={$userid}'>{$ir['username']}</a> [{$userid}] and won.");
    //Increase opponent's XP for winning.
    $api->UserInfoSetStatic($_GET['ID'], "xp", $r['xp'] + $expgainp2);
    $_SESSION['attacklost'] = 0;
    $additionaltext = "";
    //Both players in a guild.
    if ($ir['guild'] > 0 && $r['guild'] > 0) {
        $oppguild = $db->query("/*qc=on*/SELECT `guild_id` FROM `guild` WHERE `guild_id` = {$r['guild']} LIMIT 1");
        if ($db->num_rows($oppguild) > 0) {
            $warq = $db->query("/*qc=on*/SELECT `gw_id` FROM `guild_wars`
				WHERE `gw_winner` = 0 AND (`gw_declarer` = {$ir['guild']} AND `gw_declaree` = {$r['guild']})
				OR (`gw_declaree` = {$ir['guild']} AND `gw_declarer` = {$r['guild']})");
            //Both players' guilds are at war.
            if ($db->num_rows($warq) > 0) {
                $wr = $db->fetch_single($warq);
                $whoswho = $db->fetch_row($db->query("/*qc=on*/SELECT `gw_declarer`, `gw_declaree` FROM `guild_wars` WHERE `gw_id` = {$wr}"));
                //Give opponent's guild a point.
                if ($whoswho['gw_declarer'] == $ir['guild']) {
                    $db->query("UPDATE `guild_wars` SET `gw_depoints` = `gw_depoints` + 5 WHERE `gw_id` = {$wr}");
                } else {
                    $db->query("UPDATE `guild_wars` SET `gw_drpoints` = `gw_drpoints` + 5 WHERE `gw_id` = {$wr}");
                }
                $additionaltext = "You have gained a point for your opponent's guild.";
				guilddebt($r['guild'],$ir['guild']);
            }
        }
    }
    //Tell user they lost, and if they gave the other guild a point.
    alert('danger', "You lost to {$r['username']}!", "You have lost a fight, and lost " . number_format($expgainp, 2) . "% experience! {$additionaltext}", true, 'index.php');
    $db->query("UPDATE `users` SET `kills` = `kills` + 1 WHERE `userid` = {$_GET['ID']}");
    $db->query("UPDATE `users` SET `deaths` = `deaths` + 1 WHERE `userid` = {$userid}");
	attacklog($userid,$_GET['ID'],'lost');
}

function xp()
{
    global $db, $userid, $ir, $h, $api;
    $_GET['ID'] = isset($_GET['ID']) && ctype_digit($_GET['ID']) ? $_GET['ID'] : 0;
    $_SESSION['attacking'] = 0;
	$_SESSION['attack_scroll'] = 0;
    $ir['attacking'] = 0;
    $api->UserInfoSetStatic($userid, "attacking", 0);
    $od = $db->query("/*qc=on*/SELECT * FROM `users` WHERE `userid` = {$_GET['ID']}");
    //User did not win the attack, or the attack they won is not against the current opponent.
    if (!isset($_SESSION['attackwon']) || $_SESSION['attackwon'] != $_GET['ID']) {
        $api->UserInfoSet($userid, "xp", 0);
        alert("danger", "Security Issue!", "You didn't win your last fight against this player. You've lost your
		    experience for that.", true, 'index.php');
        die($h->endpage());
    }
    //Opponent does not exist.
    if (!$db->num_rows($od)) {
        alert('danger', "Uh Oh!", "You are trying to win a fight against a non-existent user.", true, 'index.php');
        exit($h->endpage());
    }
    if ($db->num_rows($od) > 0) {
        $r = $db->fetch_row($od);
        $db->free_result($od);
        //Opponent was already beat.
        if ($r['hp'] == 1) {
            $api->UserInfoSet($userid, "xp", 0);
            alert('danger', "Uh Oh!", "You have already beat this user. Trying to win again is how you lose experience...
			    which is what just happened to you.", true, 'index.php');
            exit($h->endpage());
        } else {
            //XP gained is Opponent's Level x Opponent's Level x Opponent's Level.
            $qe = ($r['level'] * $r['level'] * $r['level']);
            //Seasoned Warrior Skill
			$specialnumber=((getSkillLevel($userid,5)*3)/100);
            //Add 3% bonus if Exp. token is equipped
			if ($api->UserEquippedItem($userid,'primary',93))
			{
				$qe=$qe+($qe*0.03);
				$qe=$qe+($qe*$specialnumber);
			}
			if ($api->UserEquippedItem($userid,'secondary',93))
			{
				$qe=$qe+($qe*0.03);
				$qe=$qe+($qe*$specialnumber);
			}
            //Make the XP gained a little random...
            $expgain = Random($qe / 4, $qe);
			$xplostequip='';
			$xploststat='';
			$weapons=$db->fetch_row($db->query("/*qc=on*/SELECT `equip_primary`, `equip_secondary`, `equip_armor`
                                                FROM `users`
                                                WHERE `userid` = {$r['userid']}"));
            $ostat=$db->fetch_row($db->query("/*qc=on*/SELECT `strength`,`agility`,`guard` FROM `userstats` WHERE `userid` = {$r['userid']}"));
            $ototal=$ostat['strength']+$ostat['agility']+$ostat['guard'];            
            //Opponent does not have any armor or weapon equipped, dock the user's XP another 75%
            if (empty($weapons['equip_primary']) && empty($weapons['equip_secondary'])
                || empty($weapons['equip_armor']))
            {
                $expgain=$expgain*0.05;
                $xplostequip="Your experience gains were decreased by 95% because your opponent had no equipment.";
            }
            if ($expgain < 1)
                $expgain = 1;
            $expperc = round($expgain / $ir['xp_needed'] * 100);
            $hosptime = Random(5, 15) + floor($ir['level'] / 10);
            //Give user XP.
			attacklog($userid,$_GET['ID'],'xp');
            $db->query("UPDATE `users` SET `xp` = `xp` + {$expgain} WHERE `userid` = {$userid}");
            $hospreason = $db->escape("Used for Experience by <a href='profile.php?user={$userid}'>{$ir['username']}</a>");
            //Set opponent's HP to 1.
            $api->UserInfoSet($r['userid'], "hp", 1);
            //Place opponent in infirmary.
            $api->UserStatusSet($r['userid'], 'infirmary', $hosptime, $hospreason);
            //Tell opponent they were attacked by the user and lost.
            $api->GameAddNotification($r['userid'], "<a href='profile.php?user=$userid'>{$ir['username']}</a> attacked you
                and used you for experience.");
            //Log that the user won.
            $api->SystemLogsAdd($userid, 'attacking', "Attacked <a href='../profile.php?user={$_GET['ID']}'>{$r['username']}</a> [{$r['userid']}] and gained {$expperc}% Experience.");
            //Log that the opponent lost.
            $api->SystemLogsAdd($_GET['ID'], 'attacking', "Attacked by <a href='../profile.php?user={$userid}'>{$ir['username']}</a> [{$userid}] and left for experience.");
            $_SESSION['attackwon'] = 0;
            $additionaltext = "";
            //Both players are in a guild.
            if ($ir['guild'] > 0 && $r['guild'] > 0) {
                $oppguild = $db->query("/*qc=on*/SELECT `guild_id` FROM `guild` WHERE `guild_id` = {$r['guild']} LIMIT 1");
                if ($db->num_rows($oppguild) > 0) {
					$warq = $db->query("/*qc=on*/SELECT `gw_id` FROM `guild_wars`
				WHERE `gw_winner` = 0 AND (`gw_declarer` = {$ir['guild']} AND `gw_declaree` = {$r['guild']})
				OR (`gw_declaree` = {$ir['guild']} AND `gw_declarer` = {$r['guild']})");
                    //Both players' guilds are at war with each other.
                    if ($db->num_rows($warq) > 0) {
                        $wr = $db->fetch_single($warq);
                        $whoswho = $db->fetch_row($db->query("/*qc=on*/SELECT `gw_declarer`, `gw_declaree` FROM `guild_wars` WHERE `gw_id` = {$wr}"));
                        //Give user's guild a point.
                        if ($whoswho['gw_declarer'] == $ir['guild']) {
                            $db->query("UPDATE `guild_wars` SET `gw_drpoints` = `gw_drpoints` + 1 WHERE `gw_id` = {$wr}");
                        } else {
                            $db->query("UPDATE `guild_wars` SET `gw_depoints` = `gw_depoints` + 1 WHERE `gw_id` = {$wr}");
                        }
                        $additionaltext = "For winning this fight, you have gained your guild 1 point.";
						guilddebt($ir['guild'],$r['guild']);
                    }
                }

            }
            //Tell user they won, and if they received a point or not.
            alert('success', "You've bested {$r['username']}!", "You decide to finish the fight the honorable way. You
			    have gained ({$expperc}%, " . round($expgain,2) . ") experience for this. {$xploststat} {$xplostequip} {$additionaltext}", true, 'index.php');

            $db->query("UPDATE `users` SET `kills` = `kills` + 1 WHERE `userid` = {$userid}");
            $db->query("UPDATE `users` SET `deaths` = `deaths` + 1 WHERE `userid` = {$r['userid']}");
            //Opponent is NPC, so lets refill their HP, and remove infirmary time.
            if ($r['user_level'] == 'NPC') {
                $db->query("UPDATE `users` SET `hp` = `maxhp`  WHERE `userid` = {$r['userid']}");
                $db->query("UPDATE `infirmary` SET `infirmary_out` = 0 WHERE `infirmary_user` ={$r['userid']}");
            }
			$npcquery = $db->query("/*qc=on*/SELECT * FROM `botlist` WHERE `botuser` = {$r['userid']}");
			//Opponent is registered on bot list.
			if ($db->num_rows($npcquery) > 0) {
				$results2 = $db->fetch_row($npcquery);
				$timequery = $db->query("/*qc=on*/SELECT `lasthit` FROM `botlist_hits` WHERE `userid` = {$userid} && `botid` = {$r['userid']}");
				$r2 = $db->fetch_single($timequery);
				//Opponent's drop has already been collected and the time hasn't reset.
				if ((time() <= ($r2 + $results2['botcooldown'])) && ($r2 > 0)) {}
				else {
					$time = time();
					$exists = $db->query("/*qc=on*/SELECT `botid` FROM `botlist_hits` WHERE `userid` = {$userid} AND `botid` = {$r['userid']}");
					if ($db->num_rows($exists) == 0) {
						$db->query("INSERT INTO `botlist_hits` (`userid`, `botid`, `lasthit`) VALUES ('{$userid}', '{$r['userid']}', '{$time}')");
					} else {
						$db->query("UPDATE `botlist_hits` SET `lasthit` = {$time} WHERE `userid` = {$userid} AND `botid` = {$r['userid']}");
					}
				}
			}
            if ($r['user_level'] != 'NPC') {
                $last5=time()-600;
                $attackedcount=$db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`attack_id`) FROM `attack_logs` WHERE `attacked` = {$r['userid']} AND `result` = 'xp' AND `attack_time` > {$last5}"));
                if ($attackedcount > 3)
                {
                    $loss=$ototal*0.03;
                    $db->query("UPDATE `userstats` 
                                SET `strength` = `strength` - {$loss},
                                `agility` = `agility` - {$loss},
                                `guard` = `guard` - {$loss}
                                WHERE `userid` = {$r['userid']}");
                    $api->GameAddNotification($r['userid'],"You have lost " . round($loss) . " strength, agility and guard for allowing yourself to be used for experience too frequently.");
                }
            }
        }
    }
}

function mug()
{
    global $db, $userid, $ir, $h, $api;
    $_GET['ID'] = isset($_GET['ID']) && ctype_digit($_GET['ID']) ? $_GET['ID'] : 0;
    $_SESSION['attacking'] = 0;
	$_SESSION['attack_scroll'] = 0;
    $ir['attacking'] = 0;
    $api->UserInfoSetStatic($userid, "attacking", 0);
    $od = $db->query("/*qc=on*/SELECT * FROM `users` WHERE `userid` = {$_GET['ID']}");
    //User did not win fight, or won fight against someone else.
    if (!isset($_SESSION['attackwon']) || $_SESSION['attackwon'] != $_GET['ID']) {
        $api->UserInfoSet($userid, "xp", 0);
        alert("danger", "Security Issue!", "You didn't win the fight against this opponent. You've lost all your
		    experience.", true, 'index.php');
        die($h->endpage());
    }
    //Opponent does not exist.
    if (!$db->num_rows($od)) {
        alert('danger', "Uh Oh!", "You are trying to attack a non-existent user.", true, 'index.php');
        exit($h->endpage());
    }
    if ($db->num_rows($od) > 0) {
        $r = $db->fetch_row($od);
        $db->free_result($od);
        //Opponent's HP is 1, meaning fight has already concluded.
        if ($r['hp'] == 1) {
            $api->UserInfoSet($userid, "xp", 0);
            alert('danger', "Uh Oh!", "Stop trying to abuse bugs, dude. You've lost all your experience.", true, 'index.php');
            exit($h->endpage());
        } else {
			attacklog($userid,$_GET['ID'],'mugged');
			$minimum=round($r['primary_currency']*0.02);
			$maximum=round($r['primary_currency']*0.2);
			$specialnumber=((getSkillLevel($userid,14)*5)/100);
            $stole = Random($minimum,$maximum);
			$stole = $stole+($stole*$specialnumber);
            $hosptime = Random(20, 40) + floor($ir['level'] / 6);
            $hospreason = $db->escape("Robbed by <a href='profile.php?user={$userid}'>{$ir['username']}</a>");
            //Set opponent HP to 1.
            $api->UserInfoSet($r['userid'], "hp", 1);
            //Take opponent's Copper Coins and give it to user.
            $api->UserTakeCurrency($r['userid'], 'primary', $stole);
            $api->UserGiveCurrency($userid, 'primary', $stole);
            //Place opponent in infirmary.
            $api->UserStatusSet($r['userid'], 'infirmary', $hosptime, $hospreason);
            //Tell opponent they were mugged, and for how much, by user.
            $api->GameAddNotification($r['userid'], "<a href='profile.php?user=$userid'>{$ir['username']}</a> mugged you and stole " . number_format($stole) . " Copper Coins.");
            //Log that the user won and stole some Copper Coins, and that
            //the opponent lost and lost Copper Coins.
            $api->SystemLogsAdd($userid, 'attacking', "Mugged <a href='../profile.php?user={$_GET['ID']}'>{$r['username']}</a> [{$r['userid']}] and stole {$stole} Copper Coins.");
            $api->SystemLogsAdd($_GET['ID'], 'attacking', "Mugged by <a href='../profile.php?user={$userid}'>{$ir['username']}</a> [{$userid}] and lost {$stole} Copper Coins.");
            $_SESSION['attackwon'] = 0;
            $additionaltext = "";
            //Both players are in a guild.
            if ($ir['guild'] > 0 && $r['guild'] > 0) {
                $oppguild = $db->query("/*qc=on*/SELECT `guild_id` FROM `guild` WHERE `guild_id` = {$r['guild']} LIMIT 1");
                if ($db->num_rows($oppguild) > 0) {
                    $warq = $db->query("/*qc=on*/SELECT `gw_id` FROM `guild_wars`
				    WHERE `gw_winner` = 0 AND (`gw_declarer` = {$ir['guild']} AND `gw_declaree` = {$r['guild']})
				    OR (`gw_declaree` = {$ir['guild']} AND `gw_declarer` = {$r['guild']})");
                    //Both players' guilds are at war with each other.
                    if ($db->num_rows($warq) > 0) {
                        $wr = $db->fetch_single($warq);
                        $whoswho = $db->fetch_row($db->query("/*qc=on*/SELECT `gw_declarer`, `gw_declaree` FROM `guild_wars` WHERE `gw_id` = {$wr}"));
                        //Give user's guild a point.
                        if ($whoswho['gw_declarer'] == $ir['guild']) {
                            $db->query("UPDATE `guild_wars` SET `gw_drpoints` = `gw_drpoints` + 2 WHERE `gw_id` = {$wr}");
                        } else {
                            $db->query("UPDATE `guild_wars` SET `gw_depoints` = `gw_depoints` + 2 WHERE `gw_id` = {$wr}");
                        }
                        $additionaltext = "For winning this fight, you've won your guild 1 point.";
						guilddebt($ir['guild'],$r['guild']);
                    }
                }

            }
            $db->query("UPDATE `users` SET `kills` = `kills` + 1 WHERE `userid` = {$userid}");
            $db->query("UPDATE `users` SET `deaths` = `deaths` + 1 WHERE `userid` = {$r['userid']}");
            //Opponent is NPC, so remove infirmary time and refill HP.
            if ($r['user_level'] == 'NPC') {
                $db->query("UPDATE `users` SET `hp` = `maxhp`  WHERE `userid` = {$r['userid']}");
                $db->query("UPDATE `infirmary` SET `infirmary_out` = 0 WHERE `infirmary_user` ={$r['userid']}");
            }
        }
        $npcquery = $db->query("/*qc=on*/SELECT * FROM `botlist` WHERE `botuser` = {$r['userid']}");
        //Opponent is registered on bot list.
        if ($db->num_rows($npcquery) > 0) {
            $results2 = $db->fetch_row($npcquery);
            $timequery = $db->query("/*qc=on*/SELECT `lasthit` FROM `botlist_hits` WHERE `userid` = {$userid} && `botid` = {$r['userid']}");
            $r2 = $db->fetch_single($timequery);
            //Opponent's drop has already been collected and the time hasn't reset.
            if ((time() <= ($r2 + $results2['botcooldown'])) && ($r2 > 0)) {
                //Nope
            } //Bot's item can be collected.
            else {
                //Give user the bot's item.
                $api->UserGiveItem($userid, $results2['botitem'], 1);
                $time = time();
                $exists = $db->query("/*qc=on*/SELECT `botid` FROM `botlist_hits` WHERE `userid` = {$userid} AND `botid` = {$r['userid']}");
                //Place user's hittime into database.
                if ($db->num_rows($exists) == 0) {
                    $db->query("INSERT INTO `botlist_hits` (`userid`, `botid`, `lasthit`) VALUES ('{$userid}', '{$r['userid']}', '{$time}')");
                } else {
                    $db->query("UPDATE `botlist_hits` SET `lasthit` = {$time} WHERE `userid` = {$userid} AND `botid` = {$r['userid']}");
                }
                //Tell user they took an item.
                $api->GameAddNotification($userid, "For mugging the " . $api->SystemUserIDtoName($r['userid']) . " bot, you have gained 1 " . $api->SystemItemIDtoName($results2['botitem']));
            }
        }
		//Tell user they won the fight, and how much currency they took.
            alert('success', "You have bested {$r['username']}!", "You have knocked them out and taken out their wallet.
			    You snag some cash and run away. (" . number_format($stole) . "). {$additionaltext}", true, 'index.php');
    }
}
function attacklog($attacker,$attacked,$result)
{
	global $db;
	$time=time();
	$db->query("INSERT INTO `attack_logs` (`attack_time`, `attacker`, `attacked`, `result`) VALUES ('{$time}', '{$attacker}', '{$attacked}', '{$result}')");
}
function guilddebt($winner,$loser)
{
	global $db;
	$sevendays=time()+604800;
	$db->query("UPDATE `guild` SET `guild_primcurr` = `guild_primcurr` - 15000  WHERE `guild_id` = {$winner}");
	$db->query("UPDATE `guild` SET `guild_primcurr` = `guild_primcurr` - 25000  WHERE `guild_id` = {$loser}");
	$db->query("UPDATE `guild` SET `guild_debt_time` = {$sevendays} WHERE `guild_primcurr` < 0 AND `guild_debt_time` = 0");
}

$h->endpage();
