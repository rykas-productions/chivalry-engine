<?php
/*
	File: staff/staff_donates.php
	Created: 5/9/2017 at 1:36PM Eastern Time
	Info: Staff panel for adding/editing/removing donation items.
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine/
*/
require('sglobals.php');
echo "<h2>Staff VIP Pack</h2><hr />";
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case 'addpack':
        addpack();
        break;
    case 'delpack':
        delpack();
        break;
    default:
        echo '404';
        die($h->endpage());
        break;
}
function addpack()
{
    global $db, $userid, $api, $h;
    if (isset($_POST['pack'])) {
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_vip_add', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Your action has been blocked for your security. Please submit forms quickly!");
            die($h->endpage());
        }
        $_POST['pack'] = (isset($_POST['pack']) && is_numeric($_POST['pack'])) ? abs($_POST['pack']) : '';
        $_POST['qty'] = (isset($_POST['qty']) && is_numeric($_POST['qty'])) ? abs($_POST['qty']) : '';
        $cost = $_POST['cost'] * 100;
        $cost = (isset($cost) && is_numeric($cost)) ? abs($cost) : '';
        if (empty($_POST['pack'])) {
            alert('danger', "Uh Oh!", "Please select an item to add to the VIP Pack listing.");
            die($h->endpage());
        }
        if (empty($cost)) {
            alert('danger', "Uh Oh!", "Please select a cost for the VIP Pack you wish to list.");
            die($h->endpage());
        }
        if (empty($_POST['qty']))
        {
            alert('danger', "Uh Oh!", "Please select the quantity received for donating for this VIP Pack.");
            die($h->endpage());
        }
        if ($_POST['qty'] < 1)
        {
            alert('danger', "Uh Oh!", "Quantity must be greater than zero.");
            die($h->endpage());
        }
        $db_cost = $cost / 100;
        $q = $db->query("SELECT `itmid` FROM `items` WHERE `itmid` = {$_POST['pack']}");
        if ($db->num_rows($q) == 0) {
            alert('danger', "Uh Oh!", "The item you wish to list as a pack does not exist.");
            die($h->endpage());
        }
        $q2 = $db->query("SELECT `vip_item` FROM `vip_listing` WHERE `vip_item` = {$_POST['pack']}");
        if ($db->num_rows($q2) > 0) {
            alert('danger', "Uh Oh!", "You already have this item listed on the VIP Pack Listing.");
            die($h->endpage());
        }
        $db->query("INSERT INTO `vip_listing` (`vip_item`, `vip_cost`, `vip_qty`) VALUES ('{$_POST['pack']}', '{$db_cost}', '{$_POST['qty']}')");
        $api->SystemLogsAdd($userid, 'staff', "Added {$api->SystemItemIDtoName($_POST['pack'])} to the VIP Store for \${$db_cost}.");
        alert('success', "Success!", "You have successfully added the {$api->SystemItemIDtoName($_POST['pack'])} to the VIP Store for \${$db_cost}.", true, 'index.php');
    } else {
        $csrf = request_csrf_html('staff_vip_add');
        echo "<form method='post'>
				<table class='table table-bordered'>
					<tr>
						<th colspan='2'>
							You can add items to the VIP Store here.
						</th>
					</tr>
					<tr>
						<th>
							VIP Pack Item
						</th>
						<td>
							" . item_dropdown('pack') . "
						</td>
					</tr>
					<tr>
						<th>
							VIP Pack Cost
						</th>
						<td>
							<input type='number' required='1' class='form-control' name='cost' value='0.00' min='0.00' step='0.01'>
						</td>
					</tr>
					<tr>
						<th>
							VIP Pack Quantity
						</th>
						<td>
							<input type='number' required='1' class='form-control' name='qty' value='1' min='1'>
						</td>
					</tr>
					<tr>
						<td colspan='2'>
							<input type='submit' class='btn btn-primary' value='Add Pack'>
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
    global $db, $userid, $api, $h;
    if (isset($_POST['pack'])) {
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_vip_del', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Your action has been blocked for your security. Please submit forms quickly!");
            die($h->endpage());
        }
        $_POST['pack'] = (isset($_POST['pack']) && is_numeric($_POST['pack'])) ? abs($_POST['pack']) : '';
        if (empty($_POST['pack'])) {
            alert('danger', "Uh Oh!", "Please select a VIP Pack you wish to remove.");
            die($h->endpage());
        }
        $q = $db->query("SELECT `vip_item` FROM `vip_listing` WHERE `vip_id` = {$_POST['pack']}");
        if ($db->num_rows($q) == 0) {
            alert('danger', "Uh Oh!", "The VIP Pack you wish to remove has already been removed.");
            die($h->endpage());
        }
        $r = $db->fetch_single($q);
        $db->query("DELETE FROM `vip_listing` WHERE `vip_id` = {$_POST['pack']}");
        $api->SystemLogsAdd($userid, 'staff', "Removed an item from the VIP Store.");
        alert('success', "Success!", "You have successfully removed this pack from the VIP Store.", true, 'index.php');
    } else {
        $csrf = request_csrf_html('staff_vip_del');
        echo "<form method='post'>
				<table class='table table-bordered'>
					<tr>
						<th colspan='2'>
							You can remove VIP Packs from the VIP Store here.
						</th>
					</tr>
					<tr>
						<th>
							VIP Pack
						</th>
						<td>
							" . vipitem_dropdown() . "
						</td>
					</tr>
					<tr>
						<td colspan='2'>
							<input type='submit' class='btn btn-primary' value='Delete Pack'>
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
    global $db, $api;
    $ret = "<select name='$ddname' class='form-control' type='dropdown'>";
    $q =
        $db->query("SELECT *
    				 FROM `vip_listing`
    				 ORDER BY `vip_cost` ASC");
    if ($selected < 1) {
        $ret .= "<option value='0' selected='selected'>-- None --</option>";
    } else {
        $ret .= "<option value='0'>-- None --</option>";
    }
    while ($r = $db->fetch_row($q)) {
        $ret .= "\n<option value='{$r['vip_id']}'";
        if ($selected == $r['vip_id']) {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$api->SystemItemIDtoName($r['vip_item'])} (Cost: \${$r['vip_cost']})</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}