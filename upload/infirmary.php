<?php
/*
	File:		infirmary.php
	Created: 	4/5/2016 at 12:11AM Eastern Time
	Info: 		Lists the players currently in the infirmary, and allows
				them to heal those players out using secondary currency.
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
case 'heal':
    heal();
    break;
default:
    home();
    break;
}
function home()
{
	global $db,$api,$lang,$h,$userid;
	$CurrentTime=time();
	$PlayerCount=$db->fetch_single($db->query("SELECT COUNT(`infirmary_user`) FROM `infirmary` WHERE `infirmary_out` > {$CurrentTime}"));
	echo "<h3>{$lang['DUNGINFIRM_TITLE1']}</h3><hr />
	<small>{$lang['DUNGINFIRM_INFO']} " . number_format($PlayerCount) . " {$lang['DUNGINFIRM_INFO2']}</small>
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
				<th>
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
	$query = $db->query("SELECT * FROM `infirmary` WHERE `infirmary_out` > {$CurrentTime} ORDER BY `infirmary_out` DESC");
	while ($Infirmary=$db->fetch_row($query))
	{
		$UserName=$db->fetch_single($db->query("SELECT `username` FROM `users` WHERE `userid` = {$Infirmary['infirmary_user']}"));
		echo "
			<tr>
				<td>
					<a href='profile.php?user={$Infirmary['infirmary_user']}'>{$UserName}</a> [{$Infirmary['infirmary_user']}]
				</td>
				<td>
					{$Infirmary['infirmary_reason']}
				</td>
				<td>
					" . DateTime_Parse($Infirmary['infirmary_in']) . "
				</td>
				<td>
					" . TimeUntil_Parse($Infirmary['infirmary_out']) . "
				</td>
				<td>
					[<a href='?action=heal&user={$Infirmary['infirmary_user']}'>{$lang['DUNGINFIRM_ACC2']}</a>]
				</td>
			</tr>";
	}
	echo "</tbody></table>";
}
function heal()
{
	global $db,$api,$lang,$h,$userid,$ir;
	if (isset($_GET['user']))
	{
		$_GET['user'] = (isset($_GET['user']) && is_numeric($_GET['user'])) ? abs($_GET['user']) : 0;
		if (empty($_GET['user']) || $_GET['user'] == 0)
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['DUNG_BAILERR'],true,'infirmary.php');
			die($h->endpage());
		}
		if ($api->UserStatus($_GET['user'],'infirmary') == false)
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['DUNG_HEALERR1'],true,'infirmary.php');
			die($h->endpage());
		}
		$cost=round(7.5*$api->UserInfoGet($_GET['user'],'level',false));
		if ($api->UserHasCurrency($userid,'secondary', $cost) == false)
		{
			alert('danger',$lang['ERROR_GENERIC'],"{$lang['DUNG_HEALERR2']} " . number_format($cost) . ".",true,'infirmary.php');
			die($h->endpage());
		}
		$api->UserTakeCurrency($userid,'secondary',$cost);
		$api->GameAddNotification($_GET['user'],"<a href='profile.php?user={$userid}'>{$ir['username']}</a> has successfully healed you from the infirmary");
		alert('success',$lang['ERROR_SUCCESS'],$lang['DUNG_HEALSUCC'],true,'infirmary.php');
		$db->query("UPDATE `infirmary` SET `infirmary_out` = 0 WHERE `infirmary_user` = {$_GET['user']}");
		die($h->endpage());
	}
	else
	{
		alert('danger',$lang['ERROR_GENERIC'],$lang['DUNG_BAILERR'],true,'infirmary.php');
	}
}
$h->endpage();