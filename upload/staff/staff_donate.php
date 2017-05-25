<?php
/*
	File: staff/staff_donates.php
	Created: 5/9/2017 at 1:36PM Eastern Time
	Info: Staff panel for adding/editing/removing donation items.
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine/
*/
require('sglobals.php');
echo "<h2>{$lang['STAFF_DONATE_TITLE']}</h2><hr />";
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
switch ($_GET['action'])
{
	case 'addpack':
		addpack();
		break;
	case 'delpack':
		delpack();
		break;
	default:
		echo '404'; die($h->endpage());
		break;
}
function addpack()
{
	global $db,$userid,$lang,$api,$h;
	if (isset($_POST['pack']))
	{
		if (!isset($_POST['verf']) || !verify_csrf_code('staff_vip_add', stripslashes($_POST['verf'])))
		{
			alert('danger',$lang["CSRF_ERROR_TITLE"],$lang["CSRF_ERROR_TEXT"]);
			die($h->endpage());
		}
		$_POST['pack'] = (isset($_POST['pack']) && is_numeric($_POST['pack'])) ? abs($_POST['pack']) : '';
		$cost=$_POST['cost']*100;
		$cost = (isset($cost) && is_numeric($cost)) ? abs($cost) : '';
		if (empty($_POST['pack']))
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_DONATE_ADD_ERR']);
			die($h->endpage());
		}
		if (empty($cost))
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_DONATE_ADD_ERR2']);
			die($h->endpage());
		}
		$db_cost=$cost/100;
		$q=$db->query("SELECT `itmid` FROM `items` WHERE `itmid` = {$_POST['pack']}");
		if ($db->num_rows($q) == 0)
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_DONATE_ADD_ERR3']);
			die($h->endpage());
		}
		$q2=$db->query("SELECT `vip_item` FROM `vip_listing` WHERE `vip_item` = {$_POST['pack']}");
		if ($db->num_rows($q2) > 0)
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_DONATE_ADD_ERR4']);
			die($h->endpage());
		}
		$db->query("INSERT INTO `vip_listing` (`vip_item`, `vip_cost`) VALUES ('{$_POST['pack']}', '{$db_cost}')");
		$api->SystemLogsAdd($userid,'staff',"Added {$api->SystemItemIDtoName($_POST['pack'])} to the VIP Store for \${$db_cost}.");
		alert('success',$lang['ERROR_SUCCESS'],$lang['STAFF_DONATE_ADD_SUCC'],true,'index.php');
	}
	else
	{
		$csrf=request_csrf_html('staff_vip_add');
		echo "<form method='post'>
				<table class='table table-bordered'>
					<tr>
						<th colspan='2'>
							{$lang['STAFF_DONATE_ADD_INFO']}
						</th>
					</tr>
					<tr>
						<th>
							{$lang['STAFF_DONATE_ADD_TH']}
						</th>
						<td>
							" . item_dropdown('pack') . "
						</td>
					</tr>
					<tr>
						<th>
							{$lang['STAFF_DONATE_ADD_TH1']}
						</th>
						<td>
							<input type='number' required='1' class='form-control' name='cost' value='0.00' min='0.00' step='0.01'>
						</td>
					</tr>
					<tr>
						<td colspan='2'>
							<input type='submit' class='btn btn-primary' value='{$lang['STAFF_DONATE_ADD_BTN']}'>
						</td>
					</tr>
				</table>
				{$csrf}
		</form>";
	}
	$h->endpage();
}
function delpack()
{
	global $db,$userid,$lang,$api,$h;
	if (isset($_POST['pack']))
	{
		if (!isset($_POST['verf']) || !verify_csrf_code('staff_vip_del', stripslashes($_POST['verf'])))
		{
			alert('danger',$lang["CSRF_ERROR_TITLE"],$lang["CSRF_ERROR_TEXT"]);
			die($h->endpage());
		}
		$_POST['pack'] = (isset($_POST['pack']) && is_numeric($_POST['pack'])) ? abs($_POST['pack']) : '';
		if (empty($_POST['pack']))
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_DONATE_DEL_ERR']);
			die($h->endpage());
		}
		$q=$db->query("SELECT `vip_item` FROM `vip_listing` WHERE `vip_id` = {$_POST['pack']}");
		if ($db->num_rows($q) == 0)
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_DONATE_DEL_ERR1']);
			die($h->endpage());
		}
		$r=$db->fetch_single($q);
		$db->query("DELETE FROM `vip_listing` WHERE `vip_id` = {$_POST['pack']}");
		$api->SystemLogsAdd($userid,'staff',"Removed an item from the VIP Store.");
		alert('success',$lang['ERROR_SUCCESS'],$lang['STAFF_DONATE_DEL_SUCC'],true,'index.php');
	}
	else
	{
		$csrf=request_csrf_html('staff_vip_del');
		echo "<form method='post'>
				<table class='table table-bordered'>
					<tr>
						<th colspan='2'>
							{$lang['STAFF_DONATE_DEL_INFO']}
						</th>
					</tr>
					<tr>
						<th>
							{$lang['STAFF_DONATE_ADD_TH']}
						</th>
						<td>
							" . vipitem_dropdown() . "
						</td>
					</tr>
					<tr>
						<td colspan='2'>
							<input type='submit' class='btn btn-primary' value='{$lang['STAFF_DONATE_DEL_BTN']}'>
						</td>
					</tr>
				</table>
				{$csrf}
		</form>";
	}
	$h->endpage();
}
function vipitem_dropdown($ddname = "pack", $selected = -1)
{
    global $db,$api;
    $ret = "<select name='$ddname' class='form-control' type='dropdown'>";
    $q =
            $db->query("SELECT *
    				 FROM `vip_listing`
    				 ORDER BY `vip_cost` ASC");
    if ($selected < 1)
    {
        $ret .= "<option value='0' selected='selected'>-- None --</option>";
    }
    else
    {
        $ret .= "<option value='0'>-- None --</option>";
    }
    while ($r = $db->fetch_row($q))
    {
        $ret .= "\n<option value='{$r['vip_id']}'";
        if ($selected == $r['vip_id'])
        {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$api->SystemItemIDtoName($r['vip_item'])} (Cost: \${$r['vip_cost']})</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}