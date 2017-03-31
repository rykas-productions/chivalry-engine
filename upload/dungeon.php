<?php
require("globals.php");
if (!isset($_GET['slot']))
{
    $_GET['slot'] = '';
}
switch ($_GET['slot'])
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
	global $db,$lang,$h;
	$CurrentTime=time();
	$PlayerCount=$db->fetch_single($db->query("SELECT COUNT(`dungeon_user`) FROM `dungeon` WHERE `dungeon_out` = {$CurrentTime}"));
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
				<th>
					{$lang['DUNGINFIRM_TD3']}
				</th>
				<th>
					{$lang['DUNGINFIRM_TD4']}
				</th>
			</tr>
		</thead>
		<tbody>";
	$query = $db->query("SELECT * FROM `dungeon` WHERE `dungeon_out` > {$CurrentTime} ORDER BY `dungeon_out` DESC");
	while ($Infirmary=$db->fetch_row($query))
	{
		$UserName=$db->fetch_single($db->query("SELECT `username` FROM `users` WHERE `userid` = {$Infirmary['dungeon_user']}"));
		echo "
			<tr>
				<td>
					<a href='profile.php?user={$Infirmary['dungeon_user']}'>{$UserName}</a> [{$Infirmary['dungeon_user']}]
				</td>
				<td>
					{$Infirmary['dungeon_reason']}
				</td>
				<td>
					" . DateTime_Parse($Infirmary['dungeon_in']) . "
				</td>
				<td>
					" . DateTime_Parse($Infirmary['dungeon_out']) . "
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
		if (empty($_GET['user']) || $_GET['empty'] == 0)
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['DUNG_BAILERR'],true,'dungeon.php');
			die($h->endpage());
		}
		if ($api->UserStatus($_GET['user'],'dungeon') == false)
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['DUNG_BAILERR1'],true,'dungeon.php');
			die($h->endpage());
		}
		$cost=250*$api->UserInfoGet($_GET['user'],'level',false);
		if ($api->UserHasCurrency($_GET['user'],'primary', $cost) == false)
		{
			alert('danger',$lang['ERROR_GENERIC'],"{$lang['DUNG_BAILERR2']} " . number_format($cost) . ".",true,'dungeon.php');
			die($h->endpage());
		}
		$un=$api->SystemUserIDtoName($_GET['user']);
		$api->UserTakeCurrency($userid,'primary',$cost);
		$api->GameAddNotification($_GET['user'],"<a href='profile.php?user={$userid}'>{$ir['username']}</a> has successfully bailed you out of the dungeon.");
		alert('success',$lang['ERROR_SUCCESS'],$lang['DUNG_BAILSUCC'],true,'dungeon.php');
		die($h->endpage());
	}
	else
	{
		alert('danger',$lang['ERROR_GENERIC'],$lang['DUNG_BAILERR'],true,'dungeon.php');
	}
}