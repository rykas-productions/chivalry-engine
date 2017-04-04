<?php
/*
	File: staff/staff_estates.php
	Created: 4/4/2017 at 7:02PM Eastern Time
	Info: Staff panel for handling/editing/creating estates for players to buy.
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
case "addestate":
    addestate();
    break;
case "editestate":
    editestate();
    break;
case "delestate":
    delestate();
    break;
default:
    die();
    break;
}
function addestate()
{
	global $db,$userid,$h,$lang,$ir,$api;
	echo "<h3>{$lang['STAFF_ESTATE_ADD']}</h3><hr />";
	if (isset($_POST['name']))
	{
		$lvl = (isset($_POST['lvl']) && is_numeric($_POST['lvl'])) ? abs(intval($_POST['lvl'])) : 1;
		$name = (isset($_POST['name']) && is_string($_POST['name'])) ? $db->escape(htmlentities($_POST['name'])) : '';
		$will = (isset($_POST['will']) && is_numeric($_POST['will'])) ? abs(intval($_POST['will'])) : 100;
		$cost = (isset($_POST['cost']) && is_numeric($_POST['cost'])) ? abs($_POST['cost']) : 0;
		if (!isset($_POST['verf']) || !verify_csrf_code('staff_addestate', stripslashes($_POST['verf'])))
		{
			alert('danger',$lang["CSRF_ERROR_TITLE"],$lang["CSRF_ERROR_TEXT"]);
			die($h->endpage());
		}
		$q = $db->query("SELECT COUNT(`house_id`) FROM `estates` WHERE `house_name` = '{$name}'");
		if ($db->fetch_single($q) > 0)
        {
            $db->free_result($q);
			alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_ESTATE_ADD_ERROR1']);
            die($h->endpage());
        }
		$db->free_result($q);
		$q = $db->query("SELECT COUNT(`house_id`) FROM `estates` WHERE `house_will` = {$will}");
		if ($db->fetch_single($q) > 0)
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_ESTATE_ADD_ERROR2']);
            die($h->endpage());
		}
		if ($lvl < 1)
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_ESTATE_ADD_ERROR3']);
            die($h->endpage());
		}
		if ($will <= 99)
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_ESTATE_ADD_ERROR4']);
            die($h->endpage());
		}
		$api->SystemLogsAdd($userid,'staff',"Created an estate named {$name}.");
		alert('success',$lang['ERROR_SUCCESS'],$lang['STAFF_ESTATE_ADD_SUCCESS'],true,'index.php');
		$db->query("INSERT INTO `estates` (`house_name`, `house_price`, `house_will`, `house_level`) VALUES ('{$name}', '{$cost}', '{$will}', '{$lvl}')");
	}
	else
	{
		$csrf = request_csrf_html('staff_addestate');
		echo "<form action='?action=addestate' method='post'>
		<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					{$lang['STAFF_ESTATE_ADD_TABLE']}
				</th>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_ESTATE_ADD_TH1']}
				</th>
				<td>
					<input type='text' name='name' required='1' class='form-control'>
				</td>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_ESTATE_ADD_TH2']}
				</th>
				<td>
					<input type='number' name='cost' min='0' required='1' class='form-control'>
				</td>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_ESTATE_ADD_TH3']}
				</th>
				<td>
					<input type='number' name='lvl' min='0' required='1' class='form-control'>
				</td>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_ESTATE_ADD_TH4']}
				</th>
				<td>
					<input type='number' name='will' min='101' required='1' class='form-control'>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' class='btn btn-default' value='{$lang['STAFF_ESTATE_ADD_BTN']}'>
				</td>
			</tr>
			{$csrf}
		</table>
		</form>";
	}
}
function delestate()
{
	global $db,$userid,$lang,$api,$h;
	if (isset($_POST['estate']))
	{
		$_POST['estate'] = (isset($_POST['estate']) && is_numeric($_POST['estate'])) ? abs(intval($_POST['estate'])) : '';
		if (!isset($_POST['verf']) || !verify_csrf_code('staff_delestate', stripslashes($_POST['verf'])))
		{
			alert('danger',$lang["CSRF_ERROR_TITLE"],$lang["CSRF_ERROR_TEXT"]);
			die($h->endpage());
		}
		$q = $db->query("SELECT * FROM `estates` WHERE `house_id` = {$_POST['estate']}");
		if ($db->num_rows($q) == 0)
		{
			$db->free_result($q);
			alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_ESTATE_DEL_ERR']);
			die($h->endpage());
		}
		$old = $db->fetch_row($q);
        $db->free_result($q);
		if ($old['house_will'] == 100)
        {
            alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_ESTATE_DEL_ERR1']);
            die($h->endpage());
        }
		$db->query("UPDATE `users`  SET `primary_currency` = `primary_currency` + {$old['house_price']},
                 `maxwill` = 100, `will` = LEAST(100, `will`) WHERE `maxwill` = {$old['house_will']}");
		$db->query("DELETE FROM `estates` WHERE `house_id` = {$old['house_id']}");
		alert('success',$lang['ERROR_SUCCESS'],$lang['STAFF_ESTATE_DEL_SUCC'],true,'index.php');
		$api->SystemLogsAdd($userid,'staff',"Deleted the {$old['house_name']} estate.");
	}
	else
	{
		$csrf = request_csrf_html('staff_delestate');
		echo "<form method='post'>
			<table class='table table-bordered'>
				<tr>
					<th colspan='2'>
						{$lang['STAFF_ESTATE_DEL_INFO']}
					</th>
				</tr>
				<tr>
					<th>
						{$lang['STAFF_ESTATE_ADD_TH1']}
					</th>
					<td>
						" . estate_dropdown() . "
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' class='btn btn-default' value='{$lang['STAFF_ESTATE_DEL_BTN']}'>
					</td>
				</tr>
			</table>
			{$csrf}
		</form>";
	}
}
function editestate()
{
	global $db,$userid,$h,$lang,$api;
	if (!isset($_POST['step']))
    {
        $_POST['step'] = '0';
    }
	if ($_POST['step'] == 2)
	{
		$lvl = (isset($_POST['lvl']) && is_numeric($_POST['lvl'])) ? abs(intval($_POST['lvl'])) : 1;
		$name = (isset($_POST['name']) && is_string($_POST['name'])) ? $db->escape(htmlentities($_POST['name'])) : '';
		$will = (isset($_POST['will']) && is_numeric($_POST['will'])) ? abs(intval($_POST['will'])) : 100;
		$cost = (isset($_POST['cost']) && is_numeric($_POST['cost'])) ? abs($_POST['cost']) : 0;
        $_POST['id'] = (isset($_POST['id']) && is_numeric($_POST['id'])) ? abs(intval($_POST['id'])) : 0;
		if (!isset($_POST['verf']) || !verify_csrf_code('staff_editestate2', stripslashes($_POST['verf'])))
		{
			alert('danger',$lang["CSRF_ERROR_TITLE"],$lang["CSRF_ERROR_TEXT"]);
			die($h->endpage());
		}
		if (empty($_POST['id']) || empty($_POST['lvl']) || empty($_POST['name'])
			|| empty($_POST['will']) || empty($_POST['cost']))
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_ESTATE_EDIT_ERR1']);
            die($h->endpage());
		}
		$q = $db->query("SELECT `house_id` FROM `estates` WHERE `house_will` = {$will} AND `house_id` != {$_POST['id']}");
        if ($db->num_rows($q))
        {
            alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_ESTATE_ADD_ERROR2']);
            die($h->endpage());
        }
		$q = $db->query("SELECT `house_will` FROM `estates` WHERE `house_id` = {$_POST['id']}");
        if ($db->num_rows($q) == 0)
        {
            $db->free_result($q);
            alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_ESTATE_EDIT_ERR']);
            die($h->endpage());
        }
		$oldwill = $db->fetch_single($q);
		if ($oldwill == 100 && $will > 100)
        {
            alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_ESTATE_EDIT_ERR3']);
            die($h->endpage());
        }
		$db->query("UPDATE `estates` SET `house_will` = {$will}, `house_price` = {$cost}, 
					`house_name` = '{$name}', `house_level` = {$lvl} WHERE `house_id` = {$_POST['id']}");
		$db->query("UPDATE `users` SET `maxwill` = {$will}, `will` = LEAST(`will`, {$will})
					WHERE `maxwill` = {$oldwill}");
		alert('success',$lang['ERROR_SUCCESS'],$lang['STAFF_ESTATE_EDIT_SUCC'],true,'index.php');
		$api->SystemLogsAdd($userid,'staff',"Edited the {$name} estate.");
		die($h->endpage());
	}
	if ($_POST['step'] == 1)
	{
		$_POST['estate'] = (isset($_POST['estate']) && is_numeric($_POST['estate'])) ? abs(intval($_POST['estate'])) : 0;
		if (!isset($_POST['verf']) || !verify_csrf_code('staff_editestate1', stripslashes($_POST['verf'])))
		{
			alert('danger',$lang["CSRF_ERROR_TITLE"],$lang["CSRF_ERROR_TEXT"]);
			die($h->endpage());
		}
		$q = $db->query("SELECT * FROM `estates` WHERE `house_id` = {$_POST['estate']}");
        if ($db->num_rows($q) == 0)
        {
            $db->free_result($q);
            alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_ESTATE_EDIT_ERR']);
            die($h->endpage());
        }
		$old = $db->fetch_row($q);
        $db->free_result($q);
        $csrf = request_csrf_html('staff_editestate2');
		echo "<form method='post'>
		<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					{$lang['STAFF_ESTATE_EDIT_TABLE']}
				</th>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_ESTATE_ADD_TH1']}
				</th>
				<td>
					<input type='text' name='name' required='1' class='form-control' value='{$old['house_name']}'>
				</td>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_ESTATE_ADD_TH2']}
				</th>
				<td>
					<input type='number' name='cost' min='1' required='1' class='form-control' value='{$old['house_price']}'>
				</td>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_ESTATE_ADD_TH3']}
				</th>
				<td>
					<input type='number' name='lvl' min='1' required='1' class='form-control' value='{$old['house_level']}'>
				</td>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_ESTATE_ADD_TH4']}
				</th>
				<td>
					<input type='number' name='will' min='100' required='1' class='form-control' value='{$old['house_will']}'>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' class='btn btn-default' value='{$lang['STAFF_ESTATE_EDIT_BTN']}'>
				</td>
			</tr>
			{$csrf}
			<input type='hidden' name='step' value='2' />
        	<input type='hidden' name='id' value='{$_POST['estate']}' />
		</table>
		</form>";
	}
	if ($_POST['step'] == 0)
	{
		$csrf = request_csrf_html('staff_editestate1');
		echo "<form method='post'>
			<input type='hidden' name='step' value='1' />
			<table class='table table-bordered'>
				<tr>
					<th colspan='2'>
						{$lang['STAFF_ESTATE_EDIT_INFO']}
					</th>
				</tr>
				<tr>
					<th>
						{$lang['STAFF_ESTATE_ADD_TH1']}
					</th>
					<td>
						" . estate_dropdown() . "
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' class='btn btn-default' value='{$lang['STAFF_ESTATE_EDIT_BTN']}'>
					</td>
				</tr>
			</table>
			{$csrf}
		</form>";
	}
}
$h->endpage();
