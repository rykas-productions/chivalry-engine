<?php
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
    echo "<a href='?action=add'>Block Player</a><br />
	These are the players you've added to your block list.
	<br />
	<table class='table table-bordered table-striped'>
		<tr>
			<th>
				User
			</th>
			<th>
				Remove
			</th>
		</tr>";
    $q = $db->query("SELECT `b`.*, `u`.* FROM `blocklist` AS `b`
                     LEFT JOIN `users` AS `u` ON `b`.`blocked` = `u`.`userid` WHERE `b`.`blocker` = {$userid}
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
				<a href='?action=remove&user={$r['block_id']}'>Unblock</a>
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
        $qc = $db->query("SELECT COUNT(`block_id`) FROM `blocklist` WHERE `blocker` = {$userid} AND `blocked` = {$_POST['user']}");
        $dupe_count = $db->fetch_single($qc);
        $db->free_result($qc);
        $q = $db->query("SELECT `username` FROM `users` WHERE `userid` = {$_POST['user']}");
        //Person specifed already on contact list.
        if ($dupe_count > 0) {
            alert('danger', "Uh Oh!", "You already have this user on your block list.");
        } //Person specifed is the current user.
        else if ($userid == $_POST['user']) {
            alert('danger', "Uh Oh!", "You cannot add yourself to your block list.");
        } //Person specifed does not exist.
        else if ($db->num_rows($q) == 0) {
            $db->free_result($q);
            alert('danger', "Uh Oh!", "User you wish to add to your block list does not exist.");
        }
		else if ($api->UserMemberLevelGet($_POST['user'], 'forum moderator'))
		{
			alert('danger', "Uh Oh!", "You cannot block staff members.");
		}
		//Person is added to contacts list.
        else {
            $db->query("INSERT INTO `blocklist` VALUES (NULL, {$_POST['user']}, {$userid})");
            $db->free_result($q);
            alert('success', "Success!", "You have successfully added " . $api->SystemUserIDtoName($_POST['user']) . "
			    to your block list.", true, 'blocklist.php');
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
				Blocking a user...
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
					<input type='submit' value='Block User' class='btn btn-primary'>
				</td>
			</tr>
		</form>
		</table>";
    }
}

function remove()
{
    global $db, $userid, $h;
    $_GET['user'] = (isset($_GET['user']) && is_numeric($_GET['user'])) ? abs($_GET['user']) : '';
    //User is trying to remove someone from contact list, but didn't specify their ID.
    if (empty($_GET['user'])) {
        alert('danger', "Uh Oh!", "You must specify a contact you wish to remove.", true, 'blocklist.php');
        die($h->endpage());
    }
    $qc = $db->query("SELECT COUNT(`block_id`) FROM `blocklist` WHERE `blocker` = {$userid} AND `block_id` = {$_GET['user']}");
    $exist_count = $db->fetch_single($qc);
    $db->free_result($qc);
    //Specified person is not on list.
    if ($exist_count == 0) {
        alert('danger', "Uh Oh!", "This user is not on your block list.", true, 'blocklist.php');
        die($h->endpage());
    }
    //Remove from list.
    $db->query("DELETE FROM `blocklist` WHERE `block_id` = {$_GET['user']} AND `blocker` = {$userid}");
    alert('success', "Success!", "You have successfully removed this player from your block list.", true, 'blocklist.php');
}

$h->endpage();