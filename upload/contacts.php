<?php
/*
	File:		contacts.php
	Created: 	4/4/2016 at 11:55PM Eastern Time
	Info: 		Allows players to add and delete users from their contact list.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require('globals.php');
echo "
<table class='table table-bordered'>
	<tr>
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
			<a href='blocklist.php'>Blocklist</a>
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
	</tr>
</table>";
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
	<table class='table table-bordered table-striped'>
		<tr>
			<th>
				User
			</th>
			<th>
				Message
			</th>
			<th>
				Remove
			</th>
		</tr>";
    $q = $db->query("SELECT `c`.`c_ID`, `u`.`vip_days`, `username`, `userid` FROM `contact_list` AS `c`
                     LEFT JOIN `users` AS `u` ON `c`.`c_ADDED` = `u`.`userid` WHERE `c`.`c_ADDER` = $userid
                     ORDER BY `u`.`username` ASC");
    //List the user's contact list.
    while ($r = $db->fetch_row($q)) {
        $r['username'] = ($r['vip_days']) ? "<span class='text-danger'>{$r['username']} <i class='fa fa-shield'
        data-toggle='tooltip' title='{$r['vip_days']} VIP Days remaining.'></i></span>" : $r['username'];
        echo "
		<tr>
			<td>
				<a href='profile.php?user={$r['userid']}'>{$r['username']}</a> [{$r['userid']}]
			</td>
			<td>
				<a href='inbox.php?action=compose&user={$r['userid']}'>Message</a>
			</td>
			<td>
				<a href='?action=remove&contact={$r['c_ID']}'>Remove Contact</a>
			</td>
		</tr>";
    }
    $db->free_result($q);
    echo '</table>';
}

function add()
{
    global $db, $userid, $api;
    //User has specifed someone to add to contact list.
    if (isset($_POST['user'])) {
        $_POST['user'] = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs($_POST['user']) : '';
        $qc = $db->query("SELECT COUNT(`c_ADDER`) FROM `contact_list` WHERE `c_ADDER` = {$userid} AND `c_ADDED` = {$_POST['user']}");
        $dupe_count = $db->fetch_single($qc);
        $db->free_result($qc);
        $q = $db->query("SELECT `username` FROM `users` WHERE `userid` = {$_POST['user']}");
        //Person specifed already on contact list.
        if ($dupe_count > 0) {
            alert('danger', "Uh Oh!", "You already have this user on your contact list.");
        } //Person specifed is the current user.
        else if ($userid == $_POST['user']) {
            alert('danger', "Uh Oh!", "You cannot add yourself to your contact list.");
        } //Person specifed does not exist.
        else if ($db->num_rows($q) == 0) {
            $db->free_result($q);
            alert('danger', "Uh Oh!", "User you wish to add does not exist.");
        } //Person is added to contacts list.
        else {
            $db->query("INSERT INTO `contact_list` VALUES (NULL, {$_POST['user']}, {$userid})");
            $db->free_result($q);
            alert('success', "Success!", "You have successfully added " . $api->SystemUserIDtoName($_POST['user']) . "
			    to your contact list.", true, 'contacts.php');
        }
    } else {
        if (!isset($_GET['user'])) {
            $_GET['user'] = $userid;
        } else {
            $_POST['user'] = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs($_POST['user']) : '';
        }
        echo "<table class='table table-bordered'>
		<form action='?action=add' method='post'>
			<tr>
				<th colspan='2'>
				Add Contact Form
				</th>
			</tr>
			<tr>
				<th>
					Select User
				</th>
				<td>
					" . user_dropdown('user',$_GET['user']) . "
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' value='Add Contact' class='btn btn-primary'>
				</td>
			</tr>
		</form>
		</table>";
    }
}

function remove()
{
    global $db, $userid, $h;
    $_GET['contact'] = (isset($_GET['contact']) && is_numeric($_GET['contact'])) ? abs($_GET['contact']) : '';
    //User is trying to remove someone from contact list, but didn't specify their ID.
    if (empty($_GET['contact'])) {
        alert('danger', "Uh Oh!", "You must specify a contact you wish to remove.", true, 'contacts.php');
        die($h->endpage());
    }
    $qc = $db->query("SELECT COUNT(`c_ADDER`) FROM `contact_list` WHERE `c_ADDER` = {$userid} AND `c_ID` = {$_GET['contact']}");
    $exist_count = $db->fetch_single($qc);
    $db->free_result($qc);
    //Specified person is not on list.
    if ($exist_count == 0) {
        alert('danger', "Uh Oh!", "This contact is not on your list.", true, 'contacts.php');
        die($h->endpage());
    }
    //Remove from list.
    $db->query("DELETE FROM `contact_list` WHERE `c_ID` = {$_GET['contact']} AND `c_ADDER` = {$userid}");
    alert('success', "Success!", "You have successfully removed this user from your contact list.", true, 'contacts.php');
}

$h->endpage();