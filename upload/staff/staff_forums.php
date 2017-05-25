<?php
/*
	File: staff/staff_forums.php
	Created: 4/4/2017 at 7:02PM Eastern Time
	Info: Staff panel for handling/editing/creating the in-game forums.
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine/
*/
require('sglobals.php');
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
switch ($_GET['action'])
{
case "addforum":
    addforum();
    break;
case "editforum":
    editforum();
    break;
case "delforum":
    delforum();
    break;
default:
    die();
    break;
}
function addforum()
{
	global $lang,$h,$db,$userid,$api;
	if (!isset($_POST['name']))
	{
		$csrf = request_csrf_html('staff_addforum');
        echo "
        <h3>{$lang['STAFF_FORUM_ADD']}</h3>
        <hr />
		<form method='post'>
			<table class='table table-bordered table-responsive'>
				<tr>
					<th width='33%'>
						{$lang['STAFF_FORUM_ADD_NAME']}
					</th>
					<td>
						<input type='text' required='1' name='name' class='form-control'>
					</td>
				</tr>
				<tr>
					<th>
						{$lang['STAFF_FORUM_ADD_DESC']}
					</th>
					<td>
						<input type='text' required='1' name='desc' class='form-control'>
					</td>
				</tr>
				<tr>
					<th>
						{$lang['STAFF_FORUM_ADD_AUTHORIZE']}
					</th>
					<td>
						 <select name='auth' required='1' class='form-control' type='dropdown'>
							<option value='public'>{$lang['STAFF_FORUM_ADD_AUTHORIZEP']}</option>
							<option value='staff'>{$lang['STAFF_FORUM_ADD_AUTHORIZES']}</option>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' value='{$lang['STAFF_FORUM_ADD_BTN']}' class='btn btn-primary'>
					</td>
				</tr>
				{$csrf}
			</table>
        </form>";
	}
	else
	{
		$name = (isset($_POST['name']) && preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i", $_POST['name'])) ? $db->escape(strip_tags(stripslashes($_POST['name']))) : '';
		$desc = (isset($_POST['desc'])) ? $db->escape(strip_tags(stripslashes($_POST['desc']))) : '';
		$auth = (isset($_POST['auth']) && in_array($_POST['auth'], array('staff', 'public'), true)) ? $_POST['auth'] : 'public';
		if (!isset($_POST['verf']) || !verify_csrf_code('staff_addforum', stripslashes($_POST['verf'])))
		{
			alert('danger',$lang["CSRF_ERROR_TITLE"],$lang["CSRF_ERROR_TEXT"]);
			die($h->endpage());
		}
		if (empty($name))
		{
			alert('danger',$lang['ERROR_INVALID'],$lang['STAFF_FORUM_ADD_ERRNAME']);
			die($h->endpage());
		}
		elseif (empty($name))
		{
			alert('danger',$lang['ERROR_INVALID'],$lang['STAFF_FORUM_ADD_ERRDESC']);
			die($h->endpage());
		}
		else
		{
			$q = $db->query("SELECT COUNT(`ff_id`) FROM `forum_forums` WHERE `ff_name` = '{$name}'");
			if ($db->fetch_single($q))
			{
				$db->free_result($q);
				alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['STAFF_FORUM_ADD_ERRNIU']}");
				die($h->endpage());
			}
			$db->free_result($q);
			$db->query("INSERT INTO `forum_forums` (`ff_name`, `ff_desc`, `ff_lp_t_id`, `ff_lp_poster_id`, `ff_auth`, `ff_lp_time`) 
			VALUES ('{$name}', '{$desc}', '0', '0', '{$auth}', '0');");
			alert('success',$lang['ERROR_SUCCESS'],$lang['STAFF_FORUM_ADD_SUCCESS'],true,'index.php');
			$api->SystemLogsAdd($userid,'staff',"Created a {$auth} Forum called {$name}.");
			$h->endpage();
		}
	}
}
function editforum()
{
	global $db, $h, $lang, $userid, $api;
				echo "<h3>{$lang['STAFF_FORUM_EDIT_BTN']}</h3><hr />";
    if (!isset($_POST['step']))
    {
        $_POST['step'] = '0';
    }
    switch ($_POST['step'])
    {
		case "2":
			$name = (isset($_POST['name'])  && preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i",  $_POST['name'])) ? $db->escape(strip_tags(stripslashes($_POST['name']))) : '';
			$desc = (isset($_POST['desc'])) ? $db->escape(strip_tags(stripslashes($_POST['desc'])))  : '';
			$auth = (isset($_POST['auth']) && in_array($_POST['auth'], array('staff', 'public'))) ? $_POST['auth'] : 'public';
			$_POST['id'] = (isset($_POST['id']) && is_numeric($_POST['id'])) ? abs(intval($_POST['id'])) : '';
			if (empty($_POST['id']) || empty($name) || empty($desc))
			{
				alert('danger',$lang['ERROR_EMPTY'],$lang['STAFF_FORUM_EDIT_ERREMPTY']);
				die($h->endpage());
			}
			if (!isset($_POST['verf']) || !verify_csrf_code('staff_editforum2', stripslashes($_POST['verf'])))
			{
				alert('danger',$lang["CSRF_ERROR_TITLE"],$lang["CSRF_ERROR_TEXT"]);
				die($h->endpage());
			}
			$q = $db->query("SELECT COUNT(`ff_id`) FROM `forum_forums` WHERE `ff_name` = '{$name}' AND `ff_id` != {$_POST['id']}");
			if ($db->fetch_single($q) > 0)
			{
				$db->free_result($q);
				alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_FORUM_ADD_ERRNIU']);
				die($h->endpage());
			}
			$db->free_result($q);
			$q = $db->query("SELECT COUNT(`ff_id`)  FROM `forum_forums` WHERE `ff_id` = {$_POST['id']}");
			if ($db->fetch_single($q) == 0)
			{
				$db->free_result($q);
				alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_FORUM_EDIT_ERRINV']);
				die($h->endpage());
			}
			$db->free_result($q);
			$db->query("UPDATE `forum_forums` SET `ff_desc` = '$desc', `ff_name` = '$name', `ff_auth` = '$auth' WHERE `ff_id` = {$_POST['id']}");
			alert('success',$lang['ERROR_SUCCESS'],$lang['STAFF_FORUM_EDIT_SUCCESS'],true,'index.php');
			$api->SystemLogsAdd($userid,'staff',"Edited forum {$name}");
			break;
		case "1":
			$_POST['id'] = (isset($_POST['id']) && is_numeric($_POST['id'])) ? abs(intval($_POST['id'])) : '';
			if (empty($_POST['id']))
			{
				alert('danger',$lang['ERROR_INVALID'],$lang['STAFF_FORUM_EDIT_ERRINV']);
				die($h->endpage());
			}
			if (!isset($_POST['verf']) || !verify_csrf_code('staff_editforum1', stripslashes($_POST['verf'])))
			{
				alert('danger',$lang["CSRF_ERROR_TITLE"],$lang["CSRF_ERROR_TEXT"]);
				die($h->endpage());
			}
			$q = $db->query("SELECT `ff_auth`, `ff_name`, `ff_desc`  FROM `forum_forums` WHERE `ff_id` = {$_POST['id']}");
			if ($db->num_rows($q) == 0)
			{
				$db->free_result($q);
				alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_FORUM_ADD_ERRNIU']);
				die($h->endpage());
			}
			$old = $db->fetch_row($q);
			$db->free_result($q);
			$check_p = ($old['ff_auth'] == 'public') ? 'selected' : '';
			$check_s = ($old['ff_auth'] == 'staff') ? 'selected' : '';
			$csrf = request_csrf_html('staff_editforum2');
			echo "
			<form method='post'>
							<input type='hidden' name='step' value='2'>
							<input type='hidden' name='id' value='{$_POST['id']}'>
				<table class='table table-bordered table-responsive'>
					<tr>
						<th width='33%'>
							{$lang['STAFF_FORUM_ADD_NAME']}
						</th>
						<td>
							<input type='text' name='name' class='form-control' value='{$old['ff_name']}'>
						</td>
					</tr>
					<tr>
						<th>
							{$lang['STAFF_FORUM_ADD_DESC']}
						</th>
						<td>
							<input type='text' name='desc' class='form-control' value='{$old['ff_desc']}'>
						</td>
					</tr>
					<tr>
						<th>
							{$lang['STAFF_FORUM_ADD_AUTHORIZE']}
						</th>
						<td>
							<select name='auth' required='1' class='form-control' type='dropdown'>
								<option value='public' {$check_p}>{$lang['STAFF_FORUM_ADD_AUTHORIZEP']}</option>
								<option value='staff' {$check_s}>{$lang['STAFF_FORUM_ADD_AUTHORIZES']}</option>
							</select>
						</td>
					</tr>
					<tr>
						<td colspan='2'>
							<input type='submit' class='btn btn-primary' value='{$lang['STAFF_FORUM_EDIT_BTN']}'>
						</td>
					</tr>
				{$csrf}
				</table>
			</form>";
			break;
		default:
			$csrf = request_csrf_html('staff_editforum1');
			echo "
			<form method='post'>
				<input type='hidden' name='step' value='1' />
				<b>{$lang['STAFF_FORUM_ADD_NAME']}</b> " . forum2_dropdown("id") . "<br />
				{$csrf}
				<input type='submit' class='btn btn-primary' value='Edit Forum' />
			</form>
			   ";
			break;
    }
	$h->endpage();
}
function delforum()
{
	global $db, $h, $lang, $userid, $api;
	echo "<h3>{$lang['STAFF_FORUM_DEL_BTN']}</h3><hr />";
	if (!isset($_POST['forum']))
	{
		$csrf = request_csrf_html('staff_delforum');
		echo "
		{$lang['STAFF_FORUM_DEL_INFO']}<br />
		<form method='post'>
        	<b>{$lang['STAFF_FORUM_ADD_NAME']}</b> " . forum2_dropdown("forum") . "
        <br />
        	{$csrf}
        	<input type='submit' class='btn btn-primary' value='{$lang['STAFF_FORUM_DEL_BTN']}' />
        </form>";
	}
	else
	{
		$_POST['forum'] = (isset($_POST['forum']) && is_numeric($_POST['forum'])) ? abs(intval($_POST['forum'])) : '';
		if (!isset($_POST['verf']) || !verify_csrf_code('staff_delforum', stripslashes($_POST['verf'])))
		{
			alert('danger',$lang["CSRF_ERROR_TITLE"],$lang["CSRF_ERROR_TEXT"]);
			die($h->endpage());
		}
		$q = $db->query("SELECT COUNT(`ff_id`) FROM `forum_forums` WHERE `ff_id` = {$_POST['forum']}");
		if ($db->fetch_single($q) == 0)
        {
            $db->free_result($q);
            alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_FORUM_EDIT_ERRFDNE']);
            die($h->endpage());
        }
		$db->free_result($q);
		$db->query("DELETE FROM `forum_posts` WHERE `ff_id` = {$_POST['forum']}");
		$db->query("DELETE FROM `forum_topics` WHERE `ft_forum_id` = {$_POST['forum']}");
		$db->query("DELETE FROM `forum_forums` WHERE `ff_id` = {$_POST['forum']}");
		$q = $db->query("SELECT `ff_name` FROM `forum_forums`  WHERE `ff_id` = {$_POST['forum']}");
        $old = $db->fetch_single($q);
        $db->free_result($q);
		alert('success',$lang['ERROR_SUCCESS'],$lang['STAFF_FORUM_DEL_SUCCESS'],true,'index.php');
		$api->SystemLogsAdd($userid,'staff',"Deleted forum {$old}, along with posts and topics posted inside.");
	}
	$h->endpage();
}