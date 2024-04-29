<?php
/*
	File: staff/staff_towns.php
	Created: 4/4/2017 at 7:05PM Eastern Time
	Info: Staff panel for creating/editing/deleting in-game towns.
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine/
*/
require('sglobals.php');
if ($api->UserMemberLevelGet($userid, 'Admin') == false) {
    alert('danger', "Uh Oh!", "You do not have permission to be here.");
    die($h->endpage());
}
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case "addtown":
        addtown();
        break;
    case "edittown":
        edittown();
        break;
    case "deltown":
        deltown();
        break;
    default:
        alert('danger', "Uh Oh!", "Please select a valid action to perform.", true, 'index.php');
        die($h->endpage());
        break;
}
function addtown()
{
    global $db, $userid, $h, $api;
    if (isset($_POST['name'])) {
        $level = (isset($_POST['minlevel']) && is_numeric($_POST['minlevel'])) ? abs(intval($_POST['minlevel'])) : 1;
        $name = (isset($_POST['name']) && is_string($_POST['name'])) ? $db->escape(htmlentities($_POST['name'])) : '';
        $tax = (isset($_POST['tax']) && is_numeric($_POST['tax'])) ? abs(intval($_POST['tax'])) : 0;
        $desc = (isset($_POST['desc'])) ? $db->escape(strip_tags(stripslashes($_POST['desc']))) : '';
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_addtown', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "This action has been blocked for your security. Please submit the form quickly");
            die($h->endpage());
        }
        if (empty($name) || (!isset($name)))
        {
            alert('danger', "Uh Oh!", "Please fill out the town name correctly.");
            die($h->endpage());
        }
        $q = $db->query("/*qc=on*/SELECT COUNT(`town_id`) FROM `town` WHERE `town_name` = '{$name}'");
        if ($db->fetch_single($q) > 0) {
            $db->free_result($q);
            alert('danger', "Uh Oh!", "The town name you've chosen is already in use.");
            die($h->endpage());
        }
        if (empty($desc) || (!isset($desc)))
        {
            alert('danger', "Action Blocked!", "Please input a valid town description.");
            die($h->endpage());
        }
        if ($tax < 0 || $tax > 20) {
            alert('danger', "Uh Oh!", "Tax levels can only be between 0-20%.");
            die($h->endpage());
        }
        if ($level < 0) {
            alert('danger', "Uh Oh!", "Please specify a minimum level requirement.");
            die($h->endpage());
        }
        $db->free_result($q);
        $db->query("INSERT INTO `town` (`town_name`, `town_min_level`, `town_guild_owner`, `town_tax`, `town_desc`) VALUES ('{$name}', '{$level}', '0', '{$tax}', '{$desc}');");
        $api->SystemLogsAdd($userid, 'staff', "Created a town named {$name}.");
        alert('success', "Success!", "You have successfully created the town: <b>{$name}</b>", true, 'index.php');
    } else {
        $csrf = request_csrf_html('staff_addtown');
        echo "  <form action='?action=addtown' method='post'>
                <div class='card'>
                    <div class='card-header'>
                        Create town
                    </div>
                    <div class='card-body'>
                        <div class='row'>
                            <div class='col-12'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small><b>Town Name</b></small>
                                    </div>
                                    <div class='col-12'>
                                        <input type='text' name='name' required='1' class='form-control'>
                                    </div>
                                </div>
                            </div>
                            <div class='col-12'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small><b>Town Description</b></small>
                                    </div>
                                    <div class='col-12'>
                                        <textarea name='desc' required='1' class='form-control'></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class='col-12 col-sm-6'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small><b>Level Requirement</b></small>
                                    </div>
                                    <div class='col-12'>
                                        <input type='number' name='minlevel' min='1' required='1' class='form-control'>
                                    </div>
                                </div>
                            </div>
                            <div class='col-12 col-sm-6'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small><b>Tax Level</b></small>
                                    </div>
                                    <div class='col-12'>
                                        <input type='number' name='tax' min='0' max='20' required='1' class='form-control'>
                                    </div>
                                </div>
                            </div>
                            <div class='col-12'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small><b>Confirm?</b></small>
                                    </div>
                                    <div class='col-12'>
                                        <input type='submit' class='btn btn-primary' value='Create Town'>
                                    </div>
                                </div>
                            </div>
                            {$csrf}
                        </div>
                    </div>
                </div></form>";
    }
}

function deltown()
{
    global $db, $userid, $api, $h;
    if (isset($_POST['town'])) {
        $town = (isset($_POST['town']) && is_numeric($_POST['town'])) ? abs(intval($_POST['town'])) : 0;
        $q = $db->query("/*qc=on*/SELECT `town_id`, `town_name` FROM `town` WHERE `town_id` = {$town}");
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_deltown', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "This action has been blocked for your security. Please submit the form quickly");
            die($h->endpage());
        }
        if ($db->num_rows($q) == 0) {
            $db->free_result($q);
            alert('danger', "Uh Oh!", "You are trying to remove a town that doesn't exist.");
            die($h->endpage());
        }
        $old = $db->fetch_row($q);
        $db->free_result($q);
        if ($old['town_id'] == 1) {
            alert('danger', "Uh Oh!", "You cannot delete the starter town.");
            die($h->endpage());
        }
        $db->query("UPDATE `users` SET `location` = 1 WHERE `location` = {$old['town_id']}");
        $db->query("UPDATE `shops` SET `shopLOCATION` = 1 WHERE `shopLOCATION` = {$old['town_id']}");
        $db->query("DELETE FROM `town` WHERE `town_id` = {$old['town_id']}");
        alert('success', "Success!", "You have successfully removed the {$old['town_name']} town from the game.", true, 'index.php');
        $api->SystemLogsAdd($userid, 'staff', "Deleted the town called {$old['town_name']}.");
    } else {
        $csrf = request_csrf_html('staff_deltown');
        echo "  <form method='post'>
                <div class='card'>
                    <div class='card-header'>
                        Delete town
                    </div>
                    <div class='card-body'>
                        <div class='row'>
                            <div class='col-12 col-md-8 col-lg-9 col-xl-10'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small><b>Select Town</b></small>
                                    </div>
                                    <div class='col-12'>
                                        " . location_dropdown("town") . "
                                    </div>
                                </div>
                            </div>
                            <div class='col-12 col-md'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small><b>Action</b></small>
                                    </div>
                                    <div class='col-12'>
                                        <input type='submit' class='btn btn-primary' value='Delete Town'>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {$csrf}
                </form>";
    }
}

function edittown()
{
    global $api, $userid, $db, $h;
    if (!isset($_POST['step'])) {
        $_POST['step'] = '0';
    }
    switch ($_POST['step']) {
        case 2:
            $level = (isset($_POST['minlevel']) && is_numeric($_POST['minlevel'])) ? abs(intval($_POST['minlevel'])) : 1;
            $name = (isset($_POST['name']) && is_string($_POST['name'])) ? $db->escape(htmlentities($_POST['name'])) : '';
            $tax = (isset($_POST['tax']) && is_numeric($_POST['tax'])) ? abs(intval($_POST['tax'])) : 0;
            $id = (isset($_POST['id']) && is_numeric($_POST['id'])) ? abs(intval($_POST['id'])) : 0;
            $desc = (isset($_POST['desc'])) ? $db->escape(strip_tags(stripslashes($_POST['desc']))) : '';
            $q = $db->query("/*qc=on*/SELECT * FROM `town` WHERE `town_id` = {$id}");
            if ($db->num_rows($q) == 0) {
                $db->free_result($q);
                alert("danger", "Uh Oh!", "The town you are wishing to edit does not exist, or is invalid.");
                die($h->endpage());
            }
            if (!isset($_POST['verf']) || !verify_csrf_code('staff_edittown2', stripslashes($_POST['verf']))) {
                alert('danger', "Action Blocked!", "This action has been blocked for your security. Please submit the form quickly");
                die($h->endpage());
            }
            if (empty($name) || (!isset($name)))
            {
                alert('danger', "Uh Oh!", "Please fill out the town name correctly.");
                die($h->endpage());
            }
            if (empty($desc) || (!isset($desc)))
            {
                alert('danger', "Action Blocked!", "Please input a valid town description.");
                die($h->endpage());
            }
            $q = $db->query("/*qc=on*/SELECT COUNT(`town_id`) FROM `town` WHERE `town_name` = '{$name}' && `town_id` != {$id}");
            if ($db->fetch_single($q) > 0) {
                $db->free_result($q);
                alert('danger', "Uh Oh!", "The town name you've chosen is already in use.");
                die($h->endpage());
            }
            if ($tax < 0 || $tax > 20) {
                alert('danger', "Uh Oh!", "Tax levels can only be between 0-20%.");
                die($h->endpage());
            }
            if ($level < 0) {
                alert('danger', "Uh Oh!", "Please specify a minimum level requirement.");
                die($h->endpage());
            }
            $db->free_result($q);
            $db->query("UPDATE `town`
                        SET `town_name` = '{$name}', 
                            `town_min_level` = {$level}, 
                            `town_tax` = {$tax},
                            `town_desc` = '{$desc}'
                        WHERE `town_id` = {$id}");
            alert("success", "Success!", "You have successfully edited the {$name} town.", true, 'index.php');
            $api->SystemLogsAdd($userid, 'staff', "Edited the {$name} town.");
            break;
        case 1:
            $_POST['location'] = (isset($_POST['location']) && is_numeric($_POST['location'])) ? abs(intval($_POST['location'])) : 0;
            $q = $db->query("/*qc=on*/SELECT * FROM `town` WHERE `town_id` = {$_POST['location']}");
            if ($db->num_rows($q) == 0) {
                $db->free_result($q);
                alert("danger", "Uh Oh!", "The town you are wishing to edit does not exist, or is invalid.");
                die($h->endpage());
            }
            if (!isset($_POST['verf']) || !verify_csrf_code('staff_edittown1', stripslashes($_POST['verf']))) {
                alert('danger', "Action Blocked!", "Forms expire fairly quickly. Go back and submit it quicker!");
                die($h->endpage());
            }
            $r = $db->fetch_row($q);
            $csrf = request_csrf_html('staff_edittown2');
            echo "  <form method='post'>
                <div class='card'>
                    <div class='card-header'>
                        Create town
                    </div>
                    <div class='card-body'>
                        <div class='row'>
                            <div class='col-12'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small><b>Town Name</b></small>
                                    </div>
                                    <div class='col-12'>
                                        <input type='text' name='name' required='1' value='{$r['town_name']}' class='form-control'>
                                    </div>
                                </div>
                            </div>
                            <div class='col-12'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small><b>Town Description</b></small>
                                    </div>
                                    <div class='col-12'>
                                        <textarea name='desc' required='1' class='form-control'>{$r['town_desc']}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class='col-12 col-sm-6'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small><b>Level Requirement</b></small>
                                    </div>
                                    <div class='col-12'>
                                        <input type='number' name='minlevel' min='1' required='1' value='{$r['town_min_level']}' class='form-control'>
                                    </div>
                                </div>
                            </div>
                            <div class='col-12 col-sm-6'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small><b>Tax Level</b></small>
                                    </div>
                                    <div class='col-12'>
                                        <input type='number' name='tax' min='0' max='20' required='1' value='{$r['town_tax']}' class='form-control'>
                                    </div>
                                </div>
                            </div>
                            <div class='col-12'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small><b>Confirm?</b></small>
                                    </div>
                                    <div class='col-12'>
                                        <input type='submit' class='btn btn-primary' value='Edit Town'>
                                    </div>
                                </div>
                            </div>
                            {$csrf}
                        </div>
                    </div>
                </div>
                <input type='hidden' name='step' value='2' />
        	    <input type='hidden' name='id' value='{$_POST['location']}' />
                </form>";
            break;
        default:
            $csrf = request_csrf_html('staff_edittown1');
            echo "  <form method='post'>
                <div class='card'>
                    <div class='card-header'>
                        Edit town
                    </div>
                    <div class='card-body'>
                        <div class='row'>
                            <div class='col-12 col-md-8 col-lg-9 col-xl-10'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small><b>Select Town</b></small>
                                    </div>
                                    <div class='col-12'>
                                        " . location_dropdown("location") . "
                                    </div>
                                </div>
                            </div>
                            <div class='col-12 col-md'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small><b>Action</b></small>
                                    </div>
                                    <div class='col-12'>
                                        <input type='submit' value='Edit Town' class='btn btn-primary'>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {$csrf}
                <input type='hidden' name='step' value='1'>
                </form>";
            break;
    }
}

$h->endpage();
