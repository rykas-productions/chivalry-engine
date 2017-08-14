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
//Sanitize GET.
$_GET['user'] = (isset($_GET['user']) && is_numeric($_GET['user'])) ? abs($_GET['user']) : '';
//Current user is in the infirmary, don't let them buy a spy.
if ($api->UserStatus($userid,'infirmary'))
{
	alert('danger',$lang["GEN_INFIRM"],$lang['SPY_ERROR6'],true,'back');
	die($h->endpage());
}
//Current user is in the dungeon, don't let them buy a spy.
if ($api->UserStatus($userid,'dungeon'))
{
	alert('danger',$lang["GEN_DUNG"],$lang['SPY_ERROR5'],true,'back');
	die($h->endpage());
}
//GET is empty/truncated after sanitation,  don't let player buy a spy.
if (empty($_GET['user']))
{
	alert("danger",$lang['ERROR_GENERIC'],$lang['SPY_ERROR1'],true,'index.php');
	die($h->endpage());
}
//GET is the same player as the current user, do not allow to buy spy.
if ($_GET['user'] == $userid)
{
	alert("danger",$lang['ERROR_GENERIC'],$lang['SPY_ERROR2'],true,'index.php');
	die($h->endpage());
}
//Grab GET user's information.
$q=$db->query("SELECT `u`.*, `us`.* FROM `users` `u` INNER JOIN `userstats` AS `us` ON `us`.`userid` = `u`.`userid` WHERE `u`.`userid` = {$_GET['user']}");
//User does not exist, so do not allow spy to be bought.
if ($db->num_rows($q) == 0)
{
	alert("danger",$lang['ERROR_GENERIC'],$lang['SPY_ERROR3'],true,'index.php');
	die($h->endpage());
}
$r=$db->fetch_row($q);
//GET User is in the same guild as the current player, do not allow spy to be bought.
if (($r['guild'] == $ir['guild']) && ($ir['guild'] != 0))
{
	alert("danger",$lang['ERROR_GENERIC'],$lang['SPY_ERROR7'],true,'index.php');
	die($h->endpage());
}
//Spy has been bought, and all other tests have passed!
if (isset($_POST['do']) && (isset($_GET['user'])))
{
    //Random Number Generator to choose what happens.
	$rand=Random(1,4);
    //Current user does not have the required Primary Currency to buy a spy.
	if ($ir['primary_currency'] < $r['level']*500)
	{
		alert("danger",$lang['ERROR_GENERIC'],$lang['SPY_ERROR4'],true,'back');
		die($h->endpage());
	}
    //Take the spy cost from the player.
	$api->UserTakeCurrency($userid,'primary',$r['level']*500);
    //RNG equals 1 or 2, the spy has failed.
	if ($rand == 1 || $rand == 2)
	{
        //Specific event RNG
		$rand2=Random(1,3);
        //Spy failed and the person being spied on only knows that /someone/ has made an attempt to spy on them.
		if ($rand2 <= 2)
		{
			$api->GameAddNotification($_GET['user'],"An unknown user has attempted to spy on you and failed.");
			alert("danger",$lang['ERROR_GENERIC'],$lang['SPY_FAIL1'],true,'index.php');
			$api->SystemLogsAdd($userid,'spy',"Tried to spy on " . $api->SystemUserIDtoName($_GET['user']) .  " and failed.");
			die($h->endpage());
		}
        //Spy failed and hte person bein spied on now knows who's been attempting to spy.
		else
		{
			$api->GameAddNotification($_GET['user'],"<a href='profile.php?user={$userid}'>{$ir['username']}</a> has attempted to spy on you and failed.");
			alert("danger",$lang['ERROR_GENERIC'],$lang['SPY_FAIL2'],true,'index.php');
			$api->SystemLogsAdd($userid,'spy',"Tried to spy on " . $api->SystemUserIDtoName($_GET['user']) .  " and failed.");
			die($h->endpage());
		}
	}
    //RNG equals 3, send current player to the dungeon.
	elseif ($rand == 3)
	{
		alert("danger",$lang['ERROR_GENERIC'],$lang['SPY_FAIL3'],true,'index.php');
		$dungtime=Random($ir['level'],$ir['level']*3);
		$api->UserStatusSet($userid,'dungeon',$dungtime,"Stalkerish Tendencies");
		$api->SystemLogsAdd($userid,'spy',"Tried to spy on " . $api->SystemUserIDtoName($_GET['user']) .  " and was sent to the dungeon.");
		die($h->endpage());
	}
    //RNG equals 4, show the current player the person's stats and weapons.
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
        //Save to the log.
		$api->SystemLogsAdd($userid,'spy',"Successfully spied on " . $api->SystemUserIDtoName($_GET['user']));
	}
}
//Starting form.
else
{
	echo "{$lang['SPY_START']} " . $api->SystemUserIDtoName($_GET['user']) . 
	"{$lang['SPY_START1']}" . number_format(500*$r['level']) . " 
	{$lang['SPY_START2']}<br />
	<form action='?user={$_GET['user']}' method='post'>
		<input type='hidden' name='do' value='yes'>
		<input type='submit' class='btn btn-secondary' value='{$lang['SPY_BTN']}'>
	</form>";
}
$h->endpage();