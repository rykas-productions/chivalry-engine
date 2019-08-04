<?php
/*
	File: 		staff/staff_items.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Allows staff to do actions relating to the in-game items.
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
echo "<h3>Staff Items</h3><hr />";
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case "create":
        create();
        break;
    case "createitmgroup":
        createitmgroup();
        break;
    case "delete":
        deleteitem();
        break;
    case "edit":
        edititem();
        break;
    case "giveitem":
        giveitem();
        break;
    default:
        menu();
        break;
}
function menu()
{
	global $api, $userid;
	if ($api->user->getStaffLevel($userid, 'admin'))
	{
		echo "
		<a href='?action=create' class='btn btn-primary'>Create Item</a><br /><br />
		<a href='?action=edit' class='btn btn-primary'>Edit Item</a><br /><br />
		<a href='?action=delete' class='btn btn-primary'>Delete Item</a><br /><br />
		<a href='?action=createitmgroup' class='btn btn-primary'>Create Item Group</a><br /><br />";
	}
	echo "
	<a href='?action=giveitem' class='btn btn-primary'>Give Item</a><br /><br />";
}
function create()
{
    global $db, $ir, $h, $userid, $api;
    if ($ir['user_level'] != 'Admin') {
        alert('danger', "Uh Oh!", "You do not have permission to be here.", true, 'index.php');
        die($h->endpage());
    }
    if (!isset($_POST['itemname'])) {
        $csrf = getHtmlCSRF('staff_newitem');
        echo "<form method='post'>
		<table class='table table-bordered'>
			<tr>
				<th width='33%'>
					Item Name
				</th>
				<td>
					<input type='text' required='1' name='itemname' class='form-control'>
				</td>
			</tr>
			<tr>
				<th width='33%'>
					Description
				</th>
				<td>
					<input type='text' required='1' name='itemdesc' class='form-control'>
				</td>
			</tr>
			<tr>
				<th width='33%'>
					Item Type
				</th>
				<td>
					" . dropdownItemType('itmtype') . "
				</td>
			</tr>
			<tr>
				<th width='33%'>
					Purchasable?
				</th>
				<td>
					<input type='checkbox' class='form-control' checked='checked' name='itembuyable'>
				</td>
			</tr>
			<tr>
				<th width='33%'>
					Buying Price
				</th>
				<td>
					<input type='number' required='1' name='itembuy' min='0' class='form-control'>
				</td>
			</tr>
			<tr>
				<th width='33%'>
					Selling Price
				</th>
				<td>
					<input type='number' required='1' name='itemsell' min='0' class='form-control'>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<h4>Item Usage</h4>
				</td>
			</tr>";
        for ($i = 1; $i <= constant("itemeffects"); $i++) {
            echo "
				<tr>
					<th>
						<b><u>Effect #{$i}</u></b>
					</th>
					<td>
						<select name='effecton[]' type='dropdown' class='form-control'>
                            <option value='0'>Disable Effect</option>
                            <option value='1'>Enable Effect</option>
                        </select>
					<br />
					<b>Stat</b> <select name='effectstat[]' type='dropdown' class='form-control'>
						<option value='energy'>Energy</option>
						<option value='will'>Will</option>
						<option value='brave'>Bravery</option>
						<option value='hp'>Health</option>
						<option value='level'>Level</option>
						<option value='strength'>" . constant("stat_strength") . "</option>
						<option value='agility'>" . constant("stat_agility") . "</option>
						<option value='guard'>" . constant("stat_guard") . "</option>
						<option value='labor'>" . constant("stat_labor") . "</option>
						<option value='iq'>" . constant("stat_iq") . "</option>
						<option value='infirmary'>Infirmary Time</option>
						<option value='dungeon'>Dungeon Time</option>
						<option value='primary_currency'>" . constant("primary_currency") . "</option>
						<option value='secondary_currency'>" . constant("secondary_currency") . "</option>
						<option value='xp'>Experience</option>
						<option value='vip_days'>VIP Days</option>
					</select>
					<br />
					<b>Direction</b> <select name='effectdir[]' class='form-control' type='dropdown'>
						<option value='pos'>Increase/Add</option>
						<option value='neg'>Decrease/Remove</option>
					</select>
					<br />
					<b>Amount</b> <input type='number' min='0' class='form-control' name='effectamount[]' value='0' />
					<select name='effecttype[]' class='form-control' type='dropdown'>
						<option value='figure'>Value</option>
						<option value='percent'>Percentage</option>
					</select>
					</td>
				</tr>";
        }

        echo "
			<tr>
				<td colspan='2'>
					<h4>Equipment Stats</h4>
				</td>
			</tr>
			<tr>
				<th>
					Weapon Strength
				</th>
				<td>
					<input type='number' class='form-control' name='weapon' min='0' value='0' />
				</td>
			</tr>
			<tr>
				<th>
					Armor Defense
				</th>
				<td>
					<input type='number' class='form-control' name='armor' min='0' value='0' />
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' value='Create Item' class='btn btn-primary'>
				</td>
			</tr>
		</table>
		{$csrf}
		</form>";
    } else {
        if (!isset($_POST['verf']) || !checkCSRF('staff_newitem', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly after opening them. Go back and submit the form quicker!");
            die($h->endpage());
        }
        $itmname = (isset($_POST['itemname']) && is_string($_POST['itemname'])) ? $db->escape(strip_tags(stripslashes($_POST['itemname']))) : '';
        $itmdesc = (isset($_POST['itemdesc'])) ? $db->escape(strip_tags(stripslashes($_POST['itemdesc']))) : '';
        $weapon = (isset($_POST['weapon']) && is_numeric($_POST['weapon'])) ? abs(intval($_POST['weapon'])) : 0;
        $armor = (isset($_POST['armor']) && is_numeric($_POST['armor'])) ? abs(intval($_POST['armor'])) : 0;
        $itmtype = (isset($_POST['itmtype']) && is_numeric($_POST['itmtype'])) ? abs(intval($_POST['itmtype'])) : '';
        $itmbuyprice = (isset($_POST['itembuy']) && is_numeric($_POST['itembuy'])) ? abs(intval($_POST['itembuy'])) : 0;
        $itmsellprice = (isset($_POST['itemsell']) && is_numeric($_POST['itemsell'])) ? abs(intval($_POST['itemsell'])) : 0;
        if (empty($itmname) || empty($itmdesc) || empty($itmtype) || empty($itmbuyprice) || empty($itmsellprice)) {
            alert('danger', "Uh Oh!", "You are missing one or more of the required inputs on the previous form.");
            die($h->endpage());
        }
        $inq = $db->query("SELECT `itmid` FROM `items` WHERE `itmname` = '{$itmname}'");
        if ($db->num_rows($inq) > 0) {
            $db->free_result($inq);
            alert('danger', "Uh Oh!", "An item with the same name already exists.");
            die($h->endpage());
        }
        $q = $db->query("SELECT `itmtypeid` FROM `itemtypes` WHERE `itmtypeid` = '{$itmtype}'");
        if ($db->num_rows($q) == 0) {
            $db->free_result($q);
            alert('danger', "Uh Oh!", "The item type you've chosen does not exist.");
            die($h->endpage());
        }
        $itmbuy = ($_POST['itembuyable'] == 'on') ? 'true' : 'false';
        foreach($_POST['effecton'] as $key => $field)
        {
            $field=($field == 1) ? 1 : 0;
        }
        foreach($_POST['effectstat'] as $key => $field)
        {
            $field=(isset($field) && in_array($field, 
                array('energy', 'will', 'brave', 'hp', 'level',
                'strength', 'agility', 'guard',
                'labor', 'iq', 'infirmary', 'dungeon',
                'primary_currency', 'secondary_currency', 'xp', 'vip_days')))
                ? $field : 'energy';
        }
        foreach($_POST['effectamount'] as $key => $field)
        {
            $field = (isset($field) && is_numeric($field)) ? abs(intval($field)) : 0;
        }
        foreach($_POST['effectdir'] as $key => $field)
        {
            $field = (isset($field) && in_array($field, array('pos', 'neg'))) ? $field : 'pos';
        }
        foreach($_POST['effecttype'] as $key => $field)
        {
            $field = (isset($field) && in_array($field, array('figure', 'percent'))) ? $field : 'figure';
        }
        $effectarray=(json_encode($_POST['effecton']));
        $statarray=(json_encode($_POST['effectstat']));
        $amountarray=(json_encode($_POST['effectamount']));
        $dirarray=(json_encode($_POST['effectdir']));
        $typearray=(json_encode($_POST['effecttype']));
        $m =
            $db->query(
                "INSERT INTO `items`
						VALUES(NULL, '{$itmtype}', '{$itmname}', '{$itmdesc}',
                     {$itmbuyprice}, {$itmsellprice}, '{$itmbuy}', '{$effectarray}', '{$statarray}', '{$dirarray}', '{$amountarray}', '{$typearray}',
					 '{$weapon}', '{$armor}')");
        $api->game->addLog($userid, 'staff', "Created item {$itmname}.");
        alert('success', "Success!", "You have successfully created the {$itmname} item.", true, 'index.php');
    }
}

function createitmgroup()
{
    global $db, $h, $ir, $api, $userid;
    if ($ir['user_level'] != 'Admin') {
        alert('danger', "Uh Oh!", "You do not have permission to be here.", true, 'index.php');
        die($h->endpage());
    }
    if (!isset($_POST['name'])) {
        $csrf = getHtmlCSRF('staff_newitemtype');
        echo "
        <h4>Create Item Group</h4>
		<form method='post'>
			<table class='table table-bordered'>
			<tr>
				<th width='33%'>
					Item Group Name
				</th>
				<td>
					<input type='text' class='form-control' required='1' name='name' />
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' class='btn btn-primary' value='Create Item Group' />
				</td>
			</tr>
        	{$csrf}
			</table>
		</form>
           ";
    } else {
        $name = (isset($_POST['name']) && preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i", $_POST['name'])) ? $db->escape(strip_tags(stripslashes($_POST['name']))) : '';
        if (!isset($_POST['verf']) || !checkCSRF('staff_newitemtype', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly after opening them. Go back and submit the form quicker!");
            die($h->endpage());
        }
        if (empty($name)) {
            alert('danger', "Uh Oh!", "Please fill out the previous form completely before submitting it.");
            die($h->endpage());
        }
        $q = $db->query("SELECT `itmtypeid` FROM `itemtypes` WHERE `itmtypename` = '{$name}'");
        if ($db->num_rows($q) > 0) {
            $db->free_result($q);
            alert("danger", "Uh Oh!", "The item group name you've chosen is already in use.");
            die($h->endpage());
        }
        $api->game->addLog($userid, 'staff', "Created item type {$name}.");
        alert('success', "Success!", "You have successfully created the {$name} item group.", true, 'index.php');
        $db->query("INSERT INTO `itemtypes` VALUES(NULL, '{$name}')");

    }
}

function deleteitem()
{
    global $db, $ir, $h, $userid, $api;
    if ($ir['user_level'] != 'Admin') {
        alert('danger', 'No Permission!', 'You have no permission to be here. If this is false, please contact an admin for help!');
        die($h->endpage());
    }
    if (!isset($_POST['item'])) {
        $csrf = getHtmlCSRF('staff_killitem');
        echo "<h4>Deleting an Item</h4>
		The item you select will be deleted permanently. There isn't a confirmation prompt, so be 100% sure.
		<form method='post'>
			<table class='table table-bordered'>
				<tr>
					<th width='33%'>
						Item
					</th>
					<td>
						" . dropdownItem('item') . "
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' class='btn btn-primary' value='Delete Item'>
					</td>
				</tr>
			</table>
			{$csrf}
		</form>";
    } else {
        if (!isset($_POST['verf']) || !checkCSRF('staff_killitem', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly after opening them. Go back and submit the form quicker!");
            die($h->endpage());
        }
        $_POST['item'] = (isset($_POST['item']) && is_numeric($_POST['item'])) ? abs(intval($_POST['item'])) : '';
        if (empty($_POST['item'])) {
            alert('warning', "Uh Oh!", 'You did not specify an item to delete. Go back and try again.');
            die($h->endpage());
        }
        $d =
            $db->query(
                "SELECT `itmname`
                     FROM `items`
                     WHERE `itmid` = {$_POST['item']}");
        if ($db->num_rows($d) == 0) {
            $db->free_result($d);
            alert('danger', "Uh oh!", "The item you chose to delete does not exist!");
            die($h->endpage());
        }
        $itemname = $db->fetch_single($d);
        $db->free_result($d);
        $api->game->addLog($userid, 'staff', "Deleted item {$itemname}.");
        $db->query("DELETE FROM `items` WHERE `itmid` = {$_POST['item']}");
        $db->query("DELETE FROM `inventory` WHERE `inv_itemid` = {$_POST['item']}");
        alert("success", "Success!", "You have successfully deleted the {$itemname} item from the game.", true, 'index.php');
        die($h->endpage());
    }
}

function giveitem()
{
    global $db, $userid, $h, $api;
    if (!$api->user->getStaffLevel($userid,'assistant')) {
        alert('danger', "Uh Oh!", "You do not have permission to be here.", true, 'index.php');
        die($h->endpage());
    }
    if (!isset($_POST['user']) || !isset($_POST['item'])) {
        echo "<h3>Gift Item Form</h3>";
        $csrf = getHtmlCSRF('staff_giveitem');
        echo "
		<form method='post'>
			<table class='table table-bordered table-responsive'>
				<tr>
					<th>
						User
					</th>
					<td>
						" . dropdownUser('user') . "
					</td>
				</tr>
				<tr>
					<th>
						Item
					</th>
					<td>
						" . dropdownItem('item') . "
					</td>
				</tr>
				<tr>
					<th>
						Quantity
					</th>
					<td>
						<input type='number' required='1' class='form-control' name='qty' value='1' min='1' />
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' class='btn btn-primary' value='Gift Item' />
					</td>
				</tr>
				{$csrf}
			</table>
		</form>";
    } else {
        if (!isset($_POST['verf']) || !checkCSRF('staff_giveitem', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly after opening them. Go back and submit the form quicker!");
            die($h->endpage());
        }
        $_POST['item'] = (isset($_POST['item']) && is_numeric($_POST['item'])) ? abs(intval($_POST['item'])) : '';
        $_POST['user'] = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs(intval($_POST['user'])) : '';
        $_POST['qty'] = (isset($_POST['qty']) && is_numeric($_POST['qty'])) ? abs(intval($_POST['qty'])) : '';
        if (empty($_POST['item'])) {
            alert('danger', "Uh Oh!", "Please specify the item you wish to give.");
            die($h->endpage());
        } elseif (empty($_POST['user'])) {
            alert('danger', "Uh Oh!", "Please specify the user you wish to give an item to.");
            die($h->endpage());
        } elseif (empty($_POST['qty'])) {
            alert('danger', "Uh Oh!", "Please specify the qunatity of item you wish to give.");
            die($h->endpage());
        } else {
            $q = $db->query("SELECT `itmid`,`itmname` FROM `items` WHERE `itmid` = {$_POST['item']}");
            $q2 = $db->query("SELECT `userid`,`username` FROM `users` WHERE `userid` = {$_POST['user']}");
            if ($db->num_rows($q) == 0) {
                alert('danger', "Uh Oh!", "The item you wish to give does not exist.");
                die($h->endpage());
            } elseif ($db->num_rows($q2) == 0) {
                alert('danger', "Uh Oh!", "The user you wish to give to does not exist.");
                die($h->endpage());
            } else {
                $item = $db->fetch_row($q);
                $user = $db->fetch_row($q2);
                $db->free_result($q);
                $db->free_result($q2);
                $api->user->giveItem($_POST['user'], $_POST['item'], $_POST['qty']);
                $api->user->addNotification($_POST['user'], "The administration has gifted you {$_POST['qty']} {$item['itmname']}(s) to your inventory.");
                $api->game->addLog($userid, 'staff', "Gave {$_POST['qty']} <a href='../iteminfo.php?ID={$_POST['item']}'>{$item['itmname']}</a>(s) to <a href='../profile.php?user={$_POST['user']}'>{$user['username']}</a>.");
                alert('success', "Success!", "You have successfully given {$_POST['qty']} {$item['itmname']}(s) to {$user['username']}.", true, 'index.php');
                die($h->endpage());
            }
        }
    }
}

function edititem()
{
    global $db, $api, $userid, $h;
    if (!$api->user->getStaffLevel($userid,'admin')) {
        alert('danger', "Uh Oh!", "You do not have permission to be here.", true, 'index.php');
        die($h->endpage());
    }
    if (!isset($_POST['step'])) {
        $_POST['step'] = 0;
    }
    if ($_POST['step'] == 2) {
        if (!isset($_POST['verf']) || !checkCSRF('staff_edititem1', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly after opening them. Go back and submit the form quicker!");
            die($h->endpage());
        }
        $_POST['item'] = (isset($_POST['item']) && is_numeric($_POST['item'])) ? abs(intval($_POST['item'])) : 0;
        if (empty($_POST['item'])) {
            alert('danger', "Uh Oh!", "Please fill out the form completely before submitting it.");
            die($h->endpage());
        }
        $d = $db->query("SELECT * FROM `items` WHERE `itmid` = {$_POST['item']}");
        if ($db->num_rows($d) == 0) {
            $db->free_result($d);
            alert('danger', "Uh Oh!", "You are trying to edit an item that does not exist.");
            die($h->endpage());
        }
        $itemi = $db->fetch_row($d);
        $db->free_result($d);
        $csrf = getHtmlCSRF('staff_edititem2');
        $itmname = addslashes($itemi['itmname']);
        $itmdesc = addslashes($itemi['itmdesc']);
        echo "<form method='post'>
					<input type='hidden' name='itemid' value='{$_POST['item']}' />
					<input type='hidden' name='step' value='3' />
		<table class='table table-bordered'>
			<tr>
				<th width='33%'>
					Item Name
				</th>
				<td>
					<input type='text' required='1' name='itemname' value='{$itmname}' class='form-control'>
				</td>
			</tr>
			<tr>
				<th width='33%'>
					Description
				</th>
				<td>
					<input type='text' required='1' name='itemdesc' value='{$itmdesc}' class='form-control'>
				</td>
			</tr>
			<tr>
				<th width='33%'>
					Item Type
				</th>
				<td>
					" . dropdownItemType('itmtype', $itemi['itmtype']) . "
				</td>
			</tr>
			<tr>
				<th width='33%'>
					Purchasable?
				</th>
				<td>
					<input type='checkbox' class='form-control' checked='checked' name='itembuyable'>
				</td>
			</tr>
			<tr>
				<th width='33%'>
					Buying Price
				</th>
				<td>
					<input type='number' required='1' name='itembuy' min='0' value='{$itemi['itmbuyprice']}' class='form-control'>
				</td>
			</tr>
			<tr>
				<th width='33%'>
					Selling Price
				</th>
				<td>
					<input type='number' required='1' name='itemsell' min='0' value='{$itemi['itmsellprice']}' class='form-control'>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<h4>Item Usage</h4>
				</td>
			</tr>";
        $stats =
            array("energy" => "Energy", "will" => "Will",
                "brave" => "Bravery", "level" => "Level",
                "hp" => "Health", "strength" => constant("stat_strength"),
                "agility" => constant("stat_agility"), "guard" => constant("stat_guard"),
                "labor" => constant("stat_labor"), "iq" => constant("stat_iq"),
                "infirmary" => "Infirmary Time", "dungeon" => "Dungeon Time",
                "primary_currency" => constant("primary_currency"), "secondary_currency"
            => constant("secondary_currency"), "crimexp" => "Experience", "vip_days" =>
                "VIP Days");
        $iterations=count(json_decode($itemi['itmeffects_toggle']));
        $toggle=json_decode($itemi['itmeffects_toggle']);
        $stat=json_decode($itemi['itmeffects_stat']);
        $dir=json_decode($itemi['itmeffects_dir']);
        $type=json_decode($itemi['itmeffects_type']);
        $amount=json_decode($itemi['itmeffects_amount']);
        $usecount=0;
        while ($usecount != $iterations)
        {
            $switch1 = ($toggle[$usecount] == 1) ? " selected" : "";
            $switch2 = ($toggle[$usecount] == 1) ? "" : " selected";
            $switch3 = ($dir[$usecount] == "pos") ? "selected" : "";
            $switch4 = ($dir[$usecount] == "pos") ? "" : " selected";
            $switch5 = ($type[$usecount] == "figure") ? "selected" : "";
            $switch6 = ($type[$usecount] == "figure") ? "" : " selected";
            echo "<tr>
                <th>
                    Item Effect
                </th>
                <td>
                    <select name='effecton[]' type='dropdown' class='form-control'>
                        <option value='0'{$switch2}>Disable Effect</option>
                        <option value='1'{$switch1}>Enable Effect</option>
                    </select>
                <br />
                <b>Stat</b> <select name='effectstat[]' type='dropdown' class='form-control'>";
                    foreach ($stats as $k => $v)
                    {
                        echo ($k == $stat[$usecount])
                        ? '<option value="' . $k . '" selected="selected">' . $v
                        . '</option>'
                        : '<option value="' . $k . '">' . $v . '</option>';
                    }
                    echo"
                </select>
                <br />
                <b>Direction</b> <select name='effectdir[]' class='form-control' type='dropdown'>
                    <option value='pos'{$switch3}>Increase/Add</option>
                    <option value='neg'{$switch4}>Decrease/Remove</option>
                </select>
                <br />
                <b>Amount</b> <input type='number' min='0' class='form-control' name='effectamount[]' value='{$amount[$usecount]}' />
                <select name='effecttype[]' class='form-control' type='dropdown'>
                    <option value='figure'{$switch5}>Value</option>
                    <option value='percent'{$switch6}>Percentage</option>
                </select>
                </td>
            </tr>";
            $usecount=$usecount+1;
        }
        echo "
			<tr>
				<td colspan='2'>
					<h4>Equipment Stats</h4>
				</td>
			</tr>
			<tr>
				<th>
					Weapon Strength
				</th>
				<td>
					<input type='number' class='form-control' value='{$itemi['weapon']}' name='weapon' min='0' value='0' />
				</td>
			</tr>
			<tr>
				<th>
					Armor Defense
				</th>
				<td>
					<input type='number' class='form-control' value='{$itemi['armor']}' name='armor' min='0' value='0' />
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' value='Edit Item' class='btn btn-primary'>
				</td>
			</tr>
		</table>
		{$csrf}
		</form>";
    } elseif ($_POST['step'] == 3) {
        if (!isset($_POST['verf']) || !checkCSRF('staff_edititem2', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly after opening them. Go back and submit the form quicker!");
            die($h->endpage());
        }
        $itemid = (isset($_POST['itemid']) && is_numeric($_POST['itemid'])) ? abs(intval($_POST['itemid'])) : 0;
        $itmname = (isset($_POST['itemname']) && is_string($_POST['itemname'])) ? stripslashes($_POST['itemname']) : '';
        $itmdesc = (isset($_POST['itemdesc'])) ? $db->escape(strip_tags(stripslashes($_POST['itemdesc']))) : '';
        $weapon = (isset($_POST['weapon']) && is_numeric($_POST['weapon'])) ? abs(intval($_POST['weapon'])) : 0;
        $armor = (isset($_POST['armor']) && is_numeric($_POST['armor'])) ? abs(intval($_POST['armor'])) : 0;
        $itmtype = (isset($_POST['itmtype']) && is_numeric($_POST['itmtype'])) ? abs(intval($_POST['itmtype'])) : '';
        $itmbuyprice = (isset($_POST['itembuy']) && is_numeric($_POST['itembuy'])) ? abs(intval($_POST['itembuy'])) : 0;
        $itmsellprice = (isset($_POST['itemsell']) && is_numeric($_POST['itemsell'])) ? abs(intval($_POST['itemsell'])) : 0;
        if (empty($itmname) || empty($itemid) || empty($itmdesc) || empty($itmtype) || empty($itmbuyprice) || empty($itmsellprice)) {
            alert('danger', "Uh Oh!", "You are missing one or more required inputs from the previous form.");
            die($h->endpage());
        }
        $inq = $db->query("SELECT `itmid` FROM `items` WHERE `itmname` = '{$itmname}' AND `itmid` != {$itemid}");
        if ($db->num_rows($inq) > 0) {
            $db->free_result($inq);
            alert('danger', "Uh Oh!", "You cannot have more than one item with the same name.");
            die($h->endpage());
        }
        $q = $db->query("SELECT `itmtypeid` FROM `itemtypes` WHERE `itmtypeid` = '{$itmtype}'");
        if ($db->num_rows($q) == 0) {
            $db->free_result($q);
            alert('danger', "Uh Oh!", "The item group you've chosen does not exist.");
            die($h->endpage());
        }
        $itmbuy = ($_POST['itembuyable'] == 'on') ? 'true' : 'false';
        foreach($_POST['effecton'] as $key => $field)
        {
            $field=($field == 1) ? 1 : 0;
        }
        foreach($_POST['effectstat'] as $key => $field)
        {
            $field=(isset($field) && in_array($field, 
                array('energy', 'will', 'brave', 'hp', 'level',
                'strength', 'agility', 'guard',
                'labor', 'iq', 'infirmary', 'dungeon',
                'primary_currency', 'secondary_currency', 'xp', 'vip_days')))
                ? $field : 'energy';
        }
        foreach($_POST['effectamount'] as $key => $field)
        {
            $field = (isset($field) && is_numeric($field)) ? abs(intval($field)) : 0;
        }
        foreach($_POST['effectdir'] as $key => $field)
        {
            $field = (isset($field) && in_array($field, array('pos', 'neg'))) ? $field : 'pos';
        }
        foreach($_POST['effecttype'] as $key => $field)
        {
            $field = (isset($field) && in_array($field, array('figure', 'percent'))) ? $field : 'figure';
        }
        $effectarray=(json_encode($_POST['effecton']));
        $statarray=(json_encode($_POST['effectstat']));
        $amountarray=(json_encode($_POST['effectamount']));
        $dirarray=(json_encode($_POST['effectdir']));
        $typearray=(json_encode($_POST['effecttype']));
        $db->query("UPDATE `items` SET `itmname` = '{$itmname}',
						`itmtype` = {$itmtype}, `itmdesc` = '{$itmdesc}',
						`itmbuyprice` = {$itmbuyprice}, `itmsellprice` = {$itmsellprice},
                        `itmeffects_toggle` = '{$effectarray}', `itmeffects_stat` = '{$statarray}',
                        `itmeffects_dir` = '{$dirarray}', `itmeffects_amount` = '{$amountarray}', 
                        `itmeffects_type` = '{$typearray}', `weapon` = {$weapon}, `armor` = {$armor} 
                        WHERE `itmid` = {$itemid }");
        alert('success', "Success!", "You successfully have edited the {$api->SystemItemIDtoName($itemid)} item.", true, 'index.php');
        $api->game->addLog($userid, 'staff', "Edited Item {$api->SystemItemIDtoName($itemid)}.");
    } else {
        $csrf = getHtmlCSRF('staff_edititem1');
        echo "
	<table class='table table-bordered'>
		<form method='post'>
			<tr>
				<th colspan='2'>
					Select the item you wish to edit.
				</th>
			</tr>
			<tr>
				<th>
					Item
				</th>
				<td>
					" . dropdownItem('item') . "
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					{$csrf}
					<input type='hidden' name='step' value='2'>
					<input type='submit' class='btn btn-primary' value='Edit Item' />
				</th>
			</tr>
		</form>
	</table>
	";
    }
}

$h->endpage();