<?php
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
	$tresder = mt_rand(100, 999);
	$_GET['user'] =  (isset($_GET['user']) && is_numeric($_GET['user']))  ? abs(intval($_GET['user'])) : '';
	if (empty($_GET['nextstep']))
	{
		$_GET['nextstep']=0;
	}
	if ($_GET['nextstep'] > 0)
	{
		$_GET['tresde'] = (isset($_GET['tresde']) && is_numeric($_GET['tresde'])) ? abs(intval($_GET['tresde'])) : 0;
		if (!isset($_SESSION['tresde']))
		{
			$_SESSION['tresde'] = 0;
		}
		if (($_SESSION['tresde'] == $_GET['tresde']) || $_GET['tresde'] < 100)
		{
			alert("danger","{$lang['ERROR_GENERIC']}","{$lang['ATTACK_START_NOREFRESH']} <a href='index.php'>{$lang['GEN_GOHOME']}</a>.");
			die($h->endpage());
		}
		$_SESSION['tresde'] = $_GET['tresde'];
	}
	if (!$_GET['user'])
	{
		alert("danger","{$lang['ERROR_NONUSER']}","{$lang['ATTACK_START_NOUSER']} <a href='index.php'>{$lang['GEN_GOHOME']}</a>.");
		die($h->endpage());
	}
	else if ($_GET['user'] == $userid)
	{
		alert("danger","{$lang['ERROR_NONUSER']}","{$lang['ATTACK_START_NOTYOU']} <a href='index.php'>{$lang['GEN_GOHOME']}</a>.");
		die($h->endpage());
	}
	else if ($ir['hp'] <= 1)
	{
		alert("danger","{$lang["GEN_INFIRM"]}","{$lang['ATTACK_START_YOUNOHP']} <a href='index.php'>{$lang['GEN_GOHOME']}</a>.");
		die($h->endpage());
	}
	else if (isset($_SESSION['attacklost']) && $_SESSION['attacklost'] == 1)
	{
		$_SESSION['attacklost'] = 0;
		alert("danger","{$lang['ERROR_GENERIC']}","{$lang['ATTACK_START_YOUCHICKEN']} <a href='index.php'>{$lang['GEN_GOHOME']}</a>.");
		die($h->endpage());
	}
	$youdata = $ir;
	$q = $db->query("SELECT `u`.`userid`, `hp`, `equip_armor`, `username`,
	       `equip_primary`, `equip_secondary`, `guild`, `location`, `maxhp`,
	       `guard`, `agility`, `strength`, `gender`
			FROM `users` AS `u`
			INNER JOIN `userstats` AS `us` ON `u`.`userid` = `us`.`userid`
			WHERE `u`.`userid` = {$_GET['user']}
			LIMIT 1");
	if ($db->num_rows($q) == 0)
	{
		alert("danger","{$lang['ERROR_NONUSER']}","{$lang['ATTACK_START_NONUSER']} <a href='index.php'>{$lang['GEN_GOHOME']}</a>.");
		die($h->endpage());
	}
	$odata = $db->fetch_row($q);
	$db->free_result($q);
	if ($ir['attacking'] && $ir['attacking'] != $_GET['user'])
	{
		$_SESSION['attacklost'] = 'false';
		alert("danger","{$lang['ERROR_UNKNOWN']}","{$lang['ATTACK_START_UNKNOWNERROR']} <a href='index.php'>{$lang['GEN_GOHOME']}</a>.");
		$db->query("UPDATE `users` SET `attacking` = 'false' WHERE `userid` = {$userid}");
		die($h->endpage());
	}
	if ($odata['hp'] == 1)
	{
		$_SESSION['attacking'] = 'false';
		$ir['attacking'] = 0;
		$db->query("UPDATE `users` SET `attacking` = 'false' WHERE `userid` = {$userid}");
		alert("danger","{$lang['ERROR_GENERIC']}","{$odata['username']} {$lang['ATTACK_START_OPPNOHP']} <a href='index.php'>{$lang['GEN_GOHOME']}</a>.");
		die($h->endpage());
	}
	else if (user_infirmary($_GET['user']) == true)
	{
		$_SESSION['attacking'] = 'false';
		$ir['attacking'] = 0;
		$db->query("UPDATE `users` SET `attacking` = 'false' WHERE `userid` = {$userid}");
		alert("danger","{$lang['GEN_INFIRM']}","{$odata['username']} {$lang['ATTACK_START_OPPINFIRM']} <a href='index.php'>{$lang['GEN_GOHOME']}</a>.");
		die($h->endpage());
	}
	else if (user_infirmary($ir['userid']) == true)
	{
		$_SESSION['attacking'] = 'false';
		$ir['attacking'] = 0;
		$db->query("UPDATE `users` SET `attacking` = 'false' WHERE `userid` = {$userid}");
	   alert("danger","{$lang['GEN_INFIRM']}","{$lang['ATTACK_START_YOUINFIRM']} <a href='index.php'>{$lang['GEN_GOHOME']}</a>.");
		die($h->endpage());
	}
	else if (user_dungeon($_GET['user']) == true)
	{
		$_SESSION['attacking'] = 'false';
		$ir['attacking'] = 0;
		$db->query("UPDATE `users` SET `attacking` = 'false' WHERE `userid` = {$userid}");
		alert("danger","{$lang['GEN_DUNG']}","{$odata['username']} {$lang['ATTACK_START_OPPDUNG']} <a href='index.php'>{$lang['GEN_GOHOME']}</a>.");
		die($h->endpage());
	}
	else if (user_dungeon($userid) == true)
	{
		$_SESSION['attacking'] = 'false';
		$ir['attacking'] = 0;
		$db->query("UPDATE `users` SET `attacking` = 'false' WHERE `userid` = {$userid}");
		alert("danger","{$lang['GEN_DUNG']}","{$lang['ATTACK_START_YOUDUNG']} <a href='index.php'>{$lang['GEN_GOHOME']}</a>.");
		die($h->endpage());
	}
	else if (permission('CanBeAttack',$_GET['user']) == false)
	{
		$_SESSION['attacking'] = 'false';
		$ir['attacking'] = 0;
		$db->query("UPDATE `users` SET `attacking` = 'false' WHERE `userid` = {$userid}");
		alert("danger","{$lang['ERROR_GENERIC']}","{$lang['ATTACK_START_OPPUNATTACK']} <a href='index.php'>{$lang['GEN_GOHOME']}</a>.");
		die($h->endpage());
	}
	else if (permission('CanAttack',$userid) == false)
	{
		$_SESSION['attacking'] = 'false';
		$ir['attacking'] = 0;
		$db->query("UPDATE `users` SET `attacking` = 'false' WHERE `userid` = {$userid}");
		alert("danger","{$lang['ERROR_GENERIC']}","{$lang['ATTACK_START_YOUUNATTACK']} <a href='index.php'>{$lang['GEN_GOHOME']}</a>.");
		die($h->endpage());
	}
	$_GET['weapon'] = (isset($_GET['weapon']) && is_numeric($_GET['weapon'])) ? abs(intval($_GET['weapon'])) : '';
	if ($_GET['weapon'])
	{
		if (!$_GET['nextstep'])
		{
			$_GET['nextstep'] = 1;
		}
		if ($_GET['nextstep'] >= $set['MaxAttacksPerSession'])
		{
			$_SESSION['attacking'] = 'false';
			$ir['attacking'] = 0;
			$db->query("UPDATE `users` SET `attacking` = 'false' WHERE `userid` = {$userid}");
			alert("warning","{$lang['ERROR_GENERIC']}","{$lang['ATTACK_FIGHT_STALEMATE']} <a href='index.php'>{$lang['GEN_GOHOME']}</a>.");
			die($h->endpage());
		}
		if ($_SESSION['attacking'] == 'false' && $ir['attacking'] == 0)
		{
			if ($youdata['energy'] >= $youdata['maxenergy'] / $set['AttackEnergyCost'])
			{
				$youdata['energy'] -= floor($youdata['maxenergy'] / $set['AttackEnergyCost']);
				$cost = floor($youdata['maxenergy'] / $set['AttackEnergyCost']);
				$db->query("UPDATE `users` SET `energy` = `energy` - {$cost} WHERE `userid` = {$userid}");
				$_SESSION['attackdmg'] = 0;
			}
			else
			{
				$EnergyPercent=floor(100/$set['AttackEnergyCost']);
				$UserCurrentEnergy=floor($ir['maxenergy']/$ir['energy']);
				alert("danger","{$lang['ERROR_GENERIC']}","{$lang['ATTACK_FIGHT_LOWENG1']} {$EnergyPercent}{$lang['ATTACK_FIGHT_LOWENG2']} {$UserCurrentEnergy}% <a href='index.php'>{$lang['GEN_GOHOME']}</a>.");
				die($h->endpage());
			}
		}
		$_SESSION['attacking'] = 'true';
		$ir['attacking'] = $odata['userid'];
		$attackstatus_sql ="UPDATE `users` SET `attacking` = 'true' WHERE `userid` = {$userid}";
		$db->query($attackstatus_sql);
		$_GET['nextstep'] = (isset($_GET['nextstep']) && is_numeric($_GET['nextstep'])) ? abs(intval($_GET['nextstep'])) : '';
		if ($_GET['weapon'] != $ir['equip_primary'] && $_GET['weapon'] != $ir['equip_secondary'])
		{
			$abuse_sql ="UPDATE `users` SET `xp` = 0 WHERE `userid` = {$userid}";
			$db->query($abuse_sql);
			$api->UserStatusSet($userid,1,666,'Bug Abuse');
			alert("danger","{$lang['ERROR_SECURITY']}","{$lang['ATTACK_FIGHT_BUGABUSE']} <a href='index.php'>{$lang['GEN_GOHOME']}</a>.");
			die($h->endpage());
		}
		$winfo_sql ="SELECT `itmname`, `weapon` FROM `items` WHERE `itmid` = {$_GET['weapon']} LIMIT 1";
		$qo = $db->query($winfo_sql);
		if ($db->num_rows($qo) == 0)
		{
			alert("danger","{$lang['ERROR_GENERIC']}","{$lang['ATTACK_FIGHT_BADWEAP']} <a href='index.php'>{$lang['GEN_GOHOME']}</a>.");
			die($h->endpage());
		}
		$r1 = $db->fetch_row($qo);
		$db->free_result($qo);
		$mydamage = round(($r1['weapon'] * $youdata['strength'] / ($odata['guard'] / 1.5)) * (mt_rand(10000, 12000) / 10000));
		$hitratio = max(10, min(60 * $ir['agility'] / $odata['agility'], 95));
		if (mt_rand(1, 100) <= $hitratio)
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
			$crit = mt_rand(1, 40);
			if ($crit == 17)
			{
				$mydamage *= mt_rand(20, 40) / 10;
			}
			else if ($crit == 25 OR $crit == 8)
			{
				$mydamage /= (mt_rand(20, 40) / 10);
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
			$db->query("UPDATE `users` SET `hp` = '0' WHERE `userid` = {$_GET['user']}");
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
				$dam = round(round((($odata['strength']/$ir['guard'] / 100))+ 1)*(mt_rand(10000, 12000) / 10000));
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
				$weptouse = mt_rand(0, $cnt - 1);
				$wep = $enweps[$weptouse]['itmname'];
				$dam = round(($enweps[$weptouse]['weapon'] * $odata['strength'] / ($youdata['guard'] / 1.5)) * (mt_rand(10000, 12000) / 10000));
			}
			$hitratio = max(10, min(60 * $odata['agility'] / $ir['agility'], 95));
			if (mt_rand(1, 100) <= $hitratio)
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
				$crit = mt_rand(1, 40);
				if ($crit == 17)
				{
					$dam *= mt_rand(20, 40) / 10;
				}
				else if ($crit == 25 OR $crit == 8)
				{
					$dam /= (mt_rand(20, 40) / 10);
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
				$api->UserInfoSet($userid,"hp","-{$dam}",false);
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
				$_SESSION['attacklost'] = 1;
				$db->query("UPDATE `users` SET `hp` = '0' WHERE `userid` = $userid");
				echo "<form action='?action=lost&ID={$_GET['user']}' method='post'><input type='submit' class='btn btn-default' value='{$lang['GEN_CONTINUE']}' />";
			}
		}
	}
	else if ($odata['hp'] < 5)
	{
		alert("danger","{$lang['ERROR_GENERIC']}","{$odata['username']} {$lang['ATTACK_START_OPPNOHP']} <a href='index.php'>{$lang['GEN_GOHOME']}</a>.");
		die($h->endpage());
	}
	else if ($ir['guild'] == $odata['guild'] && $ir['guild'] > 0)
	{
		alert("danger","{$lang['ERROR_GENERIC']}","{$odata['username']} {$lang['ATTACK_FIGHT_FINAL_GUILD']} <a href='index.php'>{$lang['GEN_GOHOME']}</a>.");
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
		alert("danger","{$lang['ERROR_GENERIC']}","{$lang['ATTACK_FIGHT_FINAL_CITY']}<a href='index.php'>{$lang['GEN_GOHOME']}</a>.");
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
			alert("warning","{$lang['ERROR_GENERIC']}","{$lang['ATTACK_FIGHT_START2']}");
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
	$_SESSION['attacking'] = 'false';
	$ir['attacking'] = 'false';
	$db->query("UPDATE `users` SET `attacking` = 'false' WHERE `userid` = $userid");
	$od = $db->query("SELECT * FROM `users` WHERE `userid` = {$_GET['ID']}");
	if (!isset($_SESSION['attackwon']) || $_SESSION['attackwon'] != $_GET['ID'])
	{
		$abuse_sql ="UPDATE `users` SET `xp` = 0 WHERE `userid` = {$userid}";
		$db->query($abuse_sql);
		$api->UserStatusSet($userid,1,666,'Bug Abuse');
		alert("danger","{$lang['ERROR_SECURITY']}","{$lang['ATTACK_FIGHT_BUGABUSE']} <a href='index.php'>{$lang['GEN_GOHOME']}</a>.");
		die($h->endpage());
	}
	if(!$db->num_rows($od)) 
	{
		$abuse_sql ="UPDATE `users` SET `xp` = 0 WHERE `userid` = {$userid}";
		$db->query($abuse_sql);
		$api->UserStatusSet($userid,1,666,'Bug Abuse');
		alert('danger',"{$lang['ERROR_NONUSER']}","{$lang['ATTACK_START_NONUSER']}");
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
		$hosptime = mt_rand(75, 175) + floor($ir['level'] / 2);
		alert('success',"{$lang['ATTACK_FIGHT_END']} {$r['username']}!!","{$lang['ATTACK_FIGHT_END2']} {$lang['ATTACK_FIGHT_END3']} {$hosptime} {$lang["GEN_MINUTES"]} {$lang['ATTACK_FIGHT_END4']}");
		$hospreason = $db->escape("Beat up by <a href='profile.php?user={$userid}'>{$ir['username']}</a>");
		$db->query("UPDATE `users` SET `hp` = 1 WHERE `userid` = {$r['userid']}");
		put_infirmary($r['userid'],$hosptime,$hospreason);
		event_add($r['userid'], "<a href='profile.php?user=$userid'>{$ir['username']}</a> brutally attacked you and caused {$hosptime} minutes worth of damage.");
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
	$_SESSION['attacking'] = 'false';
	$_SESSION['attacklost'] = 'false';
	if(!$_GET['ID']) 
	{
		alert('warning',"{$lang['CSRF_ERROR_TITLE']}","{$lang['ATT_NC']}");
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
	$qe = $r['level'] * $r['level'] * $r['level'];
	$expgain = mt_rand($qe / 2, $qe);
	if ($expgain < 0)
	{
		$expgain=$expgain*-1;
	}
	$expgainp = $expgain / $ir['xp_needed'] * 100;
	alert('danger',"{$lang['ATTACK_FIGHT_END5']} {$r['username']}!","{$lang['ATTACK_FIGHT_END6']} (" . number_format($expgainp, 2) . "%)!");
	$db->query("UPDATE `users` SET `xp` = `xp` - {$expgain}, `attacking` = 0 WHERE `userid` = {$userid}");
	$hosptime = mt_rand(75, 175) + floor($ir['level'] / 2);
	$hospreason = 'Picked a fight and lost';
	put_infirmary($userid,$hosptime,$hospreason);
	//Give winner some XP
	$r['xp_needed'] = round($r['level']+($r['level'] * 115)+($r['level'] * 115));
	$qe2 = $ir['level'] * $ir['level'] * $ir['level'];
	$expperc2 = round($expgainp / $r['xp_needed'] * 100);
	event_add($_GET['ID'], "<a href='profile.php?user=$userid'>{$ir['username']}</a> attacked you and lost, which gave you {$expperc2}% Experience.");
	$api->SystemLogsAdd($userid,'attacking',"Attacked {$r['username']} [{$_GET['ID']}] and lost, gaining {$hosptime} minutes in the infirmary.");
	$db->query("UPDATE `users` SET `xp` = `xp` + {$expgainp} WHERE `userid` = {$_GET['ID']}");
	$db->query("UPDATE `users` SET `xp` = 0 WHERE `xp` < 0");
}
function xp()
{
	global $db,$userid,$ir,$h,$lang,$api,$set,$atkpage;
	$_GET['ID'] = isset($_GET['ID']) && ctype_digit($_GET['ID']) ? $_GET['ID'] : 0;
	$_SESSION['attacking'] = 'false';
	$ir['attacking'] = 'false';
	$db->query("UPDATE `users` SET `attacking` = 'false' WHERE `userid` = $userid");
	$od = $db->query("SELECT * FROM `users` WHERE `userid` = {$_GET['ID']}");
	if (!isset($_SESSION['attackwon']) || $_SESSION['attackwon'] != $_GET['ID'])
	{
		$abuse_sql ="UPDATE `users` SET `xp` = 0 WHERE `userid` = {$userid}";
		$db->query($abuse_sql);
		$api->UserStatusSet($userid,1,666,'Bug Abuse');
		alert("danger","{$lang['ERROR_SECURITY']}","{$lang['ATTACK_FIGHT_BUGABUSE']} <a href='index.php'>{$lang['GEN_GOHOME']}</a>.");
		die($h->endpage());
	}
	if(!$db->num_rows($od)) 
	{
		alert('danger',"{$lang['ERROR_NONUSER']}","{$lang['ATTACK_START_NONUSER']}");
		exit($h->endpage());
	}
	if ($db->num_rows($od) > 0)
	{
		$r = $db->fetch_row($od);
		$db->free_result($od);
		if ($r['hp'] == 1)
		{
			$abuse_sql ="UPDATE `users` SET `xp` = 0 WHERE `userid` = {$userid}";
			$db->query($abuse_sql);
			$api->UserStatusSet($userid,1,666,'Bug Abuse');
			alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['ATTACK_FIGHT_BUGABUSE']}");
			exit($h->endpage());
		}
		else
		{
			$qe = $r['level'] * $r['level'] * $r['level'];
			$expgain = mt_rand($qe / 2, $qe);
			if ($expgain < 0)
			{
				$expgain=$expgain*-1;
			}
			$expperc = round($expgain / $ir['xp_needed'] * 300);
			alert('success',"{$lang['ATTACK_FIGHT_END']} {$r['username']}!","{$lang['ATTACK_FIGHT_END1']} {$lang['ATTACK_FIGHT_END7']} ({$expperc}%, {$expgain})");
			$hosptime = mt_rand(5, 30) + floor($ir['level'] / 10);
			$db->query("UPDATE `users` SET `xp` = `xp` + $expgain WHERE `userid` = $userid");
			$hospreason = $db->escape("Used for experience by <a href='profile.php?user={$userid}'>{$ir['username']}</a>");
			$db->query("UPDATE `users` SET `hp` = 1 WHERE `userid` = {$r['userid']}");
			put_infirmary($r['userid'],$hosptime,$hospreason);
			event_add($r['userid'],"<a href='profile.php?u=$userid'>{$ir['username']}</a> attacked you and left you for experience.");
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
	$_SESSION['attacking'] = 'false';
	$ir['attacking'] = 'false';
	$db->query("UPDATE `users` SET `attacking` = 'false' WHERE `userid` = $userid");
	$od = $db->query("SELECT * FROM `users` WHERE `userid` = {$_GET['ID']}");
	if (!isset($_SESSION['attackwon']) || $_SESSION['attackwon'] != $_GET['ID'])
	{
		$abuse_sql ="UPDATE `users` SET `xp` = 0 WHERE `userid` = {$userid}";
		$db->query($abuse_sql);
		$api->UserStatusSet($userid,1,666,'Bug Abuse');
		alert("danger","{$lang['ERROR_SECURITY']}","{$lang['ATTACK_FIGHT_BUGABUSE']} <a href='index.php'>{$lang['GEN_GOHOME']}</a>.");
		die($h->endpage());
	}
	if(!$db->num_rows($od)) 
	{
		alert('danger',"{$lang['ERROR_NONUSER']}","{$lang['ATTACK_START_NONUSER']}");
		exit($h->endpage());
	}
	if ($db->num_rows($od) > 0)
	{
		$r = $db->fetch_row($od);
		$db->free_result($od);
		if ($r['hp'] == 1)
		{
			$abuse_sql ="UPDATE `users` SET `xp` = 0 WHERE `userid` = {$userid}";
			$db->query($abuse_sql);
			$api->UserStatusSet($userid,1,666,'Bug Abuse');
			alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['ATTACK_FIGHT_BUGABUSE']}");
			exit($h->endpage());
		}
		else
		{
			$stole = round($r['primary_currency'] / (mt_rand(50, 1000) / 5));
			alert('success',"{$lang['ATTACK_FIGHT_END']} {$r['username']}!","{$lang['ATTACK_FIGHT_END1']} {$lang['ATTACK_FIGHT_END8']} (" . number_format($stole) . ")");
			$hosptime = rand(20, 40) + floor($ir['level'] / 8);
			$hospreason = $db->escape("Mugged by <a href='viewuser.php?u={$userid}'>{$ir['username']}</a>");
			$db->query("UPDATE `users` SET `hp` = 1, `primary_currency` = `primary_currency` - {$stole} WHERE `userid` = {$r['userid']}");
			$db->query("UPDATE `users` SET `primary_currency` = `primary_currency` + {$stole} WHERE `userid` = {$userid}");
			$api->UserStatusSet($r['userid'],1,$hosptime,$hospreason);
			event_add($r['userid'], "<a href='viewuser.php?u=$userid'>{$ir['username']}</a> mugged you and stole " . number_format($stole) . " Primary Currency.");
			$api->SystemLogsAdd($userid,'attacking',"Attacked {$r['username']} [{$r['userid']}] and stole {$stole} Primary Currency.");					
			$_SESSION['attackwon'] = 'false';
			if ($r['user_level'] == 0)
			{
				$db->query("UPDATE `users` SET `hp` = `maxhp`  WHERE `userid` = {$r['userid']}");
				$db->query("UPDATE `infirmary` SET `infirmary_out` = 0 WHERE `infirmary_user` ={$r['userid']}");
			}
		}
	}
}
$h->endpage();