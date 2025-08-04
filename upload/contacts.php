<?php
/*
	File:		contacts.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Allows a player to add or remove players from their contact list.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
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
require('globals.php');
echo "
<div class='table-responsive'>
<table class='table table-bordered'>
	<div class='row'>
		<td>
			<a href='inbox.php'>Inbox</a>
		</td>
		<td>
			<a href='inbox.php?action=outbox'>Outbox</a>
		</td>
		<td>
			<a href='inbox.php?action=compose'>Compose</a>
		</td>
		<td>
			<a href='inbox.php?action=delall'>Delete All</a>
		</td>
		<td>
			<a href='inbox.php?action=archive'>Archive</a>
		</td>
		<td>
			<a href='contacts.php'>Contacts</a>
		</td>
	</div>
</table>
</div>";
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case "add":
        add();
        break;
    case "remove":
        remove();
        break;
    default:
        home();
        break;
}
function home()
{
    global $db, $userid;
    echo "<a href='?action=add'>Add Contact</a><br />
	These are the players you've added to your contact list.
	<br />
	<div class='container'>
	    <div class='row'>
			<div class='col-sm'>
				<h4>User</h4>
			</div>
			<div class='col-sm'>
				<h4>Message</h4>
			</div>
			<div class='col-sm'>
				<h4>Remove</h4>
			</div>
		</div>
		<hr />";
    $q = $db->query("SELECT `c`.`c_ID`, `u`.`vip_days`, `username`, `userid` FROM `contact_list` AS `c`
                     LEFT JOIN `users` AS `u` ON `c`.`c_ADDED` = `u`.`userid` WHERE `c`.`c_ADDER` = $userid
                     ORDER BY `u`.`username` ASC");
    //List the user's contact list.
    while ($r = $db->fetch_row($q)) {
        $d = '';
        $r['username'] = ($r['vip_days']) ? "<span class='text-danger'>{$r['username']}</span>
                        <span class='glyphicon glyphicon-star' data-toggle='tooltip'
                        title='{$r['username']} has {$r['vip_days']} VIP Days remaining.'></span>" : $r['username'];
        echo "
		<div class='row'>
			<div class='col-sm'>
				<a href='profile.php?user={$r['userid']}'>{$r['username']}</a> [{$r['userid']}]
			</div>
			<div class='col-sm'>
				<a href='inbox.php?action=compose&user={$r['userid']}'>Message</a>
			</div>
			<div class='col-sm'>
				<a href='?action=remove&contact={$r['c_ID']}'>Remove Contact</a>
			</div>
		</div>";
    }
    $db->free_result($q);
    echo '</div>';
}

function add()
{
    global $db, $userid, $api;
    //User has specifed someone to add to contact list.
    if (isset($_POST['user'])) {
		$user = filter_input(INPUT_POST, 'user', FILTER_SANITIZE_NUMBER_INT) ?: 0;
        $qc = $db->query("SELECT COUNT(`c_ADDER`) FROM `contact_list` WHERE `c_ADDER` = {$userid} AND `c_ADDED` = {$user}");
        $dupe_count = $db->fetch_single($qc);
        $db->free_result($qc);
        $q = $db->query("SELECT `username` FROM `users` WHERE `userid` = {$user}");
        //Person specifed already on contact list.
        if ($dupe_count > 0) {
            alert('danger', "Uh Oh!", "You already have this user on your contact list.");
        } //Person specifed is the current user.
        else if ($userid == $user) {
            alert('danger', "Uh Oh!", "You cannot add yourself to your contact list.");
        } //Person specifed does not exist.
        else if ($db->num_rows($q) == 0) {
            $db->free_result($q);
            alert('danger', "Uh Oh!", "User you wish to add does not exist.");
        } //Person is added to contacts list.
        else {
            $db->query("INSERT INTO `contact_list` VALUES (NULL, {$user}, {$userid})");
            $db->free_result($q);
            alert('success', "Success!", "You have successfully added " . $api->user->getNameFromID($user) . "
			    to your contact list.", true, 'contacts.php');
        }
    } else {
        if (!isset($_GET['user'])) {
            $_GET['user'] = $userid;
        } else {
            $user = (isset($user) && is_numeric($user)) ? abs($user) : '';
        }
        echo "<form action='?action=add' method='post'>
            <div class='cotainer'>
                <div class='row'>
                        <div class='row'>
                            <div class='col-sm-12'>
                                <h4>Add Contact Form</h4>
                            </div>
                        </div>
                </div>
                <hr />
                <div class='row'>
                    <div class='col-sm'>
                        Enter a User ID
                    </div>
                    <div class='col-sm'>
                        <input type='number' class='form-control' required='1' min='1' name='user' value='{$_GET['user']}'>
                    </div>
                </div>
                <hr />
                <div class='row'>
                    <div class='col-sm'>
                        <input type='submit' value='Add Contact' class='btn btn-primary'>
                    </div>
                </div>
                    </form>
		        </div>
		    </div>";
    }
}

function remove()
{
    global $db, $userid, $h;
	$contact = filter_input(INPUT_GET, 'contact', FILTER_SANITIZE_NUMBER_INT) ?: 0;
    //User is trying to remove someone from contact list, but didn't specify their ID.
    if (empty($contact)) {
        alert('danger', "Uh Oh!", "You must specify a contact you wish to remove.", true, 'contacts.php');
        die($h->endpage());
    }
    $qc = $db->query("SELECT COUNT(`c_ADDER`) FROM `contact_list` WHERE `c_ADDER` = {$userid} AND `c_ID` = {$contact}");
    $exist_count = $db->fetch_single($qc);
    $db->free_result($qc);
    //Specified person is not on list.
    if ($exist_count == 0) {
        alert('danger', "Uh Oh!", "This contact is not on your list.", true, 'contacts.php');
        die($h->endpage());
    }
    //Remove from list.
    $db->query("DELETE FROM `contact_list` WHERE `c_ID` = {$contact} AND `c_ADDER` = {$userid}");
    alert('success', "Success!", "You have successfully removed this user from your contact list.", true, 'contacts.php');
}

$h->endpage();