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
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
switch ($_GET['action'])
{
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
	global $db,$userid,$ir,$h,$lang,$api,$set,$atkpage;
	$menuhide = 1;					//Hide the menu so players cannot load other pages,
									//and lessens the chance of a misclick and losing XP.
	$atkpage = 1;
	$tresder = Random(100, 999);	//RNG to prevent refreshing while attacking, thus
									//breaking progression of the attack system.
	$_GET['user'] =  (isset($_GET['user']) && is_numeric($_GET['user']))  ? abs($_GET['user']) : '';
	if (empty($_GET['nextstep']))
	{
		$_GET['nextstep']=0;
	}
	if ($_GET['nextstep'] > 0)
	{
		$_GET['tresde'] = (isset($_GET['tresde']) && is_numeric($_GET['tresde'])) ? abs($_GET['tresde']) : 0;
		//If RNG is not set, set it to 0.
		if (!isset($_SESSION['tresde']))
		{
			$_SESSION['tresde'] = 0;
		}
		//If RNG is not the same number stored in session
		if (($_SESSION['tresde'] == $_GET['tresde']) || $_GET['tresde'] < 100)
		{
			alert("danger",$lang['ERROR_GENERIC'],$lang['ATTACK_START_NOREFRESH'],true,'index.php');
			die($h->endpage());
		}
		//Set RNG
		$_SESSION['tresde'] = $_GET['tresde'];
	}
	//If user is not specified.
	if (!$_GET['user'])
	{
		alert("danger",$lang['ERROR_NONUSER'],$lang['ATTACK_START_NOUSER'],true,'index.php');
		die($h->endpage());
	}
    //If the user is trying to attack himself.
	else if ($_GET['user'] == $userid)
	{
		alert("danger",$lang['ERROR_GENERIC'],$lang['ATTACK_START_NOTYOU'],true,'index.php');
		die($h->endpage());
	}
    //If the user has no HP, and is not already attacking.
	else if ($ir['hp'] <= 1 && $ir['attacking'] == 0)
	{
		alert("danger",$lang["GEN_INFIRM"],$lang['ATTACK_START_YOUNOHP'],true,'index.php');
		die($h->endpage());
	}
    //If the user has left a previous  after losing.
	else if (isset($_SESSION['attacklost']) && $_SESSION['attacklost'] == 1)
	{
		$_SESSION['attacklost'] = 0;
		alert("danger",$lang['ERROR_GENERIC'],$lang['ATTACK_START_YOUCHICKEN'],true,'index.php');
		die($h->endpage());
	}
	$youdata = $ir;
	$laston = time() - 900;
	$q = $db->query("SELECT `u`.`userid`, `hp`, `equip_armor`, `username`,
	       `equip_primary`, `equip_secondary`, `guild`, `location`, `maxhp`,
	       `guard`, `agility`, `strength`, `gender`, `level`, `laston`
			FROM `users` AS `u`
			INNER JOIN `userstats` AS `us` ON `u`.`userid` = `us`.`userid`
			WHERE `u`.`userid` = {$_GET['user']}
			LIMIT 1");
	//Test for if the specified user is a valid and registered user.
	if ($db->num_rows($q) == 0)
	{
		alert("danger",$lang['ERROR_NONUSER'],$lang['ATTACK_START_NONUSER'],true,'index.php');
		die($h->endpage());
	}
	$odata = $db->fetch_row($q);
	$db->free_result($q);
	//Check current user's last attacked user, and see that its the specified user.
	if ($ir['attacking'] && $ir['attacking'] != $_GET['user'])
	{
		$_SESSION['attacklost'] = 0;
		alert("danger",$lang['ERROR_UNKNOWN'],$lang['ATTACK_START_UNKNOWNERROR'],true,'index.php');
		$api->UserInfoSetStatic($userid,"attacking",0);
		die($h->endpage());
	}
	//Check that the opponent has 1 health point.
	if ($odata['hp'] == 1)
	{
		$_SESSION['attacking'] = 0;
		$ir['attacking'] = 0;
		$api->UserInfoSetStatic($userid,"attacking",0);
		alert("danger",$lang['ERROR_GENERIC'],"{$odata['username']} {$lang['ATTACK_START_OPPNOHP']}",true,'index.php');
		die($h->endpage());
	}
	//Check if the opponent is currently in the infirmary.
	else if ($api->UserStatus($_GET['user'],'infirmary') == true)
	{
		$_SESSION['attacking'] = 0;
		$ir['attacking'] = 0;
		$api->UserInfoSetStatic($userid,"attacking",0);
		alert("danger",$lang['GEN_INFIRM'],"{$odata['username']} {$lang['ATTACK_START_OPPINFIRM']}",true,'index.php');
		die($h->endpage());
	}
	//Check if the current user is in the infirmary.
	else if ($api->UserStatus($ir['userid'],'infirmary') == true)
	{
		$_SESSION['attacking'] = 0;
		$ir['attacking'] = 0;
		$api->UserInfoSetStatic($userid,"attacking",0);
		alert("danger",$lang['GEN_INFIRM'],$lang['ATTACK_START_YOUINFIRM'],true,'index.php');
		die($h->endpage());
	}
	//Check if the opponent is in the dungeon.
	else if ($api->UserStatus($userid,'dungeon') == true)
	{
		$_SESSION['attacking'] = 0;
		$ir['attacking'] = 0;
		$api->UserInfoSetStatic($userid,"attacking",0);
		alert("danger",$lang['GEN_DUNG'],"{$odata['username']} {$lang['ATTACK_START_OPPDUNG']}",true,'index.php');
		die($h->endpage());
	}
	//Check if the current user is in the dungeon.
	else if ($api->UserStatus($userid,'dungeon') == true)
	{
		$_SESSION['attacking'] = 0;
		$ir['attacking'] = 0;
		$api->UserInfoSetStatic($userid,"attacking",0);
		alert("danger",$lang['GEN_DUNG'],$lang['ATTACK_START_YOUDUNG'],true,'index.php');
		die($h->endpage());
	}
	//Check if opponent has permission to be attacked.
	else if (permission('CanBeAttack',$_GET['user']) == false)
	{
		$_SESSION['attacking'] = 0;
		$ir['attacking'] = 0;
		$api->UserInfoSetStatic($userid,"attacking",0);
		alert("danger",$lang['ERROR_GENERIC'],$lang['ATTACK_START_OPPUNATTACK'],true,'index.php');
		die($h->endpage());
	}
	//Check if the current player has permission to attack.
	else if (permission('CanAttack',$userid) == false)
	{
		$_SESSION['attacking'] = 0;
		$ir['attacking'] = 0;
		$api->UserInfoSetStatic($userid,"attacking",0);
		alert("danger",$lang['ERROR_GENERIC'],$lang['ATTACK_START_YOUUNATTACK'],true,'index.php');
		die($h->endpage());
	}
	//Check if the opponent is level 2 or lower, and has been on in the last 15 minutes.
	else if ($odata['level'] < 3 && $odata['laston'] > $laston)
	{
		$_SESSION['attacking'] = 0;
		$ir['attacking'] = 0;
		$api->UserInfoSetStatic($userid,"attacking",0);
		alert("danger",$lang['ERROR_GENERIC'],$lang['ATTACK_START_THEYLOWLEVEL'],true,'index.php');
		die($h->endpage());
	}
	$_GET['weapon'] = (isset($_GET['weapon']) && is_numeric($_GET['weapon'])) ? abs($_GET['weapon']) : '';
    //If weapon is specified via $_GET, attack!!
	if ($_GET['weapon'])
	{
		if (!$_GET['nextstep'])
		{
			$_GET['nextstep'] = 1;
		}
		//Check for if current step is greater than the maximum attacks per session.
		if ($_GET['nextstep'] >= $set['MaxAttacksPerSession'])
		{
			$_SESSION['attacking'] = 0;
			$ir['attacking'] = 0;
			$api->UserInfoSetStatic($userid,"attacking",0);
			alert("warning",$lang['ERROR_GENERIC'],$lang['ATTACK_FIGHT_STALEMATE'],true,'index.php');
			die($h->endpage());
		}
		//Check if the attack is currently stored in session.
		if ($_SESSION['attacking'] == 0 && $ir['attacking'] == 0)
		{
			//Check if the current user has enough energy for this attack.
			if ($youdata['energy'] >= $youdata['maxenergy'] / $set['AttackEnergyCost'])
			{
				$youdata['energy'] -= floor($youdata['maxenergy'] / $set['AttackEnergyCost']);
				$cost = floor($youdata['maxenergy'] / $set['AttackEnergyCost']);
				$api->UserInfoSet($userid,'energy',"-{$cost}");
				$_SESSION['attackdmg'] = 0;
			}
            //If not enough energy, stop the fight.
			else
			{
				$EnergyPercent=floor(100/$set['AttackEnergyCost']);
				$UserCurrentEnergy=floor($ir['maxenergy']/$ir['energy']);
				alert("danger",$lang['ERROR_GENERIC'],"{$lang['ATTACK_FIGHT_LOWENG1']} {$EnergyPercent}{$lang['ATTACK_FIGHT_LOWENG2']} {$UserCurrentEnergy}%",true,'index.php');
				die($h->endpage());
			}
		}
		$_SESSION['attacking'] = 1;
		$ir['attacking'] = $odata['userid'];
		$api->UserInfoSetStatic($userid,"attacking",$ir['attacking']);
		$_GET['nextstep'] = (isset($_GET['nextstep']) && is_numeric($_GET['nextstep'])) ? abs($_GET['nextstep']) : '';
		//Check if the current user is attacking with a weapon that they have equipped.
		if ($_GET['weapon'] != $ir['equip_primary'] && $_GET['weapon'] != $ir['equip_secondary'])
		{
			$api->UserInfoSet($userid,'xp',0);
			$api->UserStatusSet($userid,'infirmary',666,'Bug Abuse');
			alert("danger",$lang['ERROR_SECURITY'],$lang['ATTACK_FIGHT_BUGABUSE'],true,'index.php');
			die($h->endpage());
		}
		$winfo_sql ="SELECT `itmname`, `weapon` FROM `items` WHERE `itmid` = {$_GET['weapon']} LIMIT 1";
		$qo = $db->query($winfo_sql);
        //If the weapon chosen is not a valid weapon.
		if ($db->num_rows($qo) == 0)
		{
			alert("danger",$lang['ERROR_GENERIC'],$lang['ATTACK_FIGHT_BADWEAP'],true,'index.php');
			die($h->endpage());
		}
		$r1 = $db->fetch_row($qo);
		$db->free_result($qo);
		$mydamage = round(($r1['weapon'] * $youdata['strength'] / ($odata['guard'] / 1.5)) * (Random(10000, 12000) / 10000));
		$hitratio = max(10, min(60 * $ir['agility'] / $odata['agility'], 95));
		//If the attack attempt was connected.
		if (Random(1, 100) <= $hitratio)
		{
            //If the opponent has armor equipped.
			if ($odata['equip_armor'] > 0)
			{
				$armorinfo_sql ="SELECT `armor` FROM `items` WHERE `itmid` = {$odata['equip_armor']} LIMIT 1";
				$q3 = $db->query($armorinfo_sql);
                //Check that the armor is valid.
				if ($db->num_rows($q3) > 0)
				{
					$mydamage -= $db->fetch_single($q3);
				}
				$db->free_result($q3);
			}
			$theirbeforehp=$odata['hp'];
            //Fix damage...
			if ($mydamage < -100000)
			{
				$mydamage = abs($mydamage);
			}
			else if ($mydamage < 1)
			{
				$mydamage = 1;
			}
			$crit = Random(1, 40);
            //If user makes a critical hit, multiply damage.
			if ($crit == 17)
			{
				$mydamage *= Random(20, 40) / 10;
			}
            //If unlucky crit... reduce damage.
			else if ($crit == 25 OR $crit == 8)
			{
				$mydamage /= (Random(20, 40) / 10);
			}
			if ($mydamage > $theirbeforehp)
			{
				$mydamage=$odata['hp'];
			}
			$mydamage = round($mydamage);
			$odata['hp'] -= $mydamage;
			if ($odata['hp'] == 1)
			{
				$odata['hp'] = 0;
				$mydamage += 1;
			}
            //Fixes query error if the opponent HP is lower than 0.
			if (($odata['hp'] - $mydamage) < 0)
			{
				$mydamage=$mydamage+$odata['hp'];
			}
            //If opponent HP lower than 0, set to 0.
			if ($odata['hp'] < 0)
			{
				$odata['hp'] = 0;
			}
            //Reduce health.
			$api->UserInfoSet($_GET['user'],"hp","-{$mydamage}",false);
			echo "{$_GET['nextstep']}) {$lang['ATTACK_FIGHT_ATTACKY_HIT1']} {$r1['itmname']} {$lang['ATTACK_FIGHT_ATTACKY_HIT2']} {$odata['username']} {$lang['ATTACK_FIGHT_ATTACKY_HIT3']} {$mydamage} {$lang['ATTACK_FIGHT_ATTACKY_HIT4']} ({$odata['hp']} {$lang['ATTACK_FIGHT_ATTACK_HPREMAIN']})<br />";
			$_SESSION['attackdmg'] += $mydamage;
		}
		else
		{
			echo "{$_GET['nextstep']}) {$lang['ATTACK_FIGHT_ATTACKY_MISS1']} {$odata['username']} {$lang['ATTACK_FIGHT_ATTACKY_MISS2']} ({$odata['hp']} {$lang['ATTACK_FIGHT_ATTACK_HPREMAIN']})<br />";
		}
        //Win fight because opponent's health is 0 or lower.
		if ($odata['hp'] <= 0)
		{
			$odata['hp'] = 0;
			$_SESSION['attackwon'] = $_GET['user'];
			$api->UserInfoSet($_GET['user'],'hp',0);
			echo "<br />
			<b>{$lang['ATTACK_FIGHT_ATTACKY_WIN1']} {$odata['username']} {$lang['ATTACK_FIGHT_ATTACKY_WIN2']}</b><br />
			<form action='?action=mug&ID={$_GET['user']}' method='post'><input class='btn btn-secondary' type='submit' value='{$lang['ATTACK_FIGHT_OUTCOME1']} {$lang['GEN_THEM']}' /></form>
			<form action='?action=beat&ID={$_GET['user']}' method='post'><input class='btn btn-secondary' type='submit' value='{$lang['ATTACK_FIGHT_OUTCOME2']} {$lang['GEN_THEM']}' /></form>
			<form action='?action=xp&ID={$_GET['user']}' method='post'><input class='btn btn-secondary' type='submit' value='{$lang['ATTACK_FIGHT_OUTCOME3']} {$lang['GEN_THEM']}' /></form>";
		}
        //The opponent is not down... he gets to attack.
		else
		{
			$eq = $db->query("SELECT `itmname`,`weapon` FROM  `items` WHERE `itmid` IN({$odata['equip_primary']}, {$odata['equip_secondary']})");
			//If opponent does not have a valid weapon equipped, make them punch with fists.
            if ($db->num_rows($eq) == 0)
			{
				$wep = $lang['ATTACK_FIGHT_ATTACK_FISTS'];
				$dam = round(round((($odata['strength']/$ir['guard'] / 100))+ 1)*(Random(10000, 12000) / 10000));
			}
			else
			{
				$cnt = 0;
				while ($r = $db->fetch_row($eq))
				{
					$enweps[] = $r;
					$cnt++;
				}
				$db->free_result($eq);
				$weptouse = Random(0, $cnt - 1);    //Select opponent weapon to use.
				$wep = $enweps[$weptouse]['itmname'];
				$dam = round(($enweps[$weptouse]['weapon'] * $odata['strength'] / ($youdata['guard'] / 1.5)) * (Random(10000, 12000) / 10000));
			}
			$hitratio = max(10, min(60 * $odata['agility'] / $ir['agility'], 95));
            //If hit connects with user.
			if (Random(1, 100) <= $hitratio)
			{
                //If user has armor equipped.
				if ($ir['equip_armor'] > 0)
				{
					$q3 = $db->query("SELECT `armor` FROM `items` WHERE `itmid` = {$ir['equip_armor']} LIMIT 1");
                    //If user has valid armor equipped.
					if ($db->num_rows($q3) > 0)
					{
						$dam -= $db->fetch_single($q3);
					}
					$db->free_result($q3);
				}
				$yourbeforehp=$ir['hp'];
                //If the user doesn't have armor equipped.
				if ($dam < -100000)
				{
					$dam = abs($dam);
				}
                //If damage is lower than 1, set to 1.
				else if ($dam < 1)
				{
					$dam = 1;
				}
				$crit = Random(1, 40);
                //RNG for critical damage, multiply damage!
				if ($crit == 17)
				{
					$dam *= Random(20, 40) / 10;
				}
                //Unlucky... crit is weak.
				else if ($crit == 25 OR $crit == 8)
				{
					$dam /= (Random(20, 40) / 10);
				}
				$dam = round($dam);
				$youdata['hp'] -= $dam;
                //If user has 1 hp.
				if ($youdata['hp'] == 1)
				{
					$dam += 1;
					$youdata['hp'] = 0;
				}
				if (($ir['hp'] - $dam) < 0)
				{
					$dam=$ir['hp'];
					$youdata['hp']=0;
				}
                //If user HP less than 0, set to 0.
				if ($ir['hp'] < 0)
				{
					$ir['hp'] = 0;
				}
				if ($dam > $yourbeforehp)
				{
					$dam=$ir['hp'];
				}
				$api->UserInfoSet($userid,"hp",-$dam);
				$ns = $_GET['nextstep'] + 1;
				echo "{$ns}) {$lang['ATTACK_FIGHT_ATTACKO_HIT1']} {$wep}, {$odata['username']} {$lang['ATTACK_FIGHT_ATTACKO_HIT2']} {$dam} {$lang['ATTACK_FIGHT_ATTACKY_HIT4']} ({$youdata['hp']} {$lang['ATTACK_FIGHT_ATTACK_HPREMAIN']})<br />";
			}
            //Opponent misses their hit.
			else
			{
				$ns = $_GET['nextstep'] + 1;
				echo "{$ns}) {$odata['username']} {$lang['ATTACK_FIGHT_ATTACKO_MISS']} ({$youdata['hp']} {$lang['ATTACK_FIGHT_ATTACK_HPREMAIN']})<br />";
			}
            //User has no HP left, redirect to loss.
			if ($youdata['hp'] <= 0)
			{
				$youdata['hp'] = 0;
				$_SESSION['attacklost'] = $_GET['user'];
				$api->UserInfoSet($userid,"hp",0);
				echo "<form action='?action=lost&ID={$_GET['user']}' method='post'><input type='submit' class='btn btn-secondary' value='{$lang['GEN_CONTINUE']}' />";
			}
		}
	}
    //Opponent has less than 5 HP, fight cannot start.
	else if ($odata['hp'] < 5)
	{
		alert("danger",$lang['ERROR_GENERIC'],"{$odata['username']} {$lang['ATTACK_START_OPPNOHP']}",true,'index.php');
		die($h->endpage());
	}
    //Stop combat if user and opponent are in same guild.
	else if ($ir['guild'] == $odata['guild'] && $ir['guild'] > 0)
	{
		alert("danger",$lang['ERROR_GENERIC'],"{$odata['username']} {$lang['ATTACK_FIGHT_FINAL_GUILD']}",true,'index.php');
		die($h->endpage());
	}
    //If user does not have enough energy.
	else if ($youdata['energy'] < $youdata['maxenergy'] / $set['AttackEnergyCost'])
	{
		$EnergyPercent=floor(100/$set['AttackEnergyCost']);
		$UserCurrentEnergy=floor($ir['energy']/$ir['maxenergy']);
		alert("danger","{$lang['ERROR_GENERIC']}","{$lang['ATTACK_FIGHT_LOWENG1']} {$EnergyPercent}{$lang['ATTACK_FIGHT_LOWENG2']} {$UserCurrentEnergy}% <a href='index.php'>{$lang['GEN_GOHOME']}</a>.");
		die($h->endpage());
	}
    //If user and opponent are in different towns.
	else if ($youdata['location'] != $odata['location'])
	{
		alert("danger",$lang['ERROR_GENERIC'],$lang['ATTACK_FIGHT_FINAL_CITY'],true,'index.php');
		die($h->endpage());
	}
    //If opponent or user have no HP.
	if ($youdata['hp'] <= 0 OR $odata['hp'] <= 0)
	{
		//lol no
	}
	else
	{
		$vars['hpperc'] = round($youdata['hp'] / $youdata['maxhp'] * 100);
		$vars['hpopp'] = 100 - $vars['hpperc'];
		$vars2['hpperc'] = round($odata['hp'] / $odata['maxhp'] * 100);
		$vars2['hpopp'] = 100 - $vars2['hpperc'];
		$mw = $db->query("SELECT `itmid`,`itmname` FROM  `items`  WHERE `itmid` IN({$ir['equip_primary']}, {$ir['equip_secondary']})");
		echo "<table class='table table-bordered'>
		<tr>
			<th colspan='2'>
				{$lang['ATTACK_FIGHT_START1']}
			</th>
		</tr>";
        //If user has weapons equipped, allow him to select one.
		if ($db->num_rows($mw) > 0)
		{
			while ($r = $db->fetch_row($mw))
			{
				if (!isset($_GET['nextstep']))
				{
					$ns = 1;
				}
				else
				{
					$ns = $_GET['nextstep'] + 2;
				}
				if ($r['itmid'] == $ir['equip_primary'])
				{
					echo "<tr><th>{$lang['EQUIP_WEAPON_SLOT1']}</th>";
				}
				if ($r['itmid'] == $ir['equip_secondary'])
				{
					echo "<tr><th>{$lang['EQUIP_WEAPON_SLOT2']}</th>";
				}
				echo "<td><a href='attack.php?nextstep={$ns}&user={$_GET['user']}&weapon={$r['itmid']}&tresde={$tresder}'>{$r['itmname']}</a></td></tr>";
			}
		}
        //If no weapons equipped, tell him to get back!
		else
		{
			alert("warning",$lang['ERROR_GENERIC'],$lang['ATTACK_FIGHT_START2'],true,'index.php');
		}
		$db->free_result($mw);
		echo "</table>";
		echo "
		<table class='table table-bordered'>
			<tr>
				<th>
					Your {$lang['INDEX_HP']} 
				</th>
				<td>
					<div class='progress'>
						<div class='progress-bar bg-success' role='progressbar' aria-valuenow='{$vars['hpperc']}' aria-valuemin='0' aria-valuemax='100' style='width:{$vars['hpperc']}%'>
							{$vars['hpperc']}% ({$youdata['hp']} / {$youdata['maxhp']})
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<th>
					{$odata['username']}'s {$lang['INDEX_HP']}
				</th>
				<td>
					<div class='progress'>
						<div class='progress-bar bg-success' role='progressbar' aria-valuenow='{$vars2['hpperc']}' aria-valuemin='0' aria-valuemax='100' style='width:{$vars2['hpperc']}%'>
							{$vars2['hpperc']}% ({$odata['hp']} / {$odata['maxhp']})
						</div>
					</div>
				</td>
			</tr>
		</table>";
	}
}
function beat()
{
	global $db,$userid,$ir,$h,$lang,$api;
	$_GET['ID'] = isset($_GET['ID']) && ctype_digit($_GET['ID']) ? $_GET['ID'] : 0;
	$_SESSION['attacking'] = 0;
	$ir['attacking'] = 0;
	$api->UserInfoSetStatic($userid,"attacking",0);
	$od = $db->query("SELECT * FROM `users` WHERE `userid` = {$_GET['ID']}");
    //User attempts to win a fight they didn't win.
	if (!isset($_SESSION['attackwon']) || $_SESSION['attackwon'] != $_GET['ID'])
	{
		$api->UserInfoSet($userid,"xp",0);
		$api->UserStatusSet($userid,'infirmay',666,'Bug Abuse');
		alert("danger",$lang['ERROR_SECURITY'],$lang['ATTACK_FIGHT_BUGABUSE'],true,'index.php');
		die($h->endpage());
	}
    //The opponent does not exist.
	if(!$db->num_rows($od)) 
	{
		$api->UserInfoSet($userid,"xp",0);
		$api->UserStatusSet($userid,'infirmary',666,'Bug Abuse');
		alert('danger',$lang['ERROR_NONUSER'],$lang['ATTACK_START_NONUSER'],true,'index.php');
		die($h->endpage());
	}
	if ($db->num_rows($od) > 0)
	{
		$r = $db->fetch_row($od);
		$db->free_result($od);
        //Opponent's HP is 1, meaning the user has already claimed victory.
		if ($r['hp'] == 1) 
		{
			alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['ATTACK_FIGHT_BUGABUSE']}");
			die($h->endpage());
		}
		$hosptime = Random(75, 175) + floor($ir['level'] / 2);
		$hospreason = $db->escape("Beat up by <a href='profile.php?user={$userid}'>{$ir['username']}</a>");
		//Set opponent's HP to 1. Means fight is over.
        $api->UserInfoSet($r['userid'],"hp",1);
        //Place opponent in infirmary.
		$api->UserStatusSet($r['userid'],'infirmary',$hosptime,$hospreason);
        //Give opponent notification that they were attacked.
		$api->GameAddNotification($r['userid'], "<a href='profile.php?user=$userid'>{$ir['username']}</a> brutally attacked you and caused {$hosptime} minutes worth of damage.");
		//Log that the user won the fight.
        $api->SystemLogsAdd($userid,'attacking',"Attacked {$r['username']} [{$r['userid']}] and brutally injured them, causing {$hosptime} minutes of infirmary time.");
		//Log that the opponent lost the fight.
        $api->SystemLogsAdd($_GET['ID'],'attacking',"Brutally injured by <a href='profile.php?user={$userid}'>{$ir['username']}</a> [{$userid}], causing {$hosptime} minutes of infirmary time.");
		$_SESSION['attackwon'] = false;
		$additionaltext = "";
        //Both players are in a guild.
		if ($ir['guild'] > 0 && $r['guild'] > 0)
		{
            $oppguild=$db->query("SELECT `guild_id` FROM `guild` WHERE `guild_id` = {$r['guild']} LIMIT 1");
            if ($db->num_rows($oppguild) > 0)
            {
				$warq = $db->query("SELECT `gw_id` FROM `guild_wars`
				WHERE (`gw_declarer` = {$ir['guild']} AND `gw_declaree` = {$r['guild']})
				OR (`gw_declaree` = {$ir['guild']} AND `gw_declarer` = {$r['guild']})");
                //Both players' guilds are at war with each other.
                if ($db->fetch_single($warq) > 0)
                {
					$wr=$db->fetch_single($warq);
					$whoswho=$db->fetch_row($db->query("SELECT `gw_declarer`, `gw_declaree` FROM `guild_wars` WHERE `gw_id` = {$wr}"));
					//Give points to user's guild.
                    if ($whoswho['gw_declarer'] == $ir['guild'])
					{
						$db->query("UPDATE `guild_wars` SET `gw_drpoints` = `gw_drpoints` + 1 WHERE `gw_id` = {$wr}");
					}
					else
					{
						$db->query("UPDATE `guild_wars` SET `gw_depoints` = `gw_depoints` + 1 WHERE `gw_id` = {$wr}");
					}
					$additionaltext=$lang['ATTACK_FIGHT_POINT'];
				}
			}
			
		}
        //Tell player they won and ended the fight, and if they gained a guild war point.
		alert('success',"{$lang['ATTACK_FIGHT_END']} {$r['username']}!!","{$lang['ATTACK_FIGHT_END2']} {$lang['ATTACK_FIGHT_END3']} {$hosptime} {$lang["GEN_MINUTES"]} {$lang['ATTACK_FIGHT_END4']} {$additionaltext}",true,'index.php');
		//Opponent an NPC? Set their HP to 100%, and remove infirmary time.
        if ($r['user_level'] == 'NPC')
		{
			$db->query("UPDATE `users` SET `hp` = `maxhp` WHERE `userid` = {$r['userid']}");
			$db->query("UPDATE `infirmary` SET `infirmary_out` = 0 WHERE `infirmary_user` ={$r['userid']}");
		}
	}
	else
	{
		die($h->endpage());
	}
}

function lost()
{
	global $db,$userid,$ir,$h,$lang,$api;
	$_GET['ID'] = isset($_GET['ID']) && ctype_digit($_GET['ID']) ? $_GET['ID'] : 0;
	$_SESSION['attacking'] = 0;
	$ir['attacking'] = 0;
    //User did not lose fight, or lost fight to someone else.
	if (!isset($_SESSION['attacklost']) || $_SESSION['attacklost'] != $_GET['ID'])
	{
		$api->UserInfoSet($userid,"xp",0);
		$api->UserStatusSet($userid,'infirmary',666,'Bug Abuse');
		alert("danger",$lang['ERROR_SECURITY'],$lang['ATTACK_FIGHT_BUGABUSE'],true,'index.php');
		die($h->endpage());
	}
    //If the opponent is not specified
	if(!$_GET['ID']) 
	{
		alert('warning',$lang['CSRF_ERROR_TITLE'],$lang['ATT_NC'],true,'index.php');
		die($h->endpage());
	}
	$od = $db->query("SELECT `username`, `level`, `user_level`, `guild`, `xp` FROM `users` WHERE `userid` = {$_GET['ID']}");
	//The opponent does not exist.
    if(!$db->num_rows($od)) 
	{
		echo "404";
		die($h->endpage());
	}
	$r = $db->fetch_row($od);
	$db->free_result($od);
	$qe = $ir['level'] * $ir['level'] * $ir['level'];
	$expgain = Random($qe / 2, $qe);
    //User loses XP for losing the fight.
	if ($expgain < 0)
	{
		$expgain=$expgain*-1;
	}
	$expgainp = $expgain / $ir['xp_needed'] * 100;
	if ($ir['xp'] - $expgain < 0)
	{
		$api->UserInfoSetStatic($userid,"xp",0);
	}
	else
	{
		$api->UserInfoSet($userid,"xp","-{$expgain}");
	}
	$api->UserInfoSetStatic($userid,"attacking",0);
	$hosptime = Random(75, 175) + floor($ir['level'] / 2);
	$hospreason = 'Picked a fight and lost';
    //Place user in infirmary.
	$api->UserStatusSet($userid,'infirmary',$hosptime,$hospreason);
	//Give winner some XP
	$r['xp_needed'] = round(($r['level'] + 2.25) * ($r['level'] + 2.25) * ($r['level'] + 2.25) * 2);
	$qe2 = $r['level'] * $r['level'] * $r['level'];
	$expgain2=Random($qe2 / 2, $qe2);
	$expgainp2 = $expgain2 / $r['xp_needed'] * 100;
	$expperc2 = round($expgainp / $r['xp_needed'] * 100);
    //Tell opponent that they were attacked by user, and emerged victorious.
	$api->GameAddNotification($_GET['ID'], "<a href='profile.php?user=$userid'>{$ir['username']}</a> attacked you and lost, which gave you {$expperc2}% Experience.");
	//Log that the user lost.
    $api->SystemLogsAdd($userid,'attacking',"Attacked {$r['username']} [{$_GET['ID']}] and lost, gaining {$hosptime} minutes in the infirmary.");
	//Log that the opponent won.
    $api->SystemLogsAdd($_GET['ID'],'attacking',"Challenged by <a href='profile.php?user={$userid}'>{$ir['username']}</a> [{$userid}] and won.");
	//Increase opponent's XP for winning.
    $api->UserInfoSetStatic($_GET['ID'],"xp",$r['xp']+$expgainp2);
	$_SESSION['attacklost'] = 0;
	$additionaltext = "";
    //Both players in a guild.
	if ($ir['guild'] > 0 && $r['guild'] > 0)
	{
		$oppguild=$db->query("SELECT `guild_id` FROM `guild` WHERE `guild_id` = {$r['guild']} LIMIT 1");
		if ($db->num_rows($oppguild) > 0)
		{
			$warq = $db->query("SELECT `gw_id` FROM `guild_wars`
			WHERE (`gw_declarer` = {$ir['guild']} AND `gw_declaree` = {$r['guild']})
			OR (`gw_declaree` = {$ir['guild']} AND `gw_declarer` = {$r['guild']})");
            //Both players' guilds are at war.
			if ($db->fetch_single($warq) > 0)
			{
				$wr=$db->fetch_single($warq);
				$whoswho=$db->fetch_row($db->query("SELECT `gw_declarer`, `gw_declaree` FROM `guild_wars` WHERE `gw_id` = {$wr}"));
				//Give opponent's guild a point.
                if ($whoswho['gw_declarer'] == $ir['guild'])
				{
					$db->query("UPDATE `guild_wars` SET `gw_depoints` = `gw_depoints` + 1 WHERE `gw_id` = {$wr}");
				}
				else
				{
					$db->query("UPDATE `guild_wars` SET `gw_drpoints` = `gw_drpoints` + 1 WHERE `gw_id` = {$wr}");
				}
				$additionaltext=$lang['ATTACK_FIGHT_POINTL'];
			}
		}
	}
    //Tell user they lost, and if they gave the other guild a point.
	alert('danger',"{$lang['ATTACK_FIGHT_END5']} {$r['username']}!","{$lang['ATTACK_FIGHT_END6']} (" . number_format($expgainp, 2) . "%)! {$additionaltext}",true,'index.php');
}
function xp()
{
	global $db,$userid,$ir,$h,$lang,$api;
	$_GET['ID'] = isset($_GET['ID']) && ctype_digit($_GET['ID']) ? $_GET['ID'] : 0;
	$_SESSION['attacking'] = 0;
	$ir['attacking'] = 0;
	$api->UserInfoSetStatic($userid,"attacking",0);
	$od = $db->query("SELECT * FROM `users` WHERE `userid` = {$_GET['ID']}");
    //User did not win the attack, or the attack they won is not against the current opponent.
	if (!isset($_SESSION['attackwon']) || $_SESSION['attackwon'] != $_GET['ID'])
	{
		$api->UserInfoSet($userid,"xp",0);
		$api->UserStatusSet($userid,'infirmary',666,'Bug Abuse');
		alert("danger",$lang['ERROR_SECURITY'],$lang['ATTACK_FIGHT_BUGABUSE'],true,'index.php');
		die($h->endpage());
	}
    //Opponent does not exist.
	if(!$db->num_rows($od)) 
	{
		alert('danger',$lang['ERROR_NONUSER'],$lang['ATTACK_START_NONUSER'],true,'index.php');
		exit($h->endpage());
	}
	if ($db->num_rows($od) > 0)
	{
		$r = $db->fetch_row($od);
		$db->free_result($od);
        //Opponent was already beat.
		if ($r['hp'] == 1)
		{
			$api->UserInfoSet($userid,"xp",0);
			$api->UserStatusSet($userid,'infirmary',666,'Bug Abuse');
			alert('danger',$lang['ERROR_GENERIC'],$lang['ATTACK_FIGHT_BUGABUSE'],true,'index.php');
			exit($h->endpage());
		}
		else
		{
			$qe = $r['level'] * $r['level'] * $r['level'];
			$expgain = Random($qe / 2, $qe);
			$ir['total']=$ir['strength']+$ir['agility']+$ir['guard'];
			$ot=$db->fetch_row($db->query("SELECT * FROM `userstats` WHERE `userid` = {$r['userid']}"));
			$ototal=$ot['strength'] + $ot['agility'] + $ot['guard'];
            //Opponent is not within 75% of user's stats, so user only 
            //gains 25% of the XP they would normally get.
			if (($ir['total']*0.75) > $ototal)
			{
				$expgain=$expgain*0.25;
			}
			if ($expgain < 0)
			{
				$expgain=$expgain*-1;
			}
			$expperc = round($expgain / $ir['xp_needed'] * 100);
			$hosptime = Random(5, 30) + floor($ir['level'] / 10);
            //Give user XP.
			$api->UserInfoSetStatic($userid,"xp",$ir['xp']+$expgain);
			$hospreason = $db->escape("Used for experience by <a href='profile.php?user={$userid}'>{$ir['username']}</a>");
			//Set opponent's HP to 1.
            $api->UserInfoSet($r['userid'],"hp",1);
            //Place opponent in infirmary.
			$api->UserStatusSet($r['userid'],'infirmary',$hosptime,$hospreason);
            //Tell opponent they were attacked by the user and lost.
			$api->GameAddNotification($r['userid'],"<a href='profile.php?u=$userid'>{$ir['username']}</a> attacked you and left you for experience.");
			//Log that the user won.
            $api->SystemLogsAdd($userid,'attacking',"Attacked {$r['username']} [{$r['userid']}] and gained {$expperc}% Experience.");
			//Log that the opponent lost.
            $api->SystemLogsAdd($_GET['ID'],'attacking',"Attacked by <a href='profile.php?user={$userid}'>{$ir['username']}</a> [{$userid}] and left for experience.");			
			$_SESSION['attackwon'] = 0;
			$additionaltext = "";
            //Both players are in a guild.
			if ($ir['guild'] > 0 && $r['guild'] > 0)
			{
				$oppguild=$db->query("SELECT `guild_id` FROM `guild` WHERE `guild_id` = {$r['guild']} LIMIT 1");
				if ($db->num_rows($oppguild) > 0)
				{
					$warq = $db->query("SELECT `gw_id` FROM `guild_wars`
					WHERE (`gw_declarer` = {$ir['guild']} AND `gw_declaree` = {$r['guild']})
					OR (`gw_declaree` = {$ir['guild']} AND `gw_declarer` = {$r['guild']})");
                    //Both players' guilds are at war with each other.
					if ($db->fetch_single($warq) > 0)
					{
						$wr=$db->fetch_single($warq);
						$whoswho=$db->fetch_row($db->query("SELECT `gw_declarer`, `gw_declaree` FROM `guild_wars` WHERE `gw_id` = {$wr}"));
						//Give user's guild a point.
                        if ($whoswho['gw_declarer'] == $ir['guild'])
						{
							$db->query("UPDATE `guild_wars` SET `gw_drpoints` = `gw_drpoints` + 1 WHERE `gw_id` = {$wr}");
						}
						else
						{
							$db->query("UPDATE `guild_wars` SET `gw_depoints` = `gw_depoints` + 1 WHERE `gw_id` = {$wr}");
						}
						$additionaltext=$lang['ATTACK_FIGHT_POINT'];
					}
				}
				
			}
            //Tell user they won, and if they received a point or not.
			alert('success',"{$lang['ATTACK_FIGHT_END']} {$r['username']}!","{$lang['ATTACK_FIGHT_END1']} {$lang['ATTACK_FIGHT_END7']} ({$expperc}%, {$expgain}) {$additionaltext}",true,'index.php');
			//Opponent is NPC, so lets refill their HP, and remove infirmary time.
            if ($r['user_level'] == 'NPC')
			{
				$db->query("UPDATE `users` SET `hp` = `maxhp`  WHERE `userid` = {$r['userid']}");
				$db->query("UPDATE `infirmary` SET `infirmary_out` = 0 WHERE `infirmary_user` ={$r['userid']}");
			}
		}
	}
}
function mug()
{
	global $db,$userid,$ir,$h,$lang,$api;
	$_GET['ID'] = isset($_GET['ID']) && ctype_digit($_GET['ID']) ? $_GET['ID'] : 0;
	$_SESSION['attacking'] = 0;
	$ir['attacking'] = 0;
	$api->UserInfoSetStatic($userid,"attacking",0);
	$od = $db->query("SELECT * FROM `users` WHERE `userid` = {$_GET['ID']}");
    //User did not win fight, or won fight against someone else.
	if (!isset($_SESSION['attackwon']) || $_SESSION['attackwon'] != $_GET['ID'])
	{
		$api->UserInfoSet($userid,"xp",0);
		$api->UserStatusSet($userid,'infirmary',666,'Bug Abuse');
		alert("danger",$lang['ERROR_SECURITY'],$lang['ATTACK_FIGHT_BUGABUSE'],true,'index.php');
		die($h->endpage());
	}
    //Opponent does not exist.
	if(!$db->num_rows($od)) 
	{
		alert('danger',$lang['ERROR_NONUSER'],$lang['ATTACK_START_NONUSER'],true,'index.php');
		exit($h->endpage());
	}
	if ($db->num_rows($od) > 0)
	{
		$r = $db->fetch_row($od);
		$db->free_result($od);
        //Opponent's HP is 1, meaning fight has already concluded.
		if ($r['hp'] == 1)
		{
			$api->UserInfoSet($userid,"xp",0);
			$api->UserStatusSet($userid,'infirmary',666,'Bug Abuse');
			alert('danger',$lang['ERROR_GENERIC'],$lang['ATTACK_FIGHT_BUGABUSE'],true,'index.php');
			exit($h->endpage());
		}
		else
		{
			$stole = round($r['primary_currency'] / (Random(200, 1000) / 5));
			$hosptime = rand(20, 40) + floor($ir['level'] / 8);
			$hospreason = $db->escape("Mugged by <a href='profile.php?user={$userid}'>{$ir['username']}</a>");
            //Set opponent HP to 1.
			$api->UserInfoSet($r['userid'],"hp",1);
            //Take opponent's primary currency and give it to user.
			$api->UserTakeCurrency($r['userid'],'primary',$stole);
			$api->UserGiveCurrency($userid,'primary',$stole);
            //Place opponent in infirmary.
			$api->UserStatusSet($r['userid'],'infirmary',$hosptime,$hospreason);
            //Tell opponent they were mugged, and for how much, by user.
			$api->GameAddNotification($r['userid'], "<a href='profile.php?user=$userid'>{$ir['username']}</a> mugged you and stole " . number_format($stole) . " Primary Currency.");
			//Log that the user won and stole some primary currency, and that
            //the opponent lost and lost primary currency.
            $api->SystemLogsAdd($userid,'attacking',"Attacked {$r['username']} [{$r['userid']}] and stole {$stole} Primary Currency.");	
			$api->SystemLogsAdd($_GET['ID'],'attacking',"Mugged by <a href='profile.php?user={$userid}'>{$ir['username']}</a> [{$userid}], losing {$stole} Primary Currency.");
			$_SESSION['attackwon'] = 0;
			$additionaltext = "";
            //Both players are in a guild.
			if ($ir['guild'] > 0 && $r['guild'] > 0)
			{
				$oppguild=$db->query("SELECT `guild_id` FROM `guild` WHERE `guild_id` = {$r['guild']} LIMIT 1");
				if ($db->num_rows($oppguild) > 0)
				{
					$warq = $db->query("SELECT `gw_id` FROM `guild_wars`
					WHERE (`gw_declarer` = {$ir['guild']} AND `gw_declaree` = {$r['guild']})
					OR (`gw_declaree` = {$ir['guild']} AND `gw_declarer` = {$r['guild']})");
                    //Both players' guilds are at war with each other.
					if ($db->fetch_single($warq) > 0)
					{
						$wr=$db->fetch_single($warq);
						$whoswho=$db->fetch_row($db->query("SELECT `gw_declarer`, `gw_declaree` FROM `guild_wars` WHERE `gw_id` = {$wr}"));
						//Give user's guild a point.
                        if ($whoswho['gw_declarer'] == $ir['guild'])
						{
							$db->query("UPDATE `guild_wars` SET `gw_drpoints` = `gw_drpoints` + 1 WHERE `gw_id` = {$wr}");
						}
						else
						{
							$db->query("UPDATE `guild_wars` SET `gw_depoints` = `gw_depoints` + 1 WHERE `gw_id` = {$wr}");
						}
						$additionaltext=$lang['ATTACK_FIGHT_POINT'];
					}
				}
				
			}
            //Tell user they won the fight, and how much currency they took.
			alert('success',"{$lang['ATTACK_FIGHT_END']} {$r['username']}!","{$lang['ATTACK_FIGHT_END1']} {$lang['ATTACK_FIGHT_END8']} (" . number_format($stole) . "). {$additionaltext}",true,'index.php');
			//Opponent is NPC, so remove infirmary time and refill HP.
            if ($r['user_level'] == 'NPC')
			{
				$db->query("UPDATE `users` SET `hp` = `maxhp`  WHERE `userid` = {$r['userid']}");
				$db->query("UPDATE `infirmary` SET `infirmary_out` = 0 WHERE `infirmary_user` ={$r['userid']}");
			}
		}
		$npcquery=$db->query("SELECT * FROM `botlist` WHERE `botuser` = {$r['userid']}");
        //Opponent is registered on bot list.
		if ($db->num_rows($npcquery) > 0)
		{
			$results2=$db->fetch_row($npcquery);
			$timequery=$db->query("SELECT `lasthit` FROM `botlist_hits` WHERE `userid` = {$userid} && `botid` = {$r['userid']}");
			$r2=$db->fetch_single($timequery);
            //Opponent's drop has already been collected and the time hasn't reset.
			if ((time() <= ($r2 + $results2['botcooldown'])) && ($r2 > 0))
			{
				//Nope
			}
            //Bot's item can be collected.
			else
			{
                //Give user the bot's item.
				$api->UserGiveItem($userid,$results2['botitem'],1);
				$time=time();
				$exists=$db->query("SELECT `botid` FROM `botlist_hits` WHERE `userid` = {$userid} AND `botid` = {$r['userid']}");
				//Place user's hittime into database.
                if ($db->num_rows($exists) == 0)
				{
					$db->query("INSERT INTO `botlist_hits` (`userid`, `botid`, `lasthit`) VALUES ('{$userid}', '{$r['userid']}', '{$time}')");
				}
				else
				{
					$db->query("UPDATE `botlist_hits` SET `lasthit` = {$time} WHERE `userid` = {$userid} AND `botid` = {$r['userid']}");
				}
                //Tell user they took an item.
				$api->GameAddNotification($userid,"For successfully mugging " . $api->SystemUserIDtoName($r['userid']) . ", you received 1 " . $api->SystemItemIDtoName($results2['botitem']));
			}
		}
	}
}
$h->endpage();