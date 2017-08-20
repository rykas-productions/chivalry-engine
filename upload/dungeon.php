<?php
/*
	File:		dungeon.php
	Created: 	4/4/2016 at 11:58PM Eastern Time
	Info: 		Lists players currently in the dungeon, and allows players
				to bust or bail them out.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require("globals.php");
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
switch ($_GET['action'])
{
case 'bust':
    bust();
    break;
case 'bail':
    bail();
    break;
default:
    home();
    break;
}
function home()
{
	global $db,$lang,$h,$api;
	$CurrentTime=time();
    //Count how many users are in the dungeon.
	$PlayerCount=$db->fetch_single($db->query("SELECT COUNT(`dungeon_user`) FROM `dungeon` WHERE `dungeon_out` > {$CurrentTime}"));
	echo "<h3>{$lang['DUNGINFIRM_TITLE']}</h3><hr />
	<small>{$lang['DUNGINFIRM_INFO']} " . number_format($PlayerCount) . " {$lang['DUNGINFIRM_INFO1']}</small>
	<hr />
	<table class='table table-hover table-bordered'>
		<thead>
			<tr>
				<th>
					{$lang['DUNGINFIRM_TD1']}
				</th>
				<th>
					{$lang['DUNGINFIRM_TD2']}
				</th>
				<th class='hidden-xs'>
					{$lang['DUNGINFIRM_TD3']}
				</th>
				<th>
					{$lang['DUNGINFIRM_TD4']}
				</th>
				<th>
					{$lang['DUNGINFIRM_TD5']}
				</th>
			</tr>
		</thead>
		<tbody>";
    //List users in the dungeon.
	$query = $db->query("SELECT * FROM `dungeon` WHERE `dungeon_out` > {$CurrentTime} ORDER BY `dungeon_out` DESC");
	while ($Infirmary=$db->fetch_row($query))
	{
		echo "
			<tr>
				<td>
					<a href='profile.php?user={$Infirmary['dungeon_user']}'>
						{$api->SystemUserIDtoName($Infirmary['dungeon_user'])}
					</a>
				</td>
				<td>
					{$Infirmary['dungeon_reason']}
				</td>
				<td class='hidden-xs'>
					" . DateTime_Parse($Infirmary['dungeon_in']) . "
				</td>
				<td>
					" . TimeUntil_Parse($Infirmary['dungeon_out']) . "
				</td>
				<td>
					[<a href='?action=bail&user={$Infirmary['dungeon_user']}'>{$lang['DUNGINFIRM_ACC']}</a>] 
					[<a href='?action=bust&user={$Infirmary['dungeon_user']}'>{$lang['DUNGINFIRM_ACC1']}</a>]
				</td>
			</tr>";
	}
	echo "</tbody></table>";
	$h->endpage();
}
function bail()
{
	global $lang,$db,$userid,$ir,$h,$api;
	if (isset($_GET['user']))
	{
		$_GET['user'] = (isset($_GET['user']) && is_numeric($_GET['user'])) ? abs($_GET['user']) : 0;
        //Specified user is invalid or empty.
		if (empty($_GET['user']) || $_GET['user'] == 0)
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['DUNG_BAILERR'],true,'dungeon.php');
			die($h->endpage());
		}
        //Specified user is not in the dungeon.
		if ($api->UserStatus($_GET['user'],'dungeon') == false)
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['DUNG_BAILERR1'],true,'dungeon.php');
			die($h->endpage());
		}
		$cost=250*$api->UserInfoGet($_GET['user'],'level',false);
        //User does not have enough primary currency to bail this user out.
		if ($api->UserHasCurrency($userid,'primary', $cost) == false)
		{
			alert('danger',$lang['ERROR_GENERIC'],"{$lang['DUNG_BAILERR2']} " . number_format($cost) . ".",true,'dungeon.php');
			die($h->endpage());
		}
        //Person specified is bailed out. Take user's currency, log the 
        //action, and tell the person what happened.
		$api->UserTakeCurrency($userid,'primary',$cost);
		$api->GameAddNotification($_GET['user'],"<a href='profile.php?user={$userid}'>{$ir['username']}</a> has successfully bailed you out of the dungeon.");
		alert('success',$lang['ERROR_SUCCESS'],$lang['DUNG_BAILSUCC'],true,'dungeon.php');
		$db->query("UPDATE `dungeon` SET `dungeon_out` = 0 WHERE `dungeon_user` = {$_GET['user']}");
		die($h->endpage());
	}
	else
	{
		alert('danger',$lang['ERROR_GENERIC'],$lang['DUNG_BAILERR'],true,'dungeon.php');
	}
}
function bust()
{
	global $lang,$db,$userid,$ir,$h,$api;
	if (isset($_GET['user']))
	{
		$_GET['user'] = (isset($_GET['user']) && is_numeric($_GET['user'])) ? abs($_GET['user']) : 0;
        //Person input is invalid or empty.
		if (empty($_GET['user']) || $_GET['user'] == 0)
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['DUNG_BAILERR'],true,'dungeon.php');
			die($h->endpage());
		}
        //Person not in the dungeon.
		if ($api->UserStatus($_GET['user'],'dungeon') == false)
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['DUNG_BUSTERR'],true,'dungeon.php');
			die($h->endpage());
		}
        //User is in the dungeon.
		if ($api->UserStatus($userid,'dungeon'))
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['DUNG_BUSTERR1'],true,'dungeon.php');
			die($h->endpage());
		}
        //User does not have 10% brave.
		if ($api->UserInfoGet($userid,'brave',true) < 10)
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['DUNG_BUSTERR2'],true,'dungeon.php');
			die($h->endpage());
		}
        //User does not have 25% will.
		if ($api->UserInfoGet($userid,'will',true) < 25)
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['DUNG_BUSTERR2'],true,'dungeon.php');
			die($h->endpage());
		}
        //Update user's info.
		$api->UserInfoSet($userid,'will',-25,true);
		$api->UserInfoSet($userid,'brave',-10,true);
		$mult = $api->UserInfoGet($_GET['user'],'level') * $api->UserInfoGet($_GET['user'],'level');
		$chance = min(($ir['level'] / $mult) * 50 + 1, 95);
        //User is successful.
		if (Random(1, 100) < $chance)
		{
            //Add notification, and tell the user.
			$api->GameAddNotification($_GET['user'],"<a href='profile.php?user={$userid}'>{$ir['username']}</a> has successfully busted you out of the dungeon.");
			alert('success',$lang['ERROR_SUCCESS'],$lang['DUNG_BUSTSUCC'],true,'dungeon.php');
			$db->query("UPDATE `dungeon` SET `dungeon_out` = 0 WHERE `dungeon_user` = {$_GET['user']}");
			die($h->endpage());
		}
        //User failed. Tell person and throw user in dungeon.
		else
		{
			$time = min($mult, 100);
			$reason = $db->escape("Caught trying to bust out {$api->SystemUserIDtoName($_GET['user'])}");
			$api->GameAddNotification($_GET['user'],"<a href='profile.php?user={$userid}'>{$ir['username']}</a> has failed to bust you out of the dungeon.");
			alert('danger',$lang['ERROR_GENERIC'],$lang['DUNG_BUSTERR4'],true,'dungeon.php');
			$api->UserStatusSet($userid,'dungeon',$time,$reason);
			die($h->endpage());
		}
	}
	else
	{
		alert('danger',$lang['ERROR_GENERIC'],$lang['DUNG_BAILERR'],true,'dungeon.php');
	}
}