<?php
/*
	File: staff/staff_smelt.php
	Created: 4/4/2017 at 7:04PM Eastern Time
	Info: Staff panel for creating/editing/deleting recipes in the smeltery.
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine/
*/
require('sglobals.php');
echo "<h3>Staff Smeltery</h3><hr />";
if ($api->UserMemberLevelGet($userid, 'Admin') == false) {
    alert('danger', "Uh Oh!", "You do not have permission to be here.");
    die($h->endpage());
}
if (!isset($_GET['action'])) {
    $_GET['action'] = 'add';
}
switch ($_GET['action']) {
    case 'add':
        add();
        break;
    case 'del':
        del();
        break;
    default:
        alert('danger', "Uh Oh!", "Please select a valid action to perform.", true, 'index.php');
        die($h->endpage());
        break;
}
function add()
{
    global $db, $api, $h, $userid;
    if (isset($_POST['smelted_item'])) {
        $_POST['smelted_item'] = (isset($_POST['smelted_item']) && is_numeric($_POST['smelted_item'])) ? abs(intval($_POST['smelted_item'])) : 0;
        $_POST['smelted_item_qty'] = (isset($_POST['smelted_item_qty']) && is_numeric($_POST['smelted_item_qty'])) ? abs(intval($_POST['smelted_item_qty'])) : 0;
        $_POST['timetocomplete'] = (isset($_POST['timetocomplete']) && is_numeric($_POST['timetocomplete'])) ? abs(intval($_POST['timetocomplete'])) : 0;
        $_POST['required_item'] = (isset($_POST['required_item']) && is_numeric($_POST['required_item'])) ? abs(intval($_POST['required_item'])) : 0;
        $_POST['required_item_qty'] = (isset($_POST['required_item_qty']) && is_numeric($_POST['required_item_qty'])) ? abs(intval($_POST['required_item_qty'])) : 0;
        if ($_POST['required_item'] == 0 || $_POST['smelted_item'] == 0 || $_POST['smelted_item_qty'] == 0 || $_POST['required_item_qty'] == 0) {
            alert('danger', "Uh Oh!", "Please fill out the previous form completely before submitting.");
            die($h->endpage());
        }
        $items = $_POST['required_item'];
        $qty = $_POST['required_item_qty'];
        for ($i = 1; $i <= 5; $i++) {
            $_POST['required_item' . $i] = (isset($_POST['required_item' . $i]) && is_numeric($_POST['required_item' . $i])) ? abs(intval($_POST['required_item' . $i])) : 0;
            $_POST['required_item_qty' . $i] = (isset($_POST['required_item_qty' . $i]) && is_numeric($_POST['required_item_qty' . $i])) ? abs(intval($_POST['required_item_qty' . $i])) : 0;
            if ($_POST['required_item' . $i] > 0) {
                if ($_POST['required_item_qty' . $i] == 0) {
                    alert('danger', "Uh Oh!", "Please specify the required item.");
                    die($h->endpage());
                }
                $items .= "," . $_POST['required_item' . $i];
                $qty .= "," . $_POST['required_item_qty' . $i];
            }
        }
        $db->query("INSERT INTO `smelt_recipes`
		(`smelt_time`, `smelt_items`, `smelt_quantity`, `smelt_output`, `smelt_qty_output`) 
		VALUES 
		('{$_POST['timetocomplete']}', '{$items}', '{$qty}', '{$_POST['smelted_item']}', '{$_POST['smelted_item_qty']}')");
        $api->SystemLogsAdd($userid, 'staff', "Created smelting recipe for " . $api->SystemItemIDtoName($_POST['smelted_item']));
        alert('success', "Success!", "You have successfully created a blacksmith recipe for " . $api->SystemItemIDtoName($_POST['smelted_item']), true, 'index.php');
    } else {
        echo "<form id='craft' method='post'>
			<table class='table table-bordered'>
				<tr>
					<th colspan='2'>
						Use this form to add a blacksmith recipe.
					</th>
				</tr>
				<tr>
					<th>
						Received Item
					</th>
					<td>
						" . item_dropdown("smelted_item") . "
					</td>
				</tr>
				<tr>
					<th>
						Received Quantity
					</th>
					<td>
						<input type='number' class='form-control' required='1' name='smelted_item_qty' value='1' min='1'>
					</td>
				</tr>
				<tr>
					<th>
						Completion Time
					</th>
					<td>
                        <input type='number' value='' placeholder='Time in seconds.' required='1' name='timetocomplete' class='form-control'>
					</td>
				</tr>
					<tr>
						<th>
							Required Item
						</th>
						<td>
							<div id='input1' class='clonedInput'>" . item_dropdown("required_item") . "<br /></div>
						</td>
					</tr>
				<tr>
				</div>
					<tr>
						<th>
							Required Quantity
						</th>
						<td>
							<div id='otherinput1' class='inputCloned'><input type='number' class='form-control' required='1' name='required_item_qty' value='1' min='1'><br /></div>
						</td>
					</tr>
				</tr>
				<tr>
					<td>
						<input type='button' class='btn btn-success' id='btnAdd' value='Add Required Item' />
					</td>
					<td>
						<input type='button' class='btn btn-danger' id='btnDel' value='Remove Required Item' />
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' class='btn btn-primary' value='Add Blacksmith Recipe' />
					</td>
				</tr>
			</table>
		</form>";
    }
}

function del()
{
    global $db, $userid, $api, $h;
    if (isset($_POST['smelt'])) {
        $_POST['smelt'] = (isset($_POST['smelt']) && is_numeric($_POST['smelt'])) ? abs(intval($_POST['smelt'])) : 0;
        if ($_POST['smelt'] == 0) {
            alert('danger', "Uh Oh!", "Please specify the blacksmith recipe you wish to remove.");
            die($h->endpage());
        }
        $db->query("DELETE FROM `smelt_recipes` WHERE `smelt_id` = {$_POST['smelt']}");
        $db->query("DELETE FROM `smelt_inprogress` WHERE `sip_recipe` = {$_POST['smelt']}");
        $api->SystemLogsAdd($userid, 'staff', "Removed Blacksmith Recipe ID #{$_POST['smelt']}");
        alert('success', "Success!", "You have successfully removed Blacksmith Recipe ID #{$_POST['smelt']}", true, 'index.php');
    } else {
        echo "<form action='?action=del' method='post'>
		<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					Select the Blacksmith Recipe you wish to remove.
				</th>
			</tr>
			<tr>
				<th>
					Recipe
				</th>
				<td>
					" . smelt_dropdown() . "
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' class='btn btn-primary' value='Remove Recipe' />
				</td>
			</tr>
		</table>
		</form>
		";
    }
}

$h->endpage();