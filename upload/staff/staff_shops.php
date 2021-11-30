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
if (!$api->UserMemberLevelGet($userid, 'Admin')) 
{
    alert('danger', "Uh Oh!", "You do not have permission to be here.",true,'index.php');
    die($h->endpage());
}
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case "newshop":
        newshop();
        break;
    case "newitem":
        newitem();
        break;
    case "delshop":
        delshop();
        break;
	case "delstock":
        delstock();
        break;
    default:
        alert('danger', "Uh Oh!", "Please select a valid action to perform.", true, 'index.php');
        die($h->endpage());
        break;
}
function newshop()
{
    global $h, $userid, $api, $db;
    if (isset($_POST['sn'])) {
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_newshop', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "This action has been blocked for your security. Please fill in the form quickly next time.");
            die($h->endpage());
        }
        $_POST['sl'] = (isset($_POST['sl']) && is_numeric($_POST['sl'])) ? abs(intval($_POST['sl'])) : 0;
        $_POST['sn'] = (isset($_POST['sn']) && preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i", $_POST['sn'])) ? $db->escape(strip_tags(stripslashes($_POST['sn']))) : '';
        $_POST['sd'] = (isset($_POST['sd'])) ? $db->escape(strip_tags(stripslashes($_POST['sd']))) : '';
        if (empty($_POST['sn']) || empty($_POST['sd'])) {
            alert('danger', "Uh Oh!", "Please fill in the form completely before submitting.");
        } else {
            $q = $db->query("/*qc=on*/SELECT COUNT(`town_id`) FROM `town` WHERE `town_id` = {$_POST['sl']}");
            if ($db->fetch_single($q) == 0) {
                $db->free_result($q);
                alert('danger', "Uh Oh!", "The town you have chosen to place the town in does not exist.");
                die($h->endpage());
            }
            $db->free_result($q);
            $q = $db->query("/*qc=on*/SELECT COUNT(`shopID`) FROM `shops` WHERE `shopNAME` = '{$_POST['sn']}'");
            if ($db->fetch_single($q) > 0) {
                $db->free_result($q);
                alert('danger', "Uh Oh!", "You cannot have more than one town with the same name.");
                die($h->endpage());
            }
            $db->free_result($q);
            $db->query("INSERT INTO `shops` VALUES(NULL, {$_POST['sl']}, '{$_POST['sn']}', '{$_POST['sd']}', '0', '0')");
            $api->SystemLogsAdd($userid, 'staff', "Created shop {$_POST['sn']}.");
            alert('success', "Success!", "You have successfully created the {$_POST['sn']} shop.", true, 'index.php');
            die($h->endpage());
        }
    } else {
        $csrf = request_csrf_html('staff_newshop');
        echo "
		<form method='post'>
		<table class='table table-bordered'>
		<tr>
			<th colspan='2'>
				You are adding a new shop to the game.
			</th>
		</tr>
		<tr>
			<th>
				Shop Name
			</th>
			<td>
				<input type='text' required='1' name='sn' class='form-control' />
			</td>
		</tr>
		<tr>
			<th>
				Shop Description
			</th>
			<td>
				<input type='text' required='1' name='sd' class='form-control' />
			</td>
		</tr>
		<tr>
			<th>
				Shop Location
			</th>
			<td>
				" . location_dropdown("sl") . "
			</td>
		</tr>
		{$csrf}
		<tr>
			<td colspan='2'>
				<input type='submit' class='btn btn-primary' value='Create Shop' />
			</td>
		</tr>
		</table>
		</form>";
    }
}

function delshop()
{
    global $db, $api, $h, $userid;
    $_POST['shop'] = (isset($_POST['shop']) && is_numeric($_POST['shop'])) ? abs(intval($_POST['shop'])) : '';
    if (!empty($_POST['shop'])) {
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_delshop', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "This action has been blocked for your security. Please fill in the form quickly next time.");
            die($h->endpage());
        }
        $shpq = $db->query("/*qc=on*/SELECT `shopNAME` FROM `shops` WHERE `shopID` = {$_POST['shop']}");
        if ($db->num_rows($shpq) == 0) {
            $db->free_result($shpq);
            alert('danger', "Uh Oh!", "The shop you have chosen to delete does not exist.");
            die($h->endpage());
        }
        $sn = $db->fetch_single($shpq);
        $db->free_result($shpq);
        $db->query("DELETE FROM `shops` WHERE `shopID` = {$_POST['shop']}");
        $db->query("DELETE FROM `shopitems` WHERE `sitemSHOP` = {$_POST['shop']}");
        $api->SystemLogsAdd($userid, 'staff', "Deleted shop {$sn}.");
        alert('success', "Success!", "You have successfully deleted the {$sn} shop.", true, 'index.php');
        die($h->endpage());
    } else {
        $csrf = request_csrf_html('staff_delshop');
        echo "<form method='post'>
		<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					Select the shop you wish to remove from the game.
				</th>
			</tr>
			<tr>
				<th>
					Shop
				</th>
				<td>
					" . shop_dropdown("shop") . "
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' class='btn btn-primary' value='Delete Shop' />
				</th>
			</tr>
			{$csrf}
		</table>
		</form>";
    }
}

function newitem()
{
    global $db, $h, $userid, $api;
    if (isset($_POST['item'])) {
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_newstock', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "This action has been blocked for your security. Please fill in the form quickly next time.");
            die($h->endpage());
        }
        $_POST['shop'] = (isset($_POST['shop']) && is_numeric($_POST['shop'])) ? abs(intval($_POST['shop'])) : '';
        $_POST['item'] = (isset($_POST['item']) && is_numeric($_POST['item'])) ? abs(intval($_POST['item'])) : '';
        if (empty($_POST['shop']) || empty($_POST['item'])) {
            alert('danger', "Uh Oh!", "Please fill out the form completely before submitting.");
            die($h->endpage());
        }
        $q = $db->query("/*qc=on*/SELECT COUNT(`shopID`) FROM `shops` WHERE `shopID` = {$_POST['shop']}");
        $q2 = $db->query("/*qc=on*/SELECT COUNT(`itmid`) FROM `items` WHERE `itmid` = {$_POST['item']}");
        $q3 = $db->query("/*qc=on*/SELECT COUNT(`sitemID`) FROM `shopitems` WHERE `sitemITEMID` = {$_POST['item']} AND `sitemSHOP` = {$_POST['shop']}");
        if ($db->fetch_single($q) == 0 || $db->fetch_single($q2) == 0) {
            $db->free_result($q);
            $db->free_result($q2);
            alert('danger', "Uh Oh!", "Either the shop, or item you wish to stock, do not exist.");
            die($h->endpage());
        }
        if ($db->fetch_single($q3) > 0) {
            $db->free_result($q3);
            alert('danger', "Uh Oh!", "You already have this item stocked in this shop.");
            die($h->endpage());
        }
        $db->free_result($q);
        $db->free_result($q2);
        $db->free_result($q3);
        $db->query("INSERT INTO `shopitems` VALUES(NULL, {$_POST['shop']}, {$_POST['item']})");
        $api->SystemLogsAdd($userid, 'staff', "Added Item ID {$api->SystemItemIDtoName($_POST['item'])} to Shop ID {$_POST['shop']}.");
        alert('success', "Success!", "You have successfully added {$api->SystemItemIDtoName($_POST['item'])} to Shop ID {$_POST['shop']}.", true, 'index.php');
        die($h->endpage());
    } else {
        $csrf = request_csrf_html('staff_newstock');
        echo "<form method='post'>
			<table class='table table-bordered'>
				<tr>
					<th colspan='2'>
						Select an item you wish to add to a shop.
					</th>
				</tr>
				<tr>
					<th>
						Shop
					</th>
					<td>
						" . shop_dropdown("shop") . "
					</td>
				</tr>
				<tr>
					<th>
						Item to Stock
					</th>
					<td>
						" . item_dropdown("item") . "
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' class='btn btn-primary' value='Add to Stock' />
					</td>
				</tr>
				{$csrf}
			</table>
		</form>";
    }
}

function delstock()
{
	global $db, $h, $userid, $api;
    if (isset($_GET['id'])) 
	{
		$stockid = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs(intval($_GET['id'])) : 0;
		if (empty($stockid))
		{
			alert('danger','Uh Oh!', 'Please select a valid entry to delete.', true, '../shops.php');
			die($h->endpage());
		}
		$q = $db->query("SELECT * FROM `shopitems` WHERE `sitemID` = {$stockid}");
		if ($db->num_rows($q) == 0)
		{
			alert('danger','Uh Oh!', 'Non-existent stock to delete.', true, '../shops.php');
			die($h->endpage());
		}
		$r=$db->fetch_row($q);
		$itemname = $api->SystemItemIDtoName($r['sitemITEMID']);
		$shopname = $db->fetch_single($db->query("SELECT `shopNAME` FROM `shops` WHERE `shopID` = {$r['sitemSHOP']}"));
		$db->query("DELETE FROM `shopitems` WHERE `sitemID` = {$stockid} LIMIT 1");
		alert('success','Success!', "You have removed {$itemname} from the {$shopname} shop.", true, '../shops.php');
		$api->SystemLogsAdd($userid, 'staff', "Removed {$itemname} from the {$shopname} [{$r['sitemSHOP']}] shop.");
	}
	else
	{
		alert('danger','Uh Oh!', 'Please select a valid entry to delete.', true, '../shops.php');
		die($h->endpage());
	}
}
$h->endpage();