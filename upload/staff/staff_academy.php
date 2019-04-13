<?php
/*
	File: staff/staff_academy.php
	Created: 6/1/2016 at 6:06PM Eastern Time
	Info: Allows admins to add/edit/delete academy courses.
	Author: ImJustIsabella
	Website: https://github.com/MasterGeneral156/chivalry-engine
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
    case "add":
        addacademy();
        break;
    case "del":
        delacademy();
        break;
    case "edit":
        editacademy();
        break;
    default:
        menu();
        break;
}
function menu()
{
    echo "<h3>Academy Staff Menu</h3><hr />
    <a href='?action=add' class='btn btn-primary'>Add Course</a><br /><br />
    <a href='?action=edit' class='btn btn-primary'>Edit Course</a><br /><br />
    <a href='?action=del' class='btn btn-primary'>Delete Course</a><br /><br />";
}
function addacademy()
{
    global $h, $db, $userid, $api, $ir;
    if (!isset($_POST['name'])) {
        $csrf = getHtmlCSRF('staff_newacademy');
        echo "<form method='post'>
		<table class='table table-bordered'>
		<tr>
			<tr>
				<th colspan='2'>
					Fill out this form to add an academy course to the game.
				</th>
			</tr>
			<th>
					Course Name
				</th>
				<td>
					<input type='text' required='1' name='name' class='form-control'>
				</td>
			</tr>
			<tr>
				<th>
					Course Description
				</th>
				<td>
					<input type='text' required='1' name='desc' class='form-control'>
				</td>
			</tr>
			<tr>
				<th>
					Enrollment Cost
				</th>
				<td>
					<input type='number' required='1' min='1' name='cost' class='form-control'>
				</td>
			</tr>
			<tr>
				<th>
					Minimal Level (Optional)
				</th>
				<td>
					<input type='number' required='1' min='0' value='0' name='lvl' class='form-control'>
				</td>
			</tr>
			<tr>
				<th>
					Course Length (In Days)
				</th>
				<td>
					<input type='number' required='1' name='day' min='1' class='form-control'>
				</td>
			</tr>
			<tr>
				<th>
					Course {$_CONFIG['strength_stat']}
				</th>
				<td>
					<input type='number' required='1' name='str' min='0' value='0' class='form-control'>
				</td>
			</tr>
			<tr>
				<th>
					Course {$_CONFIG['agility_stat']}
				</th>
				<td>
					<input type='number' required='1' name='agl' min='0' value='0' class='form-control'>
				</td>
			</tr>
			<tr>
				<th>
					Course {$_CONFIG['guard_stat']}
				</th>
				<td>
					<input type='number' required='1' name='grd' min='0' value='0' class='form-control'>
				</td>
			</tr>
			<tr>
				<th>
					Course Labor
				</th>
				<td>
					<input type='number' required='1' name='lab' min='0' value='0' class='form-control'>
				</td>
			</tr>
			<tr>
				<th>
					Course {$_CONFIG['iq_stat']}
				</th>
				<td>
					<input type='number' required='1' name='iq' min='0' value='0' class='form-control'>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' value='Create Course' class='btn btn-primary'>
				</td>
			</tr>
			</table>
		{$csrf}
		</form>";
    } else {
        if (!isset($_POST['verf']) || !checkCSRF('staff_newacademy', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly. Go back and submit it quicker!");
            die($h->endpage());
        }
        $name = (isset($_POST['name']) && is_string($_POST['name'])) ? stripslashes($_POST['name']) : '';
        $desc = (isset($_POST['desc']) && is_string($_POST['desc'])) ? stripslashes($_POST['desc']) : '';
        $cost = (isset($_POST['cost']) && is_numeric($_POST['cost'])) ? abs(intval($_POST['cost'])) : '';
        $lvl = (isset($_POST['lvl']) && is_numeric($_POST['lvl'])) ? abs(intval($_POST['lvl'])) : 0;
        $days = (isset($_POST['day']) && is_numeric($_POST['day'])) ? abs(intval($_POST['day'])) : '';
        $str = (isset($_POST['str']) && is_numeric($_POST['str'])) ? abs(intval($_POST['str'])) : 0;
        $agl = (isset($_POST['agl']) && is_numeric($_POST['agl'])) ? abs(intval($_POST['agl'])) : 0;
        $grd = (isset($_POST['grd']) && is_numeric($_POST['grd'])) ? abs(intval($_POST['grd'])) : 0;
        $lab = (isset($_POST['lab']) && is_numeric($_POST['lab'])) ? abs(intval($_POST['lab'])) : 0;
        $iq = (isset($_POST['iq']) && is_numeric($_POST['iq'])) ? abs(intval($_POST['iq'])) : 0;
        if (empty($name) || empty($desc) || empty($cost) || !isset($lvl) || empty($days)) {
            alert('danger', "Uh Oh!", "Please be sure to fill in the form completely.");
            die($h->endpage());
        }
        if (empty($str) && empty($agl) && empty($grd) && empty($lab) && empty($iq)) {
            alert('danger', "Uh Oh!", "Please be sure to input some stats to be gained by completing the course.");
            die($h->endpage());
        }
        $inq = $db->query("SELECT `ac_id` FROM `academy` WHERE `ac_name` = '{$name}'");
        if ($db->num_rows($inq) > 0) {
            $db->free_result($inq);
            alert('danger', "Uh Oh!", "You cannot have more than one course with the same name.");
            die($h->endpage());
        }
        $db->query("INSERT INTO `academy` VALUES (NULL, '{$name}', '{$desc}', '{$cost}', '{$lvl}', '{$days}', '{$str}', '{$agl}', '{$grd}', '{$lab}', '{$iq}')");
        $api->game->addLog($userid, 'staff', "Created academy course {$name}.");
        alert('success', "Success!", "You have successfully added the {$name} course.", true, 'index.php');
    }
}

function delacademy()
{
    global $db, $ir, $h, $userid, $api;
    if (!isset($_POST['academy'])) {
        $csrf = getHtmlCSRF('staff_delacademy');
        echo "<h4>Deleting an Academic Course</h4>
			The academy you select will be deleted permanently. There isn't a confirmation prompt, so be 100% sure.
			<form method='post'>
				<table class='table table-bordered'>
					<tr>
						<th width='33%'>
							Course
						</th>
						<td>
							" . dropdownAcademy('academy') . "
						</td>
					</tr>
					<tr>
						<td colspan='2'>
							<input type='submit' class='btn btn-primary' value='Delete Course'>
						</td>
					</tr>
				</table>
				{$csrf}
			</form>";
    } else {
        if (!isset($_POST['verf']) || !checkCSRF('staff_delacademy', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly. Go back and submit it quicker!");
            die($h->endpage());
        }
        $_POST['academy'] = (isset($_POST['academy']) && is_numeric($_POST['academy'])) ? abs(intval($_POST['academy'])) : '';
        if (empty($_POST['academy'])) {
            alert('warning', "Uh Oh!", "Please select an academic course to have deleted.");
            die($h->endpage());
        }
        $d =
            $db->query(
                "SELECT `ac_name`
					 FROM `academy`
					 WHERE `ac_id` = {$_POST['academy']}");
        if ($db->num_rows($d) == 0) {
            $db->free_result($d);
            alert('danger', "Uh Oh!", "The academic course you're trying to delete does not exist.");
            die($h->endpage());
        }
        $academyname = $db->fetch_single($d);
        $db->free_result($d);
        $db->query("DELETE FROM `academy` WHERE `ac_id` = {$_POST['academy']}");
        $api->game->addLog($userid, 'staff', "Deleted academy {$academyname}.");
        alert("success", "Success!", "You have successfully deleted the {$academyname} academic course.", true, 'index.php');
        die($h->endpage());
    }
}

function editacademy()
{
    global $db, $userid, $api, $h;
    if (!isset($_POST['step'])) {
        $_POST['step'] = '0';
    }
    switch ($_POST['step']) {
        case 2:
            $id = (isset($_POST['id']) && is_numeric($_POST['id'])) ? abs(intval($_POST['id'])) : 0;
            $name = (isset($_POST['name']) && is_string($_POST['name'])) ? stripslashes($_POST['name']) : '';
            $desc = (isset($_POST['desc']) && is_string($_POST['desc'])) ? stripslashes($_POST['desc']) : '';
            $cost = (isset($_POST['cost']) && is_numeric($_POST['cost'])) ? abs(intval($_POST['cost'])) : '';
            $lvl = (isset($_POST['lvl']) && is_numeric($_POST['lvl'])) ? abs(intval($_POST['lvl'])) : 0;
            $days = (isset($_POST['day']) && is_numeric($_POST['day'])) ? abs(intval($_POST['day'])) : '';
            $str = (isset($_POST['str']) && is_numeric($_POST['str'])) ? abs(intval($_POST['str'])) : 0;
            $agl = (isset($_POST['agl']) && is_numeric($_POST['agl'])) ? abs(intval($_POST['agl'])) : 0;
            $grd = (isset($_POST['grd']) && is_numeric($_POST['grd'])) ? abs(intval($_POST['grd'])) : 0;
            $lab = (isset($_POST['lab']) && is_numeric($_POST['lab'])) ? abs(intval($_POST['lab'])) : 0;
            $iq = (isset($_POST['iq']) && is_numeric($_POST['iq'])) ? abs(intval($_POST['iq'])) : 0;
            $q = $db->query("SELECT * FROM `academy` WHERE `ac_id` = {$id}");
            if ($db->num_rows($q) == 0) {
                $db->free_result($q);
                alert("danger", "Uh Oh!", "The course you are wishing to edit does not exist, or is invalid.");
                die($h->endpage());
            }
            $db->free_result($q);
            if (empty($name) || empty($desc) || empty($cost) || !isset($lvl) || empty($days)) {
                alert('danger', "Uh Oh!", "Please be sure to fill in the form completely.");
                die($h->endpage());
            }
            if (empty($str) && empty($agl) && empty($grd) && empty($lab) && empty($iq)) {
                alert('danger', "Uh Oh!", "Please be sure to input some stats to be gained by completing the course.");
                die($h->endpage());
            }
            $db->query("UPDATE `academy`
                        SET `ac_name` = '{$name}', `ac_desc` = '{$desc}', `ac_cost` = {$cost}, `ac_level` = {$lvl},
                        `ac_days` = {$days}, `ac_str` = {$str}, `ac_agl` = {$agl}, `ac_grd` = {$grd}, `ac_lab` = {$lab},
                         `ac_iq` = {$iq}
                         WHERE `ac_id` = {$id}");
            alert('success', "Success!", "You have successfully edited the {$name} Academy Course.", true, 'index.php');
            $api->game->addLog($userid, 'staff', "Edited the {$name} Course.");
            break;
        case 1:
            $_POST['academy'] = (isset($_POST['academy']) && is_numeric($_POST['academy'])) ? abs(intval($_POST['academy'])) : 0;
            $q = $db->query("SELECT * FROM `academy` WHERE `ac_id` = {$_POST['academy']}");
            if ($db->num_rows($q) == 0) {
                $db->free_result($q);
                alert("danger", "Uh Oh!", "The course you are wishing to edit does not exist, or is invalid.");
                die($h->endpage());
            }
            if (!isset($_POST['verf']) || !checkCSRF('staff_editacademy1', stripslashes($_POST['verf']))) {
                alert('danger', "Action Blocked!", "Forms expire fairly quickly. Go back and submit it quicker!");
                die($h->endpage());
            }
            $r = $db->fetch_row($q);
            $csrf = getHtmlCSRF('staff_editacademy2');
            echo "<form method='post'>
                <input type='hidden' name='step' value='2' />
        	    <input type='hidden' name='id' value='{$_POST['academy']}' />
                <table class='table table-bordered'>
                <tr>
                    <tr>
                        <th colspan='2'>
                            Editing an academy
                        </th>
                    </tr>
                    <th>
                            Course Name
                        </th>
                        <td>
                            <input type='text' required='1' name='name' class='form-control' value='{$r['ac_name']}'>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Course Description
                        </th>
                        <td>
                            <input type='text' required='1' name='desc' class='form-control' value='{$r['ac_desc']}'>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Enrollment Cost
                        </th>
                        <td>
                            <input type='number' required='1' min='1' name='cost' class='form-control' value='{$r['ac_cost']}'>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Minimal Level (Optional)
                        </th>
                        <td>
                            <input type='number' required='1' min='0' name='lvl' class='form-control' value='{$r['ac_level']}'>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Course Length (In Days)
                        </th>
                        <td>
                            <input type='number' required='1' name='day' min='1' class='form-control' value='{$r['ac_days']}'>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Course {$_CONFIG['strength_stat']}
                        </th>
                        <td>
                            <input type='number' required='1' name='str' min='0' class='form-control' value='{$r['ac_str']}'>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Course {$_CONFIG['agility_stat']}
                        </th>
                        <td>
                            <input type='number' required='1' name='agl' min='0' class='form-control' value='{$r['ac_agl']}'>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Course {$_CONFIG['guard_stat']}
                        </th>
                        <td>
                            <input type='number' required='1' name='grd' min='0' class='form-control' value='{$r['ac_grd']}'>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Course Labor
                        </th>
                        <td>
                            <input type='number' required='1' name='lab' min='0' class='form-control' value='{$r['ac_lab']}'>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Course {$_CONFIG['iq_stat']}
                        </th>
                        <td>
                            <input type='number' required='1' name='iq' min='0' class='form-control' value='{$r['ac_iq']}'>
                        </td>
                    </tr>
                    <tr>
                        <td colspan='2'>
                            <input type='submit' value='Edit Course' class='btn btn-primary'>
                        </td>
                    </tr>
                    </table>
                {$csrf}
                </form>";
            break;
        default:
            $csrf = getHtmlCSRF('staff_editacademy1');
            echo "<h3>Edit a Course</h3><hr />
            Please select the academy course you wish to edit.<br />
            <form method='post'>
                <input type='hidden' name='step' value='1'>
                " . dropdownAcademy() . " <br />
                {$csrf}
                <input type='submit' value='Edit Course' class='btn btn-primary'>
            </form>";
            break;
    }
}

$h->endpage();