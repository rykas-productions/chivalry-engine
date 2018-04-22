<?php
/*
	File: staff/staff_forums.php
	Created: 4/4/2017 at 7:02PM Eastern Time
	Info: Staff panel for handling/editing/creating the in-game forums.
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine/
*/
require('sglobals.php');
if (!$api->UserMemberLevelGet($userid,'forum moderator')) {
    alert('danger', "Uh Oh!", "You do not have permission to be here.", true, 'index.php');
    die($h->endpage());
}
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case "addforum":
        addforum();
        break;
    case "editforum":
        editforum();
        break;
    case "delforum":
        delforum();
        break;
    default:
        alert('danger', "Uh Oh!", "Please select a valid action to perform.", true, 'index.php');
        die($h->endpage());
        break;
}
function addforum()
{
    global $h, $db, $userid, $api;
    if (!isset($_POST['name'])) {
        $csrf = request_csrf_html('staff_addforum');
        echo "
        <h3>Add Forum Category</h3>
        <hr />
		<form method='post'>
			<table class='table table-bordered '>
				<tr>
					<th width='33%'>
						Category Name
					</th>
					<td>
						<input type='text' required='1' name='name' class='form-control'>
					</td>
				</tr>
				<tr>
					<th>
						Description
					</th>
					<td>
						<input type='text' required='1' name='desc' class='form-control'>
					</td>
				</tr>
				<tr>
					<th>
						Authorization
					</th>
					<td>
						 <select name='auth' required='1' class='form-control' type='dropdown'>
							<option value='public'>Public</option>
							<option value='staff'>Staff-Only</option>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' value='Create Category' class='btn btn-primary'>
					</td>
				</tr>
				{$csrf}
			</table>
        </form>";
    } else {
        $name = (isset($_POST['name']) && preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i", $_POST['name'])) ? $db->escape(strip_tags(stripslashes($_POST['name']))) : '';
        $desc = (isset($_POST['desc'])) ? $db->escape(strip_tags(stripslashes($_POST['desc']))) : '';
        $auth = (isset($_POST['auth']) && in_array($_POST['auth'], array('staff', 'public'), true)) ? $_POST['auth'] : 'public';
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_addforum', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly after opening them. Please submit the form as quickly as possible next time.");
            die($h->endpage());
        }
        if (empty($name)) {
            alert('danger', "Uh Oh!", "Please specify a category name.");
            die($h->endpage());
        } elseif (empty($name)) {
            alert('danger', "Uh Oh!", "Please specify a category description.");
            die($h->endpage());
        } else {
            $q = $db->query("/*qc=on*/SELECT COUNT(`ff_id`) FROM `forum_forums` WHERE `ff_name` = '{$name}'");
            if ($db->fetch_single($q)) {
                $db->free_result($q);
                alert('danger', "Uh Oh!", "The category name is already in used.");
                die($h->endpage());
            }
            $db->free_result($q);
            $db->query("INSERT INTO `forum_forums` (`ff_name`, `ff_desc`, `ff_lp_t_id`, `ff_lp_poster_id`, `ff_auth`, `ff_lp_time`)
			VALUES ('{$name}', '{$desc}', '0', '0', '{$auth}', '0');");
            alert('success', "Success!", "You have successfully created a {$auth} forum category called {$name}.", true, 'index.php');
            $api->SystemLogsAdd($userid, 'staff', "Created a {$auth} Forum Category called {$name}.");
        }
    }
}

function editforum()
{
    global $db, $h, $userid, $api;
    echo "<h3>Edit Category</h3><hr />";
    if (!isset($_POST['step'])) {
        $_POST['step'] = '0';
    }
    switch ($_POST['step']) {
        case "2":
            $name = (isset($_POST['name']) && preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i", $_POST['name'])) ? $db->escape(strip_tags(stripslashes($_POST['name']))) : '';
            $desc = (isset($_POST['desc'])) ? $db->escape(strip_tags(stripslashes($_POST['desc']))) : '';
            $auth = (isset($_POST['auth']) && in_array($_POST['auth'], array('staff', 'public'))) ? $_POST['auth'] : 'public';
            $_POST['id'] = (isset($_POST['id']) && is_numeric($_POST['id'])) ? abs(intval($_POST['id'])) : '';
            if (empty($_POST['id']) || empty($name) || empty($desc)) {
                alert('danger', "Uh Oh!", "Please fill out the previous form completely before submitting.");
                die($h->endpage());
            }
            if (!isset($_POST['verf']) || !verify_csrf_code('staff_editforum2', stripslashes($_POST['verf']))) {
                alert('danger', "Action Blocked!", "Forms expire fairly quickly after opening them. Please submit the form as quickly as possible next time.");
                die($h->endpage());
            }
            $q = $db->query("/*qc=on*/SELECT COUNT(`ff_id`) FROM `forum_forums` WHERE `ff_name` = '{$name}' AND `ff_id` != {$_POST['id']}");
            if ($db->fetch_single($q) > 0) {
                $db->free_result($q);
                alert('danger', "Uh Oh!", "The name you've chosen is already in use by another category.");
                die($h->endpage());
            }
            $db->free_result($q);
            $q = $db->query("/*qc=on*/SELECT COUNT(`ff_id`)  FROM `forum_forums` WHERE `ff_id` = {$_POST['id']}");
            if ($db->fetch_single($q) == 0) {
                $db->free_result($q);
                alert('danger', "Uh Oh!", "You are trying to edit a non-existent forum category.");
                die($h->endpage());
            }
            $db->free_result($q);
            $db->query("UPDATE `forum_forums` SET `ff_desc` = '$desc', `ff_name` = '$name', `ff_auth` = '$auth' WHERE `ff_id` = {$_POST['id']}");
            alert('success', "Success!", "You have successfully edited the {$name} forum category.", true, 'index.php');
            $api->SystemLogsAdd($userid, 'staff', "Edited forum {$name}");
            break;
        case "1":
            $_POST['id'] = (isset($_POST['id']) && is_numeric($_POST['id'])) ? abs(intval($_POST['id'])) : '';
            if (empty($_POST['id'])) {
                alert('danger', "Uh Oh!", "Please specify the forum category you wish to edit.");
                die($h->endpage());
            }
            if (!isset($_POST['verf']) || !verify_csrf_code('staff_editforum1', stripslashes($_POST['verf']))) {
                alert('danger', "Action Blocked!", "Forms expire fairly quickly after opening them. Please submit the form as quickly as possible next time.");
                die($h->endpage());
            }
            $q = $db->query("/*qc=on*/SELECT `ff_auth`, `ff_name`, `ff_desc`  FROM `forum_forums` WHERE `ff_id` = {$_POST['id']}");
            if ($db->num_rows($q) == 0) {
                $db->free_result($q);
                alert('danger', "Uh Oh!", "You are trying to edit a non-existent forum category.");
                die($h->endpage());
            }
            $old = $db->fetch_row($q);
            $db->free_result($q);
            $check_p = ($old['ff_auth'] == 'public') ? 'selected' : '';
            $check_s = ($old['ff_auth'] == 'staff') ? 'selected' : '';
            $csrf = request_csrf_html('staff_editforum2');
            echo "
			<form method='post'>
							<input type='hidden' name='step' value='2'>
							<input type='hidden' name='id' value='{$_POST['id']}'>
				<table class='table table-bordered '>
					<tr>
						<th width='33%'>
							Category Name
						</th>
						<td>
							<input type='text' name='name' class='form-control' value='{$old['ff_name']}'>
						</td>
					</tr>
					<tr>
						<th>
							Description
						</th>
						<td>
							<input type='text' name='desc' class='form-control' value='{$old['ff_desc']}'>
						</td>
					</tr>
					<tr>
						<th>
							Authorization
						</th>
						<td>
							<select name='auth' required='1' class='form-control' type='dropdown'>
								<option value='public' {$check_p}>Public</option>
								<option value='staff' {$check_s}>Staff Only</option>
							</select>
						</td>
					</tr>
					<tr>
						<td colspan='2'>
							<input type='submit' class='btn btn-primary' value='Edit Category'>
						</td>
					</tr>
				{$csrf}
				</table>
			</form>";
            break;
        default:
            $csrf = request_csrf_html('staff_editforum1');
            echo "
			<form method='post'>
				<input type='hidden' name='step' value='1' />
				<b>Editing a Category</b> " . forum_dropdown("id") . "<br />
				{$csrf}
				<input type='submit' class='btn btn-primary' value='Edit Category' />
			</form>
			   ";
            break;
    }
}

function delforum()
{
    global $db, $h, $userid, $api;
    echo "<h3>Delete Category</h3><hr />";
    if (!isset($_POST['forum'])) {
        $csrf = request_csrf_html('staff_delforum');
        echo "
		Deleting a category is permanent. The posts and threads inside will also be deleted. Select a category to delete
		<br />
		<form method='post'>
        	<b>Category</b> " . forum_dropdown("forum") . "
        <br />
        	{$csrf}
        	<input type='submit' class='btn btn-primary' value='Delete Category' />
        </form>";
    } else {
        $_POST['forum'] = (isset($_POST['forum']) && is_numeric($_POST['forum'])) ? abs(intval($_POST['forum'])) : '';
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_delforum', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly after opening them. Please submit the form as quickly as possible next time.");
            die($h->endpage());
        }
        $q = $db->query("/*qc=on*/SELECT COUNT(`ff_id`) FROM `forum_forums` WHERE `ff_id` = {$_POST['forum']}");
        if ($db->fetch_single($q) == 0) {
            $db->free_result($q);
            alert('danger', "Uh Oh!", "You are trying to delete a non-existent category.");
            die($h->endpage());
        }
        $db->free_result($q);
        $db->query("DELETE FROM `forum_posts` WHERE `ff_id` = {$_POST['forum']}");
        $db->query("DELETE FROM `forum_topics` WHERE `ft_forum_id` = {$_POST['forum']}");
        $db->query("DELETE FROM `forum_forums` WHERE `ff_id` = {$_POST['forum']}");
        $q = $db->query("/*qc=on*/SELECT `ff_name` FROM `forum_forums`  WHERE `ff_id` = {$_POST['forum']}");
        $old = $db->fetch_single($q);
        $db->free_result($q);
        alert('success', "Success!", "You have successfully deleted the {$old} forum category.", true, 'index.php');
        $api->SystemLogsAdd($userid, 'staff', "Deleted forum {$old}, along with posts and topics posted inside.");
    }
}

$h->endpage();