<?php
/*
	File: staff/staff_bots.php
	Created: 4/4/2017 at 7:01PM Eastern Time
	Info: Staff panel for handling the NPC Battle Tent.
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine/
*/
require('sglobals.php');
echo"<h3>Staff Bot Tent</h3>";
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
	global $db,$api,$h,$userid;
	if (isset($_POST['user']))
	{
		if (!isset($_POST['verf']) || !verify_csrf_code('staff_bot_add', stripslashes($_POST['verf'])))
		{
			alert('danger',"Action Blocked!","Forms expire fairly quickly. Try again, but be quicker!");
			die($h->endpage());
		}
		else
		{
			$item=(isset($_POST['item']) && is_numeric($_POST['item'])) ? abs(intval($_POST['item'])) : 0;
			$user=(isset($_POST['user']) && is_numeric($_POST['user'])) ? abs(intval($_POST['user'])) : 0;
			$cooldown=(isset($_POST['cooldown']) && is_numeric($_POST['cooldown'])) ? abs(intval($_POST['cooldown'])) : 1;
			if (empty($item) || empty($user) || empty($cooldown))
			{
				alert('danger',"Uh Oh!","Please fill out the form completely.");
				die($h->endpage());
			}
			$q=$db->query("SELECT `botid` FROM `botlist` WHERE `botuser` = {$user}");
			if ($db->num_rows($q) > 0)
			{
				$db->free_result($q);
				alert('danger',"Uh Oh!","You cannot have the same bot listed twice.");
				die($h->endpage());
			}
			$db->free_result($q);
			$q=$db->fetch_single($db->query("SELECT `user_level` FROM `users` WHERE `userid` = {$user}"));
			if (!($q == 'NPC'))
			{
				alert('danger',"Uh Oh!","You cannot add a non-NPC to the NPC Bot Tent.");
				die($h->endpage());
			}
			if ($api->SystemItemIDtoName($item) == false)
			{
				alert('danger',"Uh Oh!","The item you've chosen for this bot to drop does not exist.");
				die($h->endpage());
			}
			$db->query("INSERT INTO `botlist` (`botuser`, `botitem`, `botcooldown`) VALUES ('{$user}', '{$item}', '{$cooldown}')");
			alert('success',"Success!","You have successfully added NPC User ID {$user} to the Bot Tent.",true,'index.php');
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
                        Use this form to add bots to the game that drop items when mugged.
					</th>
				</tr>
				<tr>
					<th>
						Bot
					</th>
					<td>
						" . user2_dropdown('user') . "
					</td>
				</tr>
				<tr>
					<th>
						Item Drop
					</th>
					<td>
						" . item_dropdown('item') . "
					</td>
				</tr>
				<tr>
					<th>
						Cooldown Time (Seconds)
					</th>
					<td>
						<input required='1' type='number' name='cooldown' placeholder='3600=1 hr; 86400=1 day' class='form-control' min='1'>
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' value='Add Bot' class='btn btn-primary'>
					</td>
				</tr>
			</table>
			{$csrf}
		</form>";
	}
}
function delbot()
{
	global $db,$userid,$api,$h;
	if (isset($_POST['bot']))
	{
		if (!isset($_POST['verf']) || !verify_csrf_code('staff_bot_del', stripslashes($_POST['verf'])))
		{
			alert('danger',"Action Blocked!","Forms expire fairly quickly. Try again, but be quicker!");
			die($h->endpage());
		}
		else
		{
			$bot=(isset($_POST['bot']) && is_numeric($_POST['bot'])) ? abs(intval($_POST['bot'])) : 0;
			if (empty($bot))
			{
				alert('danger',"Uh Oh!","Please select a bot to delete.");
				die($h->endpage());
			}
			$q=$db->query("SELECT `botid` FROM `botlist` WHERE `botuser` = {$bot}");
			if ($db->num_rows($q) == 0)
			{
				$db->free_result($q);
				alert('danger',"Uh Oh!","The NPC you've selected is not on the NPC Bot Tent, thus, cannot be removed.");
				die($h->endpage());
			}
			$db->query("DELETE FROM `botlist` WHERE `botuser` = {$bot}");
			alert('success',"Success!","You have removed NPC ID {$bot} from the Bot Tent.",true,'index.php');
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
					Select a bot to remove from the Bot Tent.
				</th>
			</tr>
			<tr>
				<th>
					Bot
				</th>
				<td>
					" . npcbot_dropdown() . "
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' class='btn btn-primary' value='Delete Bot'>
				</td>
			</tr>
		</table>
		{$csrf}
		</form>";
	}
}
$h->endpage();