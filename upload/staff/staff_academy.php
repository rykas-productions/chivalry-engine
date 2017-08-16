<?php
/*
	File: staff/staff_academy.php
	Created: 6/1/2016 at 6:06PM Eastern Time
	Info: Allows admins to add/edit/delete academy courses.
	Author: ImJustIsabella
	Website: https://github.com/MasterGeneral156/chivalry-engine
*/
require('sglobals.php');
//Not bothering with the academy until I can redo it. Sorry Izzy!
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
switch ($_GET['action'])
{
case "add":
    addacademy();
    break;
case "del":
    delacademy();
    break;
default:
    echo "404";
	die($h->endpage());
    break;
}
function addacademy()
{
	global $lang,$h,$db,$userid,$api, $ir;
	if ($ir['user_level'] != "Admin")
    {
		alert('danger',$lang['ERROR_NOPERM'],$lang['STAFF_NOPERM'],true,'index.php');
		die($h->endpage());
    }
	if (!isset($_POST['name']))
	{
		$csrf = request_csrf_html('staff_newacademy');
		echo "<form method='post'>
		<table class='table table-bordered'>
		<tr>
			<tr>
				<th colspan='2'>
					{$lang['STAFF_ACADEMY_ADD_TH']}
				</th>
			</tr>
			<th>
					{$lang['STAFF_ACADEMY_NAME']}
				</th>
				<td>
					<input type='text' required='1' name='name' class='form-control'>
				</td>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_ACADEMY_DESC']}
				</th>
				<td>
					<input type='text' required='1' name='desc' class='form-control'>
				</td>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_ACADEMY_COST']}
				</th>
				<td>
					<input type='number' required='1' min='1' name='cost' class='form-control'>
				</td>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_ACADEMY_LVL']}
				</th>
				<td>
					<input type='number' required='1' min='0' value='0' name='lvl' class='form-control'>
				</td>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_ACADEMY_DAYS']}
				</th>
				<td>
					<input type='number' required='1' name='day' min='1' class='form-control'>
				</td>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_ACADEMY_OPTION_1']}
				</th>
				<td>
					<input type='number' required='1' name='str' min='0' value='0' class='form-control'>
				</td>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_ACADEMY_OPTION_2']}
				</th>
				<td>
					<input type='number' required='1' name='agl' min='0' value='0' class='form-control'>
				</td>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_ACADEMY_OPTION_3']}
				</th>
				<td>
					<input type='number' required='1' name='grd' min='0' value='0' class='form-control'>
				</td>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_ACADEMY_OPTION_4']}
				</th>
				<td>
					<input type='number' required='1' name='lab' min='0' value='0' class='form-control'>
				</td>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_ACADEMY_OPTION_5']}
				</th>
				<td>
					<input type='number' required='1' name='iq' min='0' value='0' class='form-control'>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' value='{$lang['STAFF_ACADEMY_CREATE']}' class='btn btn-primary'>
				</td>
			</tr>
			</table>
		{$csrf}
		</form>";
	}
	else
	{
		if (!isset($_POST['verf']) || !verify_csrf_code('staff_newacademy', stripslashes($_POST['verf'])))
		{
			alert('danger',$lang["CSRF_ERROR_TITLE"],$lang["CSRF_ERROR_TEXT"]);
			die($h->endpage());
		}
		$name = (isset($_POST['name']) && is_string($_POST['name'])) ? stripslashes($_POST['name']) : '';
		$desc = (isset($_POST['desc']) && is_string($_POST['desc'])) ? stripslashes($_POST['desc']) : '';
		$cost = (isset($_POST['cost']) && is_numeric($_POST['cost'])) ? abs(intval($_POST['cost'])) : '';
		$lvl = (isset($_POST['lvl']) && is_numeric($_POST['lvl'])) ? abs(intval($_POST['lvl'])) : 0;
		$days = (isset($_POST['day']) && is_numeric($_POST['day'])) ? abs(intval($_POST['day'])) : '';
		$str = (isset($_POST['str']) && is_numeric($_POST['str'])) ? abs(intval($_POST['str'])) : 0;
		$agl = (isset($_POST['agl']) && is_numeric($_POST['agl'])) ? abs(intval($_POST['agl'])) : 0;
		$grd = (isset($_POST['grd']) && is_numeric($_POST['grd'])) ? abs(intval($_POST['grd'])) : 0;
		$lab = (isset($_POST['lab']) && is_numeric($_POST['lab'])) ? abs(intval($_POST['lab'])) : 0;
		$iq = (isset($_POST['iq']) && is_numeric($_POST['iq'])) ? abs(intval($_POST['iq'])) : 0;
		if (empty($name) || empty($desc) || empty($cost) || !isset($lvl) || empty($days))
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_ACADEMY_ADD_ERR']);
			die($h->endpage());
		}
		if (empty($str) && empty($agl) && empty($grd) && empty($lab) && empty($iq))
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_ACADEMY_ADD_ERR1']);
			die($h->endpage());
		}
		$inq=$db->query("SELECT `ac_id` FROM `academy` WHERE `ac_name` = '{$name}'");
		if ($db->num_rows($inq) > 0)
		{
			$db->free_result($inq);
			alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_ACADEMY_ADD_ERR2']);
			die($h->endpage());
		}
		$db->query("INSERT INTO `academy` VALUES (NULL, '{$name}', '{$desc}', '{$cost}', '{$lvl}', '{$days}', '{$str}', '{$agl}', '{$grd}', '{$lab}', '{$iq}')");
		$api->SystemLogsAdd($userid,'staff',"Created academy course {$name}.");
		alert('success',$lang['ERROR_SUCCESS'],$lang['STAFF_ACADEMY_ADD_SUCC'],true,'index.php');
	}
}
function delacademy()
{
	global $db,$ir,$h,$lang,$userid,$api;
	if ($ir['user_level'] != 'Admin')
    {
        alert('danger',$lang['ERROR_NOPERM'],$lang['STAFF_NOPERM'],true,'index.php');
        die($h->endpage());
    }
	if (!isset($_POST['academy']))
	{
		$csrf = request_csrf_html('staff_delacademy');
		echo "<h4>{$lang['STAFF_ACADEMY_DELETE_HEADER']}</h4>
			{$lang['STAFF_ACADEMY_DELETE_NOTICE']}
			<form method='post'>
				<table class='table table-bordered'>
					<tr>
						<th width='33%'>
							{$lang['STAFF_ACADEMY_DELETE_TITLE']}
						</th>
						<td>
							" . academy_dropdown('academy') . "
						</td>
					</tr>
					<tr>
						<td colspan='2'>
							<input type='submit' class='btn btn-primary' value='{$lang['STAFF_ACADEMY_DELETE_BUTTON']}'>
						</td>
					</tr>
				</table>
				{$csrf}
			</form>";
	}
	else
	{
		if (!isset($_POST['verf']) || !verify_csrf_code('staff_delacademy', stripslashes($_POST['verf'])))
		{
			alert('danger',"{$lang["CSRF_ERROR_TITLE"]}","{$lang["CSRF_ERROR_TEXT"]}");
			die($h->endpage());
		}
		$_POST['academy'] =(isset($_POST['academy']) && is_numeric($_POST['academy'])) ? abs(intval($_POST['academy'])) : '';
		if (empty($_POST['academy']))
		{
			alert('warning',$lang['ERROR_GENERIC'],$lang['STAFF_ACADEMY_DEL_ERR']);
			die($h->endpage());
		}
		$d =
			$db->query(
					"SELECT `ac_name`
					 FROM `academy`
					 WHERE `ac_id` = {$_POST['academy']}");
		if ($db->num_rows($d) == 0)
		{
			$db->free_result($d);
			alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_ACADEMY_DEL_ERR1']);
			die($h->endpage());
		}
		$academyname = $db->fetch_single($d);
		$db->free_result($d);
		$db->query("DELETE FROM `academy` WHERE `ac_id` = {$_POST['academy']}");
		$api->SystemLogsAdd($userid,'staff',"Deleted academy {$academyname}.");
		alert("success",$lang['ERROR_SUCCESS'],"{$lang['STAFF_ACADEMY_DEL_SUCC']} {$academyname} {$lang['STAFF_ACADEMY_DEL_SUCC1']}",true,'index.php');
		die($h->endpage());
	}
}
$h->endpage();