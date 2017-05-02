<?php

require("globals.php");

if (isset($_GET['id']))
{
	if (isset($_GET['deny']))
	{
		$db->query("DELETE FROM `russian_roulette` WHERE `challenger` = '{$_GET['id']}' AND `challengee` = '{$userid}'");
		echo "{$lang['RUSSIANROULETTE_DENIED']}";
		require ("footer.php");
		die();
	}
	$q = $db->query("SELECT `challenger` FROM `russian_roulette` WHERE `challenger` = '{$_GET['id']}' AND `challengee` = '{$userid}'");
	if ($db->num_rows($q) == 0)
	{
		$q = $db->query("SELECT * FROM `users` WHERE `userid` = '{$_GET['id']}'");
		if ($db->num_rows($q) != 0)
		{
			$r = $db->fetch_row($q);
			echo "{$lang['RUSSIANROULETTE_NO_INVITE']} {$r['username']} ({$_GET['id']})";
			require ("footer.php");
			die();
		}
		echo "{$lang['RUSSIANROULETTE_INVALID_ACCOUNT']}";
		require ("footer.php");
		die();
	}
	else
	{
		$q = $db->query("SELECT * FROM `russian_roulette` WHERE `challenger` = '{$_GET['id']}' AND `challengee` = '{$userid}'");
		$r2 = $db->fetch_row($q);
		if ($ir['primary_currency'] < $r2['reward'])
		{
			echo "{$lang['RUSSIANROULETTE_INSUFFICIENT_CURRENCY']}";
			require ("footer.php");
			die();
		}
		$q = $db->query("SELECT * FROM `users` WHERE `userid` = '{$_GET['id']}'");
		$r = $db->fetch_row($q);
		if ($r['primary_currency'] < $r2['reward'])
		{
			echo "{$lang['RUSSIANROULETTE_SCAM']}";
			require ("footer.php");
			die();
		}
		$rand = mt_rand(1, 8);
		$player = mt_rand(1, 2);
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
			echo "<br><br>{$lang['RUSSIANROULETTE_WON']} {$r2['username']} {$lang['RUSSIANROULETTE_WON2']} {$result} {$lang['RUSSIANROULETTE_WON3']} {$r['reward']} {$lang['RUSSIANROULETTE_REWARD']}";
			$db->query("UPDATE `users` SET `primary_currency` = `primary_currency` - '{$r['reward']}' WHERE `userid` = '{$_GET['id']}'");
			$db->query("UPDATE `users` SET `primary_currency` = `primary_currency` + '{$r['reward']}' WHERE `userid` = '{$userid}'");
			$hosptime = Random(75, 175) + floor($r2['level'] / 2);
			$hospreason = $db->escape("Played the wrong game with <a href='profile.php?user={$userid}'>{$ir['username']}</a>");
			$db->query("UPDATE `users` SET `hp` = 1 WHERE `userid` = {$_GET['id']}");
			put_infirmary($_GET['id'],$hosptime,$hospreason);
		}
		if ($shot == true)
		{
			$q = $db->query("SELECT * FROM `russian_roulette` WHERE `challenger` = '{$_GET['id']}' AND `challengee` = '{$userid}'");
			$r = $db->fetch_row($q);
			$q = $db->query("SELECT * FROM `users` WHERE `userid` = '{$_GET['id']}'");
			$r2 = $db->fetch_row($q);
			echo "<br><br>{$lang['RUSSIANROULETTE_LOST']} {$result} {$lang['RUSSIANROULETTE_LOST2']} {$hosptime} {$lang['RUSSIANROULETTE_LOST3']} {$r['reward']} {$lang['RUSSIANROULETTE_REWARD']}";
			$db->query("UPDATE `users` SET `primary_currency` = `primary_currency` + '{$r['reward']}' WHERE `userid` = '{$_GET['id']}'");
			$db->query("UPDATE `users` SET `primary_currency` = `primary_currency` - '{$r['reward']}' WHERE `userid` = '{$userid}'");
			$hosptime = Random(75, 175) + floor($ir['level'] / 2);
			$hospreason = $db->escape("Played the wrong game with <a href='profile.php?user={$_GET['id']}'>{$r2['username']}</a>");
			$db->query("UPDATE `users` SET `hp` = 1 WHERE `userid` = {$_GET['id']}");
			put_infirmary($userid,$hosptime,$hospreason);
		}
		$db->query("DELETE FROM `russian_roulette` WHERE `challenger` = '{$_GET['id']}' AND `challengee` = '{$userid}'");
	}
}
else
{
	if (!isset($_POST['user_id']))
	{
		echo "
		<form method='post' class='form' role='form'>
			<table class='table table-bordered'>
				<tr>
					<th width='33%'>
						{$lang['RUSSIANROULETTE_USER_INSERT']}
					</th>
					<td>
						<input type='number' required='1' name='user_id' class='form-control'>
					</td>
				</tr>
				<tr>
					<th width='33%'>
						{$lang['RUSSIANROULETTE_REWARD_INSERT']}
					</th>
					<td>
						<input type='number' required='1' name='reward' min='0' max='{$ir['primary_currency']}' class='form-control'>
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' value='{$lang['RUSSIANROULETTE_SEND']}' class='btn btn-default'>
					</td>
				</tr>
			</table>
		</form>";
	}
	else
	{
		if ($_POST['user_id'] == 0 || $_POST['user_id'] == "")
		{
			echo "{$lang['RUSSIANROULETTE_FAILED_FORM']}";
		}
		else
		{
			$q = $db->query("SELECT * FROM `users` WHERE `userid` = '{$_POST['user_id']}'");
			if ($db->num_rows($q) != 0)
			{
				echo "{$lang['RUSSIANROULETTE_VALID_ACCOUNT_SEND']} {$r['username']} ({$r['userid']})!";
				notification_add($_POST['user_id'], "{$ir['username']} ({$userid}) has challenge you to a game of russian roulette with the prize of {$_POST['reward']} primary currency! Click one of these options <a href='russianroulette.php?id={$userid}'>Accept</a> | <a href='russianroulette.php?id={$userid}&deny=1'>Deny</a>");
				$db->query("INSERT INTO `russian_roulette` VALUES('{$userid}', '{$_POST['user_id']}', '{$_POST['reward']}')");
			}
			else
			{
				echo "{$lang['RUSSIANROULETTE_INVALID_ACCOUNT_SEND']}";
			}
		}
	}
}
require ("footer.php")
?>
