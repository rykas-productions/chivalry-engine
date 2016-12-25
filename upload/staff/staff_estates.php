<?php
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
		$will = (isset($_POST['will']) && is_numeric($_POST['will'])) ? abs(intval($_POST['will'])) : 101;
		$cost = (isset($_POST['cost']) && is_numeric($_POST['cost'])) ? abs(intval($_POST['cost'])) : 0;
		if (!isset($_POST['verf']) || !verify_csrf_code('staff_addestate', stripslashes($_POST['verf'])))
		{
			alert('danger',"{$lang["CSRF_ERROR_TITLE"]}","{$lang["CSRF_ERROR_TEXT"]}");
			die($h->endpage());
		}
		$q = $db->query("SELECT COUNT(`house_id`) FROM `estates` WHERE `house_name` = '{$name}'");
		if ($db->fetch_single($q) > 0)
        {
            $db->free_result($q);
			alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['STAFF_ESTATE_ADD_ERROR1']}");
            die($h->endpage());
        }
		$db->free_result($q);
		$q = $db->query("SELECT COUNT(`house_id`) FROM `estates` WHERE `house_will` = {$will}");
		if ($db->fetch_single($q) > 0)
		{
			alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['STAFF_ESTATE_ADD_ERROR2']}");
            die($h->endpage());
		}
		if ($lvl < 1)
		{
			alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['STAFF_ESTATE_ADD_ERROR3']}");
            die($h->endpage());
		}
		if ($will <= 1)
		{
			alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['STAFF_ESTATE_ADD_ERROR4']}");
            die($h->endpage());
		}
		$api->SystemLogsAdd($userid,'staff',"Created an estate named {$name}.");
		alert('success',"{$lang['ERROR_SUCCESS']}","{$lang['STAFF_ESTATE_ADD_SUCCESS']}");
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
$h->endpage();
