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
	$menuhide = 1;
	$atkpage = 1;
	$tresder = Random(100, 999);
	$_GET['user'] =  (isset($_GET['user']) && is_numeric($_GET['user']))  ? abs($_GET['user']) : '';
	if (empty($_GET['nextstep']))
	{
		$_GET['nextstep']=0;
	}
	if ($_GET['nextstep'] > 0)
	{
		$_GET['tresde'] = (isset($_GET['tresde']) && is_numeric($_GET['tresde'])) ? abs($_GET['tresde']) : 0;
		if (!isset($_SESSION['tresde']))
		{
			$_SESSION['tresde'] = 0;
		}
		if (($_SESSION['tresde'] == $_GET['tresde']) || $_GET['tresde'] < 100)
		{
			alert("danger",$lang['ERROR_GENERIC'],$lang['ATTACK_START_NOREFRESH'],true,'index.php');
			die($h->endpage());
		}
		$_SESSION['tresde'] = $_GET['tresde'];
	}
	if (!$_GET['user'])
	{
		alert("danger",$lang['ERROR_NONUSER'],$lang['ATTACK_START_NOUSER'],true,'index.php');
		die($h->endpage());
	}
<<<<<<< HEAD
	//Test for if the specified opponent is the current user.
=======
>>>>>>> parent of 725b513... Some more commenting
	else if ($_GET['user'] == $userid)
	{
		alert("danger",$lang['ERROR_GENERIC'],$lang['ATTACK_START_NOTYOU'],true,'index.php');
		die($h->endpage());
	}
<<<<<<< HEAD
	//Test for if the current user has more than 1 health points.
=======
>>>>>>> parent of 725b513... Some more commenting
	else if ($ir['hp'] <= 1)
	{
		alert("danger",$lang["GEN_INFIRM"],$lang['ATTACK_START_YOUNOHP'],true,'index.php');
		die($h->endpage());
	}
<<<<<<< HEAD
	//Test for if the player has already lost a fight.
=======
>>>>>>> parent of 725b513... Some more commenting
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
	if ($db->num_rows($q) == 0)
	{
		alert("danger",$lang['ERROR_NONUSER'],$lang['ATTACK_START_NONUSER'],true,'index.php');
		die($h->endpage());
	}
	$odata = $db->fetch_row($q);
	$db->free_result($q);
	if ($ir['attacking'] && $ir['attacking'] != $_GET['user'])
	{
		$_SESSION['attacklost'] = 0;
		alert("danger",$lang['ERROR_UNKNOWN'],$lang['ATTACK_START_UNKNOWNERROR'],true,'index.php');
		$api->UserInfoSetStatic($userid,"attacking",0);
		die($h->endpage());
	}
<<<<<<< HEAD
	//Check that the opponent has 1 health point.
	else if ($odata['hp'] == 1)
=======
	if ($odata['hp'] == 1)
>>>>>>> parent of 725b513... Some more commenting
	{
		$_SESSION['attacking'] = 0;
		$ir['attacking'] = 0;
		$api->UserInfoSetStatic($userid,"attacking",0);
		alert("danger",$lang['ERROR_GENERIC'],"{$odata['username']} {$lang['ATTACK_START_OPPNOHP']}",true,'index.php');
		die($h->endpage());
	}
<<<<<<< HEAD
	//Check if the opponent is currently in the infirmary.
=======
>>>>>>> parent of 725b513... Some more commenting
	else if ($api->UserStatus($_GET['user'],'infirmary') == true)
	{
		$_SESSION['attacking'] = 0;
		$ir['attacking'] = 0;
		$api->UserInfoSetStatic($userid,"attacking",0);
		alert("danger",$lang['GEN_INFIRM'],"{$odata['username']} {$lang['ATTACK_START_OPPINFIRM']}",true,'index.php');
		die($h->endpage());
	}
<<<<<<< HEAD
	//Check if the current user is in the infirmary.
=======
>>>>>>> parent of 725b513... Some more commenting
	else if ($api->UserStatus($ir['userid'],'infirmary') == true)
	{
		$_SESSION['attacking'] = 0;
		$ir['attacking'] = 0;
		$api->UserInfoSetStatic($userid,"attacking",0);
		alert("danger",$lang['GEN_INFIRM'],$lang['ATTACK_START_YOUINFIRM'],true,'index.php');
		die($h->endpage());
	}
<<<<<<< HEAD
	//Check if the opponent is in the dungeon.
	else if ($api->UserStatus($userid,'dungeon') == true)
=======
	else if ($api->UserStatus($_GET['user'],'dungeon') == true)
>>>>>>> parent of 725b513... Some more commenting
	{
		$_SESSION['attacking'] = 0;
		$ir['attacking'] = 0;
		$api->UserInfoSetStatic($userid,"attacking",0);
		alert("danger",$lang['GEN_DUNG'],"{$odata['username']} {$lang['ATTACK_START_OPPDUNG']}",true,'index.php');
		die($h->endpage());
	}
<<<<<<< HEAD
	//Check if the current user is in the dungeon.
=======
>>>>>>> parent of 725b513... Some more commenting
	else if ($api->UserStatus($userid,'dungeon') == true)
	{
		$_SESSION['attacking'] = 0;
		$ir['attacking'] = 0;
		$api->UserInfoSetStatic($userid,"attacking",0);
		alert("danger",$lang['GEN_DUNG'],$lang['ATTACK_START_YOUDUNG'],true,'index.php');
		die($h->endpage());
	}
<<<<<<< HEAD
	//Check if opponent has permission to be attacked.
=======
>>>>>>> parent of 725b513... Some more commenting
	else if (permission('CanBeAttack',$_GET['user']) == false)
	{
		$_SESSION['attacking'] = 0;
		$ir['attacking'] = 0;
		$api->UserInfoSetStatic($userid,"attacking",0);
		alert("danger",$lang['ERROR_GENERIC'],$lang['ATTACK_START_OPPUNATTACK'],true,'index.php');
		die($h->endpage());
	}
<<<<<<< HEAD
	//Check if the current player has permission to attack.
=======
>>>>>>> parent of 725b513... Some more commenting
	else if (permission('CanAttack',$userid) == false)
	{
		$_SESSION['attacking'] = 0;
		$ir['attacking'] = 0;
		$api->UserInfoSetStatic($userid,"attacking",0);
		alert("danger",$lang['ERROR_GENERIC'],$lang['ATTACK_START_YOUUNATTACK'],true,'index.php');
		die($h->endpage());
	}
<<<<<<< HEAD
	//Check if the opponent is level 2 or lower, and has been on in the last 15 minutes.
=======
>>>>>>> parent of 725b513... Some more commenting
	else if ($odata['level'] < 3 && $odata['laston'] > $laston)
	{
		$_SESSION['attacking'] = 0;
		$ir['attacking'] = 0;
		$api->UserInfoSetStatic($userid,"attacking",0);
		alert("danger",$lang['ERROR_GENERIC'],$lang['ATTACK_START_THEYLOWLEVEL'],true,'index.php');
		die($h->endpage());
	}
	$_GET['weapon'] = (isset($_GET['weapon']) && is_numeric($_GET['weapon'])) ? abs($_GET['weapon']) : '';
	if ($_GET['weapon'])
	{
		if (!$_GET['nextstep'])
		{
			$_GET['nextstep'] = 1;
		}
		if ($_GET['nextstep'] >= $set['MaxAttacksPerSession'])
		{
			$_SESSION['attacking'] = 0;
			$ir['attacking'] = 0;
			$api->UserInfoSetStatic($userid,"attacking",0);
			alert("warning",$lang['ERROR_GENERIC'],$lang['ATTACK_FIGHT_STALEMATE'],true,'index.php');
			die($h->endpage());
		}
		if ($_SESSION['attacking'] == 0 && $ir['attacking'] == 0)
		{
			if ($youdata['energy'] >= $youdata['maxenergy'] / $set['AttackEnergyCost'])
			{
				$youdata['energy'] -= floor($youdata['maxenergy'] / $set['AttackEnergyCost']);
				$cost = floor($youdata['maxenergy'] / $set['AttackEnergyCost']);
				$api->UserInfoSet($userid,'energy',"-{$cost}");
				$_SESSION['attackdmg'] = 0;
			}
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
		$api->UserINfoSetStatic($userid,"attacking",$ir['attacking']);
		$_GET['nextstep'] = (isset($_GET['nextstep']) && is_numeric($_GET['nextstep'])) ? abs($_GET['nextstep']) : '';
		if ($_GET['weapon'] != $ir['equip_primary'] && $_GET['weapon'] != $ir['equip_secondary'])
		{
			$api->UserInfoSet($userid,'xp',0);
			$api->UserStatusSet($userid,'infirmary',666,'Bug Abuse');
			alert("danger",$lang['ERROR_SECURITY'],$lang['ATTACK_FIGHT_BUGABUSE'],true,'index.php');
			die($h->endpage());
		}
		$winfo_sql ="SELECT `itmname`, `weapon` FROM `items` WHERE `itmid` = {$_GET['weapon']} LIMIT 1";
		$qo = $db->query($winfo_sql);
		if ($db->num_rows($qo) == 0)
		{
			alert("danger",$lang['ERROR_GENERIC'],$lang['ATTACK_FIGHT_BADWEAP'],true,'index.php');
			die($h->endpage());
		}
		$r1 = $db->fetch_row($qo);
		$db->free_result($qo);
		$mydamage = round(($r1['weapon'] * $youdata['strength'] / ($odata['guard'] / 1.5)) * (Random(10000, 12000) / 10000));
		$hitratio = max(10, min(60 * $ir['agility'] / $odata['agility'], 95));
		if (Random(1, 100) <= $hitratio)
		{
			if ($odata['equip_armor'] > 0)
			{
				$armorinfo_sql ="SELECT `armor` FROM `items` WHERE `itmid` = {$odata['equip_armor']} LIMIT 1";
				$q3 = $db->query($armorinfo_sql);
				if ($db->num_rows($q3) > 0)
				{
					$mydamage -= $db->fetch_single($q3);
				}
				$db->free_result($q3);
			}
			if ($mydamage < -100000)
			{
				$mydamage = abs($mydamage);
			}
			else if ($mydamage < 1)
			{
				$mydamage = 1;
			}
			$crit = Random(1, 40);
			if ($crit == 17)
			{
				$mydamage *= Random(20, 40) / 10;
			}
			else if ($crit == 25 OR $crit == 8)
			{
				$mydamage /= (Random(20, 40) / 10);
			}
			$mydamage = round($mydamage);
			$odata['hp'] -= $mydamage;
			if ($odata['hp'] == 1)
			{
				$odata['hp'] = 0;
				$mydamage += 1;
			}
			if (($odata['hp'] - $mydamage) < 0)
			{
				$mydamage=$mydamage+$odata['hp'];
			}
			if ($odata['hp'] < 0)
			{
				$odata['hp'] = 0;
			}
			$api->UserInfoSet($_GET['user'],"hp","-{$mydamage}",false);
			echo "{$_GET['nextstep']}) {$lang['ATTACK_FIGHT_ATTACKY_HIT1']} {$r1['itmname']} {$lang['ATTACK_FIGHT_ATTACKY_HIT2']} {$odata['username']} {$lang['ATTACK_FIGHT_ATTACKY_HIT3']} {$mydamage} {$lang['ATTACK_FIGHT_ATTACKY_HIT4']} ({$odata['hp']} {$lang['ATTACK_FIGHT_ATTACK_HPREMAIN']})<br />";
			$_SESSION['attackdmg'] += $mydamage;
		}
		else
		{
			echo "{$_GET['nextstep']}) {$lang['ATTACK_FIGHT_ATTACKY_MISS1']} {$odata['username']} {$lang['ATTACK_FIGHT_ATTACKY_MISS2']} ({$odata['hp']} {$lang['ATTACK_FIGHT_ATTACK_HPREMAIN']})<br />";
		}
		if ($odata['hp'] <= 0)
		{
			$odata['hp'] = 0;
			$_SESSION['attackwon'] = $_GET['user'];
			$api->UserInfoSet($_GET['user'],'hp',0);
			echo "<br />
			<b>{$lang['ATTACK_FIGHT_ATTACKY_WIN1']} {$odata['username']} {$lang['ATTACK_FIGHT_ATTACKY_WIN2']}</b><br />
			<form action='?action=mug&ID={$_GET['user']}' method='post'><input class='btn btn-default' type='submit' value='{$lang['ATTACK_FIGHT_OUTCOME1']} {$lang['GEN_THEM']}' /></form>
			<form action='?action=beat&ID={$_GET['user']}' method='post'><input class='btn btn-default' type='submit' value='{$lang['ATTACK_FIGHT_OUTCOME2']} {$lang['GEN_THEM']}' /></form>
			<form action='?action=xp&ID={$_GET['user']}' method='post'><input class='btn btn-default' type='submit' value='{$lang['ATTACK_FIGHT_OUTCOME3']} {$lang['GEN_THEM']}' /></form>";
		}
		else
		{
			$eq = $db->query("SELECT `itmname`,`weapon` FROM  `items` WHERE `itmid` IN({$odata['equip_primary']}, {$odata['equip_secondary']})");
			if ($db->num_rows($eq) == 0)
			{
				$wep = "{$lang['ATTACK_FIGHT_ATTACK_FISTS']}";
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
				$weptouse = Random(0, $cnt - 1);
				$wep = $enweps[$weptouse]['itmname'];
				$dam = round(($enweps[$weptouse]['weapon'] * $odata['strength'] / ($youdata['guard'] / 1.5)) * (Random(10000, 12000) / 10000));
			}
			$hitratio = max(10, min(60 * $odata['agility'] / $ir['agility'], 95));
			if (Random(1, 100) <= $hitratio)
			{
				if ($ir['equip_armor'] > 0)
				{
					$q3 = $db->query("SELECT `armor` FROM `items` WHERE `itmid` = {$ir['equip_armor']} LIMIT 1");
					if ($db->num_rows($q3) > 0)
					{
						$dam -= $db->fetch_single($q3);
					}
					$db->free_result($q3);
				}
				if ($dam < -100000)
				{
					$dam = abs($dam);
				}
				else if ($dam < 1)
				{
					$dam = 1;
				}
				$crit = Random(1, 40);
				if ($crit == 17)
				{
					$dam *= Random(20, 40) / 10;
				}
				else if ($crit == 25 OR $crit == 8)
				{
					$dam /= (Random(20, 40) / 10);
				}
				$dam = round($dam);
				$youdata['hp'] -= $dam;
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
				if ($ir['hp'] < 0)
				{
					$ir['hp'] = 0;
				}
				$api->UserInfoSet($userid,"hp",0);
				$ns = $_GET['nextstep'] + 1;
				echo "{$ns}) {$lang['ATTACK_FIGHT_ATTACKO_HIT1']} {$wep}, {$odata['username']} {$lang['ATTACK_FIGHT_ATTACKO_HIT2']} {$dam} {$lang['ATTACK_FIGHT_ATTACKY_HIT4']} ({$youdata['hp']} {$lang['ATTACK_FIGHT_ATTACK_HPREMAIN']})<br />";
			}
			else
			{
				$ns = $_GET['nextstep'] + 1;
				echo "{$ns}) {$odata['username']} {$lang['ATTACK_FIGHT_ATTACKO_MISS']} ({$youdata['hp']} {$lang['ATTACK_FIGHT_ATTACK_HPREMAIN']})<br />";
			}
			if ($youdata['hp'] <= 0)
			{
				$youdata['hp'] = 0;
				$_SESSION['attacklost'] = $_GET['user'];
				$api->UserInfoSet($userid,"hp",0);
				echo "<form action='?action=lost&ID={$_GET['user']}' method='post'><input type='submit' class='btn btn-default' value='{$lang['GEN_CONTINUE']}' />";
			}
		}
	}
	else if ($odata['hp'] < 5)
	{
		alert("danger",$lang['ERROR_GENERIC'],"{$odata['username']} {$lang['ATTACK_START_OPPNOHP']}",true,'index.php');
		die($h->endpage());
	}
	else if ($ir['guild'] == $odata['guild'] && $ir['guild'] > 0)
	{
		alert("danger",$lang['ERROR_GENERIC'],"{$odata['username']} {$lang['ATTACK_FIGHT_FINAL_GUILD']}",true,'index.php');
		die($h->endpage());
	}
	else if ($youdata['energy'] < $youdata['maxenergy'] / $set['AttackEnergyCost'])
	{
		$EnergyPercent=floor(100/$set['AttackEnergyCost']);
		$UserCurrentEnergy=floor($ir['energy']/$ir['maxenergy']);
		alert("danger","{$lang['ERROR_GENERIC']}","{$lang['ATTACK_FIGHT_LOWENG1']} {$EnergyPercent}{$lang['ATTACK_FIGHT_LOWENG2']} {$UserCurrentEnergy}% <a href='index.php'>{$lang['GEN_GOHOME']}</a>.");
		die($h->endpage());
	}
	else if ($youdata['location'] != $odata['location'])
	{
		alert("danger",$lang['ERROR_GENERIC'],$lang['ATTACK_FIGHT_FINAL_CITY'],true,'index.php');
		die($h->endpage());
	}
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
						<div class='progress-bar progress-bar-success' role='progressbar' aria-valuenow='{$vars['hpperc']}' aria-valuemin='0' aria-valuemax='100' style='width:{$vars['hpperc']}%'>
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
						<div class='progress-bar progress-bar-success' role='progressbar' aria-valuenow='{$vars2['hpperc']}' aria-valuemin='0' aria-valuemax='100' style='width:{$vars2['hpperc']}%'>
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
	global $db,$userid,$ir,$h,$lang,$api,$set,$atkpage;
	$_GET['ID'] = isset($_GET['ID']) && ctype_digit($_GET['ID']) ? $_GET['ID'] : 0;
	$_SESSION['attacking'] = 0;
	$ir['attacking'] = 0;
	$api->UserInfoSetStatic($userid,"attacking",0);
	$od = $db->query("SELECT * FROM `users` WHERE `userid` = {$_GET['ID']}");
	if (!isset($_SESSION['attackwon']) || $_SESSION['attackwon'] != $_GET['ID'])
	{
		$api->UserInfoSet($userid,"xp",0);
		$api->UserStatusSet($userid,'infirmay',666,'Bug Abuse');
		alert("danger",$lang['ERROR_SECURITY'],$lang['ATTACK_FIGHT_BUGABUSE'],true,'index.php');
		die($h->endpage());
	}
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
		if ($r['hp'] == 1) 
		{
			alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['ATTACK_FIGHT_BUGABUSE']}");
			die($h->endpage());
		}
		$hosptime = Random(75, 175) + floor($ir['level'] / 2);
		alert('success',"{$lang['ATTACK_FIGHT_END']} {$r['username']}!!","{$lang['ATTACK_FIGHT_END2']} {$lang['ATTACK_FIGHT_END3']} {$hosptime} {$lang["GEN_MINUTES"]} {$lang['ATTACK_FIGHT_END4']}",true,'index.php');
		$hospreason = $db->escape("Beat up by <a href='profile.php?user={$userid}'>{$ir['username']}</a>");
		$api->UserInfoSet($r['userid'],"hp",1);
		$api->UserStatusSet($r['userid'],'infirmary',$hosptime,$hospreason);
		$api->GameAddNotification($r['userid'], "<a href='profile.php?user=$userid'>{$ir['username']}</a> brutally attacked you and caused {$hosptime} minutes worth of damage.");
		$api->SystemLogsAdd($userid,'attacking',"Attacked {$r['username']} [{$r['userid']}] and brutally injured them, causing {$hosptime} minutes of damage.");
		$_SESSION['attackwon'] = false;
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
	global $db,$userid,$ir,$h,$lang,$api,$set,$atkpage;
	$_GET['ID'] = isset($_GET['ID']) && ctype_digit($_GET['ID']) ? $_GET['ID'] : 0;
	$_SESSION['attacking'] = 0;
	$ir['attacking'] = 0;
	if (!isset($_SESSION['attacklost']) || $_SESSION['attacklost'] != $_GET['ID'])
	{
		$api->UserInfoSet($userid,"xp",0);
		$api->UserStatusSet($userid,'infirmary',666,'Bug Abuse');
		alert("danger",$lang['ERROR_SECURITY'],$lang['ATTACK_FIGHT_BUGABUSE'],true,'index.php');
		die($h->endpage());
	}
	if(!$_GET['ID']) 
	{
		alert('warning',$lang['CSRF_ERROR_TITLE'],$lang['ATT_NC'],true,'index.php');
		die($h->endpage());
	}
	$od = $db->query("SELECT `username`, `level`, `user_level`, `guild` FROM `users` WHERE `userid` = {$_GET['ID']}");
	if(!$db->num_rows($od)) 
	{
		echo "404";
		die($h->endpage());
	}
	$r = $db->fetch_row($od);
	$db->free_result($od);
	$qe = $ir['level'] * $ir['level'] * $ir['level'];
	$expgain = Random($qe / 2, $qe);
	if ($expgain < 0)
	{
		$expgain=$expgain*-1;
	}
	$expgainp = $expgain / $ir['xp_needed'] * 100;
	alert('danger',"{$lang['ATTACK_FIGHT_END5']} {$r['username']}!","{$lang['ATTACK_FIGHT_END6']} (" . number_format($expgainp, 2) . "%)!",true,'index.php');
	$api->UserInfoSet($userid,"xp","-{$expgain}");
	$api->UserInfoSetStatic($userid,"attacking",0);
	$hosptime = Random(75, 175) + floor($ir['level'] / 2);
	$hospreason = 'Picked a fight and lost';
	$api->UserStatusSet($r['userid'],'infirmary',$hosptime,$hospreason);
	//Give winner some XP
	$r['xp_needed'] = round(($r['level'] + 2.25) * ($r['level'] + 2.25) * ($r['level'] + 2.25) * 2);
	$qe2 = $r['level'] * $r['level'] * $r['level'];
	$expgain2=Random($qe2 / 2, $qe2);
	$expgainp2 = $expgain2 / $r['xp_needed'] * 100;
	$expperc2 = round($expgainp / $r['xp_needed'] * 100);
	$api->GameAddNotification($_GET['ID'], "<a href='profile.php?user=$userid'>{$ir['username']}</a> attacked you and lost, which gave you {$expperc2}% Experience.");
	$api->SystemLogsAdd($userid,'attacking',"Attacked {$r['username']} [{$_GET['ID']}] and lost, gaining {$hosptime} minutes in the infirmary.");
	$api->UserInfoSet($_GET['ID'],"xp",$expgainp2);
	$_SESSION['attacklost'] = 0;
}
function xp()
{
	global $db,$userid,$ir,$h,$lang,$api,$set,$atkpage;
	$_GET['ID'] = isset($_GET['ID']) && ctype_digit($_GET['ID']) ? $_GET['ID'] : 0;
	$_SESSION['attacking'] = 0;
	$ir['attacking'] = 0;
	$api->UserInfoSetStatic($userid,"attacking",0);
	$od = $db->query("SELECT * FROM `users` WHERE `userid` = {$_GET['ID']}");
	if (!isset($_SESSION['attackwon']) || $_SESSION['attackwon'] != $_GET['ID'])
	{
		$api->UserInfoSet($userid,"xp",0);
		$api->UserStatusSet($userid,'infirmary',666,'Bug Abuse');
		alert("danger",$lang['ERROR_SECURITY'],$lang['ATTACK_FIGHT_BUGABUSE'],true,'index.php');
		die($h->endpage());
	}
	if(!$db->num_rows($od)) 
	{
		alert('danger',$lang['ERROR_NONUSER'],$lang['ATTACK_START_NONUSER'],true,'index.php');
		exit($h->endpage());
	}
	if ($db->num_rows($od) > 0)
	{
		$r = $db->fetch_row($od);
		$db->free_result($od);
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
			if (($ir['total']*0.75) > $ototal)
			{
				$expgain=$expgain*0.25;
			}
			if ($expgain < 0)
			{
				$expgain=$expgain*-1;
			}
			$expperc = round($expgain / $ir['xp_needed'] * 100);
			alert('success',"{$lang['ATTACK_FIGHT_END']} {$r['username']}!","{$lang['ATTACK_FIGHT_END1']} {$lang['ATTACK_FIGHT_END7']} ({$expperc}%, {$expgain})",true,'index.php');
			$hosptime = Random(5, 30) + floor($ir['level'] / 10);
			$api->UserInfoSetStatic($userid,"xp",$ir['xp']+$expgain);
			$hospreason = $db->escape("Used for experience by <a href='profile.php?user={$userid}'>{$ir['username']}</a>");
			$api->UserInfoSet($r['userid'],"hp",1);
			$api->UserStatusSet($r['userid'],'infirmary',$hosptime,$hospreason);
			$api->GameAddNotification($r['userid'],"<a href='profile.php?u=$userid'>{$ir['username']}</a> attacked you and left you for experience.");
			$api->SystemLogsAdd($userid,'attacking',"Attacked {$r['username']} [{$r['userid']}] and gained {$expperc}% Experience.");			
			$_SESSION['attackwon'] = false;
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
	global $db,$userid,$ir,$h,$lang,$api,$set,$atkpage;
	$_GET['ID'] = isset($_GET['ID']) && ctype_digit($_GET['ID']) ? $_GET['ID'] : 0;
	$_SESSION['attacking'] = 0;
	$ir['attacking'] = 0;
	$api->UserInfoSetStatic($userid,"attacking",0);
	$od = $db->query("SELECT * FROM `users` WHERE `userid` = {$_GET['ID']}");
	if (!isset($_SESSION['attackwon']) || $_SESSION['attackwon'] != $_GET['ID'])
	{
		$api->UserInfoSet($userid,"xp",0);
		$api->UserStatusSet($userid,'infirmary',666,'Bug Abuse');
		alert("danger",$lang['ERROR_SECURITY'],$lang['ATTACK_FIGHT_BUGABUSE'],true,'index.php');
		die($h->endpage());
	}
	if(!$db->num_rows($od)) 
	{
		alert('danger',$lang['ERROR_NONUSER'],$lang['ATTACK_START_NONUSER'],true,'index.php');
		exit($h->endpage());
	}
	if ($db->num_rows($od) > 0)
	{
		$r = $db->fetch_row($od);
		$db->free_result($od);
		if ($r['hp'] == 1)
		{
			$api->UserInfoSet($userid,"xp",0);
			$api->UserStatusSet($userid,'infirmary',666,'Bug Abuse');
			alert('danger',$lang['ERROR_GENERIC'],$lang['ATTACK_FIGHT_BUGABUSE'],true,'index.php');
			exit($h->endpage());
		}
		else
		{
			$stole = round($r['primary_currency'] / (Random(50, 1000) / 5));
			alert('success',"{$lang['ATTACK_FIGHT_END']} {$r['username']}!","{$lang['ATTACK_FIGHT_END1']} {$lang['ATTACK_FIGHT_END8']} (" . number_format($stole) . ")",true,'index.php');
			$hosptime = rand(20, 40) + floor($ir['level'] / 8);
			$hospreason = $db->escape("Mugged by <a href='profile.php?user={$userid}'>{$ir['username']}</a>");
			$api->UserInfoSet($r['userid'],"hp",1);
			$api->UserTakeCurrency($r['userid'],'primary',$stole);
			$api->UserGiveCurrency($userid,'primary',$stole);
			$api->UserStatusSet($r['userid'],'infirmary',$hosptime,$hospreason);
			$api->GameAddNotification($r['userid'], "<a href='profile.php?user=$userid'>{$ir['username']}</a> mugged you and stole " . number_format($stole) . " Primary Currency.");
			$api->SystemLogsAdd($userid,'attacking',"Attacked {$r['username']} [{$r['userid']}] and stole {$stole} Primary Currency.");					
			$_SESSION['attackwon'] = 0;
			if ($r['user_level'] == 0)
			{
				$db->query("UPDATE `users` SET `hp` = `maxhp`  WHERE `userid` = {$r['userid']}");
				$db->query("UPDATE `infirmary` SET `infirmary_out` = 0 WHERE `infirmary_user` ={$r['userid']}");
			}
		}
		$npcquery=$db->query("SELECT * FROM `botlist` WHERE `botuser` = {$r['userid']}");
		if ($db->num_rows($npcquery) > 0)
		{
			$results2=$db->fetch_row($npcquery);
			$timequery=$db->query("SELECT `lasthit` FROM `botlist_hits` WHERE `userid` = {$userid} && `botid` = {$r['userid']}");
			$r2=$db->fetch_single($timequery);
			if ((time() <= ($r2 + $results2['botcooldown'])) && ($r2 > 0))
			{
				//Nope
			}
			else
			{
				$api->UserGiveItem($userid,$results2['botitem'],1);
				$time=time();
				$exists=$db->query("SELECT `botid` FROM `botlist_hits` WHERE `userid` = {$userid} AND `botid` = {$r['userid']}");
				if ($db->num_rows($exists) == 0)
				{
					$db->query("INSERT INTO `botlist_hits` (`userid`, `botid`, `lasthit`) VALUES ('{$userid}', '{$r['userid']}', '{$time}')");
				}
				else
				{
					$db->query("UPDATE `botlist_hits` SET `lasthit` = {$time} WHERE `userid` = {$userid} AND `botid` = {$r['userid']}");
				}
				$api->GameAddNotification($userid,"For successfully mugging " . $api->SystemUserIDtoName($r['userid']) . ", you received 1 " . $api->SystemItemIDtoName($results2['botitem']));
			}
		}
	}
}
$h->endpage();