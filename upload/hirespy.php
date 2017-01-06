<?php
require('globals.php');
$_GET['user'] = (isset($_GET['user']) && is_numeric($_GET['user'])) ? abs(intval($_GET['user'])) : '';
if (user_infirmary($ir['userid']) == true)
{
	alert('danger',"{$lang["GEN_INFIRM"]}","{$lang['SPY_ERROR6']}");
	die($h->endpage());
}
if (user_dungeon($ir['userid']) == true)
{
	alert('danger',"{$lang["GEN_DUNG"]}","{$lang['SPY_ERROR5']}");
	die($h->endpage());
}
if (empty($_GET['user']))
{
	alert("danger",$lang['ERROR_GENERIC'],$lang['SPY_ERROR1']);
	die($h->endpage());
}
if ($_GET['user'] == $userid)
{
	alert("danger",$lang['ERROR_GENERIC'],$lang['SPY_ERROR2']);
	die($h->endpage());
}
$q=$db->query("SELECT `u`.*, `us`.* FROM `users` `u` INNER JOIN `userstats` AS `us` ON `us`.`userid` = `u`.`userid` WHERE `u`.`userid` = {$_GET['user']}");
if ($db->num_rows($q) == 0)
{
	alert("danger",$lang['ERROR_GENERIC'],$lang['SPY_ERROR3']);
	die($h->endpage());
}
$r=$db->fetch_row($q);
if (isset($_POST['do']) && (isset($_GET['user'])))
{
	$rand=Random(1,4);
	if ($ir['primary_currency'] < $r['level']*500)
	{
		alert("danger",$lang['ERROR_GENERIC'],$lang['SPY_ERROR4']);
		die($h->endpage());
	}
	$db->query("UPDATE `users` SET `primary_currency` = `primary_currency` - " . $r['level']*500 . " WHERE `userid` = {$userid}");
	if ($rand == 1 || $rand == 2)
	{
		$rand2=Random(1,3);
		if ($rand2 <= 2)
		{
			notification_add($_GET['user'],"An unknown user has attempted to spy on you and failed.");
			alert("danger",$lang['ERROR_GENERIC'],$lang['SPY_FAIL1']);
			$api->SystemLogsAdd($userid,'spy',"Tried to spy on " . $api->SystemUserIDtoName($_GET['user']) .  " and failed.");
			die($h->endpage());
		}
		else
		{
			notification_add($_GET['user'],"<a href='profile.php?user={$userid}'>{$ir['username']}</a> has attempted to spy on you and failed.");
			alert("danger",$lang['ERROR_GENERIC'],$lang['SPY_FAIL2']);
			$api->SystemLogsAdd($userid,'spy',"Tried to spy on " . $api->SystemUserIDtoName($_GET['user']) .  " and failed.");
			die($h->endpage());
		}
	}
	elseif ($rand == 3)
	{
		alert("danger",$lang['ERROR_GENERIC'],$lang['SPY_FAIL3']);
		$dungtime=Random($ir['level'],$ir['level']*3);
		$api->UserStatusSet($userid,2,$dungtime,"Stalkerish Tendencies");
		$api->SystemLogsAdd($userid,'spy',"Tried to spy on " . $api->SystemUserIDtoName($_GET['user']) .  " and was sent to the dungeon.");
		die($h->endpage());
	}
	else
	{
		alert("success",$lang['ERROR_SUCCESS'],"{$lang['SPY_SUCCESS']} " . number_format(500*$r['level']) ." {$lang['SPY_SUCCESS1']} {$r['username']}{$lang['SPY_SUCCESS2']}");
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
		<input type='submit' class='btn btn-default' value='{$lang['SPY_BTN']}'>
	</form>";
}
$h->endpage();