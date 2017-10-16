<?php
/*
	File: staff/staff_items.php
	Created: 6/1/2016 at 6:06PM Eastern Time
	Info: Allows admins to interact with the items in the game.
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine
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
        alert('danger', "Uh Oh!", "Please select a valid action to perform.", true, 'index.php');
        die($h->endpage());
        break;
}
function create()
{
    global $db, $ir, $h, $userid, $api;
    if ($ir['user_level'] != 'Admin') {
        alert('danger', "Uh Oh!", "You do not have permission to be here.", true, 'index.php');
        die($h->endpage());
    }
    if (!isset($_POST['itemname'])) {
        $csrf = request_csrf_html('staff_newitem');
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
					" . itemtype_dropdown('itmtype') . "
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
        for ($i = 1; $i <= 3; $i++) {
            echo "
				<tr>
					<th>
						<b><u>Effect #{$i}</u></b>
					</th>
					<td>
						<input type='radio' class='form-control' name='effect{$i}on' value='true' /> Enable Effect
						<input type='radio' class='form-control' name='effect{$i}on' value='false' checked='checked' /> Disable Effect
					<br />
					<b>Stat</b> <select name='effect{$i}stat' type='dropdown' class='form-control'>
						<option value='energy'>Energy</option>
						<option value='will'>Will</option>
						<option value='brave'>Bravery</option>
						<option value='hp'>Health</option>
						<option value='level'>Level</option>
						<option value='strength'>Strength</option>
						<option value='agility'>Agility</option>
						<option value='guard'>Guard</option>
						<option value='labor'>Labor</option>
						<option value='iq'>IQ</option>
						<option value='infirmary'>Infirmary Time</option>
						<option value='dungeon'>Dungeon Time</option>
						<option value='primary_currency'>Primary Currency</option>
						<option value='secondary_currency'>Secondary Currency</option>
						<option value='xp'>Experience</option>
						<option value='vip_days'>VIP Days</option>
					</select>
					<br />
					<b>Direction</b> <select name='effect{$i}dir' class='form-control' type='dropdown'>
						<option value='pos'>Increase/Add</option>
						<option value='neg'>Decrease/Remove</option>
					</select>
					<br />
					<b>Amount</b> <input type='number' min='0' class='form-control' name='effect{$i}amount' value='0' />
					<select name='effect{$i}type' class='form-control' type='dropdown'>
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
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_newitem', stripslashes($_POST['verf']))) {
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
        for ($i = 1; $i <= 3; $i++) {
            $efxkey = "effect{$i}";
            $_POST[$efxkey . 'stat'] =
                (isset($_POST[$efxkey . 'stat'])
                    && in_array($_POST[$efxkey . 'stat'],
                        array('energy', 'will', 'brave', 'hp', 'level',
                            'strength', 'agility', 'guard',
                            'labor', 'iq', 'infirmary', 'dungeon',
                            'primary_currency', 'secondary_currency', 'xp', 'vip_days')))
                    ? $_POST[$efxkey . 'stat'] : 'energy';
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
                                (int)$_POST[$efxkey
                                . 'amount']))));
        }
        $m =
            $db->query(
                "INSERT INTO `items`
						VALUES(NULL, '{$itmtype}', '{$itmname}', '{$itmdesc}',
                     {$itmbuyprice}, {$itmsellprice}, '{$itmbuy}', 
					 '{$_POST['effect1on']}', '{$effects[1]}',
                     '{$_POST['effect2on']}', '{$effects[2]}',
                     '{$_POST['effect3on']}', '{$effects[3]}', 
					 {$weapon}, {$armor})");
        $api->SystemLogsAdd($userid, 'staff', "Created item {$itmname}.");
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
        $csrf = request_csrf_html('staff_newitemtype');
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
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_newitemtype', stripslashes($_POST['verf']))) {
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
        $api->SystemLogsAdd($userid, 'staff', "Created item type {$name}.");
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
        $csrf = request_csrf_html('staff_killitem');
        echo "<h4>Deleting an Item</h4>
		The item you select will be deleted permanently. There isn't a confirmation prompt, so be 100% sure.
		<form method='post'>
			<table class='table table-bordered'>
				<tr>
					<th width='33%'>
						Item
					</th>
					<td>
						" . item_dropdown('item') . "
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
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_killitem', stripslashes($_POST['verf']))) {
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
        $api->SystemLogsAdd($userid, 'staff', "Deleted item {$itemname}.");
        $db->query("DELETE FROM `items` WHERE `itmid` = {$_POST['item']}");
        $db->query("DELETE FROM `inventory` WHERE `inv_itemid` = {$_POST['item']}");
        alert("success", "Success!", "You have successfully deleted the {$itemname} item from the game.", true, 'index.php');
        die($h->endpage());
    }
}

function giveitem()
{
    global $db, $userid, $h, $api;
    if (!$api->UserMemberLevelGet($userid,'assistant')) {
        alert('danger', "Uh Oh!", "You do not have permission to be here.", true, 'index.php');
        die($h->endpage());
    }
    if (!isset($_POST['user']) || !isset($_POST['item'])) {
        echo "<h3>Gift Item Form</h3>";
        $csrf = request_csrf_html('staff_giveitem');
        echo "
		<form method='post'>
			<table class='table table-bordered table-responsive'>
				<tr>
					<th>
						User
					</th>
					<td>
						" . user_dropdown('user') . "
					</td>
				</tr>
				<tr>
					<th>
						Item
					</th>
					<td>
						" . item_dropdown('item') . "
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
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_giveitem', stripslashes($_POST['verf']))) {
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
                $api->UserGiveItem($_POST['user'], $_POST['item'], $_POST['qty']);
                $api->GameAddNotification($_POST['user'], "The administration has gifted you {$_POST['qty']} {$item['itmname']}(s) to your inventory.");
                $api->SystemLogsAdd($userid, 'staff', "Gave {$_POST['qty']} <a href='../iteminfo.php?ID={$_POST['item']}'>{$item['itmname']}</a>(s) to <a href='../profile.php?user={$_POST['user']}'>{$user['username']}</a>.");
                alert('success', "Success!", "You have successfully given {$_POST['qty']} {$item['itmname']}(s) to {$user['username']}.", true, 'index.php');
                die($h->endpage());
            }
        }
    }
}

function edititem()
{
    global $db, $api, $userid, $h;
    if ($api->UserMemberLevelGet($userid,'admin')) {
        alert('danger', "Uh Oh!", "You do not have permission to be here.", true, 'index.php');
        die($h->endpage());
    }
    if (!isset($_POST['step'])) {
        $_POST['step'] = 0;
    }
    if ($_POST['step'] == 2) {
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_edititem1', stripslashes($_POST['verf']))) {
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
        $csrf = request_csrf_html('staff_edititem2');
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
					" . itemtype_dropdown('itmtype', $itemi['itmtype']) . "
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
                "hp" => "Health", "strength" => "Strength",
                "agility" => "Agility", "guard" => "Guard",
                "labor" => "Labor", "iq" => "IQ",
                "infirmary" => "Infirmary Time", "dungeon" => "Dungeon Time",
                "primary_currency" => "Primary Currency", "secondary_currency"
            => "Secondary Currency", "crimexp" => "Experience", "vip_days" =>
                "VIP Days");
        for ($i = 1; $i <= 3; $i++) {
            if (!empty($itemi["effect" . $i])) {
                $efx = unserialize($itemi["effect" . $i]);
            } else {
                $efx = array("inc_amount" => 0);
            }
            $switch1 =
                ($itemi['effect' . $i . '_on'] == 'true') ? " checked='checked'" : "";
            $switch2 =
                ($itemi['effect' . $i . '_on'] == 'true') ? "" : " checked='checked'";
            echo "
				<tr>
					<th>
						<b><u>Effect #{$i}</u></b>
					</th>
					<td>
						<input type='radio' class='form-control' name='effect{$i}on' value='true'$switch1 /> Enable Effect
						<input type='radio' class='form-control' name='effect{$i}on' value='false'$switch2 /> Disable Effect
						<br /><b>Stat</b> <select class='form-control' name='effect{$i}stat' type='dropdown'>";
            foreach ($stats as $k => $v) {
                echo ($k == $efx['stat'])
                    ? '<option value="' . $k . '" selected="selected">' . $v
                    . '</option>'
                    : '<option value="' . $k . '">' . $v . '</option>';
            }
            $str =
                ($efx['dir'] == "neg")
                    ? "<option value='pos'>Increase/Add</option>
									<option value='neg' selected='selected'>Decrease/Remove</option>"
                    : "<option value='pos' selected='selected'>Increase/Add</option>
									<option value='neg'>Decrease/Remove</option>";
            $str2 =
                ($efx['inc_type'] == "percent")
                    ? "<option value='figure'>Value</option>
									<option value='percent' selected='selected'>Percentage</option>"
                    : "<option value='figure' selected='selected'>Value</option>
									<option value='percent'>Percentage</option>";

            echo "
				</select>
				<br />
					<b>Direction</b> <select class='form-control' name='effect{$i}dir' type='dropdown'> {$str} </select>
				<br />
					<b>Amount</b> <input type='text' class='form-control' name='effect{$i}amount' value='{$efx['inc_amount']}' />
						<select name='effect{$i}type' class='form-control' type='dropdown'>{$str2}</select>
				</td></tr>
				   ";
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
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_edititem2', stripslashes($_POST['verf']))) {
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
        for ($i = 1; $i <= 3; $i++) {
            $efxkey = "effect{$i}";
            $_POST[$efxkey . 'stat'] =
                (isset($_POST[$efxkey . 'stat'])
                    && in_array($_POST[$efxkey . 'stat'],
                        array('energy', 'will', 'brave', 'hp',
                            'strength', 'agility', 'guard',
                            'labor', 'iq', 'infirmary', 'dungeon',
                            'primary_currency', 'secondary_currency', 'xp', 'vip_days')))
                    ? $_POST[$efxkey . 'stat'] : 'energy';
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
                                (int)$_POST[$efxkey
                                . 'amount']))));
        }
        $db->query("UPDATE `items` SET `itmname` = '{$itmname}',
						`itmtype` = {$itmtype}, `itmdesc` = '{$itmdesc}',
						`itmbuyprice` = {$itmbuyprice}, `itmsellprice` = {$itmsellprice},
						`effect1_on` = '{$_POST['effect1on']}', `effect1` = '{$effects[1]}',
						`effect2_on` = '{$_POST['effect2on']}', `effect2` = '{$effects[2]}',
						`effect3_on` = '{$_POST['effect3on']}', `effect3` = '{$effects[3]}',
						`weapon` = {$weapon}, `armor` = {$armor} WHERE `itmid` = {$itemid }");
        alert('success', "Success!", "You successfully have edited the {$api->SystemItemIDtoName($itemid)} item.", true, 'index.php');
        $api->SystemLogsAdd($userid, 'staff', "Edited Item {$api->SystemItemIDtoName($itemid)}.");
    } else {
        $csrf = request_csrf_html('staff_edititem1');
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
					" . item_dropdown('item') . "
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