<?php
require('sglobals.php');
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
switch ($_GET['action'])
{
case "addtown":
    addtown();
    break;
case "edittown":
    edittown();
    break;
case "deltown":
    deltown();
    break;
default:
    die();
    break;
}
function addtown()
{
	global $db,$userid,$h,$lang,$ir,$api;
	echo "<h3>{$lang['STAFF_TRAVEL_ADD']}</h3><hr />";
	if (isset($_POST['name']))
	{
		$level = (isset($_POST['minlevel']) && is_numeric($_POST['minlevel'])) ? abs(intval($_POST['minlevel'])) : 1;
		$name = (isset($_POST['name']) && is_string($_POST['name'])) ? $db->escape(stripslashes($_POST['name'])) : '';
		$tax = (isset($_POST['tax']) && is_numeric($_POST['tax'])) ? abs(intval($_POST['tax'])) : 0;
		if (!isset($_POST['verf']) || !verify_csrf_code('staff_addtown', stripslashes($_POST['verf'])))
		{
			alert('danger',"{$lang["CSRF_ERROR_TITLE"]}","{$lang["CSRF_ERROR_TEXT"]}");
			die($h->endpage());
		}
		$q = $db->query("SELECT COUNT(`town_id`) FROM `town` WHERE `town_name` = '{$name}'");
		if ($db->fetch_single($q) > 0)
        {
            $db->free_result($q);
			alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['STAFF_TRAVEL_ADDTOWN_SUB_ERROR1']}");
            die($h->endpage());
        }
		if ($tax < 0 || $tax > 20)
		{
			alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['STAFF_TRAVEL_ADDTOWN_SUB_ERROR2']}");
            die($h->endpage());
		}
		if ($level < 0)
		{
			alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['STAFF_TRAVEL_ADDTOWN_SUB_ERROR3']}");
            die($h->endpage());
		}
		$db->free_result($q);
		$db->query("INSERT INTO `town` (`town_name`, `town_min_level`, `town_guild_owner`, `town_tax`) VALUES ('{$name}', '{$level}', '0', '{$tax}');");
		$api->SystemLogsAdd($userid,'staff',"Created a town named {$name}.");
		alert('success',"{$lang['ERROR_SUCCESS']}","{$lang['STAFF_TRAVEL_ADDTOWN_SUB_SUCCESS']}");
	}
	else
	{
		$csrf = request_csrf_html('staff_addtown');
		echo "<form action='?action=addtown' method='post'>
		<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					{$lang['STAFF_TRAVEL_ADDTOWN_TABLE']}
				</th>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_TRAVEL_ADDTOWN_TH1']}
				</th>
				<td>
					<input type='text' name='name' required='1' class='form-control'>
				</td>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_TRAVEL_ADDTOWN_TH2']}
				</th>
				<td>
					<input type='number' name='level' min='0' required='1' class='form-control'>
				</td>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_TRAVEL_ADDTOWN_TH3']}
				</th>
				<td>
					<input type='number' name='tax' min='0' max='20' required='1' class='form-control'>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' class='btn btn-default' value='{$lang['STAFF_TRAVEL_ADDTOWN_BTN']}'>
				</td>
			</tr>
			{$csrf}
		</table>
		</form>";
	}
}
function deltown()
{
	global $db,$userid,$api,$lang,$h;
	echo "<h3>{$lang['STAFF_TRAVEL_DEL']}</h3><hr />";
	if (isset($_POST['town']))
	{
		$town = (isset($_POST['town']) && is_numeric($_POST['town'])) ? abs(intval($_POST['town'])) : 0;
		$q = $db->query("SELECT `town_id`, `town_name` FROM `town` WHERE `town_id` = {$town}");
		if (!isset($_POST['verf']) || !verify_csrf_code('staff_deltown', stripslashes($_POST['verf'])))
		{
			alert('danger',"{$lang["CSRF_ERROR_TITLE"]}","{$lang["CSRF_ERROR_TEXT"]}");
			die($h->endpage());
		}
		if ($db->num_rows($q) == 0)
        {
            $db->free_result($q);
            alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['STAFF_TRAVEL_DELTOWN_SUB_ERROR1']}");
            die($h->endpage());
        }
		$old = $db->fetch_row($q);
        $db->free_result($q);
        if ($old['town_id'] == 1)
        {
            alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['STAFF_TRAVEL_DELTOWN_SUB_ERROR2']}");
            die($h->endpage());
        }
		$db->query("UPDATE `users` SET `location` = 1 WHERE `location` = {$old['town_id']}");
        $db->query("UPDATE `shops` SET `shopLOCATION` = 1 WHERE `shopLOCATION` = {$old['town_id']}");
        $db->query("DELETE FROM `town` WHERE `town_id` = {$old['town_id']}");
		alert('success',"{$lang['ERROR_SUCCESS']}","{$lang['STAFF_TRAVEL_DELTOWN_SUB_SUCCESS']}");
		$api->SystemLogsAdd($userid,'staff',"Deleted the town called {$old['town_name']}.");
	}
	else
	{
		$csrf = request_csrf_html('staff_deltown');
		echo "
		<form action='?action=deltown' method='post'>
			<table class='table table-bordered'>
				<tr>
					<th colspan='2'>
						{$lang['STAFF_TRAVEL_DELTOWN_TABLE']}
					</th>
				</tr>
				<tr>
					<th>
						{$lang['STAFF_TRAVEL_DELTOWN_TH1']}
					</th>
					<td>
						" . location_dropdown("town") . "
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' class='btn btn-default' value='{$lang['STAFF_TRAVEL_DELTOWN_BTN']}'>
					</td>
				</tr>
				{$csrf}
			</table>
		</form>";
	}
}
$h->endpage();