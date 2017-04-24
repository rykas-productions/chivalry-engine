<?php
/*
	File: staff/staff_punish.php
	Created: 4/4/2017 at 7:03PM Eastern Time
	Info: Staff panel for punishiments on users.
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine/
*/
require_once('sglobals.php');
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
switch ($_GET['action'])
{
	case 'fedjail':
		fedjail();
		break;
	case 'unfedjail':
		unfedjail();
		break;
	case 'forumwarn':
		forumwarn();
		break;
	default:
    echo 'Error: This script requires an action.';
	$h->endpage();
    break;
}
function fedjail()
{
	global $db,$userid,$lang,$h,$api;
	if (isset($_POST['user']))
	{
		$_POST['user'] = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs($_POST['user']) : 0;
		$_POST['reason'] = (isset($_POST['reason'])) ? $db->escape(strip_tags(stripslashes($_POST['reason']))) : 0;
		$_POST['days'] = (isset($_POST['days']) && is_numeric($_POST['days'])) ? abs($_POST['days']) : 0;
		if (!isset($_POST['verf']) || !verify_csrf_code('staff_feduser', stripslashes($_POST['verf'])))
		{
			alert('danger',$lang["CSRF_ERROR_TITLE"],$lang["CSRF_ERROR_TEXT"]);
			die($h->endpage());
		}
		if (empty($_POST['user']) || empty($_POST['reason']) || empty($_POST['days']))
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_PUNISHFED_ERR1']);
			die($h->endpage());
		}
		$q = $db->query("SELECT `user_level` FROM `users` WHERE `userid` = {$_POST['user']}");
		if ($db->num_rows($q) == 0)
		{
			$db->free_result($q);
			alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_PUNISHFED_ERR']);
			die($h->endpage());
		}
		$f_userlevel = $db->fetch_single($q);
		$db->free_result($q);
		if ($f_userlevel == 'Admin')
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_PUNISHFED_ERR2']);
			die($h->endpage());
		}
		$re = $db->query("UPDATE `users` SET `fedjail` = 1  WHERE `userid` = {$_POST['user']}");
		$days=$_POST['days'];
		$_POST['days']=time()+($_POST['days']*86400);
		if ($db->affected_rows() > 0)
		{
			$db->query("INSERT INTO `fedjail` VALUES(NULL, {$_POST['user']}, {$_POST['days']}, {$userid}, '{$_POST['reason']}')");
		}
		$api->SystemLogsAdd($userid,'staff',"Placed User ID {$_POST['user']} into the federal jail for {$days} days for {$_POST['reason']}.");
		$api->SystemLogsAdd($userid,'fedjail',"Placed User ID {$_POST['user']} into the federal jail for {$days} days for {$_POST['reason']}.");
		alert('success',$lang['ERROR_SUCCESS'],$lang['STAFF_PUNISHFED_SUCC'],true,'index.php');
		die($h->endpage());
	}
	else
	{
		$_GET['user'] = (isset($_GET['user']) && is_numeric($_GET['user'])) ? abs(intval($_GET['user'])) : 0;
		$csrf = request_csrf_html('staff_feduser');
		echo "
		<h3>
			{$lang['STAFF_PUNISHFED_FORM']}
		</h3>
		<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					{$lang['STAFF_PUNISHFED_INFO']}
				</th>
			</tr>
			<tr>
				<form method='post'>
				<th>
					User: 
				</th>
				<td>
					" . user_dropdown('user', $_GET['user']) . "
				</td>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_PUNISHFED_TH1']}
				</th>
				<td>
					<input type='number' class='form-control' min='1' required='1' name='days' />
				</td>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_PUNISHFED_TH2']}
				</th>
				<td>
					<input type='text' required='1' class='form-control' name='reason' />
				</td>
			</tr>
			<tr>
			{$csrf}
				<td colspan='2'>
					<input type='submit' class='btn btn-default' value='{$lang['STAFF_PUNISHFED_BTN']}' />
				</td>
			</tr>
			</form>
		</table>";
	}
}
function unfedjail()
{
	global $db,$userid,$api,$lang,$h;
	echo "<h3>{$lang['STAFF_UNFED_TITLE']}</h3><hr />";
	$_GET['user'] = (isset($_GET['user']) && is_numeric($_GET['user'])) ? abs($_GET['user']) : 0;
	if (isset($_POST['user']))
	{
		$_POST['user'] = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs($_POST['user']) : 0;
		if (!isset($_POST['verf']) || !verify_csrf_code('staff_unfeduser', stripslashes($_POST['verf'])))
		{
			alert('danger',$lang["CSRF_ERROR_TITLE"],$lang["CSRF_ERROR_TEXT"]);
			die($h->endpage());
		}
		$check = $db->query("SELECT `fed_id` FROM `fedjail` WHERE `fed_userid` = {$_POST['user']} LIMIT 1");
		if ($db->num_rows($check) == 0)
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_UNFED_ERR']);
			die($h->endpage());
		}
		$db->query("DELETE FROM `fedjail` WHERE `fed_userid` = {$_POST['user']}");
		$db->query("UPDATE `users` SET `fedjail` = 0 WHERE `userid` = {$_POST['user']}");
		$api->SystemLogsAdd($userid,'staff',"Removed User ID {$_POST['user']} from the federal jail.");
		$api->SystemLogsAdd($userid,'fedjail',"Removed User ID {$_POST['user']} from the federal jail.");
		alert('success',$lang['ERROR_SUCCESS'],$lang['STAFF_UNFED_SUCC']);
	}
	else
	{
		$csrf=request_csrf_html('staff_unfeduser');
		echo "<form method='post'>
			<table class='table table-bordered'>
				<tr>
					<th colspan='2'>
						{$lang['STAFF_UNFED_INFO']}
					</th>
				</tr>
				<tr>
					<th>
						{$lang['STAFF_PRIV_USER']}
					</th>
					<td>
						" . fed_user_dropdown('user',$_GET['user']) . "
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' class='btn btn-default' value='{$lang['STAFF_UNFED_BTN']}'>
					</td>
				</tr>
				{$csrf}
			</table>
		</form>";
	}
}
function forumwarn()
{
	global $db,$userid,$api,$lang,$h;
	echo "<h3>{$lang['STAFF_FWARN_TITLE']}</h3><hr />";
	$_GET['user'] = (isset($_GET['user']) && is_numeric($_GET['user'])) ? abs(intval($_GET['user'])) : 0;
	if (isset($_POST['user']))
	{
		$_POST['user'] = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs($_POST['user']) : 0;
		$_POST['reason'] = $db->escape(strip_tags(stripslashes($_POST['reason'])));
		if (!isset($_POST['verf']) || !verify_csrf_code('staff_forumwarn', stripslashes($_POST['verf'])))
		{
			alert('danger',$lang["CSRF_ERROR_TITLE"],$lang["CSRF_ERROR_TEXT"]);
			die($h->endpage());
		}
		if (empty($_POST['reason'] || $_POST['user']))
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_FWARN_ERR1']);
			die($h->endpage());
		}
		$check = $db->query("SELECT `userid` FROM `users` WHERE `userid` = {$_POST['user']} LIMIT 1");
		if ($db->num_rows($check) == 0)
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_FWARN_ERR']);
			die($h->endpage());
		}
		$api->SystemLogsAdd($userid,'staff',"Forum Warned User ID {$_POST['user']} for '{$_POST['reason']}'.");
		$api->SystemLogsAdd($userid,'forumwarn',"Forum Warned User ID {$_POST['user']} for '{$_POST['reason']}'.");
		$api->GameAddNotification($_POST['user'],"You have been received a forum warning for the following reason: {$_POST['reason']}.");
		alert('success',$lang['ERROR_SUCCESS'],$lang['STAFF_FWARN_SUCC']);
	}
	else
	{
		$csrf=request_csrf_html('staff_forumwarn');
		echo "<form method='post'>
			<table class='table table-bordered'>
				<tr>
					<th colspan='2'>
						{$lang['STAFF_FWARN_INFO']}
					</th>
				</tr>
				<tr>
					<th>
						{$lang['STAFF_PRIV_USER']}
					</th>
					<td>
						" . user_dropdown('user',$_GET['user']) . "
					</td>
				</tr>
				<tr>
					<th>
						{$lang['STAFF_FWARN_REASON']}
					</th>
					<td>
						<input type='text' class='form-control' name='reason' required='1'>
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' class='btn btn-default' value='{$lang['STAFF_FWARN_BTN']}'>
					</td>
				</tr>
				{$csrf}
			</table>
		</form>";
	}
}
$h->endpage();