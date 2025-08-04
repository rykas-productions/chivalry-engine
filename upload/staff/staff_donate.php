<?php
/*
	File: 		staff/staff_donate.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Allows staff to do actions relating to the in-game VIP Packs.
	Author: 	TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine/
	
	MIT License

	Copyright (c) 2019 TheMasterGeneral

	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in all
	copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
	SOFTWARE.
*/
require('sglobals.php');
echo "<h2>Staff VIP Pack</h2><hr />";
if ($ir['user_level'] != "Admin") {
    alert('danger', "Uh Oh!", "You do not have permission to be here.", true, 'index.php');
    die($h->endpage());
}
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
    case 'editpack':
        editpack();
        break;
    default:
        alert('danger', "Uh Oh!", "Please select a valid action to perform.", true, 'index.php');
        die($h->endpage());
        break;
}
function addpack()
{
    global $db, $userid, $api, $h;
    if (isset($_POST['pack'])) {
        if (!isset($_POST['verf']) || !checkCSRF('staff_vip_add', stripslashes($_POST['verf']))) {
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
        if (empty($_POST['qty'])) {
            alert('danger', "Uh Oh!", "Please select the quantity received for donating for this VIP Pack.");
            die($h->endpage());
        }
        if ($_POST['qty'] < 1) {
            alert('danger', "Uh Oh!", "Quantity must be greater than zero.");
            die($h->endpage());
        }
        $db_cost = $cost / 100;
        $q = $db->query("SELECT `itmid` FROM `items` WHERE `itmid` = {$_POST['pack']}");
        if ($db->num_rows($q) == 0) {
            alert('danger', "Uh Oh!", "The item you wish to list as a pack does not exist.");
            die($h->endpage());
        }
        $q2 = $db->query("SELECT `vip_item` FROM `vip_listing` WHERE `vip_item` = {$_POST['pack']} AND `vip_qty` = {$_POST['qty']} AND `vip_cost` = '{$db_cost}'");
        if ($db->num_rows($q2) > 0) {
            alert('danger', "Uh Oh!", "You already have this item listed on the VIP Pack Listing.");
            die($h->endpage());
        }
        $db->query("INSERT INTO `vip_listing` (`vip_item`, `vip_cost`, `vip_qty`) VALUES ('{$_POST['pack']}', '{$db_cost}', '{$_POST['qty']}')");
        $api->game->addLog($userid, 'staff', "Added {$api->game->getItemNameFromID($_POST['pack'])} to the VIP Store for \${$db_cost}.");
        alert('success', "Success!", "You have successfully added the {$api->game->getItemNameFromID($_POST['pack'])} to the VIP Store for \${$db_cost}.", true, 'index.php');
    } else {
        $csrf = getHtmlCSRF('staff_vip_add');
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
							" . dropdownItem('pack') . "
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
        if (!isset($_POST['verf']) || !checkCSRF('staff_vip_del', stripslashes($_POST['verf']))) {
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
        $api->game->addLog($userid, 'staff', "Removed an item from the VIP Store.");
        alert('success', "Success!", "You have successfully removed this pack from the VIP Store.", true, 'index.php');
    } else {
        $csrf = getHtmlCSRF('staff_vip_del');
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
							" . vipdropdownItem() . "
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

function editpack()
{
    global $db, $h, $userid, $api;
    if (!isset($_POST['step'])) {
        $_POST['step'] = 0;
    }
    if ($_POST['step'] == 2) {
        if (!isset($_POST['verf']) || !checkCSRF('staff_vip_edit2', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Your action has been blocked for your security. Please submit forms quickly!");
            die($h->endpage());
        }
        $_POST['pack'] = (isset($_POST['pack']) && is_numeric($_POST['pack'])) ? abs($_POST['pack']) : '';
        $_POST['item'] = (isset($_POST['item']) && is_numeric($_POST['item'])) ? abs($_POST['item']) : '';
        $_POST['qty'] = (isset($_POST['qty']) && is_numeric($_POST['qty'])) ? abs($_POST['qty']) : '';
        $cost = $_POST['cost'] * 100;
        $cost = (isset($cost) && is_numeric($cost)) ? abs($cost) : '';
        if (empty($_POST['pack'])) {
            alert('danger', "Uh Oh!", "Please select a pack you wish to edit.");
            die($h->endpage());
        }
        if (empty($_POST['item'])) {
            alert('danger', "Uh Oh!", "Please select an item to add to the VIP Pack listing.");
            die($h->endpage());
        }
        if (empty($cost)) {
            alert('danger', "Uh Oh!", "Please select a cost for the VIP Pack you wish to list.");
            die($h->endpage());
        }
        if (empty($_POST['qty'])) {
            alert('danger', "Uh Oh!", "Please select the quantity received for donating for this VIP Pack.");
            die($h->endpage());
        }
        if ($_POST['qty'] < 1) {
            alert('danger', "Uh Oh!", "Quantity must be greater than zero.");
            die($h->endpage());
        }
        $db_cost = $cost / 100;
        $q = $db->query("SELECT `itmid` FROM `items` WHERE `itmid` = {$_POST['item']}");
        if ($db->num_rows($q) == 0) {
            alert('danger', "Uh Oh!", "The item you wish to list as a pack does not exist.");
            die($h->endpage());
        }
        $q2 = $db->query("SELECT `vip_item` FROM `vip_listing` WHERE `vip_item` = {$_POST['item']} AND `vip_id` != {$_POST['pack']}");
        if ($db->num_rows($q2) > 0) {
            alert('danger', "Uh Oh!", "You already have this item listed on the VIP Pack Listing.");
            die($h->endpage());
        }
        $db->query("UPDATE `vip_listing` SET `vip_item` = {$_POST['item']}, `vip_cost` = '{$db_cost}', `vip_qty` = {$_POST['qty']} WHERE `vip_id` = {$_POST['pack']}");
        $api->game->addLog($userid, 'staff', "Edited {$api->game->getItemNameFromID($_POST['item'])}'s VIP Pack.");
        alert('success', "Success!", "You have successfully edited the {$api->game->getItemNameFromID($_POST['item'])} VIP Pack.", true, 'index.php');
    } elseif ($_POST['step'] == 1) {
        if (!isset($_POST['verf']) || !checkCSRF('staff_vip_edit1', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Your action has been blocked for your security. Please submit forms quickly!");
            die($h->endpage());
        }
        $_POST['pack'] = (isset($_POST['pack']) && is_numeric($_POST['pack'])) ? abs($_POST['pack']) : '';
        if (empty($_POST['pack'])) {
            alert('danger', "Uh Oh!", "Please select a VIP Pack you wish to edit.");
            die($h->endpage());
        }
        $q = $db->query("SELECT * FROM `vip_listing` WHERE `vip_id` = {$_POST['pack']}");
        if ($db->num_rows($q) == 0) {
            alert('danger', "Uh Oh!", "The VIP Pack you wish to edit does not exist or is invalid.");
            die($h->endpage());
        }
        $r = $db->fetch_row($q);
        $csrf = getHtmlCSRF('staff_vip_edit2');
        echo "<form method='post'>
				<table class='table table-bordered'>
				<input type='hidden' value='2' name='step'>
				<input type='hidden' value='{$_POST['pack']}' name='pack'>
					<tr>
						<th colspan='2'>
							Edit the pack and click submit.
						</th>
					</tr>
					<tr>
						<th>
							VIP Pack Item
						</th>
						<td>
							" . dropdownItem('item', $r['vip_item']) . "
						</td>
					</tr>
					<tr>
						<th>
							VIP Pack Cost
						</th>
						<td>
							<input type='number' required='1' class='form-control' name='cost' value='{$r['vip_cost']}' min='0.00' step='0.01'>
						</td>
					</tr>
					<tr>
						<th>
							VIP Pack Quantity
						</th>
						<td>
							<input type='number' required='1' class='form-control' name='qty' value='{$r['vip_qty']}' min='1'>
						</td>
					</tr>
					<tr>
						<td colspan='2'>
							<input type='submit' class='btn btn-primary' value='Edit Pack'>
						</td>
					</tr>
				</table>
				{$csrf}
		</form>";
    } else {
        $csrf = getHtmlCSRF('staff_vip_edit1');
        echo "<form method='post'>
				<table class='table table-bordered'>
					<tr>
						<th colspan='2'>
							Select the VIP Pack you wish to edit, then submit the form.
						</th>
					</tr>
					<tr>
						<th>
							VIP Pack
						</th>
						<td>
							" . vipdropdownItem() . "
						</td>
					</tr>
					<tr>
						<td colspan='2'>
							<input type='submit' class='btn btn-primary' value='Edit Pack'>
						</td>
					</tr>
				</table>
				<input type='hidden' value='1' name='step'>
				{$csrf}
		</form>";
    }
    $h->endpage();
}

function vipdropdownItem($ddname = "pack", $selected = -1)
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
        $ret .= ">{$api->game->getItemNameFromID($r['vip_item'])} (Cost: \${$r['vip_cost']})</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}