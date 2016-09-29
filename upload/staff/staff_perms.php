<?php
/*
	File: staff/staff_perms.php
	Created: 6/1/2016 at 6:06PM Eastern Time
	Info: Allows admins to interact with the permissions in game.
	Author: TheMasterGeneral
	Website: http://mastergeneral156.pcriot.com/
*/
require('sglobals.php');
echo "<h3>Permissions</h3><hr />";
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
switch ($_GET['action'])
{
case "viewperm":
    viewperm();
    break;
case "editperm":
    editperm();
    break;
case "resetperm":
    resetperm();
    break;
default:
    die();
    break;
}
function viewperm()
{
	global $h,$ir,$db,$lang;
	if (!isset($_POST['userid']))
	{
		$csrf=request_csrf_html('staff_perm_1');
		echo "Firstly, select a user from the dropdown to view their permissions.";
		echo "<form method='post'>
        	" . user_dropdown(NULL, 'userid')
                . "
        	<br />
        	{$csrf}
        	<input type='submit' class='btn btn-default' value='Edit Permissions' />
        </form>";
		$h->endpage();
	}
	else
	{
		$_POST['userid'] = (isset($_POST['userid']) && is_numeric($_POST['userid'])) ? abs(intval($_POST['userid'])) : '';
		if (empty($_POST['userid']))
		{
			alert('danger',"{$lang['ERROR_INVALID']}","You specified an invalid input. Try again!");
			die($h->endpage());
		}
		else
		{
			$UserPermissionSelectQuery=$db->query("SELECT `p`.*,`u`.`username`,`u`.`userid` FROM `permissions` AS `p` INNER JOIN `users` AS `u` ON `u`.`userid` = `p`.`perm_user` WHERE `perm_user` = {$_POST['userid']}");
			$UserName=$db->fetch_single($db->query("SELECT `username` FROM `users` WHERE `userid` = {$_POST['userid']}"));
			staff_csrf_stdverify('staff_perm_1', '?action=viewperm');
			if ($db->num_rows($UserPermissionSelectQuery) == 0)
			{
				alert('danger',"All Permissions!","This user has all permissions allowed to them! We cannot display users who have all the permissions, only the users who has had their permissions tweaked.");
				die($h->endpage());
			}
			else
			{
				echo
				"Displaying {$UserName}'s Permissions.<br />
				<small>Remember, this will only show permissions if the user's permissions have been tweaked.</small>
				<table class='table table-bordered table-hover'>
					<thead>
						<tr>
							<th>Permission Name</th>
							<th>Disabled?</th>
						</tr>
					</thead>
					<tbody>";
				while ($UserPerm = $db->fetch_row($UserPermissionSelectQuery))
				{
					echo"<tr>
							<td>
								{$UserPerm['perm_name']}
							</td>
							<td>
								{$UserPerm['perm_disable']}
							</td>
						</tr>
							";
				}
				echo "</tbody></table>";
				stafflog_add("Viewed <a href='profile.php?user={$_POST['userid']}'>{$UserName}</a> [{$_POST['userid']}]'s Permissions.");
			}
		}
		$h->endpage();
	}
}
function editperm()
{
	global $h,$lang,$db,$ir;
	if (!isset($_POST['userid']))
	{
		$csrf=request_csrf_html('staff_perm_2');
		echo "Please fill out the form.";
		echo "<form method='post'>
		<table class='table table-bordered'>
			<tbody>
				<tr>
					<th>Select A User</th>
					<td>" . user_dropdown(NULL, 'userid') . "</td>
				</tr>
				<tr>
					<th>Select A Permission</th>
					<td><select class='form-control' name='permission' type='dropdown'>
							<option value='CanAttack'>Attack</option>
							<option value='CanBeAttack'>Can Be Attacked</option>
							<option value='CanReplyMail'>Reply To Mail</option>
							<option value='CanReplyForum'>Reply To Forums</option>
							<option value='CanCreateThread'>Create Forum Thread</option>
							<option value='CanComment'>Comment On Profiles</option>
							<option value='CanSellToGame'>Sell To Game</option>
							<option value='CanBuyFromGame'>Buy From Game</option>
						</select>
					</td>
				</tr>
				<tr>
					<th>Enable/Disable Permission?</th>
					<td><select class='form-control' name='enable' type='dropdown'>
							<option value='disable'>Disable/Ban</option>
							<option value='enable'>Enable/Unban</option>
						</select>
					</td>
				</tr>
			</tbody>
		</table>";
		echo "
        	{$csrf}
        	<input type='submit' class='btn btn-default' value='Edit Permissions' />
        </form>";
		$h->endpage();
	}
	else
	{
		$_POST['userid'] = (isset($_POST['userid']) && is_numeric($_POST['userid'])) ? abs(intval($_POST['userid'])) : '';
		staff_csrf_stdverify('staff_perm_2', '?action=editperm');
		if (empty($_POST['userid']))
		{
			alert('danger',"{$lang['ERROR_INVALID']}","You specified an invalid input. Try again!");
			die($h->endpage());
		}
		elseif (!in_array($_POST['enable'], array('disable', 'enable')))
		{
			alert('danger',"{$lang['ERROR_INVALID']}","You specified an invalid input. Try again!");
			die($h->endpage());
		}
		elseif (!in_array($_POST['permission'], array('CanAttack', 'CanBeAttack', 'CanReplyMail', 'CanReplyForum', 'CanCreateThread', 'CanComment', 'CanSellToGame', 'CanBuyFromGame')))
		{
			alert('danger',"{$lang['ERROR_INVALID']}","You specified an invalid input. Try again!");
			die($h->endpage());
		}
		else
		{
			$UserName=$db->fetch_single($db->query("SELECT `username` FROM `users` WHERE `userid` = {$_POST['userid']}"));
			if ($_POST['enable'] == 'disable')
			{
				$BanQuery=$db->fetch_row($db->query("SELECT `perm_id` FROM `permissions` WHERE `perm_name` = '{$_POST['permission']}' AND `perm_user` = {$_POST['userid']}"));
				if ($db->num_rows($db->query("SELECT `perm_id` FROM `permissions` WHERE `perm_name` = '{$_POST['permission']}' AND `perm_user` = {$_POST['userid']}")) > 0)
				{
					alert('danger',"Permission Already Given!","This user already has this permission specified. Try again, please!");
					die($h->endpage());
				}
				else
				{
					$db->query("INSERT INTO `permissions` (`perm_id`, `perm_user`, `perm_name`, `perm_disable`) VALUES (NULL, '{$_POST['userid']}', '{$_POST['permission']}', 'true');");
					stafflog_add("Disabled <a href='../profile.php?user={$_POST['userid']}'>{$UserName}</a> [{$_POST['userid']}]'s {$_POST['permission']} permission.");
					alert('success',"Permission Updated!","You have successfully updated that {$UserName}'s {$_POST['permission']} permission to disabled/banned.");
					die($h->endpage());
				}
			}
			else
			{
				$BanQuery=$db->fetch_row($db->query("SELECT `perm_id` FROM `permissions` WHERE `perm_name` = '{$_POST['permission']}' AND `perm_user` = {$_POST['userid']}"));
				if ($db->num_rows($db->query("SELECT `perm_id` FROM `permissions` WHERE `perm_name` = '{$_POST['permission']}' AND `perm_user` = {$_POST['userid']}")) == 0)
				{
					alert('danger',"Permission Not Existent!","This user already has full access to this permission. Enabling it again would do nothing...");
					die($h->endpage());
				}
				else
				{
					$db->query("DELETE FROM `permissions` WHERE `perm_user` = {$_POST['userid']} AND `perm_name` = '{$_POST['permission']}'");
					alert('success',"Permission Updated!","You have successfully updated that {$UserName}'s {$_POST['permission']} permission to enabled/unbanned.");
					stafflog_add("Enabled <a href='../profile.php?user={$_POST['userid']}'>{$UserName}</a> [{$_POST['userid']}]'s {$_POST['permission']} permission.");
					die($h->endpage());
				}
			}
		}
	}
}
function resetperm()
{
	global $db,$lang,$h;
	if (!isset($_POST['userid']))
	{
		$csrf=request_csrf_html('staff_perm_3');
		echo "Select a user to reset their permissions.";
		echo "
        	<form method='post'>
			" . user_dropdown(NULL, 'userid') . "
			{$csrf}
			<br />
			<small>Please type <i>CONFIRM</i> to confirm the permission reset</small><br />
			<input type='text' name='confirm' class='form-control'>
        	<input type='submit' class='btn btn-default' value='Reset Permissions' />
        </form>";
		$h->endpage();
	}
	else
	{
		$_POST['userid'] = (isset($_POST['userid']) && is_numeric($_POST['userid'])) ? abs(intval($_POST['userid'])) : '';
		staff_csrf_stdverify('staff_perm_3', '?action=resetperm');
		if ($_POST['confirm'] != 'CONFIRM')
		{
			alert('danger','Confirm Action!','Go back and make sure you type CONFIRM in the box.');
			die($h->endpage());
		}
		else
		{
			$UserName=$db->fetch_single($db->query("SELECT `username` FROM `users` WHERE `userid` = {$_POST['userid']}"));
			$db->query("DELETE FROM `permissions` WHERE `perm_user` = {$_POST['userid']}");
			alert('success',"User's Permissions Reset!","You have successfully reset {$UserName}'s permissions.");
			stafflog_add("Reset <a href='../profile.php?user={$_POST['userid']}'>{$UserName}</a> [{$_POST['userid']}]'s permissions.");
			die($h->endpage());
		}
	}
}