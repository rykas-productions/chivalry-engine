<?php
/*
	File:		hirespy.php
	Created: 	4/5/2016 at 12:10AM Eastern Time
	Info: 		Allows players to hire spies on other players at a cost.
				Spy will fetch stats and equipment.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require('globals.php');
$_GET['user'] = (isset($_GET['user']) && is_numeric($_GET['user'])) ? abs($_GET['user']) : '';
if ($api->UserStatus($userid,'infirmary') == true)
{
	alert('danger',$lang["GEN_INFIRM"],$lang['SPY_ERROR6'],true,'back');
	die($h->endpage());
}
if ($api->UserStatus($userid,'dungeon') == true)
{
	alert('danger',$lang["GEN_DUNG"],$lang['SPY_ERROR5'],true,'back');
	die($h->endpage());
}
if (empty($_GET['user']))
{
	alert("danger",$lang['ERROR_GENERIC'],$lang['SPY_ERROR1'],true,'index.php');
	die($h->endpage());
}
if ($_GET['user'] == $userid)
{
	alert("danger",$lang['ERROR_GENERIC'],$lang['SPY_ERROR2'],true,'index.php');
	die($h->endpage());
}
$q=$db->query("SELECT `u`.*, `us`.* FROM `users` `u` INNER JOIN `userstats` AS `us` ON `us`.`userid` = `u`.`userid` WHERE `u`.`userid` = {$_GET['user']}");
if ($db->num_rows($q) == 0)
{
	alert("danger",$lang['ERROR_GENERIC'],$lang['SPY_ERROR3'],true,'index.php');
	die($h->endpage());
}
$r=$db->fetch_row($q);
if (($r['guild'] == $ir['guild']) && ($ir['guild'] != 0))
{
	alert("danger",$lang['ERROR_GENERIC'],$lang['SPY_ERROR7'],true,'index.php');
	die($h->endpage());
}
if (isset($_POST['do']) && (isset($_GET['user'])))
{
	$rand=Random(1,4);
	if ($ir['primary_currency'] < $r['level']*500)
	{
		alert("danger",$lang['ERROR_GENERIC'],$lang['SPY_ERROR4'],true,'back');
		die($h->endpage());
	}
	$api->UserTakeCurrency($userid,'primary',$r['level']*500);
	if ($rand == 1 || $rand == 2)
	{
		$rand2=Random(1,3);
		if ($rand2 <= 2)
		{
			$api->GameAddNotification($_GET['user'],"An unknown user has attempted to spy on you and failed.");
			alert("danger",$lang['ERROR_GENERIC'],$lang['SPY_FAIL1'],true,'index.php');
			$api->SystemLogsAdd($userid,'spy',"Tried to spy on " . $api->SystemUserIDtoName($_GET['user']) .  " and failed.");
			die($h->endpage());
		}
		else
		{
			$api->GameAddNotification($_GET['user'],"<a href='profile.php?user={$userid}'>{$ir['username']}</a> has attempted to spy on you and failed.");
			alert("danger",$lang['ERROR_GENERIC'],$lang['SPY_FAIL2'],true,'index.php');
			$api->SystemLogsAdd($userid,'spy',"Tried to spy on " . $api->SystemUserIDtoName($_GET['user']) .  " and failed.");
			die($h->endpage());
		}
	}
	elseif ($rand == 3)
	{
		alert("danger",$lang['ERROR_GENERIC'],$lang['SPY_FAIL3'],true,'index.php');
		$dungtime=Random($ir['level'],$ir['level']*3);
		$api->UserStatusSet($userid,'dungeon',$dungtime,"Stalkerish Tendencies");
		$api->SystemLogsAdd($userid,'spy',"Tried to spy on " . $api->SystemUserIDtoName($_GET['user']) .  " and was sent to the dungeon.");
		die($h->endpage());
	}
	else
	{
		alert("success",$lang['ERROR_SUCCESS'],"{$lang['SPY_SUCCESS']} " . number_format(500*$r['level']) ." {$lang['SPY_SUCCESS1']} {$r['username']}{$lang['SPY_SUCCESS2']}",false);
		echo"<br />
		<table class='table table-bordered'>
			<tr>
				<th>
					{$lang['EQUIP_WEAPON_SLOT1']}
				</th>
				<td>
					" . $api->SystemItemIDtoName($r['equip_primary']) ."
				</td>
			</tr>
			<tr>
				<th>
					{$lang['EQUIP_WEAPON_SLOT2']}
				</th>
				<td>
					" . $api->SystemItemIDtoName($r['equip_secondary']) ."
				</td>
			</tr>
			<tr>
				<th>
					{$lang['EQUIP_WEAPON_SLOT3']}
				</th>
				<td>
					" . $api->SystemItemIDtoName($r['equip_armor']) ."
				</td>
			</tr>
			<tr>
				<th>
					{$lang['GEN_STR']}
				</th>
				<td>
					" . number_format($r['strength']) . "
				</td>
			</tr>
			<tr>
				<th>
					{$lang['GEN_AGL']}
				</th>
				<td>
					" . number_format($r['agility']) . "
				</td>
			</tr>
			<tr>
				<th>
					{$lang['GEN_GRD']}
				</th>
				<td>
					" . number_format($r['guard']) . "
				</td>
			</tr>
			<tr>
				<th>
					{$lang['GEN_IQ']}
				</th>
				<td>
					" . number_format($r['iq']) . "
				</td>
			</tr>
			<tr>
				<th>
					{$lang['GEN_LAB']}
				</th>
				<td>
					" . number_format($r['labor']) . "
				</td>
			</tr>
		</table>";
		$api->SystemLogsAdd($userid,'spy',"Successfully spied on " . $api->SystemUserIDtoName($_GET['user']));
	}
}
else
{
	echo "{$lang['SPY_START']} " . $api->SystemUserIDtoName($_GET['user']) . 
	"{$lang['SPY_START1']}" . number_format(500*$r['level']) . " 
	{$lang['SPY_START2']}<br />
	<form action='?user={$_GET['user']}' method='post'>
		<input type='hidden' name='do' value='yes'>
		<input type='submit' class='btn btn-primary' value='{$lang['SPY_BTN']}'>
	</form>";
}
$h->endpage();