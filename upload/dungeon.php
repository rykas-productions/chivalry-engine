<?php
/*
	File:		dungeon.php
	Created: 	4/4/2016 at 11:58PM Eastern Time
	Info: 		Lists players currently in the dungeon, and allows players
				to bust or bail them out.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require("globals.php");
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case 'bust':
        bust();
        break;
    case 'bail':
        bail();
        break;
    default:
        home();
        break;
}
function home()
{
    global $db, $h, $api;
    $CurrentTime = time();
    //Count how many users are in the dungeon.
    $PlayerCount = $db->fetch_single($db->query("SELECT COUNT(`dungeon_user`) FROM `dungeon` WHERE `dungeon_out` > {$CurrentTime}"));
    echo "<h3>The Dungeon</h3><hr />
	<small>There's current " . number_format($PlayerCount) . " players in the dungeon.</small>
	<hr />
	<table class='table table-hover table-bordered'>
		<thead>
			<tr>
				<th>
					User
				</th>
				<th>
					Reason
				</th>
				<th class='hidden-xs'>
					Check-in
				</th>
				<th>
					Check-out
				</th>
				<th>
					Actions
				</th>
			</tr>
		</thead>
		<tbody>";
    //List users in the dungeon.
    $query = $db->query("SELECT * FROM `dungeon` WHERE `dungeon_out` > {$CurrentTime} ORDER BY `dungeon_out` DESC");
    while ($Infirmary = $db->fetch_row($query)) {
        echo "
			<tr>
				<td>
					<a href='profile.php?user={$Infirmary['dungeon_user']}'>
						{$api->SystemUserIDtoName($Infirmary['dungeon_user'])}
					</a>
				</td>
				<td>
					{$Infirmary['dungeon_reason']}
				</td>
				<td class='hidden-xs'>
					" . DateTime_Parse($Infirmary['dungeon_in']) . "
				</td>
				<td>
					" . TimeUntil_Parse($Infirmary['dungeon_out']) . "
				</td>
				<td>
					[<a href='?action=bail&user={$Infirmary['dungeon_user']}'>Bail Out</a>]
					[<a href='?action=bust&user={$Infirmary['dungeon_user']}'>Bust Out</a>]
				</td>
			</tr>";
    }
    echo "</tbody></table>";
    $h->endpage();
}

function bail()
{
    global $db, $userid, $ir, $h, $api;
    if (isset($_GET['user'])) {
        $_GET['user'] = (isset($_GET['user']) && is_numeric($_GET['user'])) ? abs($_GET['user']) : 0;
        //Specified user is invalid or empty.
        if (empty($_GET['user']) || $_GET['user'] == 0) {
            alert('danger', "Uh Oh!", "You must select a user you wish to bail out.", true, 'dungeon.php');
            die($h->endpage());
        }
        //Specified user is not in the dungeon.
        if ($api->UserStatus($_GET['user'], 'dungeon') == false) {
            alert('danger', "Uh Oh!", "The user you wish to bail out is not in the dungeon.", true, 'dungeon.php');
            die($h->endpage());
        }
        $cost = 250 * $api->UserInfoGet($_GET['user'], 'level', false);
        //User does not have enough primary currency to bail this user out.
        if ($api->UserHasCurrency($userid, 'primary', $cost) == false) {
            alert('danger', "Uh Oh!", "You do not have enough cash to bail this user out. You need
			    " . number_format($cost) . ".", true, 'dungeon.php');
            die($h->endpage());
        }
        //Person specified is bailed out. Take user's currency, log the action, and tell the person what happened.
        $api->UserTakeCurrency($userid, 'primary', $cost);
        $api->GameAddNotification($_GET['user'], "<a href='profile.php?user={$userid}'>{$ir['username']}</a> has
            successfully bailed you out of the dungeon.");
        alert('success', "Success!", "You have successfully bailed out {$api->SystemUserIDtoName($_GET['user'])}", true, 'dungeon.php');
        $db->query("UPDATE `dungeon` SET `dungeon_out` = 0 WHERE `dungeon_user` = {$_GET['user']}");
        die($h->endpage());
    } else {
        alert('danger', "Uh Oh!", "You must select a person to bail out.", true, 'dungeon.php');
    }
}

function bust()
{
    global $db, $userid, $ir, $h, $api;
    if (isset($_GET['user'])) {
        $_GET['user'] = (isset($_GET['user']) && is_numeric($_GET['user'])) ? abs($_GET['user']) : 0;
        //Person input is invalid or empty.
        if (empty($_GET['user']) || $_GET['user'] == 0) {
            alert('danger', "Uh Oh!", "You must select a person to bust out of the dungeon.", true, 'dungeon.php');
            die($h->endpage());
        }
        //Person not in the dungeon.
        if ($api->UserStatus($_GET['user'], 'dungeon') == false) {
            alert('danger', "Uh Oh!", "This person is not in the dungeon, so you cannot bust them out.", true, 'dungeon.php');
            die($h->endpage());
        }
        //User is in the dungeon.
        if ($api->UserStatus($userid, 'dungeon')) {
            alert('danger', "Uh Oh!", "You are already in the dungeon, so you cannot bust others out.", true, 'dungeon.php');
            die($h->endpage());
        }
        //User does not have 10% brave.
        if ($api->UserInfoGet($userid, 'brave', true) < 10) {
            alert('danger', "Uh Oh!", "You are not brave enough to bust someone out. You need 10 Brave, you only have
			    {$ir['brave']}.", true, 'dungeon.php');
            die($h->endpage());
        }
        //User does not have 25% will.
        if ($api->UserInfoGet($userid, 'will', true) < 25) {
            alert('danger', "Uh Oh!", "You do not have enough will to bust someone out. You need at least 25%.", true, 'dungeon.php');
            die($h->endpage());
        }
        //Update user's info.
        $api->UserInfoSet($userid, 'will', -25, true);
        $api->UserInfoSet($userid, 'brave', -10, true);
        $mult = $api->UserInfoGet($_GET['user'], 'level') * $api->UserInfoGet($_GET['user'], 'level');
        $chance = min(($ir['level'] / $mult) * 50 + 1, 95);
        //User is successful.
        if (Random(1, 100) < $chance) {
            //Add notification, and tell the user.
            $api->GameAddNotification($_GET['user'], "<a href='profile.php?user={$userid}'>{$ir['username']}</a> has
                successfully busted you out of the dungeon.");
            alert('success', "Success!", "You have successfully busted them out of the dungeon.", true, 'dungeon.php');
            $db->query("UPDATE `dungeon` SET `dungeon_out` = 0 WHERE `dungeon_user` = {$_GET['user']}");
            die($h->endpage());
        } //User failed. Tell person and throw user in dungeon.
        else {
            $time = min($mult, 100);
            $reason = $db->escape("Caught trying to bust out {$api->SystemUserIDtoName($_GET['user'])}");
            $api->GameAddNotification($_GET['user'], "<a href='profile.php?user={$userid}'>{$ir['username']}</a> has
                failed to bust you out of the dungeon.");
            alert('danger', "Uh Oh!", "While trying to bust your friend out, you were spotted by a guard.", true, 'dungeon.php');
            $api->UserStatusSet($userid, 'dungeon', $time, $reason);
            die($h->endpage());
        }
    } else {
        alert('danger', "Uh Oh!", "You need to specify a user you wish to bust out of the dungeon.", true, 'dungeon.php');
    }
}