<?php
/*
	File: staff/staff_estates.php
	Created: 4/4/2017 at 7:02PM Eastern Time
	Info: Staff panel for handling/editing/creating estates for players to buy.
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine/
*/
require('sglobals.php');
if ($ir['user_level'] != "Admin") {
    alert('danger', "Uh Oh!", "You do not have permission to be here.", true, 'index.php');
    die($h->endpage());
}
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case "addestate":
        addestate();
        break;
    case "editestate":
        editestate();
        break;
    case "delestate":
        delestate();
        break;
    case "giftestate":
        giftestate();
        break;
    default:
        alert('danger', "Uh Oh!", "Please select a valid action to perform.", true, 'index.php');
        die($h->endpage());
        break;
}
function addestate()
{
    global $db, $userid, $h, $api;
    echo "<h3>Add an Estate</h3><hr />";
    if (isset($_POST['name'])) {
        $lvl = (isset($_POST['lvl']) && is_numeric($_POST['lvl'])) ? abs(intval($_POST['lvl'])) : 1;
        $name = (isset($_POST['name']) && is_string($_POST['name'])) ? $db->escape(htmlentities($_POST['name'])) : '';
        $will = (isset($_POST['will']) && is_numeric($_POST['will'])) ? abs(intval($_POST['will'])) : 100;
        $cost = (isset($_POST['cost']) && is_numeric($_POST['cost'])) ? abs($_POST['cost']) : 0;
		$upgrades = (isset($_POST['upgrades']) && is_numeric($_POST['upgrades'])) ? abs($_POST['upgrades']) : 0;
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_addestate', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Your previous action was blocked for your security. Please submit forms quickly after opening them.");
            die($h->endpage());
        }
        $q = $db->query("/*qc=on*/SELECT COUNT(`house_id`) FROM `estates` WHERE `house_name` = '{$name}'");
        if ($db->fetch_single($q) > 0) {
            $db->free_result($q);
            alert('danger', "Uh Oh!", "The estate name you've chosen is already in use.");
            die($h->endpage());
        }
        $db->free_result($q);
        $q = $db->query("/*qc=on*/SELECT COUNT(`house_id`) FROM `estates` WHERE `house_will` = {$will}");
        if ($db->fetch_single($q) > 0) {
            alert('danger', "Uh Oh!", "You cannot have more than one estate with the same Will level.");
            die($h->endpage());
        }
        if ($lvl < 1) {
            alert('danger', "Uh Oh!", "You cannot have an estate with a level requirement under 1.");
            die($h->endpage());
        }
        if ($will <= 99) {
            alert('danger', "Uh Oh!", "You cannot have an estate with less than 100 will.");
            die($h->endpage());
        }
        $api->SystemLogsAdd($userid, 'staff', "Created an estate named {$name}.");
        alert('success', "Success!", "You have successfully created the {$name} Estate.", true, 'index.php');
        $db->query("INSERT INTO `estates` (`house_name`, `house_price`, `house_will`, `house_level`, `upgradeLevel`) VALUES ('{$name}', '{$cost}', '{$will}', '{$lvl}', '{$upgrades}')");
    } else {
        $csrf = request_csrf_html('staff_addestate');
        echo "<form action='?action=addestate' method='post'>
		<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					Add an estate to the game using this form.
				</th>
			</tr>
			<tr>
				<th>
					Estate Name
				</th>
				<td>
					<input type='text' name='name' required='1' class='form-control'>
				</td>
			</tr>
			<tr>
				<th>
					Cost
				</th>
				<td>
					<input type='number' name='cost' min='0' required='1' class='form-control'>
				</td>
			</tr>
			<tr>
				<th>
                    Level Requirement
				</th>
				<td>
					<input type='number' name='lvl' min='0' required='1' class='form-control'>
				</td>
			</tr>
			<tr>
				<th>
					Will
				</th>
				<td>
					<input type='number' name='will' min='101' required='1' class='form-control'>
				</td>
			</tr>
			<tr>
				<th>
					Upgrades
				</th>
				<td>
					<input type='number' name='upgrades' min='0' max='100' required='1' class='form-control'>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' class='btn btn-primary' value='Create Estate'>
				</td>
			</tr>
			{$csrf}
		</table>
		</form>";
    }
}

function delestate()
{
    global $db, $userid, $api, $h;
    if (isset($_POST['estate'])) {
        $_POST['estate'] = (isset($_POST['estate']) && is_numeric($_POST['estate'])) ? abs(intval($_POST['estate'])) : '';
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_delestate', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Your previous action was blocked for your security. Please submit forms quickly after opening them.");
            die($h->endpage());
        }
        $q = $db->query("/*qc=on*/SELECT * FROM `estates` WHERE `house_id` = {$_POST['estate']}");
        if ($db->num_rows($q) == 0) {
            $db->free_result($q);
            alert('danger', "Uh Oh!", "You are trying to delete a non-existent Estate.");
            die($h->endpage());
        }
        $old = $db->fetch_row($q);
        $db->free_result($q);
        if ($old['house_will'] == 100) {
            alert('danger', "Uh Oh!", "You cannot delete the starter Estate.");
            die($h->endpage());
        }
        $db->query("UPDATE `users`  SET `primary_currency` = `primary_currency` + {$old['house_price']},
                 `maxwill` = 100, `will` = LEAST(100, `will`) WHERE `maxwill` = {$old['house_will']}");
        $db->query("DELETE FROM `estates` WHERE `house_id` = {$old['house_id']}");
        alert('success', "Success!", "You have deleted the {$old['house_name']} estate.", true, 'index.php');
        $api->SystemLogsAdd($userid, 'staff', "Deleted the {$old['house_name']} estate.");
    } else {
        $csrf = request_csrf_html('staff_delestate');
        echo "<form method='post'>
			<table class='table table-bordered'>
				<tr>
					<th colspan='2'>
						Choose an Estate to delete. Players will be refuned and moved to the starter estate if they own
						the estate you delete.
					</th>
				</tr>
				<tr>
					<th>
						Estate
					</th>
					<td>
						" . estate_dropdown() . "
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' class='btn btn-primary' value='Delete Estate'>
					</td>
				</tr>
			</table>
			{$csrf}
		</form>";
    }
}

function editestate()
{
    global $db, $userid, $h, $api;
    if (!isset($_POST['step'])) {
        $_POST['step'] = '0';
    }
    if ($_POST['step'] == 2) {
        $lvl = (isset($_POST['lvl']) && is_numeric($_POST['lvl'])) ? abs(intval($_POST['lvl'])) : 1;
        $name = (isset($_POST['name']) && is_string($_POST['name'])) ? $db->escape(htmlentities($_POST['name'])) : '';
        $will = (isset($_POST['will']) && is_numeric($_POST['will'])) ? abs(intval($_POST['will'])) : 100;
        $cost = (isset($_POST['cost']) && is_numeric($_POST['cost'])) ? abs($_POST['cost']) : 0;
		$upgrades = (isset($_POST['upgrade']) && is_numeric($_POST['upgrade'])) ? abs($_POST['upgrade']) : 0;
        $_POST['id'] = (isset($_POST['id']) && is_numeric($_POST['id'])) ? abs(intval($_POST['id'])) : 0;
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_editestate2', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Your previous action was blocked for your security. Please submit forms quickly after opening them.");
            die($h->endpage());
        }
        if (empty($_POST['id']) || empty($_POST['lvl']) || empty($_POST['name'])
            || empty($_POST['will']) || empty($_POST['cost'])
        ) {
            alert('danger', "Uh Oh!", "Please fill out the form completely before submitting it.");
            die($h->endpage());
        }
        $q = $db->query("/*qc=on*/SELECT `house_id` FROM `estates` WHERE `house_will` = {$will} AND `house_id` != {$_POST['id']}");
        if ($db->num_rows($q)) {
            alert('danger', "Uh Oh!", "You cannot have more than one Estate with the same Will.");
            die($h->endpage());
        }
        $q = $db->query("/*qc=on*/SELECT `house_will` FROM `estates` WHERE `house_id` = {$_POST['id']}");
        if ($db->num_rows($q) == 0) {
            $db->free_result($q);
            alert('danger', "Uh Oh!", "You are trying to edit a non-existent estate.");
            die($h->endpage());
        }
        $oldwill = $db->fetch_single($q);
        if ($oldwill == 100 && $will > 100) {
            alert('danger', "Uh Oh!", "You cannot change the will of the starter Estate.");
            die($h->endpage());
        }
        $db->query("UPDATE `estates` SET `house_will` = {$will}, `house_price` = {$cost},
					`house_name` = '{$name}', `house_level` = {$lvl}, `upgradeLevel` = {$upgrades} WHERE `house_id` = {$_POST['id']}");
        //TODO REDO THIS TO USE NEW ESTATE SYSTEM
		$db->query("UPDATE `users` SET `maxwill` = {$will}, `will` = LEAST(`will`, {$will})
					WHERE `maxwill` = {$oldwill}");
		trigger_error("Update query for updating will on estates and users.");
        alert('success', "Success!", "You have successfully updated the {$name} estate.", true, 'index.php');
        $api->SystemLogsAdd($userid, 'staff', "Edited the {$name} estate.");
        die($h->endpage());
    }
    if ($_POST['step'] == 1) {
        $_POST['estate'] = (isset($_POST['estate']) && is_numeric($_POST['estate'])) ? abs(intval($_POST['estate'])) : 0;
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_editestate1', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Your previous action was blocked for your security. Please submit forms quickly after opening them.");
            die($h->endpage());
        }
        $q = $db->query("/*qc=on*/SELECT * FROM `estates` WHERE `house_id` = {$_POST['estate']}");
        if ($db->num_rows($q) == 0) {
            $db->free_result($q);
            alert('danger', "Uh Oh!", "The estate you're trying to edit does not exist.");
            die($h->endpage());
        }
        $old = $db->fetch_row($q);
        $db->free_result($q);
        $csrf = request_csrf_html('staff_editestate2');
        echo "<form method='post'>
		<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					Editing an estate
				</th>
			</tr>
			<tr>
				<th>
					Estate Name
				</th>
				<td>
					<input type='text' name='name' required='1' class='form-control' value='{$old['house_name']}'>
				</td>
			</tr>
			<tr>
				<th>
					Cost
				</th>
				<td>
					<input type='number' name='cost' min='1' required='1' class='form-control' value='{$old['house_price']}'>
				</td>
			</tr>
			<tr>
				<th>
					Level Requirement
				</th>
				<td>
					<input type='number' name='lvl' min='1' required='1' class='form-control' value='{$old['house_level']}'>
				</td>
			</tr>
			<tr>
				<th>
					Will
				</th>
				<td>
					<input type='number' name='will' min='100' required='1' class='form-control' value='{$old['house_will']}'>
				</td>
			</tr>
			<tr>
				<th>
					Upgrades
				</th>
				<td>
					<input type='number' name='upgrade' min='0' max='100' required='1' class='form-control' value='{$old['upgradeLevel']}'>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' class='btn btn-primary' value='Edit Estate'>
				</td>
			</tr>
			{$csrf}
			<input type='hidden' name='step' value='2' />
        	<input type='hidden' name='id' value='{$_POST['estate']}' />
		</table>
		</form>";
    }
    if ($_POST['step'] == 0) {
        $csrf = request_csrf_html('staff_editestate1');
        echo "<form method='post'>
			<input type='hidden' name='step' value='1' />
			<table class='table table-bordered'>
				<tr>
					<th colspan='2'>
						Select the estate you wish to edit.
					</th>
				</tr>
				<tr>
					<th>
						Estate
					</th>
					<td>
						" . estate_dropdown() . "
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' class='btn btn-primary' value='Edit Estate'>
					</td>
				</tr>
			</table>
			{$csrf}
		</form>";
    }
}

function giftestate()
{
    global $db, $api, $h, $userid;
    if (isset($_POST['user']))
    {
        $postUser = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs(intval($_POST['user'])) : 0;
        $postEstate = (isset($_POST['estate']) && is_numeric($_POST['estate'])) ? abs(intval($_POST['estate'])) : 0;
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_gift_estate', stripslashes($_POST['verf']))) 
        {
            alert('danger', "Action Blocked!", "Your previous action was blocked for your security. Please submit forms quickly after opening them.");
            die($h->endpage());
        }
        if (empty($postEstate))
        {
            alert('danger', "Uh Oh!", "Please input a valid estate to gift.");
            die($h->endpage());
        }
        elseif (empty($postUser))
        {
            alert('danger', "Uh Oh!", "Please input a valid user to gift the estate to.");
            die($h->endpage());
        }
        $q = $db->query("SELECT `house_name` FROM `estates` WHERE `house_id` = {$postEstate}");
        if ($db->num_rows($q) == 0)
        {
            alert('danger', "Uh Oh!", "You are trying to gift a non-existent estate.");
            die($h->endpage());
        }
        $houseName = $db->fetch_single($q);
        $db->free_result($q);
        $q = $db->query("SELECT `username` FROM `users` WHERE `userid` = {$postUser}");
        if ($db->num_rows($q) == 0)
        {
            alert('danger', "Uh Oh!", "You are trying to gift an estate to a non-existent user.");
            die($h->endpage());
        }
        buyEstate($postUser, $postEstate);
        alert('success',"Success!","You have successfully gifted {$db->fetch_single($q)} the {$houseName} estate.",true,'index.php');
        $api->SystemLogsAdd($userid, "staff", "Gifted {$db->fetch_single($q)} the {$houseName} estate.");
        $db->free_result($q);
    }
    else
    {
        $csrf = request_csrf_html('staff_gift_estate');
        $getUser = (isset($_GET['user']) && is_numeric($_GET['user'])) ? abs(intval($_GET['user'])) : $userid;
        echo "<div class='card'>
                <div class='card-header'>
                    Gift Estate
                </div>
                <div class='card-body'>
                    <form method='post'>
                    <div class='row'>
                        <div class='col-12'>
                            <div class='row'>
                                <div class='col-12'>
                                    Gift a player an estate using this form. This does not cost the player anything when you use this form.
                                </div>
                            </div>
                        </div>
                        <div class='col-auto'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>Estate</b></small>
                                </div>
                                <div class='col-12'>
                                    " . estate_dropdown('estate', 2) . "
                                </div>
                            </div>
                        </div>
                        <div class='col-auto'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>Player</b></small>
                                </div>
                                <div class='col-12'>
                                    " . user_dropdown('user', $getUser) . "
                                </div>
                            </div>
                        </div>
                        <div class='col-auto'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>Link</b></small>
                                </div>
                                <div class='col-12'>
                                    <input type='submit' value='Gift Estate' class='btn btn-success btn-block'>
                                </div>
                            </div>
                        </div>
                        {$csrf}
                        </form>
                    </div>
                </div>
        </div>";
    }
}
$h->endpage();
