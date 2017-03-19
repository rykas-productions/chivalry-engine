<?php
require('sglobals.php');
//Not bothering with the academy until I can redo it. Sorry Izzy!
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
switch ($_GET['action'])
{
case "addacademy":
    addacademy();
    break;
case "delacademy":
    delacademy();
    break;
default:
    die();
    break;
}
function addacademy()
{
	global $lang,$h,$db,$userid,$api, $ir;
	if ($ir['user_level'] != "Admin")
    {
		alert('danger','No Permission!','You have no permission to be here. If this is false, please contact an admin for help!');
		die($h->endpage());
    }
	if (!isset($_POST['academyname']))
	{
	$csrf = request_csrf_html('staff_newitem');
		echo "<form method='post'>
		<table class='table table-bordered'>
		<tr>
			<th width='33%'>
					{$lang['STAFF_ACADEMY_NAME']}
				</th>
				<td>
					<input type='text' required='1' name='academyname' class='form-control'>
				</td>
			</tr>
			<tr>
				<th width='33%'>
					{$lang['STAFF_ACADEMY_DESC']}
				</th>
				<td>
					<input type='text' required='1' name='academydesc' class='form-control'>
				</td>
			</tr>
			<tr>
				<th width='33%'>
					{$lang['STAFF_ACADEMY_COST']}
				</th>
				<td>
					<input type='number' required='1' name='academycost' class='form-control'>
				</td>
			</tr>
			<tr>
				<th width='33%'>
					{$lang['STAFF_ACADEMY_LVL']}
				</th>
				<td>
					<input type='number' required='1' name='academylvl' class='form-control'>
				</td>
			</tr>
			<tr>
				<th width='33%'>
					{$lang['STAFF_ACADEMY_DAYS']}
				</th>
				<td>
					<input type='number' required='1' name='academyday' class='form-control'>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<h4>{$lang['STAFF_ACADEMY_PERKS']}</h4>
				</td>
			</tr>";
			for ($i = 1; $i <= 4; $i++)
			{
				echo "
				<tr>
					<th>
						<b><u>{$lang['STAFF_ACADEMY_PERK']}: {$i}</u></b>
					</th>
					<td>
					<b>{$lang['STAFF_ACADEMY_TOGGLE_DISP']}?</b>
						<input type='radio' class='form-control' name='effect{$i}on' value='true' /> {$lang['STAFF_ACADEMY_TOGGLE_ON']}
						<input type='radio' class='form-control' name='effect{$i}on' value='false' checked='checked' /> {$lang['STAFF_ACADEMY_TOGGLE_OFF']}
					<br />
					<b>{$lang['STAFF_ACADEMY_STAT']}:</b> <select name='effect{$i}stat' type='dropdown' class='form-control'>
						<option value='strength'>{$lang['STAFF_ACADEMY_OPTION_1']}</option>
						<option value='agility'>{$lang['STAFF_ACADEMY_OPTION_2']}</option>
						<option value='guard'>{$lang['STAFF_ACADEMY_OPTION_3']}</option>
						<option value='labor'>{$lang['STAFF_ACADEMY_OPTION_4']}</option>
						<option value='IQ'>{$lang['STAFF_ACADEMY_OPTION_5']}</option>
					</select>
					<br />
					<b>{$lang['STAFF_ACADEMY_DIRECTION']}:</b> <select name='effect{$i}dir' class='form-control' type='dropdown'>
						<option value='pos'>{$lang['STAFF_ACADEMY_INCREASE']}</option>
						<option value='neg'>{$lang['STAFF_ACADEMY_DECREASE']}</option>
					</select>
					<br />
					<b>{$lang['STAFF_ACADEMY_AMOUNT']}:</b> <input type='number' min='0' class='form-control' name='effect{$i}amount' value='0' />
					<select name='effect{$i}type' class='form-control' type='dropdown'>
						<option value='figure'>{$lang['STAFF_ACADEMY_VALUE']}</option>
						<option value='percent'>{$lang['STAFF_ACADEMY_PERCENT']}</option>
					</select>
					</td>
				</tr>";
			}
			echo "<tr>
				<td colspan='2'>
					<input type='submit' value='{$lang['STAFF_ACADEMY_CREATE']}' class='btn btn-default'>
				</td>
			</tr>
			</table>
		{$csrf}
		</form>";
	}
	else
	{
		if (!isset($_POST['verf']) || !verify_csrf_code('staff_newitem', stripslashes($_POST['verf'])))
		{
			csrf_error('create');
		}
		$academyname = (isset($_POST['academyname']) && is_string($_POST['academyname'])) ? stripslashes($_POST['academyname']) : '';
		$academydesc = (isset($_POST['academydesc']) && is_string($_POST['academydesc'])) ? stripslashes($_POST['academydesc']) : '';
		$academycost = (isset($_POST['academycost']) && is_numeric($_POST['academycost'])) ? abs(intval($_POST['academycost'])) : '';
		$academylvl = (isset($_POST['academylvl']) && is_numeric($_POST['academylvl'])) ? abs(intval($_POST['academylvl'])) : '';
		$academydays = (isset($_POST['academyday']) && is_numeric($_POST['academyday'])) ? abs(intval($_POST['academyday'])) : '';
		if (empty($academyname) || empty($academydesc) || empty($academycost) || empty($academylevel) || empty($academydays))
		{
			alert('danger',"Missing Inputs!","You are missing one of the required fields. Please go back and try again.");
			die($h->endpage());
		}
		$inq=$db->query("SELECT `academyid` FROM `academy` WHERE `academyname` = '{$academyname}'");
		if ($db->num_rows($inq) > 0)
		{
			$db->free_result($inq);
			alert("danger","Course Already Exists!","An academic course with that name already exists. Go back and choose a different name.");
			die($h->endpage());
		}
		for ($i = 1; $i <= 4; $i++)
		{
			$efxkey = "effect{$i}";
			$_POST[$efxkey . 'stat'] =
					(isset($_POST[$efxkey . 'stat'])
							&& in_array($_POST[$efxkey . 'stat'],
									array('strength', 'agility', 'guard',
											'labor', 'IQ')))
							? $_POST[$efxkey . 'stat'] : 'strength';
			$_POST[$efxkey . 'dir'] =
					(isset($_POST[$efxkey . 'dir'])
							&& in_array($_POST[$efxkey . 'dir'],
									array('pos', 'neg'))) ? $_POST[$efxkey . 'dir']
							: 'pos';
			$_POST[$efxkey . 'type'] =
					(isset($_POST[$efxkey . 'type'])
							&& in_array($_POST[$efxkey . 'type'],
									array('figure', 'percent')))
							? $_POST[$efxkey . 'type'] : 'figure';
			$_POST[$efxkey . 'amount'] =
					(isset($_POST[$efxkey . 'amount'])
							&& is_numeric($_POST[$efxkey . 'amount']))
							? abs(intval($_POST[$efxkey . 'amount'])) : 0;
			$_POST[$efxkey . 'on'] =
					(isset($_POST[$efxkey . 'on'])
							&& in_array($_POST[$efxkey . 'on'], array('true', 'false')))
							? $_POST[$efxkey . 'on'] : 0;
			$effects[$i] =
					$db->escape(
							serialize(
									array("stat" => $_POST[$efxkey . 'stat'],
											"dir" => $_POST[$efxkey . 'dir'],
											"inc_type" => $_POST[$efxkey . 'type'],
											"inc_amount" => abs(
													(int) $_POST[$efxkey
															. 'amount']))));
		}
		$m =
            $db->query(
                    "INSERT INTO `academy`
						VALUES(NULL, '{$academyname}', '{$academydesc}',
                     {$academycost}, {$academylevel}, '{$academydays}', 
					 '{$_POST['effect1on']}', '{$effects[1]}',
                     '{$_POST['effect2on']}', '{$effects[2]}',
                     '{$_POST['effect3on']}', '{$effects[3]}', 
					 '{$_POST['effect4on']}', '{$effects[4]}')");
		$api->SystemLogsAdd($userid,'staff',"Created academy {$academyname}.");
		alert('success',"Success!","You have successfully created an academic course called {$academyname}.");
	}
	$h->endpage();
}
function delacademy()
{
	global $db,$ir,$h,$lang,$userid,$api;
	if ($ir['user_level'] != 'Admin')
    {
        alert('danger','No Permission!','You have no permission to be here. If this is false, please contact an admin for help!');
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
							<input type='submit' class='btn btn-default' value='{$lang['STAFF_ACADEMY_DELETE_BUTTON']}'>
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
			alert('warning','Empty Input!','You did not specify a course to delete. Go back and try again.');
			die($h->endpage());
		}
		$d =
			$db->query(
					"SELECT `academyname`
					 FROM `academy`
					 WHERE `academyid` = {$_POST['academy']}");
		if ($db->num_rows($d) == 0)
		{
			$db->free_result($d);
			alert('danger',"Uh oh!","The course you chose to delete does not exist!");
			die($h->endpage());
		}
		$academyname = $db->fetch_single($d);
		$db->free_result($d);
		$db->query("DELETE FROM `academy` WHERE `academyid` = {$_POST['academy']}");
		$api->SystemLogsAdd($userid,'staff',"Deleted academy {$academyname}.");
		alert("success","Success!","The Course ({$academyname}) has been deleted from the game successfully.");
		die($h->endpage());
	}
}
$h->endpage();