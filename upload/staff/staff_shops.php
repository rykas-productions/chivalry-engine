<?php
/*
	File: staff/staff_shops.php
	Created: 4/4/2017 at 7:04PM Eastern Time
	Info: Staff panel for altering the shops in-game.
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine/
*/
require('sglobals.php');
echo "<h3>Shops</h3><hr />";
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
switch ($_GET['action'])
{
case "newshop":
    newshop();
    break;
case "newitem":
    newitem();
    break;
case "delshop":
    delshop();
    break;
default:
    die();
    break;
}
function newshop()
{
	global $lang,$h,$userid,$api,$db;
	if (isset($_POST['sn']))
	{
		if (!isset($_POST['verf']) || !verify_csrf_code('staff_newshop', stripslashes($_POST['verf'])))
		{
			alert('danger',$lang["CSRF_ERROR_TITLE"],$lang["CSRF_ERROR_TEXT"]);
			die($h->endpage());
		}
		$_POST['sl'] = (isset($_POST['sl']) && is_numeric($_POST['sl'])) ? abs(intval($_POST['sl'])) : 0;
		$_POST['sn'] = (isset($_POST['sn']) && preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i", $_POST['sn'])) ? $db->escape(strip_tags(stripslashes($_POST['sn']))) : '';
		$_POST['sd'] = (isset($_POST['sd'])) ? $db->escape(strip_tags(stripslashes($_POST['sd']))) : '';
		if (empty($_POST['sn']) || empty($_POST['sd']))
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_SHOP_SUB_ERROR1']);
		}
		else
		{
			$q = $db->query("SELECT COUNT(`town_id`) FROM `town` WHERE `town_id` = {$_POST['sl']}");
			if ($db->fetch_single($q) == 0)
			{
				$db->free_result($q);
				alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_SHOP_SUB_ERROR2']);
				die($h->endpage());
			}
			$db->free_result($q);
			$q = $db->query("SELECT COUNT(`shopID`) FROM `shops` WHERE `shopNAME` = '{$_POST['sn']}'");
			if ($db->fetch_single($q) > 0)
			{
				$db->free_result($q);
				alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_SHOP_SUB_ERROR3']);
				die($h->endpage());
			}
			$db->free_result($q);
			$db->query("INSERT INTO `shops` VALUES(NULL, {$_POST['sl']}, '{$_POST['sn']}', '{$_POST['sd']}')");
			$api->SystemLogsAdd($userid,'staff',"Created shop {$_POST['sn']}.");
			alert('success',$lang['ERROR_SUCCESS'],$lang['STAFF_SHOP_SUB_SUCCESS'],true,'index.php');
			die($h->endpage());
		}
	}
	else
	{
		$csrf = request_csrf_html('staff_newshop');
		echo "
		<form method='post'>
		<table class='table table-bordered'>
		<tr>
			<th colspan='2'>
				{$lang['STAFF_SHOP_FORM_TITLE']}
			</th>
		</tr>
		<tr>
			<th>
				{$lang['STAFF_SHOP_FORM_OPTION1']}
			</th>
			<td>
				<input type='text' required='1' name='sn' class='form-control' />
			</td>
		</tr>
		<tr>
			<th>
				{$lang['STAFF_SHOP_FORM_OPTION2']}
			</th>
			<td>
				<input type='text' required='1' name='sd' class='form-control' />
			</td>
		</tr>
		<tr>
			<th>
				{$lang['STAFF_SHOP_FORM_OPTION3']}
			</th>
			<td>
				" . location_dropdown("sl") . "
			</td>
		</tr>
		{$csrf}
		<tr>
			<td colspan='2'>
				<input type='submit' class='btn btn-default' value='{$lang['STAFF_SHOP_FORM_BTN']}' />
			</td>
		</tr>
		</table>
		</form>";
	}
}
function delshop()
{
	global $lang,$db,$api,$h,$userid;
	$_POST['shop'] = (isset($_POST['shop']) && is_numeric($_POST['shop'])) ? abs(intval($_POST['shop'])) : '';
    if (!empty($_POST['shop']))
    {
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_delshop', stripslashes($_POST['verf'])))
		{
			alert('danger',$lang["CSRF_ERROR_TITLE"],$lang["CSRF_ERROR_TEXT"]);
			die($h->endpage());
		}
        $shpq = $db->query("SELECT `shopNAME` FROM `shops` WHERE `shopID` = {$_POST['shop']}");
        if ($db->num_rows($shpq) == 0)
        {
            $db->free_result($shpq);
            alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_SHOP_DELFORM_SUB_ERROR1']);
            die($h->endpage());
        }
        $sn = $db->fetch_single($shpq);
        $db->free_result($shpq);
        $db->query("DELETE FROM `shops` WHERE `shopID` = {$_POST['shop']}");
        $db->query("DELETE FROM `shopitems` WHERE `sitemSHOP` = {$_POST['shop']}");
        $api->SystemLogsAdd($userid,'staff',"Deleted shop {$sn}.");
		alert('success',$lang['ERROR_SUCCESS'],$lang['STAFF_SHOP_DELFORM_SUB_SUCCESS'],true,'index.php');
        die($h->endpage());
    }
    else
    {
        $csrf = request_csrf_html('staff_delshop');
		echo "<form method='post'>
		<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					{$lang['STAFF_SHOP_DELFORM_TITLE']}
				</th>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_SHOP_DELFORM_FORM']}
				</th>
				<td>
					" . shop_dropdown("shop") . "
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' class='btn btn-default' value='{$lang['STAFF_SHOP_DELFORM_FORM_BTN']}' />
				</th>
			</tr>
			{$csrf}
		</table>
		</form>";
    }
}
function newitem()
{
	global $db,$lang,$h,$userid,$api;
	if (isset($_POST['item']))
	{
		if (!isset($_POST['verf']) || !verify_csrf_code('staff_newstock', stripslashes($_POST['verf'])))
		{
			alert('danger',$lang["CSRF_ERROR_TITLE"],$lang["CSRF_ERROR_TEXT"]);
			die($h->endpage());
		}
		$_POST['shop'] = (isset($_POST['shop']) && is_numeric($_POST['shop'])) ? abs(intval($_POST['shop'])) : '';
		$_POST['item'] = (isset($_POST['item']) && is_numeric($_POST['item'])) ? abs(intval($_POST['item'])) : '';
		if (empty($_POST['shop']) || empty($_POST['item']))
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_SHOP_IADDSUB_ERROR']);
			die($h->endpage());
		}
		$q = $db->query("SELECT COUNT(`shopID`) FROM `shops` WHERE `shopID` = {$_POST['shop']}");
		$q2 = $db->query("SELECT COUNT(`itmid`) FROM `items` WHERE `itmid` = {$_POST['item']}");
		$q3 = $db->query("SELECT COUNT(`sitemID`) FROM `shopitems` WHERE `sitemITEMID` = {$_POST['item']} AND `sitemSHOP` = {$_POST['shop']}");
		if ($db->fetch_single($q) == 0 || $db->fetch_single($q2) == 0)
		{
			$db->free_result($q);
			$db->free_result($q2);
			alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_SHOP_IADDSUB_ERROR2']);
			die($h->endpage());
		}
		if ($db->fetch_single($q3) > 0)
		{
			$db->free_result($q3);
			alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_SHOP_IADDSUB_ERROR3']);
			die($h->endpage());
		}
		$db->free_result($q);
		$db->free_result($q2);
		$db->free_result($q3);
		$db->query("INSERT INTO `shopitems` VALUES(NULL, {$_POST['shop']}, {$_POST['item']})");
		$api->SystemLogsAdd($userid,'staff',"Added Item ID {$_POST['item']} to Shop ID {$_POST['shop']}.");
		alert('success',$lang['ERROR_SUCCESS'],$lang['STAFF_SHOP_IADDSUB_SUCCESS'],true,'index.php');
		die($h->endpage());
	}
	else
	{	
		$csrf = request_csrf_html('staff_newstock');
		echo"<form method='post'>
			<table class='table table-bordered'>
				<tr>
					<th colspan='2'>
						{$lang['STAFF_SHOP_IADDFORM_TITLE']}
					</th>
				</tr>
				<tr>
					<th>
						{$lang['STAFF_SHOP_DELFORM_FORM']}
					</th>
					<td>
						" . shop_dropdown("shop") . "
					</td>
				</tr>
				<tr>
					<th>
						{$lang['STAFF_SHOP_IADDFORM_TD1']}
					</th>
					<td>
						" . item_dropdown("item") . "
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' class='btn btn-default' value='{$lang['STAFF_SHOP_IADDFORM_BTN']}' />
					</td>
				</tr>
				{$csrf}
			</table>
		</form>";
	}
}
$h->endpage();