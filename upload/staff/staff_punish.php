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
	case 'ipsearch':
		ipsearch();
		break;
	case 'massjail':
		massjail();
		break;
	case 'forumban':
		forumban();
		break;
	case 'unforumban':
		unforumban();
		break;
	case 'staffnotes':
		staffnotes();
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
		$already_fed=$db->query("SELECT `fed_id` FROM `fedjail` WHERE `fed_userid` = {$_POST['user']}");
		if ($db->num_rows($already_fed) > 0)
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_PUNISHFED_ERR3']);
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
		alert('success',$lang['ERROR_SUCCESS'],$lang['STAFF_UNFED_SUCC'],true,'index.php');
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
function ipsearch()
{
	global $db,$userid,$api,$h,$lang;
	echo "<h3>{$lang['STAFF_IP_TITLE']}</h3><hr />";
	if (isset($_POST['ip']))
	{
		$_POST['ip'] = (filter_input(INPUT_POST, 'ip', FILTER_VALIDATE_IP)) ? $_POST['ip'] : '';
		if (!isset($_POST['verf']) || !verify_csrf_code('staff_ipsearch', stripslashes($_POST['verf'])))
		{
			alert('danger',$lang["CSRF_ERROR_TITLE"],$lang["CSRF_ERROR_TEXT"]);
			die($h->endpage());
		}
		if (empty($_POST['ip']))
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_IP_IP']);
			die($h->endpage());
		}
		$echoip = htmlentities(stripslashes($_POST['ip']), ENT_QUOTES, 'ISO-8859-1');
		$queryip=$db->escape(stripslashes($_POST['ip']));
		alert('info',$lang['ERROR_INFO'],$lang['STAFF_IP_HUINFO'] . " <b>{$echoip}</b>",false);
		echo "<table class='table-bordered table'>
		<tr>
			<th>
				{$lang['STAFF_IP_OUTTH']}
			</th>
			<th>
				{$lang['STAFF_IP_OUTTH1']}
			</th>
			<th>
				{$lang['STAFF_IP_OUTTH2']}
			</th>
		</tr>";
		$q=$db->query("SELECT `username`,`userid`,`registertime`,`level` 
						FROM `users` WHERE `lastip` = '{$queryip}' 
						OR `registerip` = '{$queryip}' 
						OR `loginip` = '{$queryip}'
						ORDER BY `userid` ASC");
		$ids = array();
		while ($r = $db->fetch_row($q))
		{
			$ids[] = $r['userid'];
			echo"<tr>
				<td>
					<a href='../profile.php?user={$r['userid']}'>{$r['username']}</a> [{$r['userid']}]
				</td>
				<td>
					{$r['level']}
				</td>
				<td>
					" . date('F j, Y g:i:s a', $r['registertime']) . "
				</td>
			</tr>";
		}
		$csrf = request_csrf_html('staff_massjail');
		echo"</table>
		<form action='?action=massjail' method='post'>
		<input type='hidden' name='ids' value='" . implode(",", $ids) . "' />
		<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					{$lang['STAFF_IP_MJ']}
				</th>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_PUNISHFED_TH1']}
				</th>
				<td>
					<input type='number' required='1' name='days' class='form-control' min='1'>
				</td>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_PUNISHFED_TH2']}
				</th>
				<td>
					<input type='text' required='1' name='reason' class='form-control' value='Same IP Users'>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' class='btn btn-default' value='{$lang['STAFF_IP_MJ_BTN']}'>
				</td>
			</tr>
		</table>
		{$csrf}
		</form>";
	}
	else
	{
		$csrf=request_csrf_html('staff_ipsearch');
		echo "
		<form method='post'>
			<table class='table table-bordered'>
				<tr>
					<th colspan='2'>
						{$lang['STAFF_IP_INFO']}
					</th>
				</tr>
				<tr>
					<th>
						{$lang['STAFF_IP_TH']}
					</th>
					<td>
						<input type='text' class='form-control' required='1' name='ip' value='127.0.0.1'>
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' class='btn btn-default' value='{$lang['STAFF_IP_BTN']}'>
					</td>
				</tr>
			</table>
			{$csrf}
		</form>";
	}
}
function massjail()
{
	global $lang,$db,$userid,$api,$h;
	if (!isset($_POST['verf']) || !verify_csrf_code('staff_massjail', stripslashes($_POST['verf'])))
	{
		alert('danger',$lang["CSRF_ERROR_TITLE"],$lang["CSRF_ERROR_TEXT"]);
		die($h->endpage());
	}
	if (!isset($_POST['ids']))
    {
        $_POST['ids'] = '';
    }
	$ids = explode(",", $_POST['ids']);
    $ju = array();
	$_POST['reason'] = (isset($_POST['reason'])) ? $db->escape(strip_tags(stripslashes($_POST['reason']))) : '';
    $_POST['days'] = (isset($_POST['days']) && is_numeric($_POST['days'])) ? abs(intval($_POST['days'])) : '';
	if ((count($ids) == 1 && empty($ids[0])) || empty($_POST['reason']) || empty($_POST['days']))
    {
        alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_MJ_ERR'],true,'?action=ipsearch');
        die($h->endpage());
    }
	foreach ($ids as $id)
    {
        if (is_numeric($id) && abs($id) > 0)
        {
            $safe_id = abs($id);
			$days=($_POST['days']*86400)+time();
            $db->query("INSERT INTO `fedjail` VALUES(NULL, {$safe_id}, {$days}, {$userid}, '{$_POST['reason']}')");
			$api->SystemLogsAdd($userid,'fedjail',"Placed User ID {$safe_id} into the federal jail for {$days} days for {$_POST['reason']}.");
			echo "{$lang['STAFF_MJ_INFO']} {$safe_id} {$lang['STAFF_MJ_INFO1']}<br />";
            $ju[] = $id;
        }
    }
	if (count($ju) > 0)
    {
        $juv = implode(',', $ju);
        $re = $db->query("UPDATE `users` SET `fedjail` = 1 WHERE `userid` IN({$juv})");
		$api->SystemLogsAdd($userid,'staff',"Mass jailed User IDs {$juv} for {$_POST['days']} days for {$_POST['reason']}.");
        alert('success',$lang['ERROR_SUCCESS'],$lang['STAFF_MJ_SUCC'],true,'index.php');
        die($h->endpage());
    }
    else
    {
        alert('success',$lang['ERROR_SUCCESS'],$lang['STAFF_MJ_SUCC1'],true,'index.php');
        die($h->endpage());
    }
}
function forumban()
{
	global $db,$userid,$api,$lang,$h;
	if (isset($_POST['user']))
	{
		$_POST['user'] = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs($_POST['user']) : 0;
		$_POST['reason'] = (isset($_POST['reason'])) ? $db->escape(strip_tags(stripslashes($_POST['reason']))) : 0;
		$_POST['days'] = (isset($_POST['days']) && is_numeric($_POST['days'])) ? abs($_POST['days']) : 0;
		if (!isset($_POST['verf']) || !verify_csrf_code('staff_forumban', stripslashes($_POST['verf'])))
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
			alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_FBAN_ERR']);
			die($h->endpage());
		}
		$f_userlevel = $db->fetch_single($q);
		$db->free_result($q);
		if ($f_userlevel == 'Admin')
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_FBAN1_ERR1']);
			die($h->endpage());
		}
		$already_fed=$db->query("SELECT `fb_id` FROM `forum_bans` WHERE `fb_user` = {$_POST['user']}");
		if ($db->num_rows($already_fed) > 0)
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_FBAN_ERR2']);
			die($h->endpage());
		}
		$days=$_POST['days'];
		$_POST['days']=time()+($_POST['days']*86400);
		$db->query("INSERT INTO `forum_bans` VALUES(NULL, {$_POST['user']}, {$userid}, {$_POST['days']}, '{$_POST['reason']}')");
		$api->SystemLogsAdd($userid,'staff',"Forum banned User ID {$_POST['user']} for {$days} days for {$_POST['reason']}.");
		$api->SystemLogsAdd($userid,'forumban',"Forum banned User ID {$_POST['user']} for {$days} days for {$_POST['reason']}.");
		$api->GameAddNotification($_POST['user'],"The game administration has forum banned you for {$days} days for the following reason: '{$_POST['reason']}'.");
		alert('success',$lang['ERROR_SUCCESS'],$lang['STAFF_FBAN_SUCC'],true,'index.php');
		die($h->endpage());
	}
	else
	{
		$_GET['user'] = (isset($_GET['user']) && is_numeric($_GET['user'])) ? abs(intval($_GET['user'])) : 0;
		$csrf = request_csrf_html('staff_forumban');
		echo "
		<h3>
			{$lang['STAFF_FBAN_TITLE']}
		</h3>
		<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					{$lang['STAFF_FBAN_INFO']}
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
					<input type='submit' class='btn btn-default' value='{$lang['STAFF_FBAN_BTN']}' />
				</td>
			</tr>
			</form>
		</table>";
	}
}
function unforumban()
{
	global $db,$userid,$api,$lang,$h;
	echo "<h3>{$lang['STAFF_UFBAN_TITLE']}</h3><hr />";
	$_GET['user'] = (isset($_GET['user']) && is_numeric($_GET['user'])) ? abs($_GET['user']) : 0;
	if (isset($_POST['user']))
	{
		$_POST['user'] = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs($_POST['user']) : 0;
		if (!isset($_POST['verf']) || !verify_csrf_code('staff_unforumban', stripslashes($_POST['verf'])))
		{
			alert('danger',$lang["CSRF_ERROR_TITLE"],$lang["CSRF_ERROR_TEXT"]);
			die($h->endpage());
		}
		$check = $db->query("SELECT `fb_id` FROM `forum_bans` WHERE `fb_user` = {$_POST['user']} LIMIT 1");
		if ($db->num_rows($check) == 0)
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_UFBAN_ERR']);
			die($h->endpage());
		}
		$db->query("DELETE FROM `forum_bans` WHERE `fb_user` = {$_POST['user']}");
		$api->SystemLogsAdd($userid,'staff',"Removed User ID {$_POST['user']}'s forum ban");
		$api->SystemLogsAdd($userid,'forumban',"Removed User ID {$_POST['user']}'s forum ban.");
		$api->GameAddNotification($_POST['user'],"The game administration has removed your forum ban. You may use the forum once again.");
		alert('success',$lang['ERROR_SUCCESS'],$lang['STAFF_UFBAN_SUCC'],true,'index.php');
	}
	else
	{
		$csrf=request_csrf_html('staff_unforumban');
		echo "<form method='post'>
			<table class='table table-bordered'>
				<tr>
					<th colspan='2'>
						{$lang['STAFF_UFBAN_INFO']}
					</th>
				</tr>
				<tr>
					<th>
						{$lang['STAFF_PRIV_USER']}
					</th>
					<td>
						" . forumb_user_dropdown('user',$_GET['user']) . "
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' class='btn btn-default' value='{$lang['STAFF_UFBAN_BTN']}'>
					</td>
				</tr>
				{$csrf}
			</table>
		</form>";
	}
}
function staffnotes()
{
	global $db,$userid,$lang,$h,$api;
	$_POST['ID'] = (isset($_POST['ID']) && is_numeric($_POST['ID'])) ? abs(intval($_POST['ID'])) : '';
    $_POST['staffnotes'] = (isset($_POST['staffnotes']) && !is_array($_POST['staffnotes'])) ? $db->escape(strip_tags(stripslashes($_POST['staffnotes']))) : '';
    if (empty($_POST['ID']) || empty($_POST['staffnotes']))
    {
        alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_NOTES_ERR'],true,'index.php');
        die($h->endpage());
    }
	$q = $db->query("SELECT `staff_notes` FROM `users` WHERE `userid` = {$_POST['ID']}");
	if ($db->num_rows($q) == 0)
    {
        $db->free_result($q);
        alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_NOTES_ERR1'],true,'index.php');
        die($h->endpage());
    }
	$db->query("UPDATE `users` SET `staff_notes` = '{$_POST['staffnotes']}' WHERE `userid` = '{$_POST['ID']}'");
	$api->SystemLogsAdd($userid,'staff',"Updated User ID {$_POST['ID']}'s staff notes.");
	alert('success',$lang['ERROR_SUCCESS'],$lang['STAFF_NOTES_SUCC'],true,'index.php');
}
$h->endpage();