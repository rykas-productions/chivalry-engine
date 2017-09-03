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
	case 'massmail':
		massmail();
		break;
	case 'massemail':
		massemail();
		break;
	case 'banip':
		banip();
		break;
	case 'unbanip':
		unbanip();
		break;
	default:
		echo 'Error: This script requires an action.';
		$h->endpage();
		break;
}
function fedjail()
{
	global $db,$userid,$h,$api;
	if (isset($_POST['user']))
	{
		$_POST['user'] = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs($_POST['user']) : 0;
		$_POST['reason'] = (isset($_POST['reason'])) ? $db->escape(strip_tags(stripslashes($_POST['reason']))) : 0;
		$_POST['days'] = (isset($_POST['days']) && is_numeric($_POST['days'])) ? abs($_POST['days']) : 0;
		if (!isset($_POST['verf']) || !verify_csrf_code('staff_feduser', stripslashes($_POST['verf'])))
		{
			alert('danger',"Action Blocked!","We have blocked this action for your security. Please fill out the form quicker next time.");
			die($h->endpage());
		}
		if (empty($_POST['user']) || empty($_POST['reason']) || empty($_POST['days']))
		{
			alert('danger',"Uh Oh!","Please fill out the form completely before submitting it.");
			die($h->endpage());
		}
		$q = $db->query("SELECT `user_level` FROM `users` WHERE `userid` = {$_POST['user']}");
		if ($db->num_rows($q) == 0)
		{
			$db->free_result($q);
			alert('danger',"Uh Oh!","This user does not exist.");
			die($h->endpage());
		}
		$f_userlevel = $db->fetch_single($q);
		$db->free_result($q);
		if ($f_userlevel == 'Admin')
		{
			alert('danger',"Uh Oh!","You cannot place administrators into the federal dungeon. Please remove their privilege and try again.");
			die($h->endpage());
		}
		$already_fed=$db->query("SELECT `fed_id` FROM `fedjail` WHERE `fed_userid` = {$_POST['user']}");
		if ($db->num_rows($already_fed) > 0)
		{
			alert('danger',"Uh Oh!","This user is already in the federal dungeon. Please edit their sentence.");
			die($h->endpage());
		}
		$re = $db->query("UPDATE `users` SET `fedjail` = 1  WHERE `userid` = {$_POST['user']}");
		$days=$_POST['days'];
		$_POST['days']=time()+($_POST['days']*86400);
		$db->query("INSERT INTO `fedjail` VALUES(NULL, {$_POST['user']}, {$_POST['days']}, {$userid}, '{$_POST['reason']}')");
		$api->SystemLogsAdd($userid,'staff',"Placed <a href='../profile.php?user={$_POST['user']}'>{$api->SystemUserIDtoName($_POST['user'])}</a> [{$_POST['user']}] into the federal dungeon for {$days} days for {$_POST['reason']}.");
		$api->SystemLogsAdd($userid,'fedjail',"Placed <a href='../profile.php?user={$_POST['user']}'>{$api->SystemUserIDtoName($_POST['user'])}</a> [{$_POST['user']}] into the federal dungeon for {$days} days for {$_POST['reason']}.");
		alert('success',"Success!","You have placed {$api->SystemUserIDtoName($_POST['user'])} in the federal dungeon for {$days} days for {$_POST['reason']}. ",true,'index.php');
		die($h->endpage());
	}
	else
	{
		$_GET['user'] = (isset($_GET['user']) && is_numeric($_GET['user'])) ? abs(intval($_GET['user'])) : 0;
		$csrf = request_csrf_html('staff_feduser');
		echo "
		<h3>
			Jailing User
		</h3>
		<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					Use this form to place someone in federal dungeon. They will not be able to interact with the game.
				</th>
			</tr>
			<tr>
				<form method='post'>
				<th>
					User
				</th>
				<td>
					" . user_dropdown('user', $_GET['user']) . "
				</td>
			</tr>
			<tr>
				<th>
					Days
				</th>
				<td>
					<input type='number' class='form-control' min='1' required='1' name='days' />
				</td>
			</tr>
			<tr>
				<th>
					Reason
				</th>
				<td>
					<input type='text' required='1' class='form-control' name='reason' />
				</td>
			</tr>
			<tr>
			{$csrf}
				<td colspan='2'>
					<input type='submit' class='btn btn-primary' value='Lock Up' />
				</td>
			</tr>
			</form>
		</table>";
	}
}
function unfedjail()
{
	global $db,$userid,$api,$h;
	echo "<h3>Remove User from Federal Dungeon</h3><hr />";
	$_GET['user'] = (isset($_GET['user']) && is_numeric($_GET['user'])) ? abs($_GET['user']) : 0;
	if (isset($_POST['user']))
	{
		$_POST['user'] = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs($_POST['user']) : 0;
		if (!isset($_POST['verf']) || !verify_csrf_code('staff_unfeduser', stripslashes($_POST['verf'])))
		{
			alert('danger',"Action Blocked!","We have blocked this action for your security. Please fill out the form quicker next time.");
			die($h->endpage());
		}
		$check = $db->query("SELECT `fed_id` FROM `fedjail` WHERE `fed_userid` = {$_POST['user']} LIMIT 1");
		if ($db->num_rows($check) == 0)
		{
			alert('danger',"Uh Oh!","This user is not in the federal dungeon.");
			die($h->endpage());
		}
		$db->query("DELETE FROM `fedjail` WHERE `fed_userid` = {$_POST['user']}");
		$db->query("UPDATE `users` SET `fedjail` = 0 WHERE `userid` = {$_POST['user']}");
		$api->SystemLogsAdd($userid,'staff',"Removed <a href='../profile.php?user={$_POST['user']}'>{$api->SystemUserIDtoName($_POST['user'])}</a> [{$_POST['user']}] from the federal dungeon.");
		$api->SystemLogsAdd($userid,'fedjail',"Removed <a href='../profile.php?user={$_POST['user']}'>{$api->SystemUserIDtoName($_POST['user'])}</a> [{$_POST['user']}] from the federal dungeon.");
		alert('success',"Success!","You have successfully removed {$api->SystemUserIDtoName($_POST['user'])} from the federal dungeon.",true,'index.php');
	}
	else
	{
		$csrf=request_csrf_html('staff_unfeduser');
		echo "<form method='post'>
			<table class='table table-bordered'>
				<tr>
					<th colspan='2'>
						Select a user to remove form the Federal Dungeon
					</th>
				</tr>
				<tr>
					<th>
						User
					</th>
					<td>
						" . fed_user_dropdown('user',$_GET['user']) . "
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' class='btn btn-primary' value='Remove from Dungeon'>
					</td>
				</tr>
				{$csrf}
			</table>
		</form>";
	}
}
function forumwarn()
{
	global $db,$userid,$api,$h;
	echo "<h3>Forum Warn</h3><hr />";
	$_GET['user'] = (isset($_GET['user']) && is_numeric($_GET['user'])) ? abs(intval($_GET['user'])) : 0;
	if (isset($_POST['user']))
	{
		$_POST['user'] = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs($_POST['user']) : 0;
		$_POST['reason'] = $db->escape(strip_tags(stripslashes($_POST['reason'])));
		if (!isset($_POST['verf']) || !verify_csrf_code('staff_forumwarn', stripslashes($_POST['verf'])))
		{
			alert('danger',"Action Blocked!","We have blocked this action for your security. Please fill out the form quicker next time.");
			die($h->endpage());
		}
		if (empty($_POST['reason']) ||  empty($_POST['user']))
		{
			alert('danger',"Uh Oh!","Please fill out the form completely before submitting it.");
			die($h->endpage());
		}
		$check = $db->query("SELECT `userid` FROM `users` WHERE `userid` = {$_POST['user']} LIMIT 1");
		if ($db->num_rows($check) == 0)
		{
			alert('danger',"Uh Oh!","The user you are attempting to warn does not exist.");
			die($h->endpage());
		}
		$api->SystemLogsAdd($userid,'staff',"Forum Warned <a href='../profile.php?user={$_POST['user']}'>{$api->SystemUserIDtoName($_POST['user'])}</a> [{$_POST['user']}] for '{$_POST['reason']}'.");
		$api->SystemLogsAdd($userid,'forumwarn',"Forum Warned <a href='../profile.php?user={$_POST['user']}'>{$api->SystemUserIDtoName($_POST['user'])}</a> [{$_POST['user']}] for '{$_POST['reason']}'.");
		$api->GameAddNotification($_POST['user'],"You have been received a forum warning for the following reason: {$_POST['reason']}.");
		alert('success',"Success!","You have forum warned {$api->SystemUserIDtoName($_POST['user'])}.");
	}
	else
	{
		$csrf=request_csrf_html('staff_forumwarn');
		echo "<form method='post'>
			<table class='table table-bordered'>
				<tr>
					<th colspan='2'>
						Select a user, then give them a warning.
					</th>
				</tr>
				<tr>
					<th>
						User
					</th>
					<td>
						" . user_dropdown('user',$_GET['user']) . "
					</td>
				</tr>
				<tr>
					<th>
                        Warning
					</th>
					<td>
						<input type='text' class='form-control' name='reason' required='1'>
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' class='btn btn-primary' value='Forum Warn'>
					</td>
				</tr>
				{$csrf}
			</table>
		</form>";
	}
}
function ipsearch()
{
	global $db,$h;
	echo "<h3>IP Lookup</h3><hr />";
	if (isset($_POST['ip']))
	{
		$_POST['ip'] = (filter_input(INPUT_POST, 'ip', FILTER_VALIDATE_IP)) ? $_POST['ip'] : '';
		if (!isset($_POST['verf']) || !verify_csrf_code('staff_ipsearch', stripslashes($_POST['verf'])))
		{
			alert('danger',"Action Blocked!","We have blocked this action for your security. Please fill out the form quicker next time.");
			die($h->endpage());
		}
		if (empty($_POST['ip']))
		{
			alert('danger',"Uh Oh!","Please specify a valid IP Address to look up.");
			die($h->endpage());
		}
		$echoip = htmlentities(stripslashes($_POST['ip']), ENT_QUOTES, 'ISO-8859-1');
		$queryip=$db->escape(stripslashes($_POST['ip']));
		alert('info',"Information!","Looking up users with the IP Adddress: <b>{$echoip}</b>",false);
		echo "<table class='table-bordered table'>
		<tr>
			<th>
				User
			</th>
			<th>
				Level
			</th>
			<th>
				Registration
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
					Use this form to palce the users above into the federal dungeon.
				</th>
			</tr>
			<tr>
				<th>
					Days
				</th>
				<td>
					<input type='number' required='1' name='days' class='form-control' min='1'>
				</td>
			</tr>
			<tr>
				<th>
					Reason
				</th>
				<td>
					<input type='text' required='1' name='reason' class='form-control' value='Same IP Users'>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' class='btn btn-primary' value='Mass Lock Up'>
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
						Input an IP Address to look up. This will list any players associated with that IP Address.
					</th>
				</tr>
				<tr>
					<th>
						IP Address
					</th>
					<td>
						<input type='text' class='form-control' required='1' name='ip' value='...'>
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' class='btn btn-primary' value='Lookup IP'>
					</td>
				</tr>
			</table>
			{$csrf}
		</form>";
	}
}
function massjail()
{
	global $db,$userid,$api,$h;
	if (!isset($_POST['verf']) || !verify_csrf_code('staff_massjail', stripslashes($_POST['verf'])))
	{
		alert('danger',"Action Blocked!","We have blocked this action for your security. Please fill out the form quicker next time.");
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
        alert('danger',"Uh Oh!","Please fill out the form completely.",true,'?action=ipsearch');
        die($h->endpage());
    }
	foreach ($ids as $id)
    {
        if (is_numeric($id) && abs($id) > 0)
        {
            $safe_id = abs($id);
			$days=($_POST['days']*86400)+time();
            $db->query("INSERT INTO `fedjail` VALUES(NULL, {$safe_id}, {$days}, {$userid}, '{$_POST['reason']}')");
			$api->SystemLogsAdd($userid,'fedjail',"Placed <a href='../profile.php?user={$safe_id}'>{$api->SystemUserIDtoName($safe_id)}</a> [{$safe_id}] into the federal dungeon for {$days} days for {$_POST['reason']}.");
			echo "Placing User ID {$safe_id} into the federal dungeon.<br />";
            $ju[] = $id;
        }
    }
	if (count($ju) > 0)
    {
        $juv = implode(',', $ju);
        $re = $db->query("UPDATE `users` SET `fedjail` = 1 WHERE `userid` IN({$juv})");
		$api->SystemLogsAdd($userid,'staff',"Mass jailed User IDs {$juv} for {$_POST['days']} days for {$_POST['reason']}.");
        alert('success',"Success!","You have placed User IDs {$juv} into the federal dungeon.",true,'index.php');
        die($h->endpage());
    }
    else
    {
        alert('success',"Success!","No users were placed into the federal dungeon.",true,'index.php');
        die($h->endpage());
    }
}
function forumban()
{
	global $db,$userid,$api,$h;
	if (isset($_POST['user']))
	{
		$_POST['user'] = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs($_POST['user']) : 0;
		$_POST['reason'] = (isset($_POST['reason'])) ? $db->escape(strip_tags(stripslashes($_POST['reason']))) : 0;
		$_POST['days'] = (isset($_POST['days']) && is_numeric($_POST['days'])) ? abs($_POST['days']) : 0;
		if (!isset($_POST['verf']) || !verify_csrf_code('staff_forumban', stripslashes($_POST['verf'])))
		{
			alert('danger',"Action Blocked!","We have blocked this action for your security. Please fill out the form quicker next time.");
			die($h->endpage());
		}
		if (empty($_POST['user']) || empty($_POST['reason']) || empty($_POST['days']))
		{
			alert('danger',"Uh Oh!","Please fill out the previous form completely before submitting again.");
			die($h->endpage());
		}
		$q = $db->query("SELECT `user_level` FROM `users` WHERE `userid` = {$_POST['user']}");
		if ($db->num_rows($q) == 0)
		{
			$db->free_result($q);
			alert('danger',"Uh Oh!","This user does not exist.");
			die($h->endpage());
		}
		$f_userlevel = $db->fetch_single($q);
		$db->free_result($q);
		if ($f_userlevel == 'Admin')
		{
			alert('danger',"Uh Oh!","You cannot forum ban an administrator.");
			die($h->endpage());
		}
		$already_fed=$db->query("SELECT `fb_id` FROM `forum_bans` WHERE `fb_user` = {$_POST['user']}");
		if ($db->num_rows($already_fed) > 0)
		{
			alert('danger',"Uh Oh!","This user is already forum banned. Please edit their ban.");
			die($h->endpage());
		}
		$days=$_POST['days'];
		$_POST['days']=time()+($_POST['days']*86400);
		$db->query("INSERT INTO `forum_bans` VALUES(NULL, {$_POST['user']}, {$userid}, {$_POST['days']}, '{$_POST['reason']}')");
		$api->SystemLogsAdd($userid,'staff',"Forum banned <a href='../profile.php?user={$_POST['user']}'>{$api->SystemUserIDtoName($_POST['user'])}</a> [{$_POST['user']}] for {$days} days for {$_POST['reason']}.");
		$api->SystemLogsAdd($userid,'forumban',"Forum banned <a href='../profile.php?user={$_POST['user']}'>{$api->SystemUserIDtoName($_POST['user'])}</a> [{$_POST['user']}] for {$days} days for {$_POST['reason']}.");
		$api->GameAddNotification($_POST['user'],"The game administration has forum banned you for {$days} days for the following reason: '{$_POST['reason']}'.");
		alert('success',"Success!","You have successfully forum banned {$api->SystemUserIDtoName($_POST['user'])} for {$days} days for {$_POST['reason']}.",true,'index.php');
		die($h->endpage());
	}
	else
	{
		$_GET['user'] = (isset($_GET['user']) && is_numeric($_GET['user'])) ? abs(intval($_GET['user'])) : 0;
		$csrf = request_csrf_html('staff_forumban');
		echo "
		<h3>
			Forum Ban
		</h3>
		<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					Banning a user from the forums will not allow them to view or post.
				</th>
			</tr>
			<tr>
				<form method='post'>
				<th>
					User
				</th>
				<td>
					" . user_dropdown('user', $_GET['user']) . "
				</td>
			</tr>
			<tr>
				<th>
					Days
				</th>
				<td>
					<input type='number' class='form-control' min='1' required='1' name='days' />
				</td>
			</tr>
			<tr>
				<th>
					Reason
				</th>
				<td>
					<input type='text' required='1' class='form-control' name='reason' />
				</td>
			</tr>
			<tr>
			{$csrf}
				<td colspan='2'>
					<input type='submit' class='btn btn-primary' value='Forum Ban' />
				</td>
			</tr>
			</form>
		</table>";
	}
}
function unforumban()
{
	global $db,$userid,$api,$h;
	echo "<h3>Remove Forum Ban</h3><hr />";
	$_GET['user'] = (isset($_GET['user']) && is_numeric($_GET['user'])) ? abs($_GET['user']) : 0;
	if (isset($_POST['user']))
	{
		$_POST['user'] = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs($_POST['user']) : 0;
		if (!isset($_POST['verf']) || !verify_csrf_code('staff_unforumban', stripslashes($_POST['verf'])))
		{
			alert('danger',"Action Blocked!","We have blocked this action for your security. Please fill out the form quicker next time.");
			die($h->endpage());
		}
		$check = $db->query("SELECT `fb_id` FROM `forum_bans` WHERE `fb_user` = {$_POST['user']} LIMIT 1");
		if ($db->num_rows($check) == 0)
		{
			alert('danger',"Uh Oh!","This user is not forum banned.");
			die($h->endpage());
		}
		$db->query("DELETE FROM `forum_bans` WHERE `fb_user` = {$_POST['user']}");
		$api->SystemLogsAdd($userid,'staff',"Removed <a href='../profile.php?user={$_POST['user']}'>{$api->SystemUserIDtoName($_POST['user'])}</a> [{$_POST['user']}]'s forum ban");
		$api->SystemLogsAdd($userid,'forumban',"Removed <a href='../profile.php?user={$_POST['user']}'>{$api->SystemUserIDtoName($_POST['user'])}</a> [{$_POST['user']}]'s forum ban.");
		$api->GameAddNotification($_POST['user'],"The game administration has removed your forum ban. You may use the forum once again.");
		alert('success',"Success!","You have successfully removed {$api->SystemUserIDtoName($_POST['user'])}'s forum ban.",true,'index.php');
	}
	else
	{
		$csrf=request_csrf_html('staff_unforumban');
		echo "<form method='post'>
			<table class='table table-bordered'>
				<tr>
					<th colspan='2'>
						Select a user to remove their forum ban.
					</th>
				</tr>
				<tr>
					<th>
						User
					</th>
					<td>
						" . forumb_user_dropdown('user',$_GET['user']) . "
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' class='btn btn-primary' value='Remove Forum Ban'>
					</td>
				</tr>
				{$csrf}
			</table>
		</form>";
	}
}
function staffnotes()
{
	global $db,$userid,$h,$api;
	$_POST['ID'] = (isset($_POST['ID']) && is_numeric($_POST['ID'])) ? abs(intval($_POST['ID'])) : '';
    $_POST['staffnotes'] = (isset($_POST['staffnotes']) && !is_array($_POST['staffnotes'])) ? $db->escape(strip_tags(stripslashes($_POST['staffnotes']))) : '';
    if (empty($_POST['ID']) || empty($_POST['staffnotes']))
    {
        alert('danger',"Uh Oh!","Please specify a user's notes you wish to update.",true,'index.php');
        die($h->endpage());
    }
	$q = $db->query("SELECT `staff_notes` FROM `users` WHERE `userid` = {$_POST['ID']}");
	if ($db->num_rows($q) == 0)
    {
        $db->free_result($q);
        alert('danger',"Uh Oh!","The user's notes you're trying to update does not exist.",true,'index.php');
        die($h->endpage());
    }
	$db->query("UPDATE `users` SET `staff_notes` = '{$_POST['staffnotes']}' WHERE `userid` = '{$_POST['ID']}'");
	$api->SystemLogsAdd($userid,'staff',"Updated <a href='../profile.php?user={$_POST['ID']}'>{$api->SystemUserIDtoName($_POST['ID'])}</a> [{$_POST['ID']}]'s staff notes.");
	alert('success',"Success!","You have successfully updated {$api->SystemUserIDtoName($_POST['ID'])}'s staff notes.",true,"../profile.php?user={$_POST['ID']}");
}
function massmail()
{
	global $db,$userid,$h,$api,$set;
	echo "<h3>Mass Mailer</h3><hr>";
	if (isset($_POST['msg']))
	{
		$msg = $_POST['msg'];
		if (!isset($_POST['verf']) || !verify_csrf_code('staff_massmail', stripslashes($_POST['verf'])))
		{
			alert('danger',"Action Blocked!","We have blocked this action for your security. Please fill out the form quicker next time.");
			die($h->endpage());
		}
		if (empty($msg))
		{
			alert('danger',"Uh Oh!","Please fill in the previous form completely before submitting again.");
			die($h->endpage());
		}
		if (strlen($msg) > 65655)
		{
			alert('danger',"Uh Oh!","Sent messages can only be, at maximum, 65,655 characters in length.");
			die($h->endpage());
		}
		$q=$db->query("SELECT `userid`,`user_level` FROM `users`");
		$sent=0;
		while ($r = $db->fetch_row($q))
		{
			echo "Sending Mail to {$api->SystemUserIDtoName($r['userid'])} ...";
			if ($r['user_level'] == 'NPC')
			{
				echo "... Failed.";
			}
			else
			{
				if ($api->GameAddMail($r['userid'],"{$set['WebsiteName']} Mass Mail",$msg,$userid) == true)
				{
					echo "... Success.";
					$sent=$sent+1;
				}
				else
				{
					echo "... Failed.";
				}
			}
			echo "<br />";
		}
        alert('success',"Success!","You successfully sent a mass mail to {$sent} players",true,'index.php');
		$api->SystemLogsAdd($userid,'staff',"Sent a mass mail.");
	}
	else
	{
		$csrf=request_csrf_html('staff_massmail');
		echo "<table class='table table-bordered'>
		<form method='post'>
		<tr>
			<th colspan='2'>
				Send a mass mail to the game using this form. Larger games may struggle to send out a mass mail. If
				this is the case, create an announcement instead.
			</th>
		</tr>
		<tr>
			<th>
				Message
			</th>
			<td>
				<textarea class='form-control' name='msg' required='1'></textarea>
			</td>
		</tr>
		<tr>
			<td colspan='2'>
				<input type='submit' class='btn btn-primary' value='Send Mass Mail'>
			</td>
		</tr>
		{$csrf}
		</form>
		</table>";
	}
}
function massemail()
{
	global $db,$userid,$h,$api,$set;
	$from=$set['sending_email'];
	echo "<h3>Mass Emailer</h3><hr>";
	if (isset($_POST['msg']))
	{
		$msg = $_POST['msg'];
		if (!isset($_POST['verf']) || !verify_csrf_code('staff_massemail', stripslashes($_POST['verf'])))
		{
			alert('danger',"Action Blocked!","We have blocked this action for your security. Please fill out the form quicker next time.");
			die($h->endpage());
		}
		if (empty($msg))
		{
			alert('danger',"Uh Oh!","Please specify a message to send.");
			die($h->endpage());
		}
		if (strlen($msg) > 65655)
		{
			alert('danger',"Uh Oh!","At maximum, messages can only be 65,655 characters in length.");
			die($h->endpage());
		}
		$q=$db->query("SELECT `userid`,`user_level`,`email` FROM `users` WHERE `email_optin` = 1");
		$sent=0;
		$headers[] = 'MIME-Version: 1.0';
		$headers[] = 'Content-type: text/html; charset=iso-8859-1';
		$headers[] = "From: {$from}";
		while ($r = $db->fetch_row($q))
		{
			echo "Sending Email to {$api->SystemUserIDtoName($r['userid'])} ...";
			if ($r['user_level'] == 'NPC')
			{
				echo "... Failed.";
			}
			else
			{
				if (mail($r['email'],$set['WebsiteName'],$msg,implode("\r\n", $headers)) == true)
				{
					echo "... Success.";
					$sent=$sent+1;
				}
				else
				{
					echo "... Failed.";
				}
			}
			echo "<br />";
		}
        alert('success',"Success!","You successfully sent a mass email to {$sent} players",true,'index.php');
		$api->SystemLogsAdd($userid,'staff',"Sent a mass email.");
	}
	else
	{
		$csrf=request_csrf_html('staff_massemail');
		echo "<table class='table table-bordered'>
		<form method='post'>
		<tr>
			<th colspan='2'>
				Send an email to the players who are have chosen to opt-in. Do not spam, or you may find your domain
				blocked on email providers. You can use HTML.
			</th>
		</tr>
		<tr>
			<th>
				Message
			</th>
			<td>
				<textarea class='form-control' name='msg' required='1'></textarea>
			</td>
		</tr>
		<tr>
			<td colspan='2'>
				<input type='submit' class='btn btn-primary' value='Send Mass Email'>
			</td>
		</tr>
		{$csrf}
		</form>
		</table>";
	}
}
function banip()
{
	global $db,$api,$h,$userid;
	echo "<h3>Ban IP</h3><hr />";
	if (isset($_POST['ip']))
	{
		$IP = $db->escape($_POST['ip']);
		if (!isset($_POST['verf']) || !verify_csrf_code('staff_banip', stripslashes($_POST['verf'])))
		{
			alert('danger',"Action Blocked!","We have blocked this action for your security. Please fill out the form quicker next time.");
			die($h->endpage());
		}
		if (!filter_var($IP, FILTER_VALIDATE_IP))
		{
			alert('danger',"Uh Oh!","You did not input a valid IP Address.");
			die($h->endpage());
		}
		$q=$db->query("SELECT `ip_id` FROM `ipban` WHERE `ip_ip` = '{$IP}'");
		if ($db->num_rows($q) > 0)
		{
			alert('danger',"Uh Oh!","The IP Address you input is already banned.");
			die($h->endpage());
		}
		$db->query("INSERT INTO `ipban` VALUES (NULL, '{$IP}');");
		alert('success',"Success!","You have successfully banned the {$IP} IP Address.",true,'index.php');
		$api->SystemLogsAdd($userid,'staff',"IP Banned {$IP}.");
	}
	else
	{
		$csrf=request_csrf_html('staff_banip');
		echo "<form method='post'>
		<table class='table table-bordered'>
		<tr>
			<th colspan='2'>
				Enter the IP Address you wish to ban.
			</th>
		</tr>
		<tr>
			<th>
				IP Address
			</th>
			<td>
				<input type='text' name='ip' value='...' class='form-control' required='1'>
			</td>
		</tr>
		<tr>
			<td colspan='2'>
				<input type='submit' value='Ban IP' class='btn btn-primary'>
			</td>
		</tr>
		{$csrf}
		</table>
		</form>";
	}
}
function unbanip()
{
	global $db,$userid,$api,$h;
	echo "<h3>Pardon IP Address</h3><hr />";
	if (isset($_GET['id']))
	{
		$_GET['id'] = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs(intval($_GET['id'])) : '';
		if (!isset($_GET['verf']) || !verify_csrf_code('staff_unbanip', stripslashes($_GET['verf'])))
		{
			alert('danger',"Action Blocked!","We have blocked this action for your security. Please fill out the form quicker next time.");
			die($h->endpage());
		}
		if (empty($_GET['id']))
		{
			alert('danger',"Uh Oh!","Please specify the IP Address you wish to pardon.",true,'?action=unbanip');
			die($h->endpage());
		}
		$q=$db->query("SELECT * FROM `ipban` WHERE `ip_id` = {$_GET['id']}");
		if ($db->num_rows($q) == 0)
		{
			alert('danger',"Uh Oh!","The IP Address you wish to unban is not banned.",true,'?action=unbanip');
			die($h->endpage());
		}
		$IP=$db->fetch_row($q);
		$api->SystemLogsAdd($userid,'staff',"Unbanned IP {$IP['ip_id']}");
		$db->query("DELETE FROM `ipban` WHERE `ip_id` = {$_GET['id']}");
		alert('success',"Success!","You have successfully unbanned the {$IP['ip_id']} IP Address.",true,'index.php');
	}
	else
	{
		echo "<table class='table table-bordered'>
		<tr>
			<th>
				IP Address
			</th>
			<th>
				Link
			</th>
		</tr>";
		$q=$db->query("SELECT * FROM `ipban`");
		$csrf=request_csrf_html('staff_unbanip');
		while ($r = $db->fetch_row($q))
		{
			echo "<tr>
				<td>
					{$r['ip_ip']}
				</td>
				<td>
					<form method='get'>
						<input type='hidden' value='unbanip' name='action'>
						<input type='hidden' value='{$r['ip_id']}' name='id'>
						<input type='submit' class='btn btn-primary' value='Unban IP Address'>
						{$csrf}
					</form>
				</td>
			</tr>";
		}
		echo "</table>";
	}
}
$h->endpage();