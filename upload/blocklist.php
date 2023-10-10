<?php
require('globals.php');
echo "
<div class='row'>
    <div class='col-auto'>
        <a href='inbox.php' class='updateHoverBtn btn btn-primary btn-block'><i class='fas fa-fw fa-inbox'></i> Inbox</a>
        <br />
    </div>
    <div class='col-auto'>
        <a href='inbox.php?action=outbox' class='updateHoverBtn btn btn-warning btn-block'><i class='fas fa-fw fa-envelope'></i> Outbox</a>
        <br />
    </div>
    <div class='col-auto'>
        <a href='inbox.php?action=compose' class='updateHoverBtn btn btn-success btn-block'><i class='fas fa-fw fa-file'></i> Compose</a>
        <br />
    </div>
    <div class='col-auto'>
        <a href='blocklist.php' class='updateHoverBtn btn btn-secondary btn-block'><i class='fas fa-fw fa-ban'></i> Blocklist</a>
        <br />
    </div>
    <div class='col-auto'>
        <a href='inbox.php?action=delall' class='updateHoverBtn btn btn-danger btn-block'><i class='fas fa-fw fa-trash-alt'></i> Delete All</a>
        <br />
    </div>
    <div class='col-auto'>
        <a href='contacts.php' class='updateHoverBtn btn btn-info btn-block'><i class='fas fa-fw fa-address-book'></i> Contacts</a>
        <br />
    </div>
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
    echo "<a href='?action=add' class='updateHoverBtn'>Block Player</a><br />
	These are the players you've added to your block list.
	<br />";
    $q = $db->query("/*qc=on*/SELECT `b`.*, `u`.* FROM `blocklist` AS `b`
                     LEFT JOIN `users` AS `u` ON `b`.`blocked` = `u`.`userid` WHERE `b`.`blocker` = {$userid}
                     ORDER BY `u`.`username` ASC");
    //List the user's contact list.
    while ($r = $db->fetch_row($q)) 
	{
        $r['username'] = parseUsername($r['userid']);
		echo "
			<div class='card'>
				<div class='card-header bg-transparent'>
					<div class='row'>
						<div class='col'>
							<a href='profile.php?user={$r['userid']}'>{$r['username']}</a> [{$r['userid']}]
						</div>
						<div class='col'>
							<a href='?action=remove&user={$r['block_id']}'>Unblock</a>
						</div>
					</div>
				</div>
			</div>";
    }
    $db->free_result($q);
}

function add()
{
    global $db, $userid, $api;
    //User has specifed someone to add to contact list.
    if (isset($_POST['user'])) {
        $_POST['user'] = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs($_POST['user']) : '';
        $qc = $db->query("/*qc=on*/SELECT COUNT(`block_id`) FROM `blocklist` WHERE `blocker` = {$userid} AND `blocked` = {$_POST['user']}");
        $dupe_count = $db->fetch_single($qc);
        $db->free_result($qc);
        $q = $db->query("/*qc=on*/SELECT `username` FROM `users` WHERE `userid` = {$_POST['user']}");
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
			    to your block list.", false);
				home();
        }
    } else {
        if (!isset($_GET['user'])) {
            $_GET['user'] = $userid;
        } else {
            $_POST['user'] = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs($_POST['user']) : '';
        }
		echo "
		<form action='?action=add' method='post'>
		<div class='card'>
			<div class='card-header bg-transparent'>
				<div class='row'>
					<div class='col-md-2 col-sm-3 col-6'>
						<b>Blocked</b>
					</div>
					<div class='col-md-10 col-sm-9'>
						" . user_dropdown('user',$_GET['user']) . "
					</div>
				</div>
				<br />
				<div class='row'>
					<div class='col'>
						<input type='submit' value='Block Player' class='btn btn-primary btn-block'>
					</div>
				</div>
			</div>
		</div>
		</form>";
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
    $qc = $db->query("/*qc=on*/SELECT COUNT(`block_id`) FROM `blocklist` WHERE `blocker` = {$userid} AND `block_id` = {$_GET['user']}");
    $exist_count = $db->fetch_single($qc);
    $db->free_result($qc);
    //Specified person is not on list.
    if ($exist_count == 0) {
        alert('danger', "Uh Oh!", "This user is not on your block list.", true, 'blocklist.php');
        die($h->endpage());
    }
    //Remove from list.
    $db->query("DELETE FROM `blocklist` WHERE `block_id` = {$_GET['user']} AND `blocker` = {$userid}");
    alert('success', "Success!", "You have successfully removed this player from your block list.", false);
	home();
}

$h->endpage();