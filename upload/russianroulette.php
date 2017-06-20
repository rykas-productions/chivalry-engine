<?php
/*
	File: 		russianroulette.php
	Created: 	5/2/2017 at 12:38PM Eastern Time
	Info: 		Allows players to play a round of russian roulette.
	Author: 	ImJustIsabella
	Website:	https://github.com/MasterGeneral156/chivalry-engine
*/
require("globals.php");
if (isset($_GET['id']))
{
	$_GET['id'] = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs(intval($_GET['id'])) : 0;
	if (isset($_GET['deny']))
	{
		$db->query("DELETE FROM `russian_roulette` WHERE `challenger` = '{$_GET['id']}' AND `challengee` = '{$userid}'");
		alert('success',$lang['ERROR_SUCCESS'],$lang['RUSSIANROULETTE_DENIED'],true,'index.php');
		die($h->endpage());
	}
	$q = $db->query("SELECT `challenger` FROM `russian_roulette` WHERE `challenger` = '{$_GET['id']}' AND `challengee` = '{$userid}'");
	if ($db->num_rows($q) == 0)
	{
		$q = $db->query("SELECT * FROM `users` WHERE `userid` = '{$_GET['id']}'");
		if ($db->num_rows($q) != 0)
		{
			$r = $db->fetch_row($q);
			echo "{$lang['RUSSIANROULETTE_NO_INVITE']} {$r['username']} ({$_GET['id']})";
			die($h->endpage());
		}
		alert('danger',$lang['ERROR_GENERIC'],$lang['RUSSIANROULETTE_INVALID_ACCOUNT'],true,'index.php');
		die($h->endpage());
	}
	else
	{
		$q = $db->query("SELECT * FROM `russian_roulette` WHERE `challenger` = '{$_GET['id']}' AND `challengee` = '{$userid}'");
		$r2 = $db->fetch_row($q);
		if ($ir['primary_currency'] < $r2['reward'])
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['RUSSIANROULETTE_INSUFFICIENT_CURRENCY'],true,'index.php');
			die($h->endpage());
		}
		$q = $db->query("SELECT * FROM `users` WHERE `userid` = '{$_GET['id']}'");
		$r = $db->fetch_row($q);
		if ($r['primary_currency'] < $r2['reward'])
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['RUSSIANROULETTE_SCAM'],true,'index.php');
			die($h->endpage());
		}
		$rand = Random(1, 8);
		$player = Random(1, 2);
		$shot = false;
		if ($player == 1)
		{
			if ($rand == 1)
			{
				$shot = true;
			}
			if ($rand == 3)
			{
				$shot = true;
			}
			if ($rand == 5)
			{
				$shot = true;
			}
			if ($rand == 7)
			{
				$shot = true;
			}
			echo "{$lang['RUSSIANROULETTE_CHOICE']} {$r['username']} {$lang['RUSSIANROULETTE_FIRST']}";
		}
		else if ($player == 2)
		{
			if ($rand == 2)
			{
				$shot = true;
			}
			if ($rand == 4)
			{
				$shot = true;
			}
			if ($rand == 6)
			{
				$shot = true;
			}
			if ($rand == 8)
			{
				$shot = true;
			}
			echo "{$lang['RUSSIANROULETTE_CHOICE']} {$r['username']} {$lang['RUSSIANROULETTE_SECOND']}";
		}
		if ($rand == 1)
		{
			$result = "1st";
		}
		if ($rand == 2)
		{
			$result = "2nd";
		}
		if ($rand == 3)
		{
			$result = "3rd";
		}
		if ($rand == 4)
		{
			$result = "4th";
		}
		if ($rand == 5)
		{
			$result = "5th";
		}
		if ($rand == 6)
		{
			$result = "6th";
		}
		if ($rand == 7)
		{
			$result = "7th";
		}
		if ($rand == 8)
		{
			$result = "8th";
		}
		if ($shot == false)
		{
			$q = $db->query("SELECT * FROM `russian_roulette` WHERE `challenger` = '{$_GET['id']}' AND `challengee` = '{$userid}'");
			$r = $db->fetch_row($q);
			$q = $db->query("SELECT * FROM `users` WHERE `userid` = '{$_GET['id']}'");
			$r2 = $db->fetch_row($q);
		echo "<br><br>{$lang['RUSSIANROULETTE_WON']} {$r2['username']} {$lang['RUSSIANROULETTE_WON2']} {$result} {$lang['RUSSIANROULETTE_WON3']} {$r['reward']} {$lang['INDEX_PRIMCURR']}";
			$db->query("UPDATE `users` SET `primary_currency` = `primary_currency` - '{$r['reward']}' WHERE `userid` = '{$_GET['id']}'");
			$db->query("UPDATE `users` SET `primary_currency` = `primary_currency` + '{$r['reward']}' WHERE `userid` = '{$userid}'");
			$hosptime = Random(75, 175) + floor($r2['level'] / 2);
			$hospreason = $db->escape("Played the wrong game with <a href='profile.php?user={$userid}'>{$ir['username']}</a>");
			$db->query("UPDATE `users` SET `hp` = 1 WHERE `userid` = {$_GET['id']}");
			put_infirmary($_GET['id'],$hosptime,$hospreason);
			$api->GameAddNotification($_GET['id'], "You lost at the game of russian roulette against {$ir['username']}!");
		}
		if ($shot == true)
		{
			$q = $db->query("SELECT * FROM `russian_roulette` WHERE `challenger` = '{$_GET['id']}' AND `challengee` = '{$userid}'");
			$r = $db->fetch_row($q);
			$q = $db->query("SELECT * FROM `users` WHERE `userid` = '{$_GET['id']}'");
			$r2 = $db->fetch_row($q);
			echo "<br><br>{$lang['RUSSIANROULETTE_LOST']} {$result} {$lang['RUSSIANROULETTE_LOST2']} {$hosptime} {$lang['RUSSIANROULETTE_LOST3']} {$r['reward']} {$lang['INDEX_PRIMCURR']}";
			$db->query("UPDATE `users` SET `primary_currency` = `primary_currency` + '{$r['reward']}' WHERE `userid` = '{$_GET['id']}'");
			$db->query("UPDATE `users` SET `primary_currency` = `primary_currency` - '{$r['reward']}' WHERE `userid` = '{$userid}'");
			$hosptime = Random(75, 175) + floor($ir['level'] / 2);
			$hospreason = $db->escape("Played the wrong game with <a href='profile.php?user={$_GET['id']}'>{$r2['username']}</a>");
			$db->query("UPDATE `users` SET `hp` = 1 WHERE `userid` = {$_GET['id']}");
			put_infirmary($userid,$hosptime,$hospreason);
			$api->GameAddNotification($_GET['id'], "You won in the game of russian roulette against {$ir['username']}!");
		}
		$db->query("DELETE FROM `russian_roulette` WHERE `challenger` = '{$_GET['id']}' AND `challengee` = '{$userid}'");
		die($h->endpage());
	}
}
else
{
	if (!isset($_POST['user_id']))
	{
		echo "<h2>{$lang['RUSSIANROULETTE_TITLE']}</h2>
		<hr>
		<form method='post' class='form' role='form'>
			<table class='table table-bordered'>
				<tr>
					<th width='33%'>
						{$lang['RUSSIANROULETTE_USER_INSERT']}
					</th>
					<td>
						" . user_dropdown('user_id') . "
					</td>
				</tr>
				<tr>
					<th width='33%'>
						{$lang['RUSSIANROULETTE_REWARD_INSERT']}
					</th>
					<td>
						<input type='number' name='reward' min='0' max='{$ir['primary_currency']}' class='form-control'>
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' value='{$lang['RUSSIANROULETTE_SEND']}' class='btn btn-secondary'>
					</td>
				</tr>
			</table>
		</form>";
		die($h->endpage());
	}
	else
	{
		$_POST['user_id'] = (isset($_POST['user_id']) && is_numeric($_POST['user_id'])) ? abs(intval($_POST['user_id'])) : 0;
		$_POST['reward'] = (isset($_POST['reward']) && is_numeric($_POST['reward'])) ? abs(intval($_POST['reward'])) : 0;
		if ($_POST['user_id'] == 0 || empty($_POST['user_id']))
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['RUSSIANROULETTE_FAILED_FORM'],true,'index.php');
			die($h->endpage());
		}
		if ($_POST['user_id'] == $userid)
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['RUSSIANROULETTE_SELF'],true,'index.php');
			die($h->endpage());
		}
		else
		{
			$q = $db->query("SELECT * FROM `users` WHERE `userid` = '{$_POST['user_id']}'");
			if ($db->num_rows($q) != 0)
			{
				$r = $db->fetch_row($q);
				alert('success',$lang['ERROR_SUCCESS'],"{$lang['RUSSIANROULETTE_VALID_ACCOUNT_SEND']} {$r['username']}",true,'index.php');
				notification_add($_POST['user_id'], "{$ir['username']} ({$userid}) has challenge you to a game of russian roulette with the prize of {$_POST['reward']} primary currency! Click one of these options <a href='russianroulette.php?id={$userid}'>Accept</a> | <a href='russianroulette.php?id={$userid}&deny=1'>Deny</a>");
				$db->query("INSERT INTO `russian_roulette` VALUES('{$userid}', '{$_POST['user_id']}', '{$_POST['reward']}')");
			}
			else
			{
				alert('danger',$lang['ERROR_GENERIC'],$lang['RUSSIANROULETTE_INVALID_ACCOUNT_SEND'],true,'index.php');
				die($h->endpage());
			}
		}
	}
	die($h->endpage());
}
die($h->endpage());