<?php
/*
	File: staff/staff_bots.php
	Created: 4/4/2017 at 7:01PM Eastern Time
	Info: Staff panel for handling the NPC Battle Tent.
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine/
*/
require('sglobals.php');
echo"<h3>{$lang['STAFF_BOTS_TITLE']}</h3>";
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
switch ($_GET['action'])
{
	case "addbot":
		addbot();
		break;
	case "delbot":
		delbot();
		break;
	default:
		die();
		break;
}
function addbot()
{
	global $db,$lang,$api,$h,$userid;
	if (isset($_POST['user']))
	{
		if (!isset($_POST['verf']) || !verify_csrf_code('staff_bot_add', stripslashes($_POST['verf'])))
		{
			alert('danger',$lang["CSRF_ERROR_TITLE"],$lang["CSRF_ERROR_TEXT"]);
			die($h->endpage());
		}
		else
		{
			$item=(isset($_POST['item']) && is_numeric($_POST['item'])) ? abs(intval($_POST['item'])) : 0;
			$user=(isset($_POST['user']) && is_numeric($_POST['user'])) ? abs(intval($_POST['user'])) : 0;
			$cooldown=(isset($_POST['cooldown']) && is_numeric($_POST['cooldown'])) ? abs(intval($_POST['cooldown'])) : 1;
			if (empty($item) || empty($user) || empty($cooldown))
			{
				alert('danger',$lang["ERROR_GENERIC"],$lang['STAFF_BOTS_ADD_ERROR']);
				die($h->endpage());
			}
			$q=$db->query("SELECT `botid` FROM `botlist` WHERE `botuser` = {$user}");
			if ($db->num_rows($q) > 0)
			{
				$db->free_result($q);
				alert('danger',$lang["ERROR_GENERIC"],$lang['STAFF_BOTS_ADD_ERROR1']);
				die($h->endpage());
			}
			$db->free_result($q);
			$q=$db->fetch_single($db->query("SELECT `user_level` FROM `users` WHERE `userid` = {$user}"));
			if (!($q == 'NPC'))
			{
				alert('danger',$lang["ERROR_GENERIC"],$lang['STAFF_BOTS_ADD_ERROR2']);
				die($h->endpage());
			}
			if ($api->SystemItemIDtoName($item) == false)
			{
				alert('danger',$lang["ERROR_GENERIC"],$lang['STAFF_BOTS_ADD_ERROR3']);
				die($h->endpage());
			}
			$db->query("INSERT INTO `botlist` (`botuser`, `botitem`, `botcooldown`) VALUES ('{$user}', '{$item}', '{$cooldown}')");
			alert('success',$lang["ERROR_SUCCESS"],$lang['STAFF_BOTS_ADD_SUCCESS'],true,'staff.php');
			$api->SystemLogsAdd($userid,'staff',"Added User ID {$user} to the bot list.");
			die($h->endpage());
		}
	}
	else
	{
		$csrf=request_csrf_html('staff_bot_add');
		echo "
		<form method='post'>
			<table class='table table-bordered'>
				<tr>
					<th colspan='2'>
						{$lang['STAFF_BOTS_ADD_FRM1']}
					</th>
				</tr>
				<tr>
					<th>
						{$lang['STAFF_BOTS_ADD_FRM2']}
					</th>
					<td>
						" . user2_dropdown('user') . "
					</td>
				</tr>
				<tr>
					<th>
						{$lang['STAFF_BOTS_ADD_FRM3']}
					</th>
					<td>
						" . item_dropdown('item') . "
					</td>
				</tr>
				<tr>
					<th>
						{$lang['STAFF_BOTS_ADD_FRM4']}
					</th>
					<td>
						<input required='1' type='number' name='cooldown' placeholder='3600=1 hr, 86400=1 day' class='form-control' min='1'>
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' value='{$lang['STAFF_BOTS_ADD_BTN']}' class='btn btn-primary'>
					</td>
				</tr>
			</table>
			{$csrf}
		</form>";
	}
}
function delbot()
{
	global $db,$userid,$api,$lang;
	if (isset($_POST['bot']))
	{
		if (!isset($_POST['verf']) || !verify_csrf_code('staff_bot_del', stripslashes($_POST['verf'])))
		{
			alert('danger',$lang["CSRF_ERROR_TITLE"],$lang["CSRF_ERROR_TEXT"]);
			die($h->endpage());
		}
		else
		{
			$bot=(isset($_POST['bot']) && is_numeric($_POST['bot'])) ? abs(intval($_POST['bot'])) : 0;
			if (empty($bot))
			{
				alert('danger',$lang["ERROR_GENERIC"],$lang['STAFF_BOTS_DEL_ERROR']);
				die($h->endpage());
			}
			$q=$db->query("SELECT `botid` FROM `botlist` WHERE `botuser` = {$bot}");
			if ($db->num_rows($q) == 0)
			{
				$db->free_result($q);
				alert('danger',$lang["ERROR_GENERIC"],$lang['STAFF_BOTS_ADD_ERROR1']);
				die($h->endpage());
			}
			$db->query("DELETE FROM `botlist` WHERE `botuser` = {$bot}");
			alert('success',$lang["ERROR_SUCCESS"],$lang['STAFF_BOTS_DEL_SUCCESS'],true,'staff.php');
			$api->SystemLogsAdd($userid,'staff',"Deleted User ID {$bot} from the bot list.");
		}
	}
	else
	{
		$csrf=request_csrf_html('staff_bot_del');
		echo "
		<form action='?action=delbot' method='post'>
		<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					{$lang['STAFF_BOTS_DEL_INFO']}
				</th>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_BOTS_DEL_TH']}
				</th>
				<td>
					" . npcbot_dropdown() . "
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' class='btn btn-primary' value='{$lang['STAFF_BOTS_DEL_BTN']}'>
				</td>
			</tr>
		</table>
		{$csrf}
		</form>";
	}
}
$h->endpage();