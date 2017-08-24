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
	global $db,$api;
	$CurrentTime=time();
	$PlayerCount=$db->fetch_single($db->query("SELECT COUNT(`infirmary_user`) FROM `infirmary` WHERE `infirmary_out` > {$CurrentTime}"));
	echo "<h3>The Infirmary</h3><hr />
	<small>There's currently " . number_format($PlayerCount) . " users in the infirmary.</small>
	<hr />
	<table class='table table-hover table-bordered'>
		<thead>
			<tr>
				<th>
					User
				</th>
				<th>
					Reason
				</th>
				<th class='hidden-xs'>
					Check-In
				</th>
				<th>
					Check-out
				</th>
				<th>
					Actions
				</th>
			</tr>
		</thead>
		<tbody>";
	$query = $db->query("SELECT * FROM `infirmary` WHERE `infirmary_out` > {$CurrentTime} ORDER BY `infirmary_out` DESC");
	while ($Infirmary=$db->fetch_row($query))
	{
		echo "
			<tr>
				<td>
					<a href='profile.php?user={$Infirmary['infirmary_user']}'>
						{$api->SystemUserIDtoName($Infirmary['infirmary_user'])}
					</a> [{$Infirmary['infirmary_user']}]
				</td>
				<td>
					{$Infirmary['infirmary_reason']}
				</td>
				<td class='hidden-xs'>
					" . DateTime_Parse($Infirmary['infirmary_in']) . "
				</td>
				<td>
					" . TimeUntil_Parse($Infirmary['infirmary_out']) . "
				</td>
				<td>
					[<a href='?action=heal&user={$Infirmary['infirmary_user']}'>Heal User</a>]
				</td>
			</tr>";
	}
	echo "</tbody></table>";
}
function heal()
{
	global $db,$api,$h,$userid,$ir;
	if (isset($_GET['user']))
	{
		$_GET['user'] = (isset($_GET['user']) && is_numeric($_GET['user'])) ? abs($_GET['user']) : 0;
		if (empty($_GET['user']) || $_GET['user'] == 0)
		{
			alert('danger',"Uh Oh!","You are attempting to heal an invalid or non-existent user.",true,'infirmary.php');
			die($h->endpage());
		}
		if ($api->UserStatus($_GET['user'],'infirmary') == false)
		{
			alert('danger',"Uh Oh!","You are attempting to heal out a player who's not even in the infirmary.",true,'infirmary.php');
			die($h->endpage());
		}
		$cost=round(7.5*$api->UserInfoGet($_GET['user'],'level',false));
		if ($api->UserHasCurrency($userid,'secondary', $cost) == false)
		{
			alert('danger',"Uh Oh!","You do not have enough Secondary Currency to heal out this player. You need " . number_format($cost) . ".",true,'infirmary.php');
			die($h->endpage());
		}
		$api->UserTakeCurrency($userid,'secondary',$cost);
		$api->GameAddNotification($_GET['user'],"<a href='profile.php?user={$userid}'>{$ir['username']}</a> has successfully healed you from the infirmary");
		alert('success',"Success!","You have healed out this player for {$cost} Secondary Currency.",true,'infirmary.php');
		$db->query("UPDATE `infirmary` SET `infirmary_out` = 0 WHERE `infirmary_user` = {$_GET['user']}");
		die($h->endpage());
	}
	else
	{
		alert('danger',"Uh Oh!","Please specify a user you wish to heal out.",true,'infirmary.php');
	}
}
$h->endpage();